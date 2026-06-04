<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointments\Appointment;
use App\Models\Customer;
use App\Models\Grn;
use App\Models\GrnDetails;
use App\Models\GrnRequest;
use App\Models\GrnRequestDetails;
use App\Models\Market;
use App\Models\Patient\Patient;
use App\Models\PaymentDetail;
use App\Models\PaymentType;
use App\Models\PharmacyTransfer;
use App\Models\PharmacyTransferDetails;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetails;
use App\Models\SupplierPayment;
use App\Models\TempSale;
use App\Models\TempSaleDetails;
use App\Exports\CustomerBillA4ExcelExport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Finance\FinanceVoucher;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class SupplierPayments extends Controller
{
    public function supplier_receive_payment()
    {
        $data['title'] = 'Supplier Receive Payment';
        $data['suppliers'] = Customer::where('Type', 1)
            ->where('IsActive', 1)
            ->orderBy('Name', 'asc')
            ->get(['SCID', 'Name', 'ContactNo', 'finance_head_id']);

        return view('warehouse.supplier_receive_payment', $data);
    }

    public function store_supplier_receive_payment()
    {
        request()->validate([
            'SCID' => ['required', 'integer'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'remarks' => ['nullable', 'string', 'max:255'],
        ]);

        SupplierPayment::create([
            'sale_id' => 0,
            'SCID' => (int) request()->SCID,
            // Keep consistent with existing customer logic: admission_id=1 means direct payment
            'admission_id' => 1,
            'is_posted' => 0,
            'amount' => (float) request()->amount,
            'remarks' => request()->remarks ?? '',
            'created_by' => Auth::id() ?? 0,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Payment received successfully.'
        ]);
    }

    public function supplier_receive_payment_list()
    {
        $scid = (int) request()->get('SCID', 0);
        $fromDate = request()->get('from_date');
        $toDate = request()->get('to_date');

        $query = SupplierPayment::query()
            ->where('is_active', 1)
            ->where('sale_id', 0)
            ->where('admission_id', 1);

        if ($scid > 0) {
            $query->where('SCID', $scid);
        }
        if (!empty($fromDate)) {
            $query->whereDate('created_at', '>=', $fromDate);
        }
        if (!empty($toDate)) {
            $query->whereDate('created_at', '<=', $toDate);
        }

        $query->orderByDesc('id');

        return DataTables::of($query)
            ->addColumn('actions', function ($row) {
                $id = $row->id;
                if ((int) ($row->is_posted ?? 0) === 0) {
                    return '<button type="button" class="btn btn-sm btn-warning edit_payment" data-id="' . $id . '">Edit</button> '
                        . '<button type="button" class="btn btn-sm btn-danger delete_payment" data-id="' . $id . '">Delete</button> '
                        . '<button type="button" class="btn btn-sm btn-primary approve_payment" data-id="' . $id . '">Approve</button>';
                }
                return '<span class="badge bg-success">Approved</span>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    private function findDirectSupplierPaymentOrFail(int $paymentId): SupplierPayment
    {
        $payment = SupplierPayment::with(['supplier'])->where('id', $paymentId)
            ->where('sale_id', 0)
            ->where('admission_id', 1)
            ->where('is_active', 1)
            ->first();

        if (!$payment) {
            abort(404, 'Payment not found');
        }
        return $payment;
    }

    public function get_supplier_receive_payment($payment_id)
    {
        $payment = $this->findDirectSupplierPaymentOrFail((int) $payment_id);
        return response()->json([
            'status' => true,
            'data' => $payment,
        ]);
    }

    public function update_supplier_receive_payment()
    {
        request()->validate([
            'payment_id' => ['required', 'integer'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'remarks' => ['nullable', 'string', 'max:255'],
        ]);

        $payment = $this->findDirectSupplierPaymentOrFail((int) request()->payment_id);

        $payment->update([
            'amount' => (float) request()->amount,
            'remarks' => request()->remarks ?? null,
            'updated_by' => Auth::id() ?? 0,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Payment updated successfully.'
        ]);
    }

    public function delete_supplier_receive_payment()
    {
        request()->validate([
            'payment_id' => ['required', 'integer'],
        ]);

        $payment = $this->findDirectSupplierPaymentOrFail((int) request()->payment_id);
        $payment->update([
            'is_active' => 0,
            'updated_by' => Auth::id() ?? 0,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Payment deleted successfully.'
        ]);
    }

    public function approve_supplier_receive_payment()
    {
        request()->validate([
            'payment_id' => ['required', 'integer'],
        ]);

        $payment = $this->findDirectSupplierPaymentOrFail((int) request()->payment_id);
        if ((int) ($payment->is_posted ?? 0) === 1) {
            return response()->json([
                'status' => false,
                'message' => 'Payment is already approved.'
            ]);
        }

        $payment->update([
            'is_posted' => 1,
            'posted_on' => date('Y-m-d H:i:s'),
            'updated_by' => Auth::id() ?? 0,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Finance voucher posting (matches customer approve behavior)
        $user_id = auth()->user()->id;
        $voucherNumber = generateVoucherNumber('payment', $user_id);
        $amount = $payment->amount;

        $voucher = FinanceVoucher::create([
            'voucher_number' => $voucherNumber,
            'voucher_type' => 'payment',
            'user_id' => $user_id,
            'created_by' => auth()->user()->id,
            'voucher_date' => date('Y-m-d'),
            'total_amount' => $amount,
            'remarks' => 'Daily user closing of ' . (auth()->user()->name ?? ''),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $voucher_id = $voucher->id;
        $supplierName = $payment->supplier?->Name ?? '';
        $supplierHeadId = $payment->supplier?->finance_head_id;
        $remarks = 'Cash Paid to Supplier: ' . $supplierName;

        if ($supplierHeadId) {
            // Supplier payment: supplier debit, cash credit
            make_entry($voucher_id, $supplierHeadId, $amount, 0, 'supplier_payments', $payment->id, $user_id, $remarks);
            make_entry($voucher_id, financeHeadId('cash_at_office'), 0, $amount, 'supplier_payments', $payment->id, $user_id, $remarks);
        }

        return response()->json([
            'status' => true,
            'message' => 'Payment approved successfully.'
        ]);
    }

    public function supplier_payments()
    {
        $data["title"] = "Supplier Payments";
        $data["suppliers"] = Customer::where("Type", 1)->get();
        $data["payment_type"] = PaymentType::get();
        return view("warehouse/supplier_payments", $data);
    }


    public function save_payments(Request $request)
    {

        $data = $request->all();
        $data["CreatedBy"] = Auth::user()->id;
        $data["CreatedAt"] = Carbon::today();
        $data["SCID"] = $request->SID;

        $paymentDetail = PaymentDetail::create($data);

        return response()->json([
            "data" => $paymentDetail
        ]);
    }


    public function get_payments($id)
    {
        $data = PaymentDetail::where('SCID', $id)
            ->with('paymentType');
        return DataTables::of($data)
            ->addColumn('payment_type', function ($data) {
                return $data->paymentType->payment_type ?? ''; // Safeguard against null
            })

            ->addColumn('action', function ($data) {
                // Example: Add an Edit/Delete button for actions
                return '<a class="btn btn-sm btn-primary">Edit</a>
                    <a class="btn btn-sm btn-danger">Delete</a>';
            })
            ->rawColumns(['payment_type', 'action']) // Allow raw HTML for action buttons
            ->make(true);
    }

    // public function get_payments($id){
    //     $data = PaymentDetail::where("SCID", $id)->with("paymentType")->get();

    //     // dd($data);
    //     return DataTables::of($data)
    //     ->addColumn('payment_type', function ($data) {
    //         return $data->paymentType->payment_type ?? '';
    //     });
    //     // return response()->json([
    //     //     "data"=> $data
    //     // ]);

    // }

    public function purchase_details($id = '')
    {

        $data =  Grn::when($id, function ($query) use ($id) {
            return $query->where("SCID", $id);
        });
        //  <a class="btn btn-sm btn-primary" href="'.route('pos.edit_purchase_bill',[$data->GRNID]).'">Edit</a>
        return DataTables::of($data)
            ->addColumn('final_bill', function ($data) {
                return $data->TotalPurchase - ($data->per_item_discount) - ($data->Discount);
            })
            ->addColumn('action', function ($data) {

                return '<a class="btn btn-sm btn-success" href="' . route('pos.print_purchase', [$data->SCID, $data->GRNID]) . '">Print Bill</a>
                <a class="btn btn-sm btn-success" href="' . route('pos.add_bill_items', [$data->GRNID]) . '">Edit</a>
               
                    <a class="btn btn-sm btn-danger">Delete</a>';
            })
            ->rawColumns(['final_bill', 'action']) // Allow raw HTML for action buttons
            ->make(true);
    }

    function supplier_previous_balance($customer_id, $date = '')
    {
        $customer = Customer::where(["sup_cus_details.SCID" => $customer_id])->first();

        $openingBalance = $customer->OpeningBalance;
        if (!$openingBalance) {
            $openingBalance = 0;
        }
        $where = array('SCID' => $customer_id);
        if ($date != '') {
            $where['Date <'] = $date;
        }
        /*$TotalSale = Grn::where(["SCID"=>$customer_id])
            ->when($date, function ($query) use ($date) {
                return $query->where('Dated', '>=', date("Y-m-d",strtotime($date)));
            })->sum('TotalPurchase');*/

        $query = Grn::where('SCID', $customer_id)
            ->when($date, function ($query) use ($date) {
                return $query->where('Dated', '>=', date("Y-m-d", strtotime($date)));
            });

        $totals = $query->selectRaw('SUM(TotalPurchase) as total_bill, SUM(Discount) as discount, SUM(per_item_discount) as per_item_discount')
            ->first();

        $TotalSale = $totals->total_bill;
        $totalDiscount = $totals->discount;
        $per_item_discount = $totals->per_item_discount;


        $TotalPaid = SupplierPayment::where(["SCID" => $customer_id])
            ->when($date, function ($query) use ($date) {
                return $query->where('Dated', '<', date("Y-m-d", strtotime($date)));
            })->sum('Amount');

           


        //$TotalAmount = ($openingBalance + $TotalSale) - ($totalDiscount) - ($per_item_discount) - $TotalPaid;
        $TotalAmount = ($openingBalance + $TotalSale) - $TotalPaid;
        if ($TotalAmount) {
            return $TotalAmount;
        } else {
            return 0;
        }
    }

    public function get_purchase_bill_items($id)
    {
        $data = GrnDetails::where('GRNID', $id)
            ->with('products');
        return DataTables::of($data)

            ->addColumn('total', function ($data) {
                return ($data->Quantity * $data->pack_price);
            })
            ->addColumn('action', function ($data) {
                return '<a class="btn btn-sm btn-primary edit_bill_item" data-details=\'' . $data . '\'>Edit</a>
                    ';
            })
            ->rawColumns(['total', 'action']) // Allow raw HTML for action buttons
            ->make(true);
    }


    public function add_bill_items($id)
    {
        /*$data["products"]= Product::with('generic_name')
            ->when(session('store_id'),function ($q){
                $q->where('store_id',session('store_id'));
            })
            ->where("IsActive", 1)->where("pack_size","!=",0)->where("pack_price","!=",0)->get();*/
        //Cache::forget('products_store_2');
        $data["products"] = Product::with('generic_name')
            ->when(session('store_id'), function ($q) {
                $q->where('store_id', session('store_id'));
            })
            ->orderBy("ProductName", "ASC")
            ->where("ProductName", "!=", '')
            ->where("IsActive", 1)
            ->where("pack_size", "!=", 0)
            ->where("pack_price", "!=", 0)
            ->get();
        //$data["products"] = [];
        foreach ($data['products'] as $key => $value) {
            $value->avaliable_qty = GrnDetails::where(["ProductID" => $value->ProductID])->sum('RemainingQuantity');
        }
        $data['grn'] = GrnRequest::where('GRNID', $id)->with("products")->with('store')->first();
        $data['purchase'] = GrnRequestDetails::where('GRNID', $id)->with("products")->orderBy("GDID", "DESC")->where(["ProductStatus" => 1])->paginate(400);
        $data['id'] = $id;



        // return $data;
        return view('reports/print_purchase_details', $data);
    }

    public function print_thermel_purchase_details($SaleID)
    {


        $date = date("Y-m-d");

        $data['record'] = Sale::where(['SaleID' => $SaleID])->get();
        $data['patient'] = Patient::where(["id" => $data['record'][0]->patient_id])->first();
        $customer_id = $data['record'][0]->SCID;
        $billDate = date("d-m-Y", strtotime($data['record'][0]->Date));

        //$data['PreviousBalance']=(new CustomerPayments())->customer_previous_balance($customer_id,$date);

        $data['data'] = SaleDetails::with('product')->where(['SaleID' => $SaleID])->get();
        $data['title'] = 'Sale Details Report';
        $return = "No";
        $totalAmount = 0;
        $data['prev_balance'] = (new CustomerPayments())->customer_previous_balance($customer_id, '');

        foreach ($data['data'] as $rec) {
            $rec->AvaliableQuantity = ($rec->Quantity) - ($rec->ReturnQuantity);
            $rec->totalAmount = ($rec->AvaliableQuantity) * ($rec->UnitePrice);
            $totalAmount = ($totalAmount) + ($rec->totalAmount);
            if ($rec->ReturnQuantity > 0) {
                $return = "Yes";
            }
        }

        $result = [];

        // Iterate through the array remove duplicate items . sum the quantity ,totalamount, taxamount and remove duplication for bill print only...//
        foreach ($data['data'] as $item) {
            $productId = $item->ProductID;

            // If ProductID already exists in the result, sum up the Quantity and UnitePrice
            if (isset($result[$productId])) {
                $result[$productId]->Quantity += $item->Quantity;
                $result[$productId]->totalAmount += $item->totalAmount;
                $result[$productId]->taxAmount += $item->taxAmount;
            } else {
                // Add new ProductID to result
                $result[$productId] = clone $item;
            }
        }
        $result = array_values($result);
        $data['data'] = $result;


        if ($return == "Yes") {
            $data['return'] = "Yes";
        } else {
            $data['return'] = "No";
        }


        $data['TotalAmount'] = $totalAmount;
        $data['show_customer_contact'] = "true";

        $data['customer'] = Customer::where("SCID", $customer_id)->get();

        return view('reports/print_sale_invoice', $data);
    }

    public function print_retail_thermel_purchase_details($SaleID)
    {
        $date = date("Y-m-d");
        $data['record'] = Sale::where(['SaleID' => $SaleID])->with('created_by')->first();

        if (!$data['record']) {
            abort(404, 'Sale record not found');
        }

        $data['patient'] = Patient::where(["id" => $data['record']->patient_id])->first();
        $data['appointment_patient_name'] = 'Walking Customer';
        if ($data['record']->admission_id) {
            $data['appointment_patient_name'] = $data['patient'] ? "In-Patient <br><br>MR# " . " | Name: " . $data['patient']->name : "";
        } elseif ($data['record']->appointment_id) {
            $appointments = Appointment::where('is_active', 1)
                ->where('id', $data['record']->appointment_id) // last 5 days
                ->with(['patient'])
                ->first();
            $data['appointment_patient_name'] = $appointments ? "Out Patient <br>MR# " . $appointments->patient?->mr_no . " | Name: " . $appointments->patient?->name . " <br><br>Appointment# " . $appointments->appointment_number : "";
        }

        $customer_id = $data['record']->SCID;
        $billDate = date("d-m-Y", strtotime($data['record']->Date));

        $discount_percentage = $data['record']->discount_percentage;

        //$data['PreviousBalance']=(new CustomerPayments())->customer_previous_balance($customer_id,$date);

        $data['data'] = SaleDetails::with('product')->where(['SaleID' => $SaleID])->get();
        $data['title'] = 'Sale Details Report';
        $return = "No";
        $totalAmount = 0;
        $data['prev_balance'] = (new CustomerPayments())->customer_previous_balance($customer_id, '');

        foreach ($data['data'] as $rec) {
            $rec->AvaliableQuantity = max(0, ($rec->Quantity) - ($rec->ReturnQuantity));
            $rec->totalAmount = ($rec->AvaliableQuantity) * ($rec->UnitePrice);
            $rec->itemDiscountAmount = 0;
            $rec->totalAmountAfterDiscount = $rec->totalAmount;
            $this->enrichDetailRowTaxForPrint($rec);
            $totalAmount = ($totalAmount) + ($rec->totalAmountAfterDiscount);
            if ($rec->ReturnQuantity > 0) {
                $return = "Yes";
            }
        }

        $this->applyRowTaxDataToPrint($data);

        $result = [];

        // Iterate through the array remove duplicate items . sum the quantity ,totalamount, taxamount and remove duplication for bill print only...//
        foreach ($data['data'] as $item) {
            $productId = $item->ProductID;

            // If ProductID already exists in the result, sum up the Quantity and UnitePrice
            if (isset($result[$productId])) {
                $result[$productId]->Quantity += $item->Quantity;
                $result[$productId]->totalAmount += $item->totalAmount;
                $result[$productId]->taxAmount += $item->taxAmount;
                $result[$productId]->row_sale_tax_amount = ($result[$productId]->row_sale_tax_amount ?? 0) + ($item->row_sale_tax_amount ?? 0);
                $result[$productId]->row_income_tax_amount = ($result[$productId]->row_income_tax_amount ?? 0) + ($item->row_income_tax_amount ?? 0);
            } else {
                // Add new ProductID to result
                $result[$productId] = clone $item;
            }
        }

        $result = array_values($result);
        $data['data'] = $result;


        if ($return == "Yes") {
            $data['return'] = "Yes";
        } else {
            $data['return'] = "No";
        }


        $data['TotalAmount'] = $totalAmount;
        $data['TotalDiscount'] = ($totalAmount * $discount_percentage) / 100;

        $data['show_customer_contact'] = "true";

        $data['customer'] = Customer::where("SCID", $customer_id)->get();

        return view('reports/print_retail_sale_invoice', $data);
    }

    public function print_customer_bill($SaleID)
    {
        $date = date("Y-m-d");
        $data['record'] = TempSale::with(['created_by'])->where(['SaleID' => $SaleID])->first();


        $data['patient'] = Patient::where(["id" => $data['record']->patient_id])->first();
        $data['appointment_patient_name'] = 'Walking Customer';
        if ($data['record']->admission_id) {
            $data['appointment_patient_name'] = $data['patient'] ? "In-Patient <br><br>MR# " . $data['patient']->mr_no . " | Name: " . $data['patient']->name : "";
        } elseif ($data['record']->appointment_id) {
            $appointments = Appointment::where('is_active', 1)
                ->where('id', $data['record']->appointment_id) // last 5 days
                ->with(['patient'])
                ->first();
            $data['appointment_patient_name'] = $appointments ? "Out Patient <br><br>MR# " . $appointments->patient?->mr_no . " | Name: " . $appointments->patient?->name . " <br><br>Appointment# " . $appointments->appointment_number : "";
        }

        $customer_id = $data['record']->SCID;
        $billDate = date("d-m-Y", strtotime($data['record']->Date));

        $discount_percentage = $data['record']->discount_percentage;

        //$data['PreviousBalance']=(new CustomerPayments())->customer_previous_balance($customer_id,$date);

        $data['data'] = TempSaleDetails::with('product')->where(['SaleID' => $SaleID])->get();
        $data['title'] = 'Sale Details Report';
        $return = "No";
        $totalAmount = 0;
        $data['prev_balance'] = (new CustomerPayments())->customer_previous_balance($customer_id, '');

        foreach ($data['data'] as $rec) {
            $rec->AvaliableQuantity = max(0, ($rec->Quantity) - ($rec->ReturnQuantity));
            $rec->totalAmount = ($rec->AvaliableQuantity) * ($rec->UnitePrice);

            // Calculate proportional discount amount for active quantity after returns
            if ($rec->AvaliableQuantity > 0) {
                if (isset($rec->discount_percentage_amount) && $rec->discount_percentage_amount > 0) {
                    // Calculate proportional discount based on active quantity
                    $proportion = $rec->AvaliableQuantity / max(1, $rec->Quantity); // Avoid division by zero
                    $rec->itemDiscountAmount = $rec->discount_percentage_amount * $proportion;
                } else if (isset($rec->discount_percentage) && $rec->discount_percentage > 0) {
                    // Calculate discount from percentage for active quantity
                    $rec->itemDiscountAmount = ($rec->totalAmount * $rec->discount_percentage) / 100;
                } else {
                    $rec->itemDiscountAmount = 0;
                }
            } else {
                // If no active quantity, no discount
                $rec->itemDiscountAmount = 0;
            }

            // Apply discount to total amount
            $rec->totalAmountAfterDiscount = max(0, $rec->totalAmount - $rec->itemDiscountAmount);
            $this->enrichDetailRowTaxForPrint($rec);
            $totalAmount = ($totalAmount) + ($rec->totalAmountAfterDiscount);

            if ($rec->ReturnQuantity > 0) {
                $return = "Yes";
            }
        }

        $this->applyRowTaxDataToPrint($data);

        $result = [];

        // Iterate through the array remove duplicate items . sum the quantity ,totalamount, taxamount and remove duplication for bill print only...//
        foreach ($data['data'] as $item) {
            $productId = $item->ProductID;

            // If ProductID already exists in the result, sum up the Quantity and UnitePrice
            if (isset($result[$productId])) {
                $result[$productId]->Quantity += $item->Quantity;
                $result[$productId]->totalAmount += $item->totalAmount;
                $result[$productId]->taxAmount += $item->taxAmount;
                $result[$productId]->row_sale_tax_amount = ($result[$productId]->row_sale_tax_amount ?? 0) + ($item->row_sale_tax_amount ?? 0);
                $result[$productId]->row_income_tax_amount = ($result[$productId]->row_income_tax_amount ?? 0) + ($item->row_income_tax_amount ?? 0);
            } else {
                // Add new ProductID to result
                $result[$productId] = clone $item;
            }
        }

        $result = array_values($result);
        $data['data'] = $result;


        if ($return == "Yes") {
            $data['return'] = "Yes";
        } else {
            $data['return'] = "No";
        }


        $data['TotalAmount'] = $totalAmount;
        $data['TotalDiscount'] = ($totalAmount * $discount_percentage) / 100;

        $data['show_customer_contact'] = "true";

        $data['customer'] = Customer::where("SCID", $customer_id)->get();

        return view('reports/print_retail_sale_invoice', $data);
    }

    public function print_customer_bill_a4($SaleID)
    {
        // Reuse the same dataset and calculations from the thermal bill,
        // but render using an A4 professional invoice layout.
        $date = date("Y-m-d");
        $data['record'] = TempSale::with(['created_by'])->where(['SaleID' => $SaleID])->first();

        if (!$data['record']) {
            abort(404, 'Sale record not found');
        }

        $data['patient'] = Patient::where(["id" => $data['record']->patient_id])->first();
        $data['appointment_patient_name'] = 'Name: Walking Customer';
        if ($data['record']->admission_id) {
            $data['appointment_patient_name'] = $data['patient'] ? "" . "Customer Name: " . $data['patient']->name : "";
        } elseif ($data['record']->appointment_id) {
            $appointments = Appointment::where('is_active', 1)
                ->where('id', $data['record']->appointment_id)
                ->with(['patient'])
                ->first();
            $data['appointment_patient_name'] = $appointments ? "<br>" . "Customer Name: " . $appointments->patient?->name . " <br>" . $appointments->appointment_number : "";
        }

        $customer_id = $data['record']->SCID;
        $discount_percentage = $data['record']->discount_percentage;

        $data['data'] = TempSaleDetails::with('product')->where(['SaleID' => $SaleID])->get();
        
        $return = "No";
        $totalAmount = 0;
        $data['prev_balance'] = (new CustomerPayments())->customer_previous_balance($customer_id, '');

        foreach ($data['data'] as $rec) {
           
            $rec->AvaliableQuantity = max(0, ($rec->Quantity) - ($rec->ReturnQuantity));
            $rec->totalAmount = ($rec->AvaliableQuantity) * ($rec->UnitePrice);

            // Calculate proportional discount amount for active quantity after returns
            if ($rec->AvaliableQuantity > 0) {
                if (isset($rec->discount_percentage_amount) && $rec->discount_percentage_amount > 0) {
                    $proportion = $rec->AvaliableQuantity / max(1, $rec->Quantity);
                    $rec->itemDiscountAmount = $rec->discount_percentage_amount * $proportion;
                } else if (isset($rec->discount_percentage) && $rec->discount_percentage > 0) {
                    $rec->itemDiscountAmount = ($rec->totalAmount * $rec->discount_percentage) / 100;
                } else {
                    $rec->itemDiscountAmount = 0;
                }
            } else {
                $rec->itemDiscountAmount = 0;
            }

            $rec->totalAmountAfterDiscount = max(0, $rec->totalAmount - $rec->itemDiscountAmount);
            $this->enrichDetailRowTaxForPrint($rec);
            $totalAmount = ($totalAmount) + ($rec->totalAmountAfterDiscount);

            if ($rec->ReturnQuantity > 0) {
                $return = "Yes";
            }
        }

        $this->applyRowTaxDataToPrint($data);

        // Remove duplicate items for print only (sum qty/amount/tax)
        $result = [];
        foreach ($data['data'] as $item) {
            $productId = $item->ProductID;
            if (isset($result[$productId])) {
                $result[$productId]->Quantity += $item->Quantity;
                $result[$productId]->totalAmount += $item->totalAmount;
                $result[$productId]->taxAmount += $item->taxAmount;
                $result[$productId]->row_sale_tax_amount = ($result[$productId]->row_sale_tax_amount ?? 0) + ($item->row_sale_tax_amount ?? 0);
                $result[$productId]->row_income_tax_amount = ($result[$productId]->row_income_tax_amount ?? 0) + ($item->row_income_tax_amount ?? 0);
            } else {
                $result[$productId] = clone $item;
            }
        }
        $data['data'] = array_values($result);

        $data['return'] = $return;
        $data['TotalAmount'] = $totalAmount;
        $data['TotalDiscount'] = ($totalAmount * $discount_percentage) / 100;
        $data['show_customer_contact'] = "true";
        $data['customer'] = Customer::where("SCID", $customer_id)->get();

        return view('reports/print_retail_sale_invoice_a4', $data);
    }

    public function print_customer_bill_excel($SaleID)
    {
        $saleId = (int) $SaleID;

        $sale = TempSale::where('SaleID', $saleId)->first();
        if (!$sale) {
            abort(404, 'Sale record not found');
        }

        $invoice = $sale->InvoiceNo ?: ('Sale_' . $saleId);
        $fileName = 'invoice_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', (string) $invoice) . '.xlsx';

        return Excel::download(new CustomerBillA4ExcelExport($saleId), $fileName);
    }

    public function print_pharmacy_transfer_bill($SaleID)
    {
        $date = date("Y-m-d");
        $data['record'] = PharmacyTransfer::with(['created_by'])->where(['id' => $SaleID])->first();



        $data['appointment_patient_name'] = 'Pharmacy Transfer';




        $billDate = date("d-m-Y", strtotime($data['record']->Date));

        $discount_percentage = $data['record']->discount_percentage;

        //$data['PreviousBalance']=(new CustomerPayments())->customer_previous_balance($customer_id,$date);

        $data['data'] = PharmacyTransferDetails::with('product')->where(['temp_sale_id' => $SaleID])->get();
        $data['title'] = 'Pharmacy Transfer Report';
        $return = "No";
        $totalAmount = 0;

        foreach ($data['data'] as $rec) {
            $rec->AvaliableQuantity = ($rec->Quantity) - ($rec->ReturnQuantity);
            $rec->totalAmount = ($rec->AvaliableQuantity) * ($rec->UnitePrice);
            $totalAmount = ($totalAmount) + ($rec->totalAmount);
            if ($rec->ReturnQuantity > 0) {
                $return = "Yes";
            }
        }


        $result = [];

        // Iterate through the array remove duplicate items . sum the quantity ,totalamount, taxamount and remove duplication for bill print only...//
        foreach ($data['data'] as $item) {
            $productId = $item->ProductID;

            // If ProductID already exists in the result, sum up the Quantity and UnitePrice
            if (isset($result[$productId])) {
                $result[$productId]->Quantity += $item->Quantity;
                $result[$productId]->totalAmount += $item->totalAmount;
                $result[$productId]->taxAmount += $item->taxAmount;
            } else {
                // Add new ProductID to result
                $result[$productId] = clone $item;
            }
        }

        $result = array_values($result);
        $data['data'] = $result;


        if ($return == "Yes") {
            $data['return'] = "Yes";
        } else {
            $data['return'] = "No";
        }


        $data['TotalAmount'] = $totalAmount;
        $data['TotalDiscount'] = ($totalAmount * $discount_percentage) / 100;

        $data['show_customer_contact'] = "true";

        $data['customer'] = [];

        //  $this->printFormattedReceipt($data['data'], $data['record'], $data['patient'], $data['TotalDiscount']);
        //$this->printFormattedReceipt($data['data'], $data['record'], $data['patient'], $data['TotalDiscount']);


        return view('reports/print_pharmacy_transfer_invoice', $data);
    }

    public function printFormattedReceipt($data, $record, $patient, $TotalDiscount)
    {
        try {
            $connector = new WindowsPrintConnector(env("THERMAL_PRINTER_NAME"));
            $printer = new Printer($connector);

            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text(env('COMPANY_NAME') . "\n");
            $printer->text(date("d-m-Y h:i A") . "\n");
            $printer->text("------------------------------------------\n");

            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Name: " . ($patient->name ?? '') . "\n");
            $printer->text("Printed By: " . (auth()->user()->name ?? '') . "\n");
            $printer->text("------------------------------------------\n");

            // Table Header
            $printer->text(
                str_pad("No", 4) .
                    str_pad("Item", 18) .
                    str_pad("Qty", 5, ' ', STR_PAD_LEFT) .
                    str_pad("Price", 7, ' ', STR_PAD_LEFT) .
                    str_pad("Amt", 8, ' ', STR_PAD_LEFT) . "\n"
            );
            $printer->text("------------------------------------------\n");

            $i = 1;
            $totalAmount = 0;

            foreach ($data as $d) {
                $qty = $d->Quantity - $d->ReturnQuantity;
                $name = substr($d->product->ProductName, 0, 18); // truncate if too long
                $price = number_format($d->UnitePrice, 0);
                $amount = number_format($d->totalAmount, 0);
                $totalAmount += $d->totalAmount;

                $printer->text(
                    str_pad($i++, 4) .
                        str_pad($name, 18) .
                        str_pad($qty, 5, ' ', STR_PAD_LEFT) .
                        str_pad($price, 7, ' ', STR_PAD_LEFT) .
                        str_pad($amount, 8, ' ', STR_PAD_LEFT) . "\n"
                );

                if ($d->ReturnQuantity > 0) {
                    $printer->text("     (Return: {$d->ReturnQuantity})\n");
                }
            }

            $printer->text("------------------------------------------\n");

            $discount = round($TotalDiscount + $record->invoice_discount);
            $finalAmount = max(0, round($totalAmount - $record->Discount - $record->invoice_discount));

            $printer->text(str_pad("Total:", 34) . str_pad(number_format($totalAmount, 0), 8, ' ', STR_PAD_LEFT) . "\n");
            $printer->text(str_pad("Discount:", 34) . str_pad(number_format($discount, 0), 8, ' ', STR_PAD_LEFT) . "\n");
            $printer->text(str_pad("Amount Due:", 34) . str_pad(number_format($finalAmount, 0), 8, ' ', STR_PAD_LEFT) . "\n");

            $printer->text("\nThank you for visiting!\n");
            $printer->text("\n\n\n");
            $printer->cut();
            $printer->close();

            return response("Printed successfully");
        } catch (\Exception $e) {
            return response("Print failed: " . $e->getMessage(), 500);
        }
    }

    public function previous_bills()
    {
        $bills = Sale::orderBy("SaleID", "DESC")->with("patient")
            ->where('store_id', env('SEHAT_CARD_PHARMACY_STORE_ID'))

            ->limit(50);

        return DataTables::of($bills)

            ->addColumn('action', function ($data) {
                return '<a target="_blank" href="' . route("pos.print_thermel_purchase_details", [$data->SaleID]) . '" class="btn btn-sm btn-success ">Print Thermal</a>';
            })

            ->rawColumns(["action"])
            ->make(true);
    }

    public function retail_previous_bills()
    {
        $printMode = request()->get('retail_print_mode');
        if (in_array($printMode, ['thermal', 'a4'], true)) {
            session(['retail_print_mode' => $printMode]);
        } else {
            $printMode = session('retail_print_mode', 'a4');
        }

        $bills = Sale::orderBy("SaleID", "DESC")->with("patient")->with('created_by')
            ->when(session('store_id'), function ($q) {
                $q->where('store_id', session('store_id'));
            })
            ->when(( isset($_GET['sale_type']) && $_GET['sale_type']), function ($q) {
                $q->where('sale_type', 'package_sale');
            }) ->when(( isset($_GET['appointment_id']) && $_GET['appointment_id']), function ($q) {
                $q->where('appointment_id', $_GET['appointment_id']);
            })
            ->where("admission_id", "=", 0)
            /* ->when((userRole() != "Super Admin" && userRole() != "Receiption User"), function ($q) {
                return $q->where(["CreatedBy" => auth()->user()->id]);
            })*/
            ->limit(600);

        $dataTable = DataTables::of($bills)
            ->addColumn('net_amount', function ($data) {
                return $this->calculateSaleNetAmount((int) $data->SaleID);
            })
            ->addColumn('action', function ($data) use ($printMode) {
                $buttons = "";
                $printUrl = $this->getRetailBillPrintUrl((int) $data->SaleID, $printMode);
                $buttons .= '<a target="_blank" href="' . route("pos.return_pharmacy_product", [$data->SaleID]) . '" class="btn btn-sm btn-success ">Return</a>';
                if ($data->is_return_made == 1) {
                    $buttons .= '&nbsp;&nbsp;<a target="_blank" href="' . $printUrl . '" class="btn btn-sm btn-success ">Print Bill</a>';
                    $buttons .= '&nbsp;&nbsp;<a class="btn btn-sm btn-danger" title="Return is taken in this invoice" href="javascript:void(0)" style="height:5px;width:5px;font-weight: bold;">&nbsp;&nbsp;.&nbsp;&nbsp;</a>';
                } else {
                    $buttons .= '&nbsp;&nbsp;<a target="_blank" href="' . $printUrl . '" class="btn btn-sm btn-success ">Print Bill</a>';
                }

                // Add Print E-Prescription button if appointment_id exists
                if ($data->appointment_id && $data->appointment_id > 0) {
                    $buttons .= '&nbsp;&nbsp;<a target="_blank" href="' . route("pos.print_e_prescription", [$data->appointment_id]) . '" class="btn btn-sm btn-primary ">Print E-Prescription</a>';
                }

                return $buttons;
            })
            ->rawColumns(["action"]);

        return $this->applyPreviousBillsNetAmountDataTable($dataTable)->make(true);
    }

    public function pharmacy_transfer_bills()
    {
        $bills = PharmacyTransfer::orderBy("id", "DESC")->with("patient")->with('created_by')
            ->where("admission_id", "=", 0)
            ->limit(600);

        return DataTables::of($bills)

            ->addColumn('action', function ($data) {
                $buttons = "";
                if ($data->is_return_made == 1) {
                    /*$buttons .= '&nbsp;&nbsp;<a target="_blank" href="' . route("pos.print_retail_thermel_purchase_details", [$data->SaleID]) . '" class="btn btn-sm btn-success ">Print Bill</a>';*/
                    $buttons .= '&nbsp;&nbsp;<a target="_blank" href="' . route("pos.print_pharmacy_transfer_bill", [$data->id]) . '" class="btn btn-sm btn-success ">Print Bill</a>';
                    $buttons .= '&nbsp;&nbsp;<a class="btn btn-sm btn-danger" title="Return is taken in this invoice" href="javascript:void(0)" style="height:5px;width:5px;font-weight: bold;">&nbsp;&nbsp;.&nbsp;&nbsp;</a>';
                } else {
                    $buttons .= '&nbsp;&nbsp;<a target="_blank" href="' . route("pos.print_pharmacy_transfer_bill", [$data->id]) . '" class="btn btn-sm btn-success ">Print Bill</a>';
                }

                return $buttons;
            })
            ->rawColumns(["action"])
            ->make(true);
    }

    public function in_patient_retail_previous_bills()
    {
        $printMode = request()->get('retail_print_mode');
        if (in_array($printMode, ['thermal', 'a4'], true)) {
            session(['retail_print_mode' => $printMode]);
        } else {
            $printMode = session('retail_print_mode', 'a4');
        }

        $bills = Sale::orderBy("SaleID", "DESC")->with("patient")->with('created_by')
            ->when(session('store_id'), function ($q) {
                $q->where('store_id', session('store_id'));
            })
            /* ->when((userRole() != "Super Admin" && userRole() != "Receiption User"), function ($q) {
                return $q->where(["CreatedBy" => auth()->user()->id]);
            })*/
            ->where("admission_id", "!=", 0)
            ->limit(600);

        $dataTable = DataTables::of($bills)
            ->addColumn('net_amount', function ($data) {
                return $this->calculateSaleNetAmount((int) $data->SaleID);
            })
            ->addColumn('action', function ($data) use ($printMode) {
                $buttons = "";
                $printUrl = $this->getRetailBillPrintUrl((int) $data->SaleID, $printMode);
                $buttons .= '<a target="_blank" href="' . route("pos.return_customer_product", [$data->SaleID]) . '" class="btn btn-sm btn-success ">Return</a>';
                if ($data->is_return_made == 1) {
                    $buttons .= '&nbsp;&nbsp;<a target="_blank" href="' . $printUrl . '" class="btn btn-sm btn-success ">Print Bill</a>';
                    $buttons .= '&nbsp;&nbsp;<a class="btn btn-sm btn-danger" title="Return is taken in this invoice" href="javascript:void(0)" style="height:5px;width:5px;font-weight: bold;">&nbsp;&nbsp;.&nbsp;&nbsp;</a>';
                } else {
                    $buttons .= '&nbsp;&nbsp;<a target="_blank" href="' . $printUrl . '" class="btn btn-sm btn-success ">Print Bill</a>';
                }

                return $buttons;
            })
            ->rawColumns(["action"]);

        return $this->applyPreviousBillsNetAmountDataTable($dataTable)->make(true);
    }

    protected function getRetailBillPrintUrl(int $saleId, ?string $mode = null): string
    {
        $mode = $mode ?? session('retail_print_mode', 'a4');

        if (!in_array($mode, ['thermal', 'a4'], true)) {
            $mode = 'a4';
        }

        if ($mode === 'thermal') {
            return route('pos.print_retail_thermal_bill', $saleId);
        }

        return route('pos.print_customer_bill', $saleId);
    }

    /**
     * SQL expression matching calculateSaleNetAmount() for DataTables search/sort.
     */
    protected function saleNetAmountSqlExpression(string $table = 'sale'): string
    {
        return 'ROUND(GREATEST(0, COALESCE(' . $table . '.TotalSale, 0) - COALESCE(' . $table . '.Discount, 0) - COALESCE(' . $table . '.invoice_discount, 0))) + COALESCE(' . $table . '.tax_amount, 0)';
    }

    protected function applyPreviousBillsNetAmountDataTable($dataTable)
    {
        $netExpr = $this->saleNetAmountSqlExpression('sale');

        return $dataTable
            ->filterColumn('net_amount', function ($query, $keyword) use ($netExpr) {
                $keyword = trim(str_replace([',', ' '], '', $keyword));
                if ($keyword === '') {
                    return;
                }
                $query->whereRaw('CAST((' . $netExpr . ') AS CHAR) LIKE ?', ['%' . $keyword . '%']);
            })
            ->orderColumn('net_amount', function ($query, $order) use ($netExpr) {
                $direction = strtolower($order) === 'desc' ? 'desc' : 'asc';
                $query->orderByRaw('(' . $netExpr . ') ' . $direction);
            });
    }

    protected function calculateSaleNetAmount(int $saleId): string
    {
        $sale = Sale::where('SaleID', $saleId)->first([
            'TotalSale',
            'Discount',
            'invoice_discount',
            'tax_amount',
        ]);

        if (!$sale) {
            return number_format(0, 2);
        }

        $totalBill = max(0, (float) ($sale->TotalSale ?? 0)
            - (float) ($sale->Discount ?? 0)
            - (float) ($sale->invoice_discount ?? 0));

            $totalBill = round((float) $totalBill);

        $taxAmount = (float) ($sale->tax_amount ?? 0);

        // if ($taxAmount <= 0) {
        //     $saleTaxes = SaleTax::where('sale_id', $saleId)->get(['tax_percentage']);
        //     foreach ($saleTaxes as $saleTax) {
        //         $taxPct = (float) ($saleTax->tax_percentage ?? 0);
        //         if ($taxPct > 0) {
        //             $taxAmount += round(($totalBill * $taxPct) / 100, 2);
        //         }
        //     }
        // }

        return number_format($totalBill + $taxAmount, 2);
    }

    public function print_purchase($SCID, $GRNID)
    {
        $data["supplier"] = Customer::where("Type", 1)->where("SCID", $SCID)->with("market")->first();
        $data["products"] = Product::where("IsActive", 1)->get();
        $data['purchase'] = GrnDetails::where('GRNID', $GRNID)->with("grn", "products")->orderBy("GDID", "DESC")->where(["GRNID" => $GRNID])->get();
        $data['id'] = $GRNID;

        // return $data;
        return view('reports/print_purchase', $data);
    }

    public function print_purchase_request($SCID, $GRNID)
    {
        $data["supplier"] = Customer::where("Type", 1)->where("SCID", $SCID)->with("market")->first();
        $data["products"] = Product::where("IsActive", 1)->get();
        $data["grn_request"] = GrnRequest::where("GRNID", $GRNID)->first();
        $data['purchase'] = GrnRequestDetails::where('GRNID', $GRNID)->with("products")->orderBy("GDID", "DESC")->where(["GRNID" => $GRNID])->get();


        $data['id'] = $GRNID;

        // return $data;
        return view('reports/print_purchase', $data);
    }

    /**
     * Per-row taxable amount and tax amounts for print.
     */
    protected function enrichDetailRowTaxForPrint(object $rec): void
    {
        $qty = (float) ($rec->Quantity ?? 0);
        $returnQty = (float) ($rec->ReturnQuantity ?? 0);
        $activeQty = max(0, (float) ($rec->AvaliableQuantity ?? ($qty - $returnQty)));
        $unitPrice = (float) ($rec->UnitePrice ?? 0);
        $lineTotal = $activeQty * $unitPrice;

        $discount = 0.0;
        if (isset($rec->itemDiscountAmount)) {
            $discount = (float) $rec->itemDiscountAmount;
        } elseif (!empty($rec->discount_percentage_amount) && $qty > 0) {
            $discount = (float) $rec->discount_percentage_amount * ($activeQty / $qty);
        } elseif (!empty($rec->discount_percentage)) {
            $discount = ($lineTotal * (float) $rec->discount_percentage) / 100;
        } elseif (isset($rec->totalAmountAfterDiscount)) {
            $discount = max(0, $lineTotal - (float) $rec->totalAmountAfterDiscount);
        }

        $taxable = max(0, $lineTotal - $discount);
        $rec->row_taxable_amount = $taxable;
        $rec->row_sale_tax_amount = round($taxable * ((float) ($rec->sale_tax ?? 0)) / 100, 2);
        $rec->row_income_tax_amount = round($taxable * ((float) ($rec->income_tax ?? 0)) / 100, 2);
    }

    /**
     * Build tax lines from sale_details / temp_sale_details row sale_tax & income_tax.
     */
    protected function buildSaleTaxLinesFromDetails(iterable $details): array
    {
        $saleTaxTotal = 0.0;
        $incomeTaxTotal = 0.0;

        foreach ($details as $d) {
            if (!isset($d->row_sale_tax_amount)) {
                $this->enrichDetailRowTaxForPrint($d);
            }
            $saleTaxTotal += (float) ($d->row_sale_tax_amount ?? 0);
            $incomeTaxTotal += (float) ($d->row_income_tax_amount ?? 0);
        }

        $lines = [];
        if ($saleTaxTotal > 0) {
            $lines[] = ['name' => 'Sale Tax', 'percentage' => null, 'amount' => round($saleTaxTotal, 2)];
        }
        if ($incomeTaxTotal > 0) {
            $lines[] = ['name' => 'Income Tax', 'percentage' => null, 'amount' => round($incomeTaxTotal, 2)];
        }

        if (empty($lines)) {
            $legacyTax = 0.0;
            foreach ($details as $d) {
                $legacyTax += (float) ($d->taxAmount ?? 0);
            }
            if ($legacyTax > 0) {
                $lines[] = ['name' => 'Tax', 'percentage' => null, 'amount' => round($legacyTax, 2)];
            }
        }

        $total = round($saleTaxTotal + $incomeTaxTotal, 2);
        if ($total <= 0 && !empty($lines)) {
            $total = round((float) array_sum(array_column($lines, 'amount')), 2);
        }

        return ['lines' => $lines, 'total' => $total];
    }

    /**
     * Attach row-wise tax breakdown to print view data (before product deduplication).
     */
    protected function applyRowTaxDataToPrint(array &$data): void
    {
        $details = $data['data'] ?? collect();
        $taxSummary = $this->buildSaleTaxLinesFromDetails($details);
        $data['sale_tax_lines'] = $taxSummary['lines'];

        $storedTaxAmount = (float) ($data['record']->tax_amount ?? 0);
        $data['total_row_tax_amount'] = $storedTaxAmount > 0
            ? round($storedTaxAmount, 2)
            : $taxSummary['total'];
    }
}

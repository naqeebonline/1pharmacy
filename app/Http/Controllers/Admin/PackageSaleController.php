<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PatientController\PatientAdmissionController;
use App\Models\Appointments\Appointment;
use App\Models\Customer;
use App\Models\GrnDetails;
use App\Models\Market;
use App\Models\OpdTypeProduct;
use App\Models\Patient\InPatientAdmission;
use App\Models\Patient\Patient;
use App\Models\Patient\PatientAdmission;
use App\Models\PharmacyRetrun;
use App\Models\PharmacyTransfer;
use App\Models\PharmacyTransferDetails;
use App\Models\Product;
use App\Models\ReceiveablesDetail;
use App\Models\Sale;
use App\Models\SaleDetails;
use App\Models\SalePayment;
use App\Models\Store;
use App\Models\TempSale;
use App\Models\TempSaleDetails;
use App\Models\WardRequest;
use App\Models\WardRequestDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PackageSaleController extends Controller
{
    public function package_sale()
    {
        $appointment_id = $_GET['appointment_id'];
        if(!$appointment_id){
            return "Invalid Appointment.";
        }
        $appointment = Appointment::where('id', $appointment_id)->where('is_active', 1)
            ->with(['patient'])
            ->orderBy('appointment_date', 'desc')
            ->first();
        
        if(!$appointment){
            return "Invalid Appointment.";
        }
        
        $opd_medicine = OpdTypeProduct::where("opd_type_id",$appointment->opd_type_id)->pluck('product_id');
        //dd($opd_medicine);
        $store = Store::where("id", "!=", env('SEHAT_CARD_PHARMACY_STORE_ID'))->first();
        session(['store_id' => 2]);
        session(['store_name' => "Package Sale"]);
        session(['is_free' => 0]);

        $type = $_GET['type'] ?? "Home";
        $data['type'] = $type;
        $data["ward_request"] = $_GET["ward_request"] ?? "";
        $data['patient_id'] = $appointment->patient_id;
        $data['appointment'] = $appointment;
        $data['list_products'] = [];

        $data['appointments'] = Appointment::where('is_active', 1)
            ->where('created_at', '>=', Carbon::now()->subDays(2)) // last 2 days
            ->with(['patient'])
            ->orderBy('appointment_date', 'desc')
            ->get();

        $data["title"] = "Package Sale";

        
        $data["products"] =  Product::with('generic_name')
            ->when(session('store_id'), function ($q) {
                $q->where('store_id', session('store_id'));
            })
            ->orderBy("ProductName", "ASC")
            ->where("IsActive", 1)
            ->where("ProductName", "!=", '')
            //->where("pack_size", "!=", 0)
            //->where("pack_price", "!=", 0)
            ->whereIn("ProductID", $opd_medicine)
            ->get();
            
        foreach ($data['products'] as $key => $value) {
            $value->avaliable_qty = GrnDetails::where(["ProductID" => $value->ProductID])->sum('RemainingQuantity');
        }
        
        $data['admitted_patients'] = Patient::where("patient_type", "walking_customer")->get();

        $data['invoiceNo'] = $this->returnInvoiceNumber();

        return view("sale.package_sale", $data);
    }

   
   public function save_package_sale()
    {
        $TotalSale = 0;
        foreach (request()->ProductList as $row) {
            $TotalSale = ($TotalSale) + (($row['Quantity'] * ($row['UnitePrice'])));
        }

        $patient_id = request()->patient_id;
        $invoice_discount = request()->invoice_discount;
        $admission_id = request()->patient_admission_id ?? 0;
        $customer = Patient::where(["id" => $patient_id])->first();
        $ReceivedAmountFromCustomer = 0;
        if (request()->ReceivedAmountFromCustomer) {
            $ReceivedAmountFromCustomer = request()->ReceivedAmountFromCustomer;
        }
        //-------------------------------------------//
        $Invoice = $this->returnInvoiceNumber();
        $SupplierID = $patient_id;
        $Freight = 0;
        $PDate = date("Y-m-d", strtotime(request()->bill_date));
        $Description = request()->BillDiscription;
        $appointment_id = request()->appointment_id;
        $medicine_type = request()->medicine_type;
        $bill_description = request()->BillDiscription;
        $discount_percentage = request()->discount_percentage ?? 0;
        $Discount = request()->discount_amount ?? 0;
        $demage = 0;
        $ReceivedAmount = request()->ReceivedAmount;

        $userID = auth()->user()->id;
        $totalTax = 0;

        $SalemanID = 0;
        $Commesion = 0;
        if ($customer) {
            $CustomerName = $customer->name . " - " . $customer->mr_no;
        } else {
            $CustomerName = "Walking Customer";
            $patient_id = 0;
        }

        if ($appointment_id) {
            $app = Appointment::where('is_active', 1)
                ->with(['patient'])
                ->where("id", $appointment_id)
                ->first();
            $CustomerName = $app->patient->name ?? "";
            $patient_id = $app->patient_id ?? 0;
            $admission_id = 0;
        }

        /*foreach(request()->ProductList as $row){
            $totalTax = ($totalTax) + ($row['taxAmount']);
        }*/
        $total = ($TotalSale) + $totalTax;

        $SaleArray = array(
            'SCID'     => (session('store_id') == env('SEHAT_CARD_PHARMACY_STORE_ID')) ? 1 : 2, // 1 sehat card user,2 walking customer of retail store , table use sup_cus_details
            'store_id'     => session('store_id'), // sehat card user
            'wr_id'     => request()->ward_request_id ?? 0, // sehat card user
            'ReceivedAmountFromCustomer'   => 0,
            'patient_id'   => $patient_id,
            'admission_id'   => $admission_id,
            'InvoiceNo' => $Invoice,
            'appointment_id' => $appointment_id,
            'medicine_type' => $medicine_type,
            'Date'  => $PDate,
            'Description'   =>  $CustomerName,
            'TotalSale'     => 0,
            'received_amount'     => 0,
            'Discount'     =>  0,
            'invoice_discount'     =>  $invoice_discount,
            'discount_percentage'     =>  $discount_percentage,
            'sale_descriptions' => $bill_description,
            'sale_type' => "package_sale",
            'is_posted' => 1,
            'CreatedBy'     => $userID,
            'CreatedAt'     => date('Y-m-d')
        );
        if ($SalemanID != '') {
            $SaleArray['SalemanCommesion'] = $Commesion;
            $SaleArray['SalemanID'] = $SalemanID;
        }

        $SaleArray['bill_details'] = json_encode(request()->ProductList);

        try {
            DB::beginTransaction();

            $sale = Sale::create($SaleArray);
            $last_id = $sale->SaleID;
            $SaleArray['SaleID'] = $last_id;
            $temp_sale = TempSale::create($SaleArray);
            $item_details = [];
            foreach (request()->ProductList as $row) {
                $TotalSale = ($TotalSale) + (($row['Quantity'] * ($row['UnitePrice'])));

                // Get product to validate discount
                $product = Product::find($row['ProductID']);
                $allowPercentage = $product->allow_percentage ?? null;
                $requestedDiscount = $row['discount_percentage'] ?? 0;
                $finalDiscountPercentage = 0;

                if ($allowPercentage === 0) {
                    // If allow_percentage is zero, don't apply any discount
                    $finalDiscountPercentage = 0;
                } else if ($allowPercentage > 0) {
                    // If product has allow_percentage limit
                    $finalDiscountPercentage = ($requestedDiscount > $allowPercentage) ? $allowPercentage : $requestedDiscount;
                } else {
                    // No allow_percentage field set (null) - apply user's discount
                    $finalDiscountPercentage = $requestedDiscount;
                }

                $discountAmount = ($row['Quantity'] * $row['UnitePrice'] * $finalDiscountPercentage) / 100;

                $item_details[] = array(
                    'store_id'   => session('store_id'),
                    'SaleID'   => $last_id, //$sale->SaleID,
                    'temp_sale_id'   => $temp_sale->id,
                    'patient_id'   => $patient_id,
                    'admission_id'   => $admission_id,
                    'ProductID' => $row['ProductID'],
                    'UnitePrice'  => $row['UnitePrice'],
                    'taxPercentage'  => $row['taxPercentage'],
                    'dose_type'  => $row['dose_type'],
                    'Quantity'  => $row['Quantity'],
                    'discount_percentage' => $finalDiscountPercentage,
                    'discount_percentage_amount' => $discountAmount,
                    'discount_percentage_amount' => $discountAmount,
                );
            }

            TempSaleDetails::insert($item_details);

            DB::commit(); // ✅ commit if all good
        } catch (\Exception $e) {
            DB::rollBack(); // ❌ rollback on error
            throw $e;       // optional: rethrow for logging
        }


        if ($ReceivedAmount >= 1) {
            SalePayment::create(["sale_id" => $last_id, "patient_id" => $patient_id, "admission_id" => $admission_id, "amount" => 0,"is_posted" => 1, "created_by" => auth()->user()->id, "created_at" => date("Y-m-d H:i:s")]);
        }



        $is_free = session('is_free');
        foreach (request()->ProductList as $row) {
            $soldQuantity = $row['Quantity'];
            $result = GrnDetails::where(["ProductID" => $row['ProductID'], "ProductStatus" => 1])->get();

            // Get product to check allow_percentage
            $product = Product::find($row['ProductID']);
            $allowPercentage = $product->allow_percentage ?? 0;

            // Apply discount based on allow_percentage logic
            $requestedDiscount = $row['discount_percentage'] ?? 0;
            $finalDiscountPercentage = 0;

            if ($allowPercentage === 0) {
                // If allow_percentage is zero, don't apply any discount
                $finalDiscountPercentage = 0;
            } else if ($allowPercentage > 0) {
                // If product has allow_percentage limit
                if ($requestedDiscount <= $allowPercentage) {
                    // User's discount is within limit - apply user's discount
                    $finalDiscountPercentage = $requestedDiscount;
                } else {
                    // User's discount exceeds limit - apply product's allow_percentage
                    $finalDiscountPercentage = $allowPercentage;
                }
            } else {
                // No allow_percentage field set (null) - apply user's discount
                $finalDiscountPercentage = $requestedDiscount;
            }

            $discountAmount = ($row['Quantity'] * $row['UnitePrice'] * $finalDiscountPercentage) / 100;

            $Detail_array = array(
                'store_id'   => session('store_id'),
                'SaleID'   => $last_id,
                'patient_id'   => $patient_id,
                'admission_id'   => $admission_id,
                'ProductID' => $row['ProductID'],
                'UnitePrice'  => $row['UnitePrice'],
                'taxPercentage'  => $row['taxPercentage'],
                'dose_type'  => $row['dose_type'],
                'discount_percentage' => $finalDiscountPercentage,
                'discount_percentage_amount' => $discountAmount,
            );
            $applyTax = $row['taxPercentage'] / 100;
            foreach ($result as $key => $value) {
                if ($soldQuantity <= $value->RemainingQuantity && $soldQuantity != 0) {
                    //echo "yes";
                    $total = ($soldQuantity) * ($row['UnitePrice']);
                    $taxAmount = ($total) * $applyTax;
                    $Detail_array['PurchasePrice'] = $value->UnitPrice;
                    $Detail_array['Quantity'] = $soldQuantity;
                    $Detail_array['GDID'] = $value->GDID;
                    $Detail_array['taxAmount'] = $taxAmount;
                    $remainingQuantity = $value->RemainingQuantity - $soldQuantity;
                    SaleDetails::create($Detail_array);
                    GrnDetails::where(["GDID" => $value->GDID])->update(['RemainingQuantity' => $remainingQuantity, 'SoldQuantity' => ($value->SoldQuantity + $soldQuantity)]);
                    if ($remainingQuantity == 0) {
                        GrnDetails::where(['GDID' => $value->GDID])->update(['ProductStatus' => 0]);
                    }
                    $soldQuantity = 0;
                } else {
                    if ($soldQuantity > $value->RemainingQuantity && $soldQuantity != 0) {
                        $total = ($value->RemainingQuantity) * ($row['UnitePrice']);
                        $taxAmount = ($total) * $applyTax;

                        if ($is_free) { // if free then sale price will be same as purchase price
                            $Detail_array['UnitePrice'] = $value->UnitPrice;
                        }

                        $Detail_array['PurchasePrice'] = $value->UnitPrice;
                        $Detail_array['Quantity'] = $value->RemainingQuantity;
                        $Detail_array['GDID'] = $value->GDID;
                        $Detail_array['taxAmount'] = $taxAmount;
                        $soldQuantity = ($soldQuantity) - ($value->RemainingQuantity);
                        //echo $soldQuantity;
                        SaleDetails::create($Detail_array);
                        GrnDetails::where(['GDID' => $value->GDID])->update(['RemainingQuantity' => 0, 'SoldQuantity' => ($value->SoldQuantity + $value->RemainingQuantity), 'ProductStatus' => 0]);
                    }
                }
            } //.... end of foreach
            //---- if stock is zero then also enter products in sale   -----//
            /*if($soldQuantity > 0){
                $product = GrnDetails::where("UnitPrice",">",0)->where(["ProductID"=>$row['ProductID']])->orderBy("GDID","DESC")->first();

                // dd($product,$row['ProductID']);
                $total = ($soldQuantity) * ($row['UnitePrice']);
                $taxAmount = ($total) * $applyTax;

                $Detail_array['PurchasePrice']= $product->UnitPrice;
                $Detail_array['Quantity']=$soldQuantity;
                $Detail_array['GDID']=$product->GDID;
                $Detail_array['taxAmount']=$taxAmount;
                $remainingQuantity = ($product->RemainingQuantity) - ($soldQuantity);

                SaleDetails::create($Detail_array);
                GrnDetails::where(['GDID'=>$value->GDID])->update(['RemainingQuantity'=>$remainingQuantity]);
            }*/
            //--------- end if stock is zero   ----------//
        } //------------ end of main foreach   -----------//

        if (request()->ward_request_id) {
            WardRequest::whereId(request()->ward_request_id)->update(["issued_by" => auth()->user()->id, "issued_at" => date("Y-m-d H:i:s"), "status" => 1]);
        }

        if (session('store_id') == 1) {
            (new PatientAdmissionController())->updateAdmissionDetails($admission_id);
        }
        sleep(1);

        return ["status" => true, "message" => "Sale Completed Successfully", "id" => $last_id];
    }

    private function returnInvoiceNumber()
    {
        $lastSale = Sale::orderBy('SaleID', 'DESC')->first();
        if ($lastSale) {
            return $lastSale->InvoiceNo + 1;
        }
        return 1;
    }
}

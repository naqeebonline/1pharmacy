<?php

namespace App\Http\Controllers\PatientController;

use App\Http\Controllers\Controller;
use App\Models\Configuration\Relation;
use App\Models\Finance\FinanceVoucher;
use App\Models\FinanceHead;
use App\Models\MedicineType;
use App\Models\Patient\InPatientAdmission;
use App\Models\Patient\Patient;
use App\Models\Patient\PatientAdmission;
use App\Models\Patient\PatientLocation;
use App\Models\Sale;
use App\Models\SalePayment;
use App\Models\TempSale;
use App\Exports\CustomerBillsExport;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Settings\Entities\District;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class PatientController extends Controller
{
   public function customer_regisration()
   {

      $data["relations"] = Relation::get();
      $data["district"] = District::get();
      $data["locations"] = PatientLocation::get();

      return view("patients.customer_registration", $data);
   }

    public function customer_receive_payment()
    {
        $data['title'] = 'Customer Receive Payment';
        // Patients table is treated as customers
        $data['customers'] = Patient::where('is_active', 1)
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'mr_no', 'finance_head_id']);

        return view('patients.customer_receive_payment', $data);
    }

    public function customer_bills()
    {
        $data['title'] = 'Customer Bills';
        $data['customers'] = Patient::where('is_active', 1)
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'mr_no', 'finance_head_id']);
            $data['medicine_types'] = MedicineType::orderBy('name')->get();

        return view('patients.customer_bills', $data);
    }

    public function customer_bills_list()
    {
        $invoiceNo = trim((string) request()->get('invoice_no', ''));
        $patientId = (int) request()->get('patient_id', 0);
        $fromDate = request()->get('from_date');
        $toDate = request()->get('to_date');
        $medicineType = trim((string) request()->get('medicine_type', ''));

        $query = Sale::query()
            ->with(['patient:id,name,mr_no'])
            ->when(session('store_id'), function ($q) {
                $q->where('store_id', session('store_id'));
            });

        if ($invoiceNo !== '') {
            $query->where('InvoiceNo', 'like', '%' . $invoiceNo . '%');
        }
        if ($patientId > 0) {
            $query->where('patient_id', $patientId);
        }
        if (!empty($fromDate)) {
            $query->whereDate('CreatedAt', '>=', $fromDate);
        }
        if (!empty($toDate)) {
            $query->whereDate('CreatedAt', '<=', $toDate);
        }
        if ($medicineType !== '') {
            // Column exists in this project for filtering bills by type (Ward/OT/Home)
            $query->where('medicine_type', $medicineType);
        }

        $query->orderByDesc('SaleID');

        return DataTables::of($query)
            ->addColumn('customer', function ($row) {
                return $row->patient?->name ?? '';
            })
            ->addColumn('mr_no', function ($row) {
                return $row->patient?->mr_no ?? '';
            })
            ->addColumn('actions', function ($row) {
                // Use existing bill print as reference but provide an A4 dedicated print.
                $url = route('pos.print_customer_bill_a4', [$row->SaleID]);
                $excelUrl = route('pos.print_customer_bill_excel', [$row->SaleID]);
                return '<a target="_blank" href="' . e($url) . '" class="btn btn-sm btn-primary">Print</a>'
                    . ' <a href="' . e($excelUrl) . '" class="btn btn-sm btn-success">Excel</a>';
            })
            ->editColumn('CreatedAt', function ($row) {
                return $row->CreatedAt;
            })
            ->rawColumns(['customer', 'actions'])
            ->make(true);
    }

    public function customer_bills_export_excel()
    {
        $invoiceNo = trim((string) request()->get('invoice_no', ''));
        $patientId = (int) request()->get('patient_id', 0);
        $fromDate = request()->get('from_date');
        $toDate = request()->get('to_date');
        $medicineType = trim((string) request()->get('medicine_type', ''));

        $fileName = 'customer_bills_' . date('Ymd_His') . '.xlsx';

        return Excel::download(
            new CustomerBillsExport($invoiceNo, $patientId, $fromDate, $toDate, $medicineType),
            $fileName
        );
    }

    public function customer_ledger_report()
    {
        $patientId = (int) request()->get('patient_id', 0);
        $startDate = request()->get('start_date', date('Y-m-d'));
        $endDate = request()->get('end_date', date('Y-m-d'));

        if ($patientId <= 0) {
            abort(422, 'Customer is required.');
        }

        $patient = Patient::findOrFail($patientId);
        $saleDateExpr = 'COALESCE(DATE(temp_sale.Date), temp_sale.CreatedAt)';

        $billsBeforeQuery = TempSale::query()
            ->where('patient_id', $patientId)
            ->whereRaw($saleDateExpr . ' < ?', [$startDate]);
        $this->applyStoreFilterToTempSale($billsBeforeQuery);

        $billsBefore = $billsBeforeQuery->get()->sum(function ($row) {
            return $this->resolveTempSaleNetAmount($row);
        });

        $paymentsBefore = SalePayment::query()
            ->where('patient_id', $patientId)
            ->where('is_active', 1)
            ->whereDate('created_at', '<', $startDate)
            ->sum('amount');

        $openingBalance = round((float) $billsBefore - (float) $paymentsBefore, 2);

        $billsQuery = TempSale::query()
            ->where('patient_id', $patientId)
            ->whereRaw($saleDateExpr . ' BETWEEN ? AND ?', [$startDate, $endDate]);
        $this->applyStoreFilterToTempSale($billsQuery);

        $bills = $billsQuery
            ->orderByRaw($saleDateExpr . ' ASC')
            ->orderBy('SaleID')
            ->get();

        $payments = SalePayment::query()
            ->where('patient_id', $patientId)
            ->where('is_active', 1)
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $invoiceMap = TempSale::query()
            ->whereIn('SaleID', $payments->pluck('sale_id')->filter()->unique()->values())
            ->pluck('InvoiceNo', 'SaleID');

        $entries = collect();

        foreach ($bills as $bill) {
            $billDate = $bill->Date
                ? Carbon::parse($bill->Date)->format('Y-m-d')
                : ($bill->CreatedAt ? Carbon::parse($bill->CreatedAt)->format('Y-m-d') : $startDate);

            $entries->push([
                'sort_date' => $billDate,
                'sort_key' => $billDate . '-1-' . str_pad((string) $bill->SaleID, 10, '0', STR_PAD_LEFT),
                'date' => $billDate,
                'particulars' => 'Pharmacy Sale Bill',
                'reference' => $bill->InvoiceNo ?? ('Sale #' . $bill->SaleID),
                'debit' => $this->resolveTempSaleNetAmount($bill),
                'credit' => 0.0,
            ]);
        }

        foreach ($payments as $payment) {
            $paymentDate = Carbon::parse($payment->created_at)->format('Y-m-d');
            $particulars = 'Payment Received';
            if (!empty($payment->remarks)) {
                $particulars .= ' - ' . $payment->remarks;
            } elseif ((int) ($payment->sale_id ?? 0) > 0) {
                $invoiceNo = $invoiceMap[$payment->sale_id] ?? null;
                $particulars .= $invoiceNo
                    ? ' (Invoice ' . $invoiceNo . ')'
                    : ' (Sale #' . $payment->sale_id . ')';
            }

            $entries->push([
                'sort_date' => $paymentDate,
                'sort_key' => $paymentDate . '-2-' . str_pad((string) $payment->id, 10, '0', STR_PAD_LEFT),
                'date' => $paymentDate,
                'particulars' => $particulars,
                'reference' => 'PAY-' . $payment->id,
                'debit' => 0.0,
                'credit' => round((float) ($payment->amount ?? 0), 2),
            ]);
        }

        $entries = $entries->sortBy('sort_key')->values();

        $runningBalance = $openingBalance;
        $totalDebit = 0.0;
        $totalCredit = 0.0;

        $entries = $entries->map(function ($row) use (&$runningBalance, &$totalDebit, &$totalCredit) {
            $totalDebit += $row['debit'];
            $totalCredit += $row['credit'];
            $runningBalance = round($runningBalance + $row['debit'] - $row['credit'], 2);
            $row['balance'] = $runningBalance;
            return $row;
        });

        $closingBalance = round($openingBalance + $totalDebit - $totalCredit, 2);

        return view('reports.print_customer_ledger', [
            'patient' => $patient,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'opening_balance' => $openingBalance,
            'closing_balance' => $closingBalance,
            'total_debit' => round($totalDebit, 2),
            'total_credit' => round($totalCredit, 2),
            'entries' => $entries,
            'company_name' => env('COMPANY_NAME', 'Pharmacy'),
        ]);
    }

    public function customer_profit_loss_report()
    {
        $patientId = (int) request()->get('patient_id', 0);
        $startDate = request()->get('start_date', date('Y-m-d'));
        $endDate = request()->get('end_date', date('Y-m-d'));
        $invoiceNo = trim((string) request()->get('invoice_no', ''));
        $medicineType = trim((string) request()->get('medicine_type', ''));

        if ($patientId <= 0) {
            abort(422, 'Customer is required.');
        }

        $patient = Patient::findOrFail($patientId);

        $query = Sale::query()
            ->when(session('store_id'), function ($q) {
                $q->where('store_id', session('store_id'));
            })
            ->where('patient_id', $patientId);

        if ($invoiceNo !== '') {
            $query->where('InvoiceNo', 'like', '%' . $invoiceNo . '%');
        }
        if (!empty($startDate)) {
            $query->whereDate('CreatedAt', '>=', $startDate);
        }
        if (!empty($endDate)) {
            $query->whereDate('CreatedAt', '<=', $endDate);
        }
        if ($medicineType !== '') {
            $query->where('medicine_type', $medicineType);
        }

        $sales = $query
            ->orderBy('CreatedAt')
            ->orderBy('SaleID')
            ->get([
                'SaleID',
                'InvoiceNo',
                'CreatedAt',
                'medicine_type',
                'net_amount',
                'TotalSale',
                'Discount',
                'invoice_discount',
                'tax_amount',
            ]);

        $billRows = [];
        $totalSale = 0.0;
        $totalCogs = 0.0;

        foreach ($sales as $sale) {
            $saleAmount = $this->resolveSaleTotalSaleAmount($sale);
            $cogs = (float) (DB::table('sale_details')
                ->where('SaleID', $sale->SaleID)
                ->selectRaw('SUM((Quantity - COALESCE(ReturnQuantity, 0)) * PurchasePrice) as cogs')
                ->value('cogs') ?? 0);

            $profit = round($saleAmount - $cogs, 2);

            $billRows[] = [
                'invoice_no' => $sale->InvoiceNo ?? ('Sale #' . $sale->SaleID),
                'date' => $sale->CreatedAt,
                'medicine_type' => $sale->medicine_type ?? '',
                'total_sale' => round($saleAmount, 2),
                'cogs' => round($cogs, 2),
                'profit' => $profit,
            ];

            $totalSale += $saleAmount;
            $totalCogs += $cogs;
        }

        $totalSale = round($totalSale, 2);
        $totalCogs = round($totalCogs, 2);
        $totalProfit = round($totalSale - $totalCogs, 2);
        $taxCollected = $this->aggregateCustomerTaxCollected($sales);
        $totalTaxCollected = round(array_sum(array_column($taxCollected, 'amount')), 2);

        return view('reports.print_customer_profit_loss', [
            'patient' => $patient,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'invoice_no' => $invoiceNo,
            'medicine_type' => $medicineType,
            'bill_rows' => $billRows,
            'total_sale' => $totalSale,
            'total_cogs' => $totalCogs,
            'total_profit' => $totalProfit,
            'tax_collected' => $taxCollected,
            'total_tax_collected' => $totalTaxCollected,
            'company_name' => env('COMPANY_NAME', 'Pharmacy'),
        ]);
    }

    /**
     * Sum tax collected per type from sale_details row taxes (not included in profit/loss).
     */
    protected function aggregateCustomerTaxCollected($sales): array
    {
        $saleIds = $sales->pluck('SaleID')->filter()->values();
        if ($saleIds->isEmpty()) {
            return [];
        }

        $taxableExpr = 'GREATEST(0, ((sale_details.Quantity - COALESCE(sale_details.ReturnQuantity, 0)) * sale_details.UnitePrice) - COALESCE(sale_details.discount_percentage_amount, 0))';

        $saleTaxTotal = (float) DB::table('sale_details')
            ->whereIn('SaleID', $saleIds)
            ->selectRaw("SUM({$taxableExpr} * COALESCE(sale_details.sale_tax, 0) / 100) as total")
            ->value('total');

        $incomeTaxTotal = (float) DB::table('sale_details')
            ->whereIn('SaleID', $saleIds)
            ->selectRaw("SUM({$taxableExpr} * COALESCE(sale_details.income_tax, 0) / 100) as total")
            ->value('total');

        $result = [];
        if ($saleTaxTotal > 0) {
            $result[] = ['name' => 'Sale Tax', 'amount' => round($saleTaxTotal, 2)];
        }
        if ($incomeTaxTotal > 0) {
            $result[] = ['name' => 'Income Tax', 'amount' => round($incomeTaxTotal, 2)];
        }

        return $result;
    }

    /**
     * Total sale (excl. tax) = sale.net_amount - sale.tax_amount
     */
    protected function resolveSaleTotalSaleAmount($row): float
    {
        $netAmount = (float) ($row->net_amount ?? 0);
        $taxAmount = (float) ($row->tax_amount ?? 0);

        if ($netAmount > 0) {
            return round(max(0, $netAmount - $taxAmount), 2);
        }

        return round(max(0, (float) ($row->TotalSale ?? 0)
            - (float) ($row->Discount ?? 0)
            - (float) ($row->invoice_discount ?? 0)), 2);
    }

    protected function applyStoreFilterToTempSale($query): void
    {
        if (session('store_id')) {
            $query->where('store_id', session('store_id'));
        }
    }

    protected function resolveTempSaleNetAmount($row): float
    {
        $netAmount = (float) ($row->net_amount ?? 0);
        if ($netAmount > 0) {
            return round($netAmount, 2);
        }

        $totalBill = max(0, (float) ($row->TotalSale ?? 0)
            - (float) ($row->Discount ?? 0)
            - (float) ($row->invoice_discount ?? 0));

        return round($totalBill + (float) ($row->tax_amount ?? 0), 2);
    }

    public function store_customer_receive_payment()
    {
        request()->validate([
            'patient_id' => ['required', 'integer'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'remarks' => ['nullable', 'string', 'max:255'],
        ]);

        $patientId = (int) request()->patient_id;
        $amount = (float) request()->amount;
        $remarks = request()->remarks ?? '';

        // Keep it consistent with existing style: insert in sale_payments
        // sale_id=0, admission_id=0 for direct customer payments
        SalePayment::create([
            'sale_id' => 0,
            'patient_id' => $patientId,
            'admission_id' => 1,
            'is_posted' => 0,
            'amount' => $amount,
            'remarks' => $remarks,
            'created_by' => Auth::id() ?? 0,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        

        return response()->json([
            'status' => true,
            'message' => 'Payment received successfully.'
        ]);
    }

    public function customer_payment_history($patient_id)
    {
        $payments = SalePayment::where('patient_id', (int) $patient_id)
            ->where('is_active', 1)
            ->orderByDesc('id')
            ->limit(50)
            ->get(['id', 'sale_id', 'admission_id', 'is_posted', 'amount', 'remarks', 'created_at', 'created_by']);

        return response()->json([
            'status' => true,
            'data' => $payments,
        ]);
    }

    public function customer_receive_payment_list()
    {
        $patientId = (int) request()->get('patient_id', 0);
        $fromDate = request()->get('from_date');
        $toDate = request()->get('to_date');

        $query = SalePayment::query()
            ->where('is_active', 1)
            ->where('sale_id', 0)
            ->where('admission_id', 1);

        if ($patientId > 0) {
            $query->where('patient_id', $patientId);
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

    // Balance for customer receive payment is loaded from existing route:
    // `pos.customer_previous_balance` (Admin\CustomerPayments@customer_previous_balance)

        private function findDirectCustomerPaymentOrFail(int $paymentId): SalePayment
        {
            $payment = SalePayment::with(["patient"])->where('id', $paymentId)
                ->where('sale_id', 0)
                ->where('admission_id', 1)
                ->where('is_active', 1)
                ->first();

            if (!$payment) {
                abort(404, 'Payment not found');
            }

            return $payment;
        }

        public function get_customer_receive_payment($payment_id)
        {
            $payment = $this->findDirectCustomerPaymentOrFail((int) $payment_id);
            return response()->json([
                'status' => true,
                'data' => $payment,
            ]);
        }

        public function update_customer_receive_payment()
        {
            request()->validate([
                'payment_id' => ['required', 'integer'],
                'amount' => ['required', 'numeric', 'min:0.01'],
                'remarks' => ['nullable', 'string', 'max:255'],
            ]);

            $payment = $this->findDirectCustomerPaymentOrFail((int) request()->payment_id);

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

        public function delete_customer_receive_payment()
        {
            request()->validate([
                'payment_id' => ['required', 'integer'],
            ]);

            $payment = $this->findDirectCustomerPaymentOrFail((int) request()->payment_id);
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

        public function approve_customer_receive_payment()
        {
            request()->validate([
                'payment_id' => ['required', 'integer'],
            ]);

            $payment = $this->findDirectCustomerPaymentOrFail((int) request()->payment_id);
            
           
            if ((int) ($payment->is_posted ?? 0) === 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Payment is already approved.'
                ]);
            }

            // $payment->update([
            //     'is_posted' => 1,
            //     'posted_on' => date('Y-m-d H:i:s'),
            //     'updated_by' => Auth::id() ?? 0,
            //     'updated_at' => date('Y-m-d H:i:s'),
            // ]);

            // $user_id = auth()->user()->id;
           
            return response()->json([
                'status' => true,
                'message' => 'Payment approved successfully.'
            ]);
        }

    public function store_customer_regisration() {

        $finance_head = FinanceHead::where("description","CUS-".request()->contact_no)->first();
        $finance_head_id = "";

        $type = "asset";

        if($finance_head){
            $finance_head_id = $finance_head->id;
            $code = $finance_head->description;

        }else{
            $create_head = [
                "name" => "Customer - ".request()->name,
                "type"  => $type,
                "parent_id"  => 111, // finanace head parent id customer Receivable
                "description" => "CUS-".request()->contact_no
            ];
            $finance_head = FinanceHead::create($create_head);
            $finance_head_id = $finance_head->id;
        }
        $data = request()->except(["id","_token"]);
        $data['finance_head_id'] = $finance_head_id;

         $patients = Patient::orderBy("id","desc")->first();
         $lastId = $patients?->id ?? 0;
         $number = ($lastId + 1);

         $isNew = (int) request()->id === 0;
         if($isNew){
              $data['mr_no'] = $number;
              $data['regdate'] = request()->regdate." ".date("H:i:s");
              // Use patient_type from request, default to hospital_patient if not provided
              $data['patient_type'] = request()->patient_type ?? 'customer';
         }

        $patient = Patient::updateOrCreate(
            ["id" => request()->id],
            $data
        );

        // Only on new patient registration, also create an admission entry
        // if($isNew){
        //      $userId = Auth::id() ?? 0;
        //      InPatientAdmission::create([
        //           'patient_id' => $patient->id,
        //           'ward_id' => 1,
        //           'bed_id' => 1,
        //           'consultant_id' => 1,
        //           'sub_consultant_id' => 1,
        //           'included_medicine' => 0,
        //           'sc_ref_no' => '',
        //           'g4no' => '0',
        //           'procedure_type_id' => 1,
        //           'sec_procedure_type_id' => 1,
        //           'guardian_name' => '-',
        //           'emergency_contact_no' => '0',
        //           'relation_id' => 1,
        //           'admission_date' => Carbon::now(),
        //           'discharge_date' => null,
        //           'discharge_summary' => null,
        //           'canelation_reason' => null,
        //           'is_active' => 1,
        //           'created_by' => $userId,
        //           'updated_by' => $userId,
        //           'admission_status' => 'Admit',
        //           'consultant_share' => 0.00,
        //           'consultant_share_amount' => 0.00,
        //           'procedure_rate' => 0,
        //           'total_amount_received_from_patient' => 0.00,
        //           'sec_procedure_rate' => 0.00,
        //           'investigation_cost' => 0.00,
        //           'service_charges_cost' => 0.00,
        //           'medicine_cost' => 0.00,
        //           'totalCost' => 0.00,
        //           'balance' => 0,
        //           'consultant_shares_payment_invoice_id' => 0,
        //           'amount_received_from_sehat_card' => null,
        //           'patient_type' => 'sehat_card',
        //           'advance_payment' => 0.00,
        //           'security_amount' => 0.00,
        //           'discharge_by' => null,
        //           'is_posted' => 0,
        //           'posted_on' => null,
        //           'is_sync' => 0,
        //      ]);
        // }
       
        return response()->json([
            "status"=> true,
            "message"=> "Record save successfully."
        ]);
      
    }

   public function list_patient(){
      $patients = Patient::where("is_active", 1)->with("location", "district");
      
      // Filter by patient type if provided
      if(request()->has('patient_type') && request()->patient_type != ''){
         $patients = $patients->where('patient_type', request()->patient_type);
      }
      
      return DataTables::of($patients)
         ->addColumn("actions", function ($patient) {
            return '<a href="javascript:void(0)"  data-details=\'' . $patient . '\'  class="btn btn-sm btn-warning edit_record"><i class="tf-icons bx bx-pencil"></i></a> 
                        <button data-id="' . $patient->id . '" class="btn btn-danger btn-sm delete_record"><i class="bx bx-trash tf-icons"></i></button>';
         })
         ->rawColumns(["actions"])
         ->make(true);
   }




   public function patient_admission(){

      $patients = Patient::get();
      return view("patients.patient_admission",compact("patients"));
   }

   public function get_patient_by_cnic(){

       $patient = [];
       $opdCategory = request()->opd_category;
       
      if(request()->mr_number){
          $query = Patient::where("mr_no", request()->mr_number);
          
          // Apply filter based on OPD category
          if($opdCategory === 'package'){
              $query->where('patient_type', 'package_patient');
          }
          
          $patient = $query->get();
      }

      if(request()->cnic){
          $query = Patient::where("cnic", request()->cnic);
          
          // Apply filter based on OPD category
          if($opdCategory === 'package'){
              $query->where('patient_type', 'package_patient');
          }
          
          $patient = $query->get();
      }


      if(count($patient) > 0){
         return response()->json([
            "status"=> true,
            "data"=> $patient
         ]);

      }else if(request()->contact_no){
          $query = Patient::where("contact_no", request()->contact_no);
          
          // Apply filter based on OPD category
          if($opdCategory === 'package'){
              $query->where('patient_type', 'package_patient');
          }
          
          $patient = $query->get();

          return response()->json([
              "status"=> true,
              "data"=> $patient
          ]);
      }else{
          return response()->json([
              "status"=> false,
              "data"=> []
          ]);
      }
   }

    function generateMrNumber() {
        $year = date('y');  // Last 2 digits of the year, e.g., "25"
        $month = date('m'); // 2-digit month, e.g., "07"

        // Get count of appointments for the current year and month
        $count = Patient::whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->count();

        $sequence = $count + 1;

        // Sequence should start at 2 digits and grow as needed
        $minLength = 2;
        $dynamicLength = max($minLength, strlen((string)$sequence));
        $paddedSequence = str_pad($sequence, $dynamicLength, '0', STR_PAD_LEFT);

        // Combine year, month, and padded sequence
        return $year . $month . $paddedSequence;
    }


   
   
}

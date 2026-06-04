<?php

namespace App\Http\Controllers;

use App\Jobs\SendSmsJob;
use App\Models\Configuration\Consultants;
use App\Models\Configuration\Ward;
use App\Models\Customer;
use App\Models\GeneralExpense;
use App\Models\Grn;
use App\Models\Patient\PatientAdmission;
use App\Models\Patient\PatientInvestigation;
use App\Models\PaymentDetail;
use App\Models\PoliceLine;
use App\Models\PoliceMobile;
use App\Models\PoliceStation;
use App\Models\Product;
use App\Models\ReceiveablesDetail;
use App\Models\Sale;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Modules\Settings\Entities\MyApp;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //  $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();


        $data = [
            'user' => $user,
            'apps' => []
        ];


        return redirect(route('login'));
        //  return view('layouts.landing_screen_frest', $data);
    }

    public function dashboard()
    {
        $data['total_patients'] = PatientAdmission::where(["is_active"=>1])->where("patient_admissions.admission_status","!=",'Canceled')->where("patient_type","sehat_card")->count();
        $data['admitted_patients'] = PatientAdmission::where(["admission_status"=>"Admit","is_active"=>1])->where("patient_type","sehat_card")->count();
        $data['discharged_patients'] = PatientAdmission::where(["admission_status"=>"Discharged","is_active"=>1])->where("patient_type","sehat_card")->count();
        $data['canceled_patients'] = PatientAdmission::where(["admission_status"=>"canceled","is_active"=>1])->where("patient_type","sehat_card")->count();
        $data['reffered_patients'] = PatientAdmission::where(["admission_status"=>"Reffered","is_active"=>1])->where("patient_type","sehat_card")->count();
        $data['surgery'] = PatientAdmission::selectRaw('procedure_type.type, COUNT(patient_admissions.id) as total_admissions')
            ->join('procedure_type', 'patient_admissions.procedure_type_id', '=', 'procedure_type.id')
            ->groupBy('procedure_type.type')
            ->where(["patient_admissions.is_active"=>1])
            ->where("patient_admissions.admission_status","!=",'Canceled')
            ->where("patient_type","sehat_card")
            ->get();

        $data['daily_admissions'] = $this->getDailyAdmission();
        $data['total_procedures'] = $this->getCategoryWiseProcedures();

        $data['yearly_admissions'] = $this->getYearlyPatientAdmission();
        $data['total_nvd_cases'] = PatientAdmission::where(["procedure_type_id"=>6,"is_active"=>1])->where("admission_status","!=",'Canceled')->where("patient_type","sehat_card")->count();
        $data['total_nvd_admitted'] = PatientAdmission::where(["procedure_type_id"=>6,"admission_status"=>"Admit","is_active"=>1])->where("patient_type","sehat_card")->count();
        $data['total_nvd_canceled'] = PatientAdmission::where(["procedure_type_id"=>6,"admission_status"=>"Canceled","is_active"=>1])->where("patient_type","sehat_card")->count();
        $data['total_nvd_discharged'] = PatientAdmission::where(["procedure_type_id"=>6,"admission_status"=>"Canceled","is_active"=>1])->where("patient_type","sehat_card")->count();
        $data['total_nvd_reffered'] = PatientAdmission::where(["procedure_type_id"=>6,"admission_status"=>"Reffered","is_active"=>1])->where("patient_type","sehat_card")->count();

        $data['total_procedure_amount'] = PatientAdmission::whereIsActive(1)->where("patient_type","sehat_card")->sum('procedure_rate');
        $data['total_investigation_amount'] = PatientInvestigation::whereIsActive(1)->sum('inv_amount');
        $totalPercentageSum = DB::select("SELECT SUM((consultant_share / 100) * procedure_rate) AS total_percentage_sum 
                                                FROM patient_admissions where patient_type = 'sehat_card'");
        $data['total_percentage_paid_to_consultant'] = $totalPercentageSum[0]->total_percentage_sum;

        $totalPatientMedicineAmount = DB::select("SELECT SUM((Quantity - ReturnQuantity) * UnitePrice) AS total_amount 
                                      FROM sale_details");
        $data['total_medicine_amount'] = $totalPatientMedicineAmount[0]->total_amount;
        $data['total_purchased_stock_amount'] = Product::sum('total_amount_of_purchase_stock');
        $data['avaliable_stock_amount'] = Product::sum('total_amount_of_avaliable_stock');
        $data['total_utilized'] =  ($data['total_purchased_stock_amount']) - ($data['avaliable_stock_amount']);
        $data['net_cost_on_patient'] = ($data['total_investigation_amount']) + ($data['total_percentage_paid_to_consultant']) + ($data['total_medicine_amount']);

        $chartData = DB::table('patient_investigations')
            ->select(
                'investigation_sub_category.id as sub_category_id',
                'investigation_sub_category.name as sub_category_name',
                DB::raw('COALESCE(SUM(patient_investigations.inv_amount), 0) as total_amount')
            )
            ->leftJoin('investigation_sub_category', 'patient_investigations.investigation_sub_category_id', '=', 'investigation_sub_category.id')
            ->groupBy('investigation_sub_category.id', 'investigation_sub_category.name')
            ->orderByRaw('total_amount DESC')
            ->get();
        $chart_title = $chartData->pluck('sub_category_name');
        $chart_data = $chartData->pluck('total_amount');
        $chart_data = $chart_data->map(function ($item) {
            return (float) $item;
        });

        $data['investigation_chart_title'] = $chart_title;
        $data['investigation_chart_data'] = $chart_data;



        return view('hmis_dashboard', $data);
    }

    public function pharmacy_dashboard()
    {
        $data['daily_saleData'] = $this->getDailySaleData();
        $data['monthly_saleData'] = $this->getYearlySaleData();
        $data['monthly_purchaseData'] = $this->getYearlyPurchaseData();
        $data['daily_saleData'] = $this->getDailySaleData();
        $data["totalSale"] = Sale::sum("TotalSale");
        $data["totalPurchase"] = Grn::sum("TotalPurchase");
        $data["expense"] = GeneralExpense::sum("Amount");

        $data["todaySale"] = Sale::where("Date", today())->sum("TotalSale");
        $data["cashInHand"] = PaymentDetail::where("Dated", today())->where("payment_type_id", 1)->sum("Amount");
        $data["cashByBank"] = PaymentDetail::where("Dated", today())->where("payment_type_id", 2)->sum("Amount");
        $data["todayPurchase"] = Grn::where("Dated", today())->sum("TotalPurchase");
       // $data["supplierPayment"] = ReceiveablesDetail::where("CreatedAt", today())->sum("Amount");
        $data["totalReceivables"] = $this->totalReceivables();

        return view('pharmacy_dashboard', $data);
    }


    public function getYearlySaleData()
    {
        $salesData = DB::table('sale')
            ->select(
                DB::raw("MONTH(Date) as month"),       // Extract month as a number (1-12)
                DB::raw("SUM(TotalSale) as total_sales") // Sum of TotalSale
            )
            ->whereYear('Date', date("Y"))
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->get();

        $months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

        // Step 3: Map sales data to the months
        $salesByMonth = array_fill(0, 12, 0); // Default array with 0 for all months
        foreach ($salesData as $sale) {
            $salesByMonth[$sale->month - 1] = $sale->total_sales; // Map total_sales to respective month
        }
        $monthNames = $months;
        $salesTotals = $salesByMonth;
        return [
            'months' => $monthNames,
            'amount' => $salesTotals,
        ];
    }

    public function getYearlyPurchaseData()
    {
        $salesData = DB::table('grn')
            ->select(
                DB::raw("MONTH(Dated) as month"),       // Extract month as a number (1-12)
                DB::raw("SUM(TotalPurchase) - SUM(Discount) - SUM(per_item_discount) as total_amount") // Sum of TotalSale
            )
            ->whereYear('Dated', date('Y'))
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->get();

        $months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

        // Step 3: Map sales data to the months
        $salesByMonth = array_fill(0, 12, 0); // Default array with 0 for all months
        foreach ($salesData as $sale) {
            $salesByMonth[$sale->month - 1] = $sale->total_amount; // Map total_sales to respective month
        }
        $monthNames = $months;
        $salesTotals = $salesByMonth;
        return [
            'months' => $monthNames,
            'amount' => $salesTotals,
        ];
    }

    public function getDailySaleData()
    {

        $salesData = DB::table('sale')
        ->select(
            DB::raw("DATE(Date) as sale_date"),      // Extract date (YYYY-MM-DD format)
            DB::raw("SUM(TotalSale) as total_sales") // Sum of TotalSale for each day
        )
            ->whereYear('Date', date("Y")) // Filter for the year 2024
            ->whereMonth('Date', date("m")) // Filter for the year 2024
            ->groupBy('sale_date')    // Group by each date
            ->orderBy('sale_date', 'desc') // Order by date in ascending order

            ->get();



        // Step 2: Generate all dates for the year 2024
        $month_endDate = Carbon::now()->endOfMonth()->toDateString();
        $month_endDate = date("d",strtotime($month_endDate));
        $startDate = Carbon::create(date("Y"), date("m"), 1); // January 1, 2024
        $endDate = Carbon::create(date("Y"), date("m"), $month_endDate); // December 31, 2024
        $allDates = []; // Array to store all dates of the year

        while ($startDate->lte($endDate)) {
            $allDates[] = $startDate->format('Y-m-d'); // Add the date in 'YYYY-MM-DD' format
            $startDate->addDay(); // Move to the next day
        }


        // Step 3: Map sales data to all dates
        $salesDataMapped = array_fill_keys($allDates, 0); // Default array with 0 sales for all dates
        foreach ($salesData as $sale) {
            $salesDataMapped[$sale->sale_date] = $sale->total_sales; // Map total_sales to respective date
        }

        // Step 4: Prepare the response
        $dates = array_keys($salesDataMapped);        // Get all dates
        $salesTotals = array_values($salesDataMapped); // Get sales totals for each date

        return [
                'months' => $dates,       // Array of all dates (including those with no sales)
                'amount' => $salesTotals // Corresponding sales totals for each date
            ];



    }


    public function totalReceivables()
    {
        $customer = Customer::where(["Type"=>2])->get();
        $totalReceivable = 0;
        foreach ($customer as $key => $value){
            $balance = $this->customer_previous_balance($value->SCID);
            $totalReceivable = ($totalReceivable) + $balance;
        }

        return number_format($totalReceivable,2);
    }

    function customer_previous_balance($customer_id,$date=''){
        $customer = Customer::where(["sup_cus_details.SCID"=>$customer_id])->first();
        $openingBalance=$customer->OpeningBalance;
        if(!$openingBalance){
            $openingBalance=0;
        }
        $where=array('SCID'=>$customer_id);
        if($date!=''){
            $where['Date <']=$date;

        }
        $TotalSale = Sale::where(["SCID"=>$customer_id])
            ->when($date, function ($query) use ($date) {
                return $query->where('Date', '>=', date("Y-m-d",strtotime($date)));
            })->sum('TotalSale');
        $TotalPaid = ReceiveablesDetail::where(["SCID"=>$customer_id])
            ->when($date, function ($query) use ($date) {
                return $query->where('Date', '<', date("Y-m-d",strtotime($date)));
            })->sum('Amount');

        $TotalAmount = ($openingBalance + $TotalSale) - $TotalPaid;
        if($TotalAmount){
            return $TotalAmount;
        }else{
            return 0;
        }

    }

    public function dashboardAnalytics(){
        $data["consultants"] = Consultants::withCount("patients")->get();
        $data["wards"] = Ward::withCount([
        'beds'
    ])->get();

        return $data;
    }

    public function getDailyAdmission()
    {
        $this->getCategoryWiseProcedures();

        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();


        $dateRange = collect();
        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            $dateRange->push($date->format('Y-m-d'));
        }



        $totalCount = $dateRange->map(function ($date) {
            $count = DB::table('patient_admissions')
                ->whereDate('admission_date', $date)
                ->where("patient_type","sehat_card")
                ->where(["is_active"=>1])
                ->count();

            return $count;
        });


        return [
            'months' => $dateRange,       // Array of all dates (including those with no sales)
            'amount' => $totalCount // Corresponding sales totals for each date
        ];


    }

    public function getCategoryWiseProcedures()
    {

        $procedureCounts = DB::table('patient_admissions')
            ->join('procedure_type', 'patient_admissions.procedure_type_id', '=', 'procedure_type.id') // Join with procedure_type table
            ->select(
                'procedure_type.name as type',                       // Get procedure type name
                DB::raw('COUNT(patient_admissions.id) as total_count') // Count the procedures
            )
            ->where("patient_admissions.is_active",1)
            ->where("patient_admissions.patient_type","sehat_card")
            ->groupBy('patient_admissions.procedure_type_id')                // Group by procedure type
            ->orderBy('total_count', 'desc')                // Optional: Order by count
            ->get();
        $name = [];
        $data = [];
        foreach ($procedureCounts as $key => $value){
            array_push($name,$value->type);
            array_push($data,$value->total_count);
        }

        return [
            'months' => $name,       // Array of all dates (including those with no sales)
            'amount' => $data // Corresponding sales totals for each date
        ];


    }

    public function getYearlyPatientAdmission()
    {
        $salesData = DB::table('patient_admissions')
            ->select(
                DB::raw("MONTH(admission_date) as month"),       // Extract month as a number (1-12)
                DB::raw("count(*) as total_amount") // Sum of TotalSale
            )
            ->where("patient_admissions.is_active",1)
            ->where("patient_admissions.patient_type","sehat_card")
            ->whereYear('admission_date', date('Y'))
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->get();



        $months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

        // Step 3: Map sales data to the months
        $salesByMonth = array_fill(0, 12, 0); // Default array with 0 for all months
        foreach ($salesData as $sale) {
            $salesByMonth[$sale->month - 1] = $sale->total_amount; // Map total_sales to respective month
        }
        $monthNames = $months;
        $salesTotals = $salesByMonth;


        return [
            'months' => $monthNames,
            'amount' => $salesTotals,
        ];
    }
    
    
}

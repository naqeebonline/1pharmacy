<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Appointments\Appointment;
use App\Models\Patient\PatientInvestigation;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function retailPharmacyDashboard()
    {

   /*     DB::table('products')
            ->where('store_id', 2)
            ->update([
                'total_amount_of_avaliable_stock' => 0,
                'total_amount_of_purchase_stock' => 0,
                'avaliable_quantity' => 0,
                'phy_avaliable_quantity' => 0,
            ]);*/

        $from_date = $_GET['from_date'] ?? date("Y-m-d");
        $to_date = $_GET['to_date'] ?? date("Y-m-d");
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $query = Sale::where('store_id', 2)
            ->when($from_date, function ($query) use ($from_date) {
                return $query->whereDate('Date', '>=', date("Y-m-d", strtotime($from_date)));
            })
            ->when($to_date, function ($query) use ($to_date) {
                return $query->whereDate('Date', '<=', date("Y-m-d", strtotime($to_date)));
            });

        $totals = $query->selectRaw('SUM(TotalSale) as TotalSale, SUM(received_amount) as received_amount, SUM(Discount) as Discount, SUM(invoice_discount) as invoice_discount')->first();
        $data['data'] = $totals;
        

        $data['appointments'] = $this->appointmentsPayment($from_date,$to_date);
        $data['investigations'] = $this->investigationPayment($from_date,$to_date);
       // dd($data['appointments']);

        return view("retail_dashboard",$data);

    }


    public function appointmentsPayment($from_date='',$to_date='')
    {
        $query = Appointment::when($from_date, function ($query) use ($from_date) {
                return $query->whereDate('appointment_date', '>=', date("Y-m-d", strtotime($from_date)));
            })
            ->when($to_date, function ($query) use ($to_date) {
                return $query->whereDate('appointment_date', '<=', date("Y-m-d", strtotime($to_date)));
            })->where("is_active",1);

        $totals = $query->selectRaw('SUM(fee) as total_fees, SUM(hospital_share) as total_hospital_share, SUM(consultant_share) as total_consultant_share')->first();
        return $totals;
    }

    public function investigationPayment($from_date='',$to_date='')
    {
        $query = PatientInvestigation::when($from_date, function ($query) use ($from_date) {
            return $query->whereDate('inv_date', '>=', date("Y-m-d", strtotime($from_date)));
        })
            ->when($to_date, function ($query) use ($to_date) {
                return $query->whereDate('inv_date', '<=', date("Y-m-d", strtotime($to_date)));
            });

        $totals = $query->selectRaw('SUM(sale_price) as total_inv_amount, SUM(discount_amount) as total_discount_amount, SUM(inv_amount) as total_cost')->first();
        return $totals;
    }
}

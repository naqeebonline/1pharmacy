<?php

namespace App\Http\Controllers;

use App\Models\PharmacyRetrun;
use App\Models\Sale;
use App\Models\SalePayment;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleDashboardController extends Controller
{
    public function index(Request $request)
    {
        $from_date = $request->from_date ?? date("Y-m-d");
        $to_date = $request->to_date ?? date("Y-m-d");
        $user_id = $request->user_id ?? 0;

        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['user_id'] = $user_id;
        
        // Get all active users for the dropdown
        $data['users'] = Users::where('status', 1)->get();
         

        // Get sales analytics with filters
        $data['analytics'] = $this->getSalesAnalytics($from_date, $to_date, $user_id);
        
        return view('analytics.SaleDashboard', $data);
    }

    private function getSalesAnalytics($from_date, $to_date, $user_id = null)
    {
        // Query sale_payments table for total sale amount
        $salePaymentQuery = SalePayment::where('is_active', 1)->where('sale_id', "!=",0) // Only get records where is_active = 0
            ->whereDate('created_at', '>=', $from_date)
            ->whereDate('created_at', '<=', $to_date);

        if ($user_id) {
            $salePaymentQuery->where('created_by', $user_id);
        }

        $totalSaleAmount = $salePaymentQuery->sum('amount') ?? 0;

        // Query sale table for total discount (discount + invoice_discount)
        // Using is_posted instead of is_active for sale table
        $saleQuery = Sale::join('sale_payments', 'sale_payments.sale_id', '=', 'sale.SaleID')
        ->whereDate('Date', '>=', $from_date)    
        ->whereDate('Date', '<=', $to_date);

        if ($user_id) {
            $saleQuery->where('CreatedBy', $user_id);
        }

        $discountData = $saleQuery->selectRaw('
            SUM(COALESCE(Discount, 0)) as total_discount,
            SUM(COALESCE(invoice_discount, 0)) as total_invoice_discount,
            SUM(COALESCE(Discount, 0) + COALESCE(invoice_discount, 0)) as total_combined_discount
        ')->first();

        // Get transaction count from sale_payments
        $totalTransactions = SalePayment::where('is_active', 1)
            ->whereDate('created_at', '>=', $from_date)
            ->whereDate('created_at', '<=', $to_date)
            ->when($user_id, function ($query) use ($user_id) {
                return $query->where('created_by', $user_id);
            })
            ->count();

        $totalDiscount = $discountData->total_discount ?? 0;
        $totalInvoiceDiscount = $discountData->total_invoice_discount ?? 0;
        $totalCombinedDiscount = $discountData->total_combined_discount ?? 0;

        $returns = $query = PharmacyRetrun::where("pharmacy_return_items.is_posted", 0)
                ->select("pharmacy_return_items.*", "sale.CreatedBy as bill_user")
                ->join("sale", "sale.SaleID", "=", "pharmacy_return_items.sale_id")
                ->where(function ($q) use ($user_id) {
                    $q->where("sale.CreatedBy", "!=", $user_id)
                        ->orWhere("sale.is_posted", 1);
                })
                ->whereDate('pharmacy_return_items.created_at', '>=', $from_date)
                ->whereDate('pharmacy_return_items.created_at', '<=', $to_date)
                ->when($user_id, function ($query) use ($user_id) {
                    return $query->where('pharmacy_return_items.created_by', $user_id);
                })->sum('amount');

        return [
            'total_sale_amount' => $totalSaleAmount,
            'total_discount' => $totalDiscount,
            'total_invoice_discount' => $totalInvoiceDiscount,
            'total_combined_discount' => $totalCombinedDiscount,
            'total_transactions' => $totalTransactions,
            'net_sale_amount' => $totalSaleAmount - $returns,
            'total_returns' => $returns
        ];
    }
}

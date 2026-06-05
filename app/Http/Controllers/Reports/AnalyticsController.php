<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\TempSaleDetails;
use App\Models\TempSale;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = 'Sales Analytics Dashboard';

        // Set default date range (current month)
        $from_date = $request->get('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $to_date = $request->get('to_date', Carbon::now()->format('Y-m-d'));

        // Pass dates to view for form inputs
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;

        // Get basic stats with date filtering
        $data['total_sales'] = $this->getTotalSales($from_date, $to_date);
        $data['total_products_sold'] = $this->getTotalProductsSold($from_date, $to_date);
        $data['average_sale_value'] = $this->getAverageSaleValue($from_date, $to_date);
        $data['total_discount'] = $this->getTotalDiscount($from_date, $to_date);
        $data['top_selling_products'] = $this->getTopSellingProducts(170, $from_date, $to_date);

        return view('reports.analytics.index', $data);
    }

    public function getSalesChart(Request $request)
    {
        $period = $request->get('period', 'daily');
        $days = $request->get('days', 30);
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');

        // Use date range if provided, otherwise use days parameter
        if ($from_date && $to_date) {
            $startDate = Carbon::parse($from_date);
            $endDate = Carbon::parse($to_date);
        } else {
            $startDate = Carbon::now()->subDays($days);
            $endDate = Carbon::now();
        }

        // Create a complete date range
        $dateRange = [];
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dateRange[] = $currentDate->format('Y-m-d');
            $currentDate->addDay();
        }

        $query = TempSaleDetails::select(
            DB::raw('DATE(temp_sale.Date) as date'),
            DB::raw('SUM(temp_sale_details.UnitePrice * (temp_sale_details.Quantity - COALESCE(temp_sale_details.ReturnQuantity, 0))) as gross_sales'),
            DB::raw('SUM(COALESCE(temp_sale.Discount, 0)) as total_discounts'),
            DB::raw('SUM(temp_sale_details.UnitePrice * (temp_sale_details.Quantity - COALESCE(temp_sale_details.ReturnQuantity, 0))) - SUM(COALESCE(temp_sale.Discount, 0)) as total_sales'),
            DB::raw('COUNT(DISTINCT temp_sale_details.SaleID) as total_transactions')
        )
            ->join('temp_sale', 'temp_sale_details.SaleID', '=', 'temp_sale.SaleID')
            ->whereDate('temp_sale.Date', '>=', $startDate->format('Y-m-d'))
            ->whereDate('temp_sale.Date', '<=', $endDate->format('Y-m-d'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Fill missing dates with zero values
        $salesData = collect($dateRange)->map(function ($date) use ($query) {
            return (object) [
                'date' => $date,
                'total_sales' => isset($query[$date]) ? $query[$date]->total_sales : 0,
                'total_transactions' => isset($query[$date]) ? $query[$date]->total_transactions : 0
            ];
        });

        $chartData = [
            'labels' => $salesData->pluck('date')->map(function ($date) {
                return Carbon::parse($date)->format('M d');
            })->toArray(),
            'sales' => $salesData->pluck('total_sales')->toArray(),
            'transactions' => $salesData->pluck('total_transactions')->toArray()
        ];

        return response()->json($chartData);
    }

    public function getProductSalesChart(Request $request)
    {
        $limit = $request->get('limit', 10);
        $days = $request->get('days', 30);
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');

        // Use date range if provided, otherwise use days parameter
        if ($from_date && $to_date) {
            $startDate = Carbon::parse($from_date);
            $endDate = Carbon::parse($to_date);
        } else {
            $startDate = Carbon::now()->subDays($days);
            $endDate = Carbon::now();
        }

        $productSales = TempSaleDetails::select(
            'products.ProductName',
            DB::raw('SUM(temp_sale_details.UnitePrice * (temp_sale_details.Quantity - COALESCE(temp_sale_details.ReturnQuantity, 0))) - SUM(COALESCE(temp_sale.Discount, 0) / (SELECT COUNT(*) FROM temp_sale_details tsd WHERE tsd.SaleID = temp_sale_details.SaleID)) as total_sales'),
            DB::raw('SUM(temp_sale_details.Quantity - COALESCE(temp_sale_details.ReturnQuantity, 0)) as total_quantity')
        )
            ->join('products', 'temp_sale_details.ProductID', '=', 'products.ProductID')
            ->join('temp_sale', 'temp_sale_details.SaleID', '=', 'temp_sale.SaleID')
            ->whereDate('temp_sale.Date', '>=', $startDate->format('Y-m-d'))
            ->whereDate('temp_sale.Date', '<=', $endDate->format('Y-m-d'))
            ->groupBy('temp_sale_details.ProductID', 'products.ProductName')
            ->orderBy('total_sales', 'desc')
            ->limit($limit)
            ->get();

        $chartData = [
            'labels' => $productSales->pluck('ProductName')->toArray(),
            'sales' => $productSales->pluck('total_sales')->toArray(),
            'quantities' => $productSales->pluck('total_quantity')->toArray()
        ];

        return response()->json($chartData);
    }

    public function getMonthlySalesChart(Request $request)
    {
        $months = $request->get('months', 12);
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');

        // Use date range if provided, otherwise use months parameter
        if ($from_date && $to_date) {
            $startDate = Carbon::parse($from_date)->startOfMonth();
            $endDate = Carbon::parse($to_date)->endOfMonth();
        } else {
            $startDate = Carbon::now()->subMonths($months)->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        }

        // Create a complete month range
        $monthRange = [];
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $monthRange[] = [
                'year' => $currentDate->year,
                'month' => $currentDate->month,
                'key' => $currentDate->format('Y-m')
            ];
            $currentDate->addMonth();
        }

        $monthlySales = TempSaleDetails::select(
            DB::raw('YEAR(temp_sale.Date) as year'),
            DB::raw('MONTH(temp_sale.Date) as month'),
            DB::raw('CONCAT(YEAR(temp_sale.Date), "-", LPAD(MONTH(temp_sale.Date), 2, "0")) as month_key'),
            DB::raw('SUM(temp_sale_details.UnitePrice * (temp_sale_details.Quantity - COALESCE(temp_sale_details.ReturnQuantity, 0))) - SUM(COALESCE(temp_sale.Discount, 0)) as total_sales'),
            DB::raw('COUNT(DISTINCT temp_sale_details.SaleID) as total_orders'),
            DB::raw('SUM(temp_sale_details.Quantity - COALESCE(temp_sale_details.ReturnQuantity, 0)) as total_items')
        )
            ->join('temp_sale', 'temp_sale_details.SaleID', '=', 'temp_sale.SaleID')
            ->whereDate('temp_sale.Date', '>=', $startDate->format('Y-m-d'))
            ->whereDate('temp_sale.Date', '<=', $endDate->format('Y-m-d'))
            ->groupBy('year', 'month', 'month_key')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->keyBy('month_key');

        // Fill missing months with zero values
        $completeData = collect($monthRange)->map(function ($monthInfo) use ($monthlySales) {
            $key = $monthInfo['key'];
            return (object) [
                'year' => $monthInfo['year'],
                'month' => $monthInfo['month'],
                'total_sales' => isset($monthlySales[$key]) ? $monthlySales[$key]->total_sales : 0,
                'total_orders' => isset($monthlySales[$key]) ? $monthlySales[$key]->total_orders : 0,
                'total_items' => isset($monthlySales[$key]) ? $monthlySales[$key]->total_items : 0
            ];
        });

        $chartData = [
            'labels' => $completeData->map(function ($item) {
                return Carbon::createFromDate($item->year, $item->month, 1)->format('M Y');
            })->toArray(),
            'sales' => $completeData->pluck('total_sales')->toArray(),
            'orders' => $completeData->pluck('total_orders')->toArray(),
            'items' => $completeData->pluck('total_items')->toArray()
        ];

        return response()->json($chartData);
    }

    public function getSalesStatsChart(Request $request)
    {
        $days = $request->get('days', 30);
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');

        // Use date range if provided, otherwise use days parameter
        if ($from_date && $to_date) {
            $startDate = Carbon::parse($from_date);
            $endDate = Carbon::parse($to_date);
        } else {
            $startDate = Carbon::now()->subDays($days);
            $endDate = Carbon::now();
        }

        // Revenue vs Returns
        $stats = TempSaleDetails::select(
            DB::raw('SUM(temp_sale_details.UnitePrice * temp_sale_details.Quantity) as gross_sales'),
            DB::raw('SUM(temp_sale_details.UnitePrice * COALESCE(temp_sale_details.ReturnQuantity, 0)) as total_returns'),
            DB::raw('SUM(temp_sale_details.UnitePrice * (temp_sale_details.Quantity - COALESCE(temp_sale_details.ReturnQuantity, 0))) as net_sales_before_discount'),
            DB::raw('SUM(COALESCE(temp_sale.Discount, 0)) as total_discounts'),
            DB::raw('COUNT(*) as total_transactions'),
            DB::raw('COUNT(DISTINCT temp_sale_details.ProductID) as unique_products')
        )
            ->join('temp_sale', 'temp_sale_details.SaleID', '=', 'temp_sale.SaleID')
            ->whereDate('temp_sale.Date', '>=', $startDate->format('Y-m-d'))
            ->whereDate('temp_sale.Date', '<=', $endDate->format('Y-m-d'))
            ->first();

        $net_sales_after_discount = ($stats->net_sales_before_discount ?? 0) - ($stats->total_discounts ?? 0);

        return response()->json([
            'gross_sales' => $stats->gross_sales ?? 0,
            'total_returns' => $stats->total_returns ?? 0,
            'net_sales' => $net_sales_after_discount,
            'total_discounts' => $stats->total_discounts ?? 0,
            'total_transactions' => $stats->total_transactions ?? 0,
            'unique_products' => $stats->unique_products ?? 0,
            'return_percentage' => $stats->gross_sales > 0 ? round(($stats->total_returns / $stats->gross_sales) * 100, 2) : 0
        ]);
    }

    private function getTotalSales($from_date = null, $to_date = null)
    {
        $query = TempSaleDetails::join('temp_sale', 'temp_sale_details.SaleID', '=', 'temp_sale.SaleID');
        // $query->whereDate('temp_sale.admission_id', '=', 0);
        // Apply date filtering if provided
        if ($from_date) {
            $query->whereDate('temp_sale.Date', '>=', $from_date);
        }
        if ($to_date) {
            $query->whereDate('temp_sale.Date', '<=', $to_date);
        }

        // Calculate gross sales first
        $grossSales = $query->sum(DB::raw('temp_sale_details.UnitePrice * (temp_sale_details.Quantity - COALESCE(temp_sale_details.ReturnQuantity, 0))')) ?? 0;

        // Calculate total discounts with date filtering
        $totalDiscounts = $this->getTotalDiscount($from_date, $to_date);

        // Return net sales (gross sales - discounts)
        return $grossSales - $totalDiscounts;
    }

    private function getTotalDiscount($from_date = null, $to_date = null)
    {
        $query = TempSale::query();
        //  $query->whereDate('temp_sale.admission_id', '=', 0);
        // Apply date filtering if provided
        if ($from_date) {
            $query->whereDate('Date', '>=', $from_date);
        }
        if ($to_date) {
            $query->whereDate('Date', '<=', $to_date);
        }

        // Sum all discounts from temp_sale table (both Discount and invoice_discount fields)
        return $query->sum(DB::raw('COALESCE(Discount, 0) + COALESCE(invoice_discount, 0)')) ?? 0;
    }

    private function getTotalProductsSold($from_date = null, $to_date = null)
    {
        $query = TempSaleDetails::join('temp_sale', 'temp_sale_details.SaleID', '=', 'temp_sale.SaleID');

        // Apply date filtering if provided
        if ($from_date) {
            $query->whereDate('temp_sale.Date', '>=', $from_date);
        }
        if ($to_date) {
            $query->whereDate('temp_sale.Date', '<=', $to_date);
        }

        return $query->sum(DB::raw('temp_sale_details.Quantity - COALESCE(temp_sale_details.ReturnQuantity, 0)')) ?? 0;
    }

    private function getAverageSaleValue($from_date = null, $to_date = null)
    {
        $totalSales = $this->getTotalSales($from_date, $to_date);

        $query = TempSaleDetails::join('temp_sale', 'temp_sale_details.SaleID', '=', 'temp_sale.SaleID');

        // Apply date filtering if provided
        if ($from_date) {
            $query->whereDate('temp_sale.Date', '>=', $from_date);
        }
        if ($to_date) {
            $query->whereDate('temp_sale.Date', '<=', $to_date);
        }

        $totalTransactions = $query->count();

        return $totalTransactions > 0 ? round($totalSales / $totalTransactions, 2) : 0;
    }

    private function getTopSellingProducts($limit = 5, $from_date = null, $to_date = null)
    {
        $query = TempSaleDetails::select(
            'products.ProductName',
            DB::raw('SUM(temp_sale_details.Quantity - COALESCE(temp_sale_details.ReturnQuantity, 0)) as total_sold'),
            DB::raw('SUM(temp_sale_details.UnitePrice * (temp_sale_details.Quantity - COALESCE(temp_sale_details.ReturnQuantity, 0))) as total_revenue')
        )
            ->join('products', 'temp_sale_details.ProductID', '=', 'products.ProductID')
            ->join('temp_sale', 'temp_sale_details.SaleID', '=', 'temp_sale.SaleID');

        // Apply date filtering if provided
        if ($from_date) {
            $query->whereDate('temp_sale.Date', '>=', $from_date);
        }
        if ($to_date) {
            $query->whereDate('temp_sale.Date', '<=', $to_date);
        }

        return $query->groupBy('temp_sale_details.ProductID', 'products.ProductName')
            ->orderBy('total_revenue', 'desc')
            ->limit($limit)
            ->get();
    }
}

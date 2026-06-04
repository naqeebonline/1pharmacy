<?php

namespace App\Http\Controllers\Reports;

use App\Exports\DailyProductSalesTableExport;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use App\Models\TempSale;
use App\Models\TempSaleDetails;
use App\Models\SaleDetails;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class DailyProductSalesController extends Controller
{
    public function index(Request $request)
    {
        
        $from_date = $request->from_date ?? date('Y-m-d');
        $to_date = $request->to_date ?? date('Y-m-d');

        $data = [
            'from_date' => $from_date,
            'to_date' => $to_date,
            'page_title' => 'Daily Product-wise Sales Dashboard'
        ];

        return view('reports.daily_product_sales', $data);
    }

    public function getProductSalesData(Request $request)
    {
        $from_date = $request->from_date ?? date('Y-m-d');
        $to_date = $request->to_date ?? date('Y-m-d');

        // Get product-wise sales data from temp_sale_details
        $productSales = TempSaleDetails::select([
            'products.ProductID',
            'products.ProductName',
            DB::raw('SUM(temp_sale_details.Quantity - COALESCE(temp_sale_details.ReturnQuantity, 0)) as total_quantity_sold'),
            DB::raw('SUM(temp_sale_details.ReturnQuantity) as total_returned'),
            DB::raw('SUM(temp_sale_details.Quantity) as gross_quantity'),
            DB::raw('AVG(temp_sale_details.UnitePrice) as avg_sale_price'),
            DB::raw('SUM(temp_sale_details.UnitePrice * (temp_sale_details.Quantity - COALESCE(temp_sale_details.ReturnQuantity, 0))) as total_revenue'),
            DB::raw('COUNT(DISTINCT temp_sale_details.SaleID) as total_transactions'),
            DB::raw('MIN(temp_sale.Date) as first_sale_date'),
            DB::raw('MAX(temp_sale.Date) as last_sale_date')
        ])
            ->join('products', 'temp_sale_details.ProductID', '=', 'products.ProductID')
            ->join('temp_sale', 'temp_sale_details.SaleID', '=', 'temp_sale.SaleID')
            ->whereDate('temp_sale.Date', '>=', $from_date)
            ->whereDate('temp_sale.Date', '<=', $to_date)
            ->groupBy('products.ProductID', 'products.ProductName')
            ->orderBy('total_revenue', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $productSales,
            'summary' => [
                'total_products' => $productSales->count(),
                'total_revenue' => $productSales->sum('total_revenue'),
                'total_quantity_sold' => $productSales->sum('total_quantity_sold'),
                'total_transactions' => $productSales->sum('total_transactions')
            ]
        ]);
    }

    public function getProductSalesWithPurchasePrice(Request $request)
    {
        $from_date = $request->from_date ?? date('Y-m-d');
        $to_date = $request->to_date ?? date('Y-m-d');
        $sort = $request->get('sort', 'default');

        $report = $this->buildProductSalesWithCostReport($from_date, $to_date, $sort);

        return response()->json([
            'success' => true,
            'data' => $report['products']->values(),
            'summary' => $report['summary'],
        ]);
    }

    public function exportTableExcel(Request $request)
    {
        $from_date = $request->from_date ?? date('Y-m-d');
        $to_date = $request->to_date ?? date('Y-m-d');
        $sort = $request->get('sort', 'default');

        $report = $this->buildProductSalesWithCostReport($from_date, $to_date, $sort);
        $fileName = 'product_sales_' . $from_date . '_to_' . $to_date . '.xlsx';

        return Excel::download(
            new DailyProductSalesTableExport(
                $report['products'],
                $report['summary'],
                $from_date,
                $to_date
            ),
            $fileName
        );
    }

    public function exportTablePdf(Request $request)
    {
        $from_date = $request->from_date ?? date('Y-m-d');
        $to_date = $request->to_date ?? date('Y-m-d');
        $sort = $request->get('sort', 'default');

        $report = $this->buildProductSalesWithCostReport($from_date, $to_date, $sort);

        $pdf = Pdf::loadView('reports.pdf_daily_product_sales_table', [
            'from_date' => $from_date,
            'to_date' => $to_date,
            'products' => $report['products'],
            'summary' => $report['summary'],
            'report_generated_at' => now()->format('d-m-Y H:i:s'),
        ]);
        $pdf->setPaper('a4', 'landscape');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
        ]);

        $fileName = 'product_sales_' . $from_date . '_to_' . $to_date . '.pdf';

        return $pdf->download($fileName);
    }

    protected function buildProductSalesWithCostReport(string $from_date, string $to_date, ?string $sort = 'default'): array
    {
        $productSalesWithCost = SaleDetails::select([
            'products.ProductID',
            'products.ProductName',
            DB::raw('SUM(sale_details.Quantity - COALESCE(sale_details.ReturnQuantity, 0)) as total_quantity_sold'),
            DB::raw('SUM(sale_details.ReturnQuantity) as total_returned'),
            DB::raw('SUM(sale_details.Quantity) as gross_quantity'),
            DB::raw('SUM(sale_details.UnitePrice * sale_details.Quantity) as total_sale_amount'),
            DB::raw('SUM(sale_details.PurchasePrice * sale_details.Quantity) as total_purchase_amount'),
            DB::raw('SUM(sale_details.UnitePrice * (sale_details.Quantity - COALESCE(sale_details.ReturnQuantity, 0))) as total_revenue'),
            DB::raw('SUM(sale_details.PurchasePrice * (sale_details.Quantity - COALESCE(sale_details.ReturnQuantity, 0))) as total_cost'),
            DB::raw('SUM((sale_details.Quantity * sale_details.UnitePrice * COALESCE(sale.discount_percentage, 0) / 100)) as total_discount'),
            DB::raw('SUM((sale_details.UnitePrice - sale_details.PurchasePrice) * (sale_details.Quantity - COALESCE(sale_details.ReturnQuantity, 0))) as gross_profit'),
            DB::raw('COUNT(DISTINCT sale_details.SaleID) as total_transactions'),
            DB::raw('MIN(sale.Date) as first_sale_date'),
            DB::raw('MAX(sale.Date) as last_sale_date'),
        ])
            ->join('products', 'sale_details.ProductID', '=', 'products.ProductID')
            ->join('sale', 'sale_details.SaleID', '=', 'sale.SaleID')
            ->whereDate('sale.Date', '>=', $from_date)
            ->whereDate('sale.Date', '<=', $to_date)
            ->groupBy('products.ProductID', 'products.ProductName')
            ->orderBy('total_revenue', 'desc')
            ->get();

        // Display-only breakdown (does not change any totals/calculations):
        // same product can have multiple unit purchase prices from different rows.
        $priceBreakdownRows = SaleDetails::select([
            'sale_details.ProductID',
            DB::raw('ROUND(sale_details.UnitePrice, 2) as unit_sale_price'),
            DB::raw('ROUND(sale_details.PurchasePrice, 2) as unit_purchase_price'),
            DB::raw('SUM(sale_details.Quantity - COALESCE(sale_details.ReturnQuantity, 0)) as qty')
        ])
            ->join('sale', 'sale_details.SaleID', '=', 'sale.SaleID')
            ->whereDate('sale.Date', '>=', $from_date)
            ->whereDate('sale.Date', '<=', $to_date)
            ->groupBy(
                'sale_details.ProductID',
                DB::raw('ROUND(sale_details.UnitePrice, 2)'),
                DB::raw('ROUND(sale_details.PurchasePrice, 2)')
            )
            ->orderBy('sale_details.ProductID')
            ->orderByDesc('qty')
            ->get()
            ->groupBy('ProductID');

        $productSalesWithCost->transform(function ($item) {
            $item->total_profit = $item->gross_profit - $item->total_discount;
            $item->profit_margin = $item->total_revenue > 0
                ? round(($item->total_profit / $item->total_revenue) * 100, 2)
                : 0;
            $item->avg_sale_price = $item->total_quantity_sold > 0
                ? round($item->total_sale_amount / ($item->total_quantity_sold + $item->total_returned), 2)
                : 0;
            $item->avg_purchase_price = $item->total_quantity_sold > 0
                ? round($item->total_purchase_amount / ($item->total_quantity_sold + $item->total_returned), 2)
                : 0;
            $item->avg_profit_per_unit = $item->total_quantity_sold > 0
                ? round($item->total_profit / $item->total_quantity_sold, 2)
                : 0;

            $item->price_breakdown = [];

            return $item;
        });

        $productSalesWithCost->each(function ($item) use ($priceBreakdownRows) {
            $rows = $priceBreakdownRows->get($item->ProductID, collect());
            $item->price_breakdown = $rows->map(function ($row) {
                return [
                    'unit_sale_price' => (float) ($row->unit_sale_price ?? 0),
                    'unit_purchase_price' => (float) ($row->unit_purchase_price ?? 0),
                    'qty' => (float) ($row->qty ?? 0),
                ];
            })->values()->all();
        });

        $products = $this->sortProductSalesCollection($productSalesWithCost, $sort);

        $summary = [
            'total_products' => $products->count(),
            'total_sale_amount' => $products->sum('total_sale_amount'),
            'total_purchase_amount' => $products->sum('total_purchase_amount'),
            'total_revenue' => $products->sum('total_revenue'),
            'total_cost' => $products->sum('total_cost'),
            'total_discount' => $products->sum('total_discount'),
            'gross_profit' => $products->sum('gross_profit'),
            'total_profit' => $products->sum('total_profit'),
            'total_quantity_sold' => $products->sum('total_quantity_sold'),
            'total_returned' => $products->sum('total_returned'),
            'gross_quantity' => $products->sum('gross_quantity'),
            'total_transactions' => $products->sum('total_transactions'),
            'avg_profit_margin' => round($products->avg('profit_margin') ?? 0, 2),
        ];

        return [
            'products' => $products,
            'summary' => $summary,
        ];
    }

    protected function sortProductSalesCollection($collection, ?string $sort)
    {
        $sorted = $collection->values();

        switch ($sort) {
            case 'quantity_desc':
                return $sorted->sortByDesc('total_quantity_sold')->values();
            case 'quantity_asc':
                return $sorted->sortBy('total_quantity_sold')->values();
            case 'revenue_desc':
                return $sorted->sortByDesc('total_revenue')->values();
            case 'revenue_asc':
                return $sorted->sortBy('total_revenue')->values();
            case 'profit_desc':
                return $sorted->sortByDesc('total_profit')->values();
            case 'profit_asc':
                return $sorted->sortBy('total_profit')->values();
            default:
                return $sorted->sortByDesc('total_revenue')->values();
        }
    }

    public function getDailySalesChart(Request $request)
    {
        $from_date = $request->from_date ?? date('Y-m-d');
        $to_date = $request->to_date ?? date('Y-m-d');

        $dailySales = SaleDetails::select([
            DB::raw('DATE(sale.Date) as sale_date'),
            DB::raw('SUM(sale_details.UnitePrice * (sale_details.Quantity - COALESCE(sale_details.ReturnQuantity, 0))) as daily_revenue'),
            DB::raw('SUM(sale_details.Quantity - COALESCE(sale_details.ReturnQuantity, 0)) as daily_quantity'),
            DB::raw('SUM((sale_details.Quantity * sale_details.UnitePrice * COALESCE(sale.discount_percentage, 0) / 100)) as daily_discount'),
            DB::raw('COUNT(DISTINCT sale_details.SaleID) as daily_transactions')
        ])
            ->join('sale', 'sale_details.SaleID', '=', 'sale.SaleID')
            ->whereDate('sale.Date', '>=', $from_date)
            ->whereDate('sale.Date', '<=', $to_date)
            ->groupBy(DB::raw('DATE(sale.Date)'))
            ->orderBy('sale_date')
            ->get();

        return response()->json([
            'success' => true,
            'labels' => $dailySales->pluck('sale_date')->map(function ($date) {
                return Carbon::parse($date)->format('M d');
            }),
            'revenue' => $dailySales->pluck('daily_revenue'),
            'quantity' => $dailySales->pluck('daily_quantity'),
            'discount' => $dailySales->pluck('daily_discount'),
            'transactions' => $dailySales->pluck('daily_transactions')
        ]);
    }

    public function getTopSellingProducts(Request $request)
    {
        $from_date = $request->from_date ?? date('Y-m-d');
        $to_date = $request->to_date ?? date('Y-m-d');
        $limit = $request->limit ?? 10;

        $topProducts = SaleDetails::select([
            'products.ProductName',
            DB::raw('SUM(sale_details.Quantity - COALESCE(sale_details.ReturnQuantity, 0)) as total_sold'),
            DB::raw('SUM(sale_details.UnitePrice * (sale_details.Quantity - COALESCE(sale_details.ReturnQuantity, 0))) as total_revenue'),
            DB::raw('SUM((sale_details.Quantity * sale_details.UnitePrice * COALESCE(sale.discount_percentage, 0) / 100)) as total_discount')
        ])
            ->join('products', 'sale_details.ProductID', '=', 'products.ProductID')
            ->join('sale', 'sale_details.SaleID', '=', 'sale.SaleID')
            ->whereDate('sale.Date', '>=', $from_date)
            ->whereDate('sale.Date', '<=', $to_date)
            ->groupBy('products.ProductID', 'products.ProductName')
            ->orderBy('total_revenue', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'products' => $topProducts->pluck('ProductName'),
            'revenue' => $topProducts->pluck('total_revenue'),
            'quantity' => $topProducts->pluck('total_sold')
        ]);
    }

    public function getSalesStatistics(Request $request)
    {
        $from_date = $request->from_date ?? date('Y-m-d');
        $to_date = $request->to_date ?? date('Y-m-d');

        // Get statistics from sale_details with discount information
        $stats = DB::select("
            SELECT 
                COUNT(DISTINCT sd.ProductID) as unique_products,
                SUM(sd.Quantity - COALESCE(sd.ReturnQuantity, 0)) as total_quantity_sold,
                SUM(sd.ReturnQuantity) as total_returned,
                SUM(sd.UnitePrice * (sd.Quantity - COALESCE(sd.ReturnQuantity, 0))) as total_revenue,
                SUM((sd.Quantity * sd.UnitePrice * COALESCE(s.discount_percentage, 0) / 100)) as total_discount,
                COUNT(DISTINCT sd.SaleID) as total_transactions,
                AVG(sd.UnitePrice) as avg_selling_price
            FROM sale_details sd
            JOIN sale s ON sd.SaleID = s.SaleID
            WHERE DATE(s.Date) >= ? AND DATE(s.Date) <= ?
        ", [$from_date, $to_date]);

        $statistics = $stats[0] ?? null;

        if ($statistics) {
            $statistics->avg_transaction_value = $statistics->total_transactions > 0 ?
                round($statistics->total_revenue / $statistics->total_transactions, 2) : 0;
            $statistics->return_rate = $statistics->total_quantity_sold > 0 ?
                round(($statistics->total_returned / ($statistics->total_quantity_sold + $statistics->total_returned)) * 100, 2) : 0;
        }

        return response()->json([
            'success' => true,
            'statistics' => $statistics
        ]);
    }

    public function exportPrintableReport(Request $request)
    {
        $from_date = $request->from_date ?? date('Y-m-d');
        $to_date = $request->to_date ?? date('Y-m-d');

        // Get comprehensive product sales data with discounts
        $productSales = SaleDetails::select([
            'products.ProductID',
            'products.ProductName',
            DB::raw('SUM(sale_details.Quantity - COALESCE(sale_details.ReturnQuantity, 0)) as net_quantity'),
            DB::raw('SUM(sale_details.ReturnQuantity) as returned_quantity'),
            DB::raw('SUM(sale_details.UnitePrice * sale_details.Quantity) as total_sale_amount'),
            DB::raw('SUM(sale_details.PurchasePrice * sale_details.Quantity) as total_purchase_amount'),
            DB::raw('SUM(sale_details.UnitePrice * (sale_details.Quantity - COALESCE(sale_details.ReturnQuantity, 0))) as total_revenue'),
            DB::raw('SUM(sale_details.PurchasePrice * (sale_details.Quantity - COALESCE(sale_details.ReturnQuantity, 0))) as total_cost'),
            DB::raw('SUM((sale_details.Quantity * sale_details.UnitePrice * COALESCE(sale.discount_percentage, 0) / 100)) as total_discount'),
            DB::raw('SUM((sale_details.UnitePrice - sale_details.PurchasePrice) * (sale_details.Quantity - COALESCE(sale_details.ReturnQuantity, 0))) as gross_profit'),
            DB::raw('COUNT(DISTINCT sale_details.SaleID) as total_transactions')
        ])
            ->join('products', 'sale_details.ProductID', '=', 'products.ProductID')
            ->join('sale', 'sale_details.SaleID', '=', 'sale.SaleID')
            ->whereDate('sale.Date', '>=', $from_date)
            ->whereDate('sale.Date', '<=', $to_date)
            ->groupBy('products.ProductID', 'products.ProductName')
            ->orderBy('total_revenue', 'desc')
            ->get();

        // Calculate net profit after discounts
        $productSales->transform(function ($item) {
            $item->total_profit = $item->gross_profit - $item->total_discount;
            $item->profit_margin = $item->total_revenue > 0 ?
                round(($item->total_profit / $item->total_revenue) * 100, 2) : 0;
            return $item;
        });

        $data = [
            'from_date' => $from_date,
            'to_date' => $to_date,
            'products' => $productSales,
            'total_revenue' => $productSales->sum('total_revenue'),
            'total_cost' => $productSales->sum('total_cost'),
            'total_discount' => $productSales->sum('total_discount'),
            'gross_profit' => $productSales->sum('gross_profit'),
            'total_profit' => $productSales->sum('total_profit'),
            'total_products' => $productSales->count(),
            'report_generated_at' => now()->format('Y-m-d H:i:s')
        ];

        return view('reports.print_daily_product_sales', $data);
    }
}

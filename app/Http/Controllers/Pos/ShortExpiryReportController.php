<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ShortExpiryExport;

class ShortExpiryReportController extends Controller
{
    /**
     * Display the short expiry report page
     */
    public function index()
    {
        // Get suppliers for filter dropdown
        $suppliers = DB::table('grn_details as gd')
            ->join('grn as gm', 'gd.GRNID', '=', 'gm.GRNID')
            ->join('sup_cus_details as s', 'gm.SCID', '=', 's.SCID')
            ->select('s.SCID as supplier_id', 's.Name as supplier_name')
            ->where('gd.RemainingQuantity', '>', 0)
            ->where('gd.ProductStatus', 1)
            ->where('s.Type', 1) // Suppliers
            ->groupBy('s.SCID', 's.Name')
            ->orderBy('s.Name')
            ->get();

        // Get categories for filter dropdown - using a simple approach since categories might not exist
        $categories = collect([
            (object)['category_id' => 'medicine', 'category_name' => 'Medicine'],
            (object)['category_id' => 'surgical', 'category_name' => 'Surgical'],
            (object)['category_id' => 'equipment', 'category_name' => 'Equipment'],
            (object)['category_id' => 'other', 'category_name' => 'Other']
        ]);

        return view('reports.short_expiry', compact('suppliers', 'categories'));
    }

    /**
     * Get short expiry count for dashboard card
     */
    public function getShortExpiryCount()
    {
        try {
            $currentDate = Carbon::now();
            $thirtyDaysFromNow = $currentDate->copy()->addDays(180);

            // Count items expiring in next 180 days
            $count = DB::table('grn_details as gd')
                ->join('products as p', 'gd.ProductID', '=', 'p.ProductID')
                ->where('gd.RemainingQuantity', '>', 0)
                ->where('gd.ProductStatus', 1)
                ->whereNotNull('gd.expiry_date')
                ->where('gd.expiry_date', '<=', $thirtyDaysFromNow->format('Y-m-d'))
                ->where('gd.expiry_date', '>=', $currentDate->format('Y-m-d'))
                ->count();

            // Calculate total active products for percentage
            $totalProducts = DB::table('grn_details')
                ->where('RemainingQuantity', '>', 0)
                ->where('ProductStatus', 1)
                ->count();

            $percentage = $totalProducts > 0 ? round(($count / $totalProducts) * 100, 2) : 0;

            return response()->json([
                'success' => true,
                'count' => $count,
                'percentage' => $percentage,
                'total' => $totalProducts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching expiry data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get short expiry data for DataTable
     */
    public function getShortExpiryData(Request $request)
    {
        try {
            $currentDate = Carbon::now();

            // Base query
            $query = DB::table('grn_details as gd')
                ->join('products as p', 'gd.ProductID', '=', 'p.ProductID')
                ->join('grn as gm', 'gd.GRNID', '=', 'gm.GRNID')
                ->join('sup_cus_details as s', 'gm.SCID', '=', 's.SCID')
                ->select([
                    'gd.GDID as id',
                    'p.ProductName as product_name',
                    'gd.batch_no as batch_number',
                    's.Name as supplier_name',
                    DB::raw('"General" as category_name'), // Default category
                    'gd.expiry_date',
                    'gd.RemainingQuantity as current_stock',
                    'gd.UnitPrice as purchase_price',
                    'gd.UnitPrice as sale_price', // Using UnitPrice for both
                    DB::raw('DATEDIFF(gd.expiry_date, CURDATE()) as days_left'),
                    DB::raw('(gd.RemainingQuantity * gd.UnitPrice) as total_value')
                ])
                ->where('gd.RemainingQuantity', '>', 0)
                ->where('gd.ProductStatus', 1)
                ->where('s.Type', 1) // Suppliers
                ->whereNotNull('gd.expiry_date');

            // Apply filters
            if ($request->filled('days_filter')) {
                $daysFilter = $request->days_filter;

                if ($daysFilter === 'expired') {
                    $query->where('gd.expiry_date', '<', $currentDate->format('Y-m-d'));
                } elseif ($daysFilter === 'all') {
                    // No date filter
                } else {
                    $targetDate = $currentDate->copy()->addDays((int)$daysFilter);
                    $query->where('gd.expiry_date', '<=', $targetDate->format('Y-m-d'));
                }
            }

            if ($request->filled('supplier_filter')) {
                $query->where('s.SCID', $request->supplier_filter);
            }

            // Category filter is simplified since we don't have a categories table
            if ($request->filled('category_filter')) {
                // This can be extended later when categories are properly implemented
            }

            // Search functionality
            if ($request->filled('search.value')) {
                $searchValue = $request->input('search.value');
                $query->where(function ($q) use ($searchValue) {
                    $q->where('p.ProductName', 'like', "%{$searchValue}%")
                        ->orWhere('gd.batch_no', 'like', "%{$searchValue}%")
                        ->orWhere('s.Name', 'like', "%{$searchValue}%");
                });
            }

            // Get total count before pagination
            $totalRecords = $query->count();

            // Apply ordering
            if ($request->filled('order')) {
                $orderColumn = $request->input('order.0.column');
                $orderDirection = $request->input('order.0.dir');

                $columns = [
                    0 => 'gd.GDID',
                    1 => 'p.ProductName',
                    2 => 'gd.batch_no',
                    3 => 's.Name',
                    4 => 'category_name',
                    5 => 'gd.expiry_date',
                    6 => 'days_left',
                    7 => 'gd.RemainingQuantity',
                    8 => 'gd.UnitPrice',
                    9 => 'gd.UnitPrice',
                    10 => 'total_value'
                ];

                if (isset($columns[$orderColumn])) {
                    $query->orderBy($columns[$orderColumn], $orderDirection);
                }
            } else {
                $query->orderBy(DB::raw('DATEDIFF(gd.expiry_date, CURDATE())'), 'asc');
            }

            // Apply pagination
            if ($request->filled('length') && $request->input('length') != -1) {
                $query->skip($request->input('start', 0))
                    ->take($request->input('length', 25));
            }

            $data = $query->get();

            // Format data for DataTable
            $formattedData = $data->map(function ($item, $index) use ($request) {
                $daysLeft = $item->days_left;

                // Determine status
                if ($daysLeft < 0) {
                    $status = '<span class="status-expired">Expired</span>';
                } elseif ($daysLeft <= 7) {
                    $status = '<span class="status-expiring">Critical</span>';
                } elseif ($daysLeft <= 30) {
                    $status = '<span class="status-warning">Warning</span>';
                } else {
                    $status = '<span class="status-good">Good</span>';
                }

                return [
                    'DT_RowIndex' => ($request->input('start', 0) + $index + 1),
                    'product_name' => $item->product_name,
                    'batch_number' => $item->batch_number ?: 'N/A',
                    'supplier_name' => $item->supplier_name,
                    'category_name' => $item->category_name,
                    'expiry_date' => Carbon::parse($item->expiry_date)->format('d-M-Y'),
                    'days_left' => $daysLeft,
                    'current_stock' => number_format($item->current_stock),
                    'purchase_price' => number_format($item->purchase_price, 2),
                    'sale_price' => number_format($item->sale_price, 2),
                    'total_value' => number_format($item->total_value, 2),
                    'status' => $status
                ];
            });

            return response()->json([
                'draw' => $request->input('draw'),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $formattedData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'draw' => $request->input('draw'),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Error fetching data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get summary statistics
     */
    public function getShortExpiryStats(Request $request)
    {
        try {
            $currentDate = Carbon::now();

            // Base query for active products
            $baseQuery = DB::table('grn_details as gd')
                ->join('products as p', 'gd.ProductID', '=', 'p.ProductID')
                ->join('grn as gm', 'gd.GRNID', '=', 'gm.GRNID')
                ->join('sup_cus_details as s', 'gm.SCID', '=', 's.SCID')
                ->where('gd.RemainingQuantity', '>', 0)
                ->where('gd.ProductStatus', 1)
                ->where('s.Type', 1)
                ->whereNotNull('gd.expiry_date');

            // Apply filters if provided
            if ($request->filled('supplier_filter')) {
                $baseQuery->where('s.SCID', $request->supplier_filter);
            }

            if ($request->filled('category_filter')) {
                // Category filter logic can be added here when categories are implemented
            }

            // Count expired items
            $expired = (clone $baseQuery)
                ->where('gd.expiry_date', '<', $currentDate->format('Y-m-d'))
                ->count();

            // Count items expiring in 7 days
            $expiring7 = (clone $baseQuery)
                ->where('gd.expiry_date', '>=', $currentDate->format('Y-m-d'))
                ->where('gd.expiry_date', '<=', $currentDate->copy()->addDays(7)->format('Y-m-d'))
                ->count();

            // Count items expiring in 30 days
            $expiring30 = (clone $baseQuery)
                ->where('gd.expiry_date', '>=', $currentDate->format('Y-m-d'))
                ->where('gd.expiry_date', '<=', $currentDate->copy()->addDays(30)->format('Y-m-d'))
                ->count();

            // Total active items
            $total = $baseQuery->count();

            return response()->json([
                'success' => true,
                'stats' => [
                    'expired' => $expired,
                    'expiring_7' => $expiring7,
                    'expiring_30' => $expiring30,
                    'total' => $total
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching statistics: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Export short expiry data to Excel
     */
    public function exportShortExpiry(Request $request)
    {
        try {
            $currentDate = Carbon::now();

            // Build query based on filters
            $query = DB::table('grn_details as gd')
                ->join('products as p', 'gd.ProductID', '=', 'p.ProductID')
                ->join('grn as gm', 'gd.GRNID', '=', 'gm.GRNID')
                ->join('sup_cus_details as s', 'gm.SCID', '=', 's.SCID')
                ->select([
                    'p.ProductName as product_name',
                    'gd.batch_no as batch_number',
                    's.Name as supplier_name',
                    DB::raw('"General" as category_name'),
                    'gd.expiry_date',
                    'gd.RemainingQuantity as current_stock',
                    'gd.UnitPrice as purchase_price',
                    'gd.UnitPrice as sale_price',
                    DB::raw('DATEDIFF(gd.expiry_date, CURDATE()) as days_left'),
                    DB::raw('(gd.RemainingQuantity * gd.UnitPrice) as total_value')
                ])
                ->where('gd.RemainingQuantity', '>', 0)
                ->where('gd.ProductStatus', 1)
                ->where('s.Type', 1)
                ->whereNotNull('gd.expiry_date');

            // Apply filters
            if ($request->filled('days_filter')) {
                $daysFilter = $request->days_filter;

                if ($daysFilter === 'expired') {
                    $query->where('gd.expiry_date', '<', $currentDate->format('Y-m-d'));
                } elseif ($daysFilter !== 'all') {
                    $targetDate = $currentDate->copy()->addDays((int)$daysFilter);
                    $query->where('gd.expiry_date', '<=', $targetDate->format('Y-m-d'));
                }
            }

            if ($request->filled('supplier_filter')) {
                $query->where('s.SCID', $request->supplier_filter);
            }

            if ($request->filled('category_filter')) {
                // Category filter logic can be added here when categories are implemented
            }

            $query->orderBy(DB::raw('DATEDIFF(gd.expiry_date, CURDATE())'), 'asc');

            $data = $query->get();

            // Generate filename with timestamp
            $filename = 'short_expiry_report_' . date('Y-m-d_H-i-s') . '.xlsx';

            return Excel::download(new ShortExpiryExport($data), $filename);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error exporting data: ' . $e->getMessage()
            ]);
        }
    }
}

<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\InventoryExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class GrnReportController extends Controller
{
    public function supplierPurchaseReport(Request $request)
    {
        $supplier_id = $request->get('supplier_id', 1); // Default to SCID = 1, can be changed via parameter
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
        $invoice_no = $request->get('invoice_no');
        $product_ids = $request->get('product_ids', []); // Multi-select product filter
        
        // Get supplier information
        $supplier = DB::table('sup_cus_details')
            ->where('SCID', $supplier_id)
            ->where('Type', 1) // Type 1 = Supplier
            ->first();
            
        if (!$supplier) {
            return redirect()->back()->with('error', 'Supplier not found');
        }
        
        // Get detailed purchase data from this supplier (without grouping products)
        $purchaseQuery = DB::table('grn')
            ->join('grn_details', 'grn.GRNID', '=', 'grn_details.GRNID')
            ->join('products', 'grn_details.ProductID', '=', 'products.ProductID')
            ->where('grn.SCID', $supplier_id);
            
        // Apply date filters if provided
        if ($from_date) {
            $purchaseQuery->where('grn.Dated', '>=', $from_date);
        }
        if ($to_date) {
            $purchaseQuery->where('grn.Dated', '<=', $to_date);
        }
        
        // Apply invoice number filter if provided
        if ($invoice_no) {
            $purchaseQuery->where('grn.InvoiceNo', '=' ,$invoice_no);
        }
        
        // Apply product filter if provided
        if (!empty($product_ids) && is_array($product_ids)) {
            $purchaseQuery->whereIn('grn_details.ProductID', $product_ids);
        }
        
        $purchaseData = $purchaseQuery->select(
                'grn.GRNID',
                'grn.InvoiceNo',
                'grn.Dated',
                'products.ProductID',
                'products.ProductName',
                'grn_details.Quantity',
                'grn_details.UnitPrice',
                'grn_details.batch_no',
                'grn_details.expiry_date',
                'grn_details.discount',
                'grn_details.advance_tax',
                'grn_details.gst_tax',
                DB::raw('(grn_details.Quantity * grn_details.UnitPrice) as subtotal'),
                DB::raw('CASE 
                    WHEN grn_details.discount > 0 THEN 
                        (grn_details.Quantity * grn_details.UnitPrice) * (grn_details.discount / 100)
                    ELSE 0 
                END as calculated_discount'),
                DB::raw('CASE 
                    WHEN grn_details.discount > 0 THEN 
                        (grn_details.Quantity * grn_details.UnitPrice) - ((grn_details.Quantity * grn_details.UnitPrice) * (grn_details.discount / 100))
                    ELSE 
                        (grn_details.Quantity * grn_details.UnitPrice) 
                END as amount_after_discount'),
                DB::raw('CASE 
                    WHEN grn_details.advance_tax > 0 THEN 
                        ((grn_details.Quantity * grn_details.UnitPrice) - 
                         CASE WHEN grn_details.discount > 0 THEN (grn_details.Quantity * grn_details.UnitPrice) * (grn_details.discount / 100) ELSE 0 END) * 
                        (grn_details.advance_tax / 100)
                    ELSE 0 
                END as calculated_advance_tax'),
                DB::raw('CASE 
                    WHEN grn_details.gst_tax > 0 THEN 
                        ((grn_details.Quantity * grn_details.UnitPrice) - 
                         CASE WHEN grn_details.discount > 0 THEN (grn_details.Quantity * grn_details.UnitPrice) * (grn_details.discount / 100) ELSE 0 END) * 
                        (grn_details.gst_tax / 100)
                    ELSE 0 
                END as calculated_gst_tax'),
                DB::raw('(grn_details.Quantity * grn_details.UnitPrice) - 
                    CASE WHEN grn_details.discount > 0 THEN (grn_details.Quantity * grn_details.UnitPrice) * (grn_details.discount / 100) ELSE 0 END + 
                    CASE WHEN grn_details.advance_tax > 0 THEN 
                        ((grn_details.Quantity * grn_details.UnitPrice) - 
                         CASE WHEN grn_details.discount > 0 THEN (grn_details.Quantity * grn_details.UnitPrice) * (grn_details.discount / 100) ELSE 0 END) * 
                        (grn_details.advance_tax / 100) 
                    ELSE 0 END +
                    CASE WHEN grn_details.gst_tax > 0 THEN 
                        ((grn_details.Quantity * grn_details.UnitPrice) - 
                         CASE WHEN grn_details.discount > 0 THEN (grn_details.Quantity * grn_details.UnitPrice) * (grn_details.discount / 100) ELSE 0 END) * 
                        (grn_details.gst_tax / 100) 
                    ELSE 0 END as total_amount')
            )
            ->orderBy('grn.Dated', 'DESC')
            ->orderBy('grn.GRNID', 'DESC')
            ->get();
            
        // Calculate totals
        $totalItems = $purchaseData->sum('Quantity');
        $totalSubtotal = $purchaseData->sum('subtotal');
        $totalDiscount = $purchaseData->sum('calculated_discount');
        $totalAdvanceTax = $purchaseData->sum('calculated_advance_tax');
        $totalGstTax = $purchaseData->sum('calculated_gst_tax');
        $totalAmount = $purchaseData->sum('total_amount');
        $totalProducts = $purchaseData->count();
        $totalGRNs = $purchaseData->unique('GRNID')->count();
        $uniqueProducts = $purchaseData->unique('ProductID')->count(); // Unique products purchased
        
        // Get all available suppliers for debugging
        $allSuppliers = DB::table('sup_cus_details')
            ->where('Type', 1)
            ->where('IsActive', 1)
            ->select('SCID', 'Name')
            ->get();
            
        // Get all available products from this supplier for filter dropdown
        $availableProducts = DB::table('grn_details')
            ->join('grn', 'grn_details.GRNID', '=', 'grn.GRNID')
            ->join('products', 'grn_details.ProductID', '=', 'products.ProductID')
            ->where('grn.SCID', $supplier_id)
            ->select('products.ProductID', 'products.ProductName')
            ->distinct()
            ->orderBy('products.ProductName')
            ->get();
        
        $data = [
            'supplier' => $supplier,
            'purchaseData' => $purchaseData,
            'totalItems' => $totalItems,
            'totalSubtotal' => $totalSubtotal,
            'totalDiscount' => $totalDiscount,
            'totalAdvanceTax' => $totalAdvanceTax,
            'totalGstTax' => $totalGstTax,
            'totalAmount' => $totalAmount,
            'totalProducts' => $totalProducts,
            'totalGRNs' => $totalGRNs,
            'uniqueProducts' => $uniqueProducts,
            'supplier_id' => $supplier_id,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'invoice_no' => $invoice_no,
            'product_ids' => $product_ids,
            'allSuppliers' => $allSuppliers,
            'availableProducts' => $availableProducts
        ];
        
        return view('reports.grn_supplier_report', $data);
    }
    
    public function supplierPurchaseReportPdf(Request $request)
    {
        $supplier_id = $request->get('supplier_id', 1);
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
        $invoice_no = $request->get('invoice_no');
        $product_ids = $request->get('product_ids', []); // Multi-select product filter
        
        // Get supplier information
        $supplier = DB::table('sup_cus_details')
            ->where('SCID', $supplier_id)
            ->where('Type', 1)
            ->first();
            
        if (!$supplier) {
            return redirect()->back()->with('error', 'Supplier not found');
        }
        
        // Get detailed purchase data from this supplier (same query as main report)
        $purchaseQuery = DB::table('grn')
            ->join('grn_details', 'grn.GRNID', '=', 'grn_details.GRNID')
            ->join('products', 'grn_details.ProductID', '=', 'products.ProductID')
            ->where('grn.SCID', $supplier_id);
            
        // Apply date filters if provided
        if ($from_date) {
            $purchaseQuery->where('grn.Dated', '>=', $from_date);
        }
        if ($to_date) {
            $purchaseQuery->where('grn.Dated', '<=', $to_date);
        }
        
        // Apply invoice number filter if provided
        if ($invoice_no) {
            $purchaseQuery->where('grn.InvoiceNo', '=' ,$invoice_no);
        }
        
        // Apply product filter if provided
        if (!empty($product_ids) && is_array($product_ids)) {
            $purchaseQuery->whereIn('grn_details.ProductID', $product_ids);
        }
        
        $purchaseData = $purchaseQuery->select(
                'grn.GRNID',
                'grn.InvoiceNo',
                'grn.Dated',
                'products.ProductID',
                'products.ProductName',
                'grn_details.Quantity',
                'grn_details.UnitPrice',
                'grn_details.batch_no',
                'grn_details.expiry_date',
                'grn_details.discount',
                'grn_details.advance_tax',
                'grn_details.gst_tax',
                DB::raw('(grn_details.Quantity * grn_details.UnitPrice) as subtotal'),
                DB::raw('CASE 
                    WHEN grn_details.discount > 0 THEN 
                        (grn_details.Quantity * grn_details.UnitPrice) * (grn_details.discount / 100)
                    ELSE 0 
                END as calculated_discount'),
                DB::raw('CASE 
                    WHEN grn_details.discount > 0 THEN 
                        (grn_details.Quantity * grn_details.UnitPrice) - ((grn_details.Quantity * grn_details.UnitPrice) * (grn_details.discount / 100))
                    ELSE 
                        (grn_details.Quantity * grn_details.UnitPrice) 
                END as amount_after_discount'),
                DB::raw('CASE 
                    WHEN grn_details.advance_tax > 0 THEN 
                        ((grn_details.Quantity * grn_details.UnitPrice) - 
                         CASE WHEN grn_details.discount > 0 THEN (grn_details.Quantity * grn_details.UnitPrice) * (grn_details.discount / 100) ELSE 0 END) * 
                        (grn_details.advance_tax / 100)
                    ELSE 0 
                END as calculated_advance_tax'),
                DB::raw('CASE 
                    WHEN grn_details.gst_tax > 0 THEN 
                        ((grn_details.Quantity * grn_details.UnitPrice) - 
                         CASE WHEN grn_details.discount > 0 THEN (grn_details.Quantity * grn_details.UnitPrice) * (grn_details.discount / 100) ELSE 0 END) * 
                        (grn_details.gst_tax / 100)
                    ELSE 0 
                END as calculated_gst_tax'),
                DB::raw('(grn_details.Quantity * grn_details.UnitPrice) - 
                    CASE WHEN grn_details.discount > 0 THEN (grn_details.Quantity * grn_details.UnitPrice) * (grn_details.discount / 100) ELSE 0 END + 
                    CASE WHEN grn_details.advance_tax > 0 THEN 
                        ((grn_details.Quantity * grn_details.UnitPrice) - 
                         CASE WHEN grn_details.discount > 0 THEN (grn_details.Quantity * grn_details.UnitPrice) * (grn_details.discount / 100) ELSE 0 END) * 
                        (grn_details.advance_tax / 100) 
                    ELSE 0 END +
                    CASE WHEN grn_details.gst_tax > 0 THEN 
                        ((grn_details.Quantity * grn_details.UnitPrice) - 
                         CASE WHEN grn_details.discount > 0 THEN (grn_details.Quantity * grn_details.UnitPrice) * (grn_details.discount / 100) ELSE 0 END) * 
                        (grn_details.gst_tax / 100) 
                    ELSE 0 END as total_amount')
            )
            ->orderBy('grn.Dated', 'DESC')
            ->orderBy('grn.GRNID', 'DESC')
            ->get();
            
        // Calculate totals
        $totalItems = $purchaseData->sum('Quantity');
        $totalSubtotal = $purchaseData->sum('subtotal');
        $totalDiscount = $purchaseData->sum('calculated_discount');
        $totalAdvanceTax = $purchaseData->sum('calculated_advance_tax');
        $totalGstTax = $purchaseData->sum('calculated_gst_tax');
        $totalAmount = $purchaseData->sum('total_amount');
        $totalProducts = $purchaseData->count();
        $totalGRNs = $purchaseData->unique('GRNID')->count();
        $uniqueProducts = $purchaseData->unique('ProductID')->count();
        
        $data = [
            'supplier' => $supplier,
            'purchaseData' => $purchaseData,
            'totalItems' => $totalItems,
            'totalSubtotal' => $totalSubtotal,
            'totalDiscount' => $totalDiscount,
            'totalAdvanceTax' => $totalAdvanceTax,
            'totalGstTax' => $totalGstTax,
            'totalAmount' => $totalAmount,
            'totalProducts' => $totalProducts,
            'totalGRNs' => $totalGRNs,
            'uniqueProducts' => $uniqueProducts,
            'supplier_id' => $supplier_id,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'invoice_no' => $invoice_no,
            'product_ids' => $product_ids,
        ];
        
        // Generate filename based on filters
        $filename = 'GRN_Supplier_Report_' . $supplier->Name;
        if ($invoice_no) {
            $filename .= '_Invoice_' . $invoice_no;
        }
        if (!empty($product_ids)) {
            $filename .= '_Products_' . count($product_ids);
        }
        if ($from_date) {
            $filename .= '_From_' . $from_date;
        }
        if ($to_date) {
            $filename .= '_To_' . $to_date;
        }
        $filename .= '_' . date('Y-m-d_H-i-s') . '.pdf';
        
        // Generate PDF
        $pdf = Pdf::loadView('reports.grn_supplier_report_pdf', $data);
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->download($filename);
    }
    
    public function inventorySummary(Request $request)
    {
        try {
            // Get inventory data from GRN details table
            $inventoryQuery = DB::table('grn_details')
                ->join('products', 'grn_details.ProductID', '=', 'products.ProductID')
                ->leftJoin('grn', 'grn_details.GRNID', '=', 'grn.GRNID')
                ->leftJoin('sup_cus_details', 'grn.SCID', '=', 'sup_cus_details.SCID')
                ->leftJoin('generic_names', 'products.generic_name_id', '=', 'generic_names.id')
                ->where('grn_details.ProductStatus', 1); // Only active products
                //->where('grn_details.RemainingQuantity', '>', 0); // Only products with remaining quantity

            // Group by ProductID and sum the RemainingQuantity
            $inventoryData = $inventoryQuery->select(
                    'products.ProductID',
                    'products.ProductName',
                    DB::raw('COALESCE(generic_names.name, "N/A") as generic_name'),
                    DB::raw('SUM(grn_details.RemainingQuantity) as total_available_quantity'),
                    DB::raw('AVG(grn_details.UnitPrice) as average_unit_price'),
                    DB::raw('SUM(grn_details.RemainingQuantity * grn_details.UnitPrice) as total_value'),
                    DB::raw('COUNT(DISTINCT grn_details.GRNID) as purchase_entries'),
                    DB::raw('MIN(grn.Dated) as first_purchase_date'),
                    DB::raw('MAX(grn.Dated) as last_purchase_date')
                )
                ->groupBy('products.ProductID', 'products.ProductName', 'generic_names.name')
                // ->having('total_available_quantity', '>', 0)
                // ->orderBy('total_available_quantity', 'DESC')
                ->orderBy('products.ProductName', 'ASC')
                ->get();

        } catch (\Exception $e) {
            // Fallback query without generic names if there's an issue
            try {
                $inventoryQuery = DB::table('grn_details')
                    ->join('products', 'grn_details.ProductID', '=', 'products.ProductID')
                    ->leftJoin('grn', 'grn_details.GRNID', '=', 'grn.GRNID')
                    ->where('grn_details.ProductStatus', 1)
                    //->where('grn_details.RemainingQuantity', '>', 0)
                    ;

                $inventoryData = $inventoryQuery->select(
                        'products.ProductID',
                        'products.ProductName',
                        DB::raw('"N/A" as generic_name'),
                        DB::raw('SUM(grn_details.RemainingQuantity) as total_available_quantity'),
                        DB::raw('AVG(grn_details.UnitPrice) as average_unit_price'),
                        DB::raw('SUM(grn_details.RemainingQuantity * grn_details.UnitPrice) as total_value'),
                        DB::raw('COUNT(DISTINCT grn_details.GRNID) as purchase_entries'),
                        DB::raw('MIN(grn.Dated) as first_purchase_date'),
                        DB::raw('MAX(grn.Dated) as last_purchase_date')
                    )
                    ->groupBy('products.ProductID', 'products.ProductName')
                     ->having('total_available_quantity', '>', 0)
                  // ->orderBy('total_available_quantity', 'DESC')
                ->orderBy('products.ProductName', 'ASC')
                    ->get();
                    
            } catch (\Exception $e2) {
                // If even the basic query fails, return empty data with error message
                return redirect()->back()->with('error', 'Unable to load inventory data. Please check your database connection and table structure.');
            }
        }

        // Calculate summary statistics
        $totalProducts = $inventoryData->count();
        $totalQuantity = $inventoryData->sum('total_available_quantity');
        $totalValue = $this->totalInventoryValue();
        $averageValue = $totalProducts > 0 ? $totalValue / $totalProducts : 0;

        // Get products with low stock (less than 10 units)
        $lowStockProducts = $inventoryData->where('total_available_quantity', '<', 10);
        
        // Get products with high value (top 10)
        $highValueProducts = $inventoryData->sortByDesc('total_value')->take(10);

        $data = [
            'inventoryData' => $inventoryData,
            'totalProducts' => $totalProducts,
            'totalQuantity' => $totalQuantity,
            'totalValue' => number_format($totalValue, 2),
            'averageValue' => number_format($averageValue, 2),
            'lowStockProducts' => $lowStockProducts,
            'highValueProducts' => $highValueProducts,
            'totalInventoryValue' => $totalValue,
            'generated_at' => now()->format('Y-m-d H:i:s')
        ];
        
        return view('reports.inventory_summary', $data);
    }

    public function totalInventoryValue() {

$totalInventoryValue = DB::table('grn_details as gd')
    ->join('grn as g', 'gd.GRNID', '=', 'g.GRNID')
    ->where('gd.RemainingQuantity', '>', 0)
    ->where('gd.ProductStatus', '=', 1)
    ->sum(
        DB::raw('
            (
                gd.UnitPrice * gd.RemainingQuantity
            ) 
            - (
                gd.UnitPrice * gd.RemainingQuantity * IFNULL(gd.discount, 0) / 100
            ) 
            + (
                gd.UnitPrice * gd.RemainingQuantity * IFNULL(gd.gst_tax, 0) / 100
            ) 
            + (
                gd.UnitPrice * gd.RemainingQuantity * IFNULL(gd.advance_tax, 0) / 100
            )
        ')
    );

return $totalInventoryValue;

    }

    /**
     * Export all inventory data to Excel
     */
    public function exportInventoryToExcel()
    {
        return Excel::download(new InventoryExport('all'), 'complete_inventory_report_' . date('Y-m-d_H-i-s') . '.xlsx');
    }

    /**
     * Export low stock inventory data to Excel
     */
    public function exportLowStockToExcel()
    {
        return Excel::download(new InventoryExport('low_stock'), 'low_stock_report_' . date('Y-m-d_H-i-s') . '.xlsx');
    }
}

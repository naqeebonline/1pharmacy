<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\GenericName;
use App\Models\ItemForm;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockPurchaseValueReportController extends Controller
{
    public function index(Request $request)
    {
        $filterType = $request->get('filter_type');
        $filterId = (int) $request->get('filter_id', 0);

        $genericNames = GenericName::where('is_active', 1)->orderBy('name')->get(['id', 'name']);
        $itemForms = ItemForm::where('is_active', 1)->orderBy('name')->get(['id', 'name']);
        $products = Product::where('IsActive', 1)
            ->whereIn('ProductID', function ($query) {
                $query->select('ProductID')
                    ->from('grn_details')
                    ->where('ProductStatus', 1);
            })
            ->orderBy('ProductName')
            ->get(['ProductID', 'ProductName']);

        $rows = null;
        $totals = [
            'total_remaining' => 0,
            'total_row_value' => 0,
        ];
        $hasFilter = false;
        $filterLabel = '';

        if ($filterId > 0 && in_array($filterType, ['generic_name', 'item_form', 'product'], true)) {
            $hasFilter = true;
            $filterLabel = $this->resolveFilterLabel($filterType, $filterId, $genericNames, $itemForms, $products);
            $baseQuery = $this->baseQuery($filterType, $filterId);
            $totals = $this->computeTotals($baseQuery);
            $rows = $this->buildGroupedQuery($baseQuery)
                ->orderBy('products.ProductName')
                ->orderBy('grn_details.UnitPrice')
                ->paginate(100)
                ->appends([
                    'filter_type' => $filterType,
                    'filter_id' => $filterId,
                ]);
        }

        return view('reports.stock_purchase_value_report', [
            'rows' => $rows,
            'totals' => $totals,
            'hasFilter' => $hasFilter,
            'filterType' => $filterType,
            'filterId' => $filterId,
            'filterLabel' => $filterLabel,
            'genericNames' => $genericNames,
            'itemForms' => $itemForms,
            'products' => $products,
            'page_title' => 'Stock Purchase Value Report',
        ]);
    }

    private function baseQuery(string $filterType, int $filterId)
    {
        $query = DB::table('grn_details')
            ->where('grn_details.ProductStatus', 1)
            ->join('products', 'products.ProductID', '=', 'grn_details.ProductID');

        if ($filterType === 'generic_name') {
            $query->where('products.generic_name_id', $filterId);
        } elseif ($filterType === 'item_form') {
            $query->where('products.item_form_id', $filterId);
        } else {
            $query->where('grn_details.ProductID', $filterId);
        }

        return $query;
    }

    private function buildGroupedQuery($baseQuery)
    {
        return (clone $baseQuery)
            ->select([
                'grn_details.ProductID',
                'products.ProductName',
                'grn_details.UnitPrice as UnitCost',
                DB::raw('SUM(grn_details.RemainingQuantity) AS RemainingQuantity'),
                DB::raw('SUM(grn_details.RemainingQuantity * grn_details.UnitPrice) AS row_total'),
            ])
            ->groupBy(
                'grn_details.ProductID',
                'products.ProductName',
                'grn_details.UnitPrice'
            );
    }

    private function computeTotals($query): array
    {
        $totals = (clone $query)
            ->selectRaw('COALESCE(SUM(grn_details.RemainingQuantity), 0) AS total_remaining')
            ->selectRaw('COALESCE(SUM(grn_details.RemainingQuantity * grn_details.UnitPrice), 0) AS total_row_value')
            ->first();

        return [
            'total_remaining' => (float) ($totals->total_remaining ?? 0),
            'total_row_value' => (float) ($totals->total_row_value ?? 0),
        ];
    }

    private function resolveFilterLabel(
        string $filterType,
        int $filterId,
        $genericNames,
        $itemForms,
        $products
    ): string {
        if ($filterType === 'generic_name') {
            $item = $genericNames->firstWhere('id', $filterId);

            return $item ? 'Generic Name: ' . $item->name : '';
        }

        if ($filterType === 'item_form') {
            $item = $itemForms->firstWhere('id', $filterId);

            return $item ? 'Item Form: ' . $item->name : '';
        }

        $product = $products->firstWhere('ProductID', $filterId)
            ?? Product::where('ProductID', $filterId)->first(['ProductID', 'ProductName']);

        return $product
            ? 'Product: ' . $product->ProductName . ' (ID: ' . $product->ProductID . ')'
            : '';
    }
}

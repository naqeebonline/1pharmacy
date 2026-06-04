<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalePurchasePriceCompareController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->get('search', ''));
        $filter = $this->normalizeFilter($request->get('filter', 'sale_lte_purchase'));
        $products = $this->buildCompareQuery($search, $filter)
            ->paginate(50)
            ->appends(['search' => $search, 'filter' => $filter]);

        return view('reports.sale_purchase_price_compare', [
            'products' => $products,
            'search' => $search,
            'filter' => $filter,
            'filter_options' => $this->getFilterOptions(),
            'page_title' => 'Sale vs Purchase Price (Active GRN)',
        ]);
    }

    public function count(Request $request)
    {
        $filter = $this->normalizeFilter($request->get('filter', 'sale_lte_purchase'));
        $count = $this->buildCompareQuery(null, $filter)->count();

        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }

    public function bulkUpdateSalePrices(Request $request)
    {
        $validated = $request->validate([
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|integer',
            'products.*.sale_price' => 'required|numeric|min:0',
            'products.*.unit_sale_price' => 'required|numeric|min:0',
        ]);

        $updated = 0;
        $skipped = [];

        foreach ($validated['products'] as $item) {
            $product = Product::where('ProductID', $item['product_id'])
                ->where('IsActive', 1)
                ->first();

            if (!$product) {
                $skipped[] = (int) $item['product_id'];
                continue;
            }

            $salePrice = round((float) $item['sale_price'], 2);
            $unitSalePrice = round((float) $item['unit_sale_price'], 2);

            Product::where('ProductID', $product->ProductID)->update([
                'SalePrice' => $salePrice,
                'unit_sale_price' => $unitSalePrice,
            ]);

            $updated++;
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $updated . ' product sale price(s) and unit sale price(s) updated successfully.',
                'updated' => $updated,
                'skipped' => $skipped,
            ]);
        }

        return redirect()
            ->route('reports.sale_purchase_price_compare', $request->only(['search', 'filter']))
            ->with('success', $updated . ' product sale price(s) and unit sale price(s) updated successfully.');
    }

    protected function getFilterOptions(): array
    {
        return [
            'sale_lte_purchase' => 'Sale Price ≤ Purchase Price (GRN)',
            'margin_lt_5' => 'Profit margin < 5%',
            'margin_lt_10' => 'Profit margin < 10%',
            'margin_lt_15' => 'Profit margin < 15%',
            'margin_lt_20' => 'Profit margin < 20%',
            'margin_gt_20' => 'Profit margin > 20%',
        ];
    }

    protected function normalizeFilter(?string $filter): string
    {
        $allowed = array_keys($this->getFilterOptions());

        return in_array($filter, $allowed, true) ? $filter : 'sale_lte_purchase';
    }

    protected function buildCompareQuery(?string $search = null, ?string $filter = 'sale_lte_purchase')
    {
        $filter = $this->normalizeFilter($filter);

        $latestGrnSub = DB::table('grn_details')
            ->select('ProductID', DB::raw('MAX(GDID) as max_gdid'))
            ->where('ProductStatus', 1)
            ->where('RemainingQuantity', '>', 0)
            ->groupBy('ProductID');

        $query = DB::table('products as p')
            ->joinSub($latestGrnSub, 'lg', 'p.ProductID', '=', 'lg.ProductID')
            ->join('grn_details as gd', 'gd.GDID', '=', 'lg.max_gdid')
            ->where('p.IsActive', 1);

        $this->applyPriceFilter($query, $filter);

        $query->select([
                'p.ProductID',
                'p.ProductName',
                'p.SalePrice',
                'p.pack_size',
                'p.pack_price as product_pack_price',
                'p.unit_sale_price',
                'p.avaliable_quantity',
                'gd.pack_price as grn_pack_price',
                'gd.pack_size as grn_pack_size',
                'gd.batch_no',
                'gd.RemainingQuantity as grn_remaining_qty',
                'gd.GDID',
                'gd.expiry_date',
            ])
            ->orderBy('p.ProductName');

        if ($search !== null && $search !== '') {
            $query->where('p.ProductName', 'LIKE', '%' . $search . '%');
        }

        return $query;
    }

    protected function applyPriceFilter($query, string $filter): void
    {
        switch ($filter) {
            case 'margin_lt_5':
                $query->where('gd.pack_price', '>', 0)
                    ->whereRaw('((p.SalePrice - gd.pack_price) / gd.pack_price * 100) < ?', [5]);
                break;
            case 'margin_lt_10':
                $query->where('gd.pack_price', '>', 0)
                    ->whereRaw('((p.SalePrice - gd.pack_price) / gd.pack_price * 100) < ?', [10]);
                break;
            case 'margin_lt_15':
                $query->where('gd.pack_price', '>', 0)
                    ->whereRaw('((p.SalePrice - gd.pack_price) / gd.pack_price * 100) < ?', [15]);
                break;
            case 'margin_lt_20':
                $query->where('gd.pack_price', '>', 0)
                    ->whereRaw('((p.SalePrice - gd.pack_price) / gd.pack_price * 100) < ?', [20]);
                break;
            case 'margin_gt_20':
                $query->where('gd.pack_price', '>', 0)
                    ->whereRaw('((p.SalePrice - gd.pack_price) / gd.pack_price * 100) > ?', [20]);
                break;
            case 'sale_lte_purchase':
            default:
                $query->whereColumn('p.SalePrice', '<=', 'gd.pack_price');
                break;
        }
    }
}

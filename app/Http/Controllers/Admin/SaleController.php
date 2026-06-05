<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PatientController\PatientAdmissionController;
use App\Models\Appointments\Appointment;
use App\Models\Customer;
use App\Models\Finance\FinanceVoucher;
use App\Models\GrnDetails;
use App\Models\Market;
use App\Models\MedicineType;
use App\Models\Patient\InPatientAdmission;
use App\Models\Patient\Patient;
use App\Models\Patient\PatientAdmission;
use App\Models\PharmacyRetrun;
use App\Models\PharmacyTransfer;
use App\Models\PharmacyTransferDetails;
use App\Models\Product;
use App\Models\ReceiveablesDetail;
use App\Models\Sale;
use App\Models\SaleDetails;
use App\Models\SalePayment;
use App\Models\Store;
use App\Models\Tax;
use App\Models\TempSale;
use App\Models\TempSaleDetails;
use App\Models\WardRequest;
use App\Models\WardRequestDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SaleController extends Controller
{
    /**
     * Available quantity for a product (GRN remaining minus consumption, by store).
     */
    public function getProductAvailableQuantity(int $productId, $storeId = null): float
    {
        return (float) (new StockController())->avaliableQuantity($productId, $storeId);
    }

    /** Maximum products returned for short POS search (legacy / API). */
    private const RETAIL_SEARCH_MAX_RESULTS = 50;

    /** Maximum products when search term is longer than 4 characters. */
    private const RETAIL_SEARCH_MAX_RESULTS_EXTENDED = 300;

    /** Products preloaded on POS (recently sold + catalog fill). */
    private const RETAIL_INITIAL_LOAD_LIMIT = 40;

    /** Recent bills to scan for customer-specific product history (in-patient / admitted). */
    private const RETAIL_INITIAL_BILL_LIMIT = 130;

    /** Rows to load from DB before stock filter (many rows have zero stock). */
    private const RETAIL_SEARCH_FETCH_LIMIT = 130;

    /** Fetch limit for extended search (before in-stock filter). */
    private const RETAIL_SEARCH_FETCH_LIMIT_EXTENDED = 900;

    /**
     * Base query for retail sale product search.
     */
    protected function retailSaleProductBaseQuery(?int $limit = null)
    {
        $query = Product::with('generic_name')
            ->when(session('store_id'), function ($q) {
                $q->where('store_id', session('store_id'));
            })
            ->where('IsActive', 1)
            ->where('ProductName', '!=', '')
            ->where('pack_size', '!=', 0)
            ->where('pack_price', '!=', 0)
            ->orderBy('ProductName', 'ASC');

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query;
    }

    protected function filterRetailSaleProductsWithStock($products)
    {
        foreach ($products as $value) {
            $value->avaliable_qty = $this->getProductAvailableQuantity((int) $value->ProductID);
        }

        return $products->filter(function ($value) {
            return $value->ProductName !== ''
                && $value->ProductName !== '-'
                && (float) $value->avaliable_qty != 0;
        })->values();
    }

    /**
     * Split search into words (flexible: "NS 500", etc.).
     */
    protected function parseRetailSaleSearchTerm(string $term): array
    {
        $termLower = mb_strtolower(trim($term));
        if ($termLower === '') {
            return [];
        }

        $words = preg_split('/\s+/', $termLower, -1, PREG_SPLIT_NO_EMPTY);

        return $words ?: [$termLower];
    }

    /**
     * Primary word starts with — ProductName OR generic name (e.g. ns%).
     */
    protected function queryRetailSaleProductsStartsWith(array $words, ?int $fetchLimit = null)
    {
        if ($words === []) {
            return collect();
        }

        $fetchLimit = $fetchLimit ?? self::RETAIL_SEARCH_FETCH_LIMIT;
        $primary = $words[0];
        $pattern = $primary . '%';

        $query = $this->retailSaleProductBaseQuery($fetchLimit)
            ->where(function ($q) use ($pattern) {
                $q->whereRaw('LOWER(ProductName) LIKE ?', [$pattern])
                    ->orWhereHas('generic_name', function ($gq) use ($pattern) {
                        $gq->whereRaw('LOWER(name) LIKE ?', [$pattern]);
                    });
            });

        foreach (array_slice($words, 1) as $word) {
            $like = '%' . $word . '%';
            $query->where(function ($sub) use ($like) {
                $sub->whereRaw('LOWER(ProductName) LIKE ?', [$like])
                    ->orWhereHas('generic_name', function ($gq) use ($like) {
                        $gq->whereRaw('LOWER(name) LIKE ?', [$like]);
                    });
            });
        }

        return $query->get();
    }

    /**
     * Term anywhere — ProductName OR generic name (e.g. %ns%); all words must match.
     */
    protected function queryRetailSaleProductsContainsAnywhere(array $words, ?int $fetchLimit = null)
    {
        if ($words === []) {
            return collect();
        }

        $fetchLimit = $fetchLimit ?? self::RETAIL_SEARCH_FETCH_LIMIT;

        $query = $this->retailSaleProductBaseQuery($fetchLimit);

        foreach ($words as $word) {
            $pattern = '%' . $word . '%';
            $query->where(function ($q) use ($pattern) {
                $q->whereRaw('LOWER(ProductName) LIKE ?', [$pattern])
                    ->orWhereHas('generic_name', function ($gq) use ($pattern) {
                        $gq->whereRaw('LOWER(name) LIKE ?', [$pattern]);
                    });
            });
        }

        return $query->get();
    }

    protected function productMatchesStartsWithTerm($product, string $primary): bool
    {
        $productName = mb_strtolower((string) ($product->ProductName ?? ''));
        $genericName = mb_strtolower((string) ($product->generic_name?->name ?? ''));

        return ($productName !== '' && str_starts_with($productName, $primary))
            || ($genericName !== '' && str_starts_with($genericName, $primary));
    }

    protected function sortRetailSaleSearchResults($products, string $termLower)
    {
        $words = $this->parseRetailSaleSearchTerm($termLower);
        $primary = $words[0] ?? mb_strtolower(trim($termLower));

        return $products->sort(function ($a, $b) use ($primary) {
            $aStarts = $this->productMatchesStartsWithTerm($a, $primary) ? 0 : 1;
            $bStarts = $this->productMatchesStartsWithTerm($b, $primary) ? 0 : 1;

            if ($aStarts !== $bStarts) {
                return $aStarts <=> $bStarts;
            }

            return strcasecmp((string) $a->ProductName, (string) $b->ProductName);
        })->values();
    }

    protected function getRetailProductSearchRank($product, string $termLower): int
    {
        $words = $this->parseRetailSaleSearchTerm($termLower);
        $primary = $words[0] ?? mb_strtolower(trim($termLower));

        return $this->productMatchesStartsWithTerm($product, $primary) ? 1 : 2;
    }

    protected function getWalkingCustomerPatientIds(): array
    {
        return Patient::where('patient_type', 'walking_customer')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    protected function isWalkingCustomerPatient(int $patientId): bool
    {
        if ($patientId <= 0) {
            return false;
        }

        return Patient::where('id', $patientId)
            ->where('patient_type', 'walking_customer')
            ->exists();
    }

    /**
     * Last N sale IDs for one customer (newest bills first).
     */
    protected function getRecentSaleIdsForPatient(int $patientId, int $billLimit): array
    {
        if ($patientId <= 0 || $billLimit <= 0) {
            return [];
        }

        $storeId = session('store_id');

        $query = DB::table('sale')
            ->where('patient_id', $patientId)
            ->orderByDesc(DB::raw('COALESCE(Date, CreatedAt)'))
            ->orderByDesc('SaleID')
            ->limit($billLimit);

        if ($storeId) {
            $query->where('store_id', $storeId);
        }

        return $query->pluck('SaleID')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    /**
     * Distinct products from sales, optionally filtered by patient(s) and/or sale IDs.
     */
    protected function getRecentlySoldProductIds(
        int $productLimit,
        ?array $patientIds = null,
        ?array $saleIds = null
    ): array {
        $storeId = session('store_id');

        $query = DB::table('sale_details as sd')
            ->join('sale as s', 's.SaleID', '=', 'sd.SaleID')
            ->whereNotNull('sd.ProductID');

        if ($saleIds !== null && $saleIds !== []) {
            $query->whereIn('sd.SaleID', $saleIds);
        }

        if ($patientIds !== null && $patientIds !== []) {
            $query->whereIn('s.patient_id', $patientIds);
        }

        if ($storeId) {
            $query->where('s.store_id', $storeId);
        }

        return $query
            ->select('sd.ProductID', DB::raw('MAX(COALESCE(s.Date, s.CreatedAt)) as last_sold_at'))
            ->groupBy('sd.ProductID')
            ->orderByDesc('last_sold_at')
            ->limit($productLimit)
            ->pluck('sd.ProductID')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    protected function getRecentlySoldProductIdsForWalkingCustomers(int $limit): array
    {
        $patientIds = $this->getWalkingCustomerPatientIds();
        if ($patientIds === []) {
            return [];
        }

        return $this->getRecentlySoldProductIds($limit, $patientIds);
    }

    /**
     * Products purchased by one customer in their last N bills.
     */
    protected function getRecentlySoldProductIdsForPatient(int $patientId, int $productLimit, int $billLimit): array
    {
        $saleIds = $this->getRecentSaleIdsForPatient($patientId, $billLimit);
        if ($saleIds === []) {
            return [];
        }

        return $this->getRecentlySoldProductIds($productLimit, null, $saleIds);
    }

    protected function loadRetailSaleProductsByIdsInOrder(array $productIds)
    {
        if ($productIds === []) {
            return collect();
        }

        $safeIds = array_map('intval', $productIds);

        return Product::with('generic_name')
            ->when(session('store_id'), function ($q) {
                $q->where('store_id', session('store_id'));
            })
            ->where('IsActive', 1)
            ->where('ProductName', '!=', '')
            ->where('pack_size', '!=', 0)
            ->where('pack_price', '!=', 0)
            ->whereIn('ProductID', $safeIds)
            ->orderByRaw('FIELD(ProductID, ' . implode(',', $safeIds) . ')')
            ->get();
    }

    /**
     * Fill remaining slots from catalog (in-stock), excluding already loaded IDs.
     */
    protected function loadRetailSaleFillProducts(array $excludeIds, int $needCount)
    {
        if ($needCount <= 0) {
            return collect();
        }

        $fetchLimit = min($needCount * 4, 2000);

        $query = $this->retailSaleProductBaseQuery($fetchLimit);
        if ($excludeIds !== []) {
            $query->whereNotIn('ProductID', $excludeIds);
        }

        return $this->filterRetailSaleProductsWithStock($query->get())
            ->take($needCount)
            ->values();
    }

    /**
     * Up to 250 in-stock products: customer/walking sale history first, then catalog fill.
     */
    protected function loadRetailSaleInitialProducts(
        int $patientId = 0,
        string $context = 'retail',
        int $billLimit = self::RETAIL_INITIAL_BILL_LIMIT
    ) {
        $limit = self::RETAIL_INITIAL_LOAD_LIMIT;
        $billLimit = $billLimit > 0 ? $billLimit : self::RETAIL_INITIAL_BILL_LIMIT;
        $soldIds = [];

        if ($patientId > 0) {
            if ($context === 'in_patient' || !$this->isWalkingCustomerPatient($patientId)) {
                $soldIds = $this->getRecentlySoldProductIdsForPatient($patientId, $limit, $billLimit);
            } else {
                $soldIds = $this->getRecentlySoldProductIdsForWalkingCustomers($limit);
            }
        } elseif ($context === 'retail') {
            $soldIds = $this->getRecentlySoldProductIdsForWalkingCustomers($limit);
        }

        $result = collect();
        $includedIds = [];

        if ($soldIds !== []) {
            $productsById = $this->loadRetailSaleProductsByIdsInOrder($soldIds)
                ->keyBy('ProductID');

            foreach ($soldIds as $productId) {
                if ($result->count() >= $limit) {
                    break;
                }

                $product = $productsById->get($productId);
                if (!$product) {
                    continue;
                }

                $product->avaliable_qty = $this->getProductAvailableQuantity((int) $product->ProductID);
                if ($product->ProductName === '' || $product->ProductName === '-' || (float) $product->avaliable_qty == 0) {
                    continue;
                }

                $result->push($product);
                $includedIds[] = $productId;
            }
        }

        $remaining = $limit - $result->count();
        if ($remaining > 0) {
            $result = $result->concat($this->loadRetailSaleFillProducts($includedIds, $remaining));
        }

        return $result->values();
    }

    /**
     * 1) ProductName or generic STARTS WITH term, then 2) term ANYWHERE — up to 50 in-stock.
     */
    protected function loadRetailSaleProductsForDropdown(
        ?string $term = null,
        ?int $maxResults = null,
        ?int $fetchLimit = null
    ) {
        if ($term === null || trim($term) === '') {
            return collect();
        }

        $maxResults = $maxResults ?? self::RETAIL_SEARCH_MAX_RESULTS;
        $fetchLimit = $fetchLimit ?? self::RETAIL_SEARCH_FETCH_LIMIT;
        $termLower = mb_strtolower(trim($term));
        $words = $this->parseRetailSaleSearchTerm($term);

        $startsWithStock = $this->filterRetailSaleProductsWithStock(
            $this->queryRetailSaleProductsStartsWith($words, $fetchLimit)
        );

        $startsWithIdLookup = array_flip($startsWithStock->pluck('ProductID')->all());

        $containsStock = $this->filterRetailSaleProductsWithStock(
            $this->queryRetailSaleProductsContainsAnywhere($words, $fetchLimit)
        )->filter(function ($product) use ($startsWithIdLookup) {
            return !isset($startsWithIdLookup[$product->ProductID]);
        })->values();

        $merged = $startsWithStock
            ->concat($containsStock)
            ->values();

        return $this->sortRetailSaleSearchResults($merged, $termLower)
            ->take($maxResults)
            ->values();
    }

    /**
     * Unit sale price for POS dropdown (unit_sale_price, else pack_price / pack_size).
     */
    protected function resolveProductUnitSalePrice($product): float
    {
        $unitSale = (float) ($product->unit_sale_price ?? 0);
        if ($unitSale > 0) {
            return round($unitSale, 2);
        }

        $packSize = (float) ($product->pack_size ?? 0);
        $packPrice = (float) ($product->pack_price ?? 0);
        if ($packSize > 0 && $packPrice > 0) {
            return round($packPrice / $packSize, 2);
        }

        return round((float) ($product->SalePrice ?? $product->PurchasePrice ?? 0), 2);
    }

    /**
     * Select options for #product_id (matches retial_sale.blade.php option attributes).
     */
    protected function retailSaleProductsToSelectOptions($products): array
    {
        $options = [];

        foreach ($products as $value) {
            if ($value->ProductName === '' || $value->ProductName === '-' || (float) $value->avaliable_qty == 0) {
                continue;
            }

            $options[] = [
                'id' => $value->ProductID,
                'text' => $value->ProductName . ' | ' . ($value->generic_name?->name ?? '') . ' | PS: ' . $value->pack_size . ' | Qty ' . $value->avaliable_qty,
                'packsize' => $value->pack_size,
                'purchasePrice' => $this->resolveProductUnitSalePrice($value),
                'taxPercentage' => $value->taxPercentage ?? 0,
                'allowPercentage' => $value->allow_percentage ?? 0,
            ];
        }

        return $options;
    }

    public function refresh_retail_products(Request $request)
    {
        $term = trim((string) $request->get('q', ''));
        $initial = $request->boolean('initial');
        $patientId = (int) $request->get('patient_id', 0);
        $context = (string) $request->get('context', 'retail');
        $billLimit = max(0, (int) $request->get('bill_limit', self::RETAIL_INITIAL_BILL_LIMIT));

        if ($initial || $term === '') {
            $products = $this->loadRetailSaleInitialProducts($patientId, $context, $billLimit);
        } else {
            $termLength = mb_strlen($term);
            $extendedSearch = $termLength > 2;
            $products = $this->loadRetailSaleProductsForDropdown(
                $term,
                $extendedSearch ? self::RETAIL_SEARCH_MAX_RESULTS_EXTENDED : self::RETAIL_SEARCH_MAX_RESULTS,
                $extendedSearch ? self::RETAIL_SEARCH_FETCH_LIMIT_EXTENDED : self::RETAIL_SEARCH_FETCH_LIMIT
            );
        }

        $options = $this->retailSaleProductsToSelectOptions($products);

        return response()->json([
            'status' => true,
            'products' => $options,
            'count' => count($options),
            'initial' => $initial || $term === '',
            'patient_id' => $patientId,
            'context' => $context,
        ]);
    }

    public function sehat_card_pharmacy_sale()
    {
        $store = Store::whereId(env('SEHAT_CARD_PHARMACY_STORE_ID'))->first();

        session(['store_id' => 1]);
        session(['store_name' => "Sehat Card Pharmacy Sale"]);
        session(['is_free' => 1]);
        /*if($store){
            session(['store_id' => $store->id]);
            session(['store_name' => $store->store_name]);
            session(['is_free' => $store->use_purchase_price_as_sale_price]);
        }*/


        $type = $_GET['type'] ?? "";
        $data['type'] = $type;
        $data["ward_request"] = $_GET["ward_request"] ?? "";
        $data['patient_id'] = "";
        $data['list_products'] = [];

        if ($data['ward_request']) {
            $ward_request = WardRequest::whereId($data['ward_request'])->first();
            $data['patient_id'] = $ward_request->patient_id;
            $ward_request_details = WardRequestDetails::with(['products'])->where(["wr_id" => $ward_request->id])->get();
            $list_products = [];
            foreach ($ward_request_details as $key => $value) {
                $avliable_qty = $this->getProductAvailableQuantity((int) $value->product_id);
                $res = [
                    "ProductID" => $value->product_id,
                    "Product" => $value->products->ProductName,
                    "Name" => $value->products->ProductName,
                    "UnitePrice" => $value->products->PurchasePrice,
                    "Quantity" => $value->quantity,
                    "Total" => ($value->quantity) * ($value->products->PurchasePrice),
                    "AvailableQuantity" => $avliable_qty,
                    "taxAmount" => 0,
                    "taxPercentage" => 0,
                    "currentAvailableQuantity" => $avliable_qty,
                    "dose_type" => '-',
                ];
                array_push($list_products, $res);
            }
            $data['list_products'] = $list_products;
        }
        $data["title"] = "Add New Sale";
        $data['products'] = Product::orderBy("ProductName", "ASC")
            ->when($type == 'Home', function ($query) {
                return $query->where("item_form_id", "!=", 16);
            })
            /* ->when(session('store_id'),function ($q){
                $q->where('store_id',env('SEHAT_CARD_PHARMACY_STORE_ID'));
            })*/
            ->where('store_id', env('SEHAT_CARD_PHARMACY_STORE_ID'))
            ->get();
        foreach ($data['products'] as $key => $value) {
            $value->avaliable_qty = $this->getProductAvailableQuantity(
                (int) $value->ProductID,
                (int) env('SEHAT_CARD_PHARMACY_STORE_ID')
            );
        }
        //$data['customers'] = Customer::where(["Type" => 2])->orderBy("Name", "ASC")->get();
        $data['admitted_patients'] = PatientAdmission::where(["admission_status" => "Admit", "is_active" => 1, "patient_type" => "sehat_card"])
            //->orWhereDate('discharge_date', '>=', Carbon::now()->subDay(2)->format('Y-m-d H:i:s'))
            ->with(["patient"])->get();

        $data['invoiceNo'] = $this->returnInvoiceNumber();

        return view("sale.new_sale", $data);
    }


    public function retail_pharmacy_sale()
    {
        $store = Store::where("id", "!=", env('SEHAT_CARD_PHARMACY_STORE_ID'))->first();
        session(['store_id' => 2]);
        session(['store_name' => "Retail Pharmacy Sale"]);
        session(['is_free' => 0]);
        /*if($store){
            session(['store_id' => $store->id]);
            session(['store_name' => $store->store_name]);
            session(['is_free' => $store->use_purchase_price_as_sale_price]);
        }*/

        $type = $_GET['type'] ?? "Home";
        $data['type'] = $type;
        $data["ward_request"] = $_GET["ward_request"] ?? "";
        $data['patient_id'] = "";
        $data['list_products'] = [];

        $data['appointments'] = Appointment::where('is_active', 1)
            ->where('created_at', '>=', Carbon::now()->subDays(2)) // last 5 days
            ->with(['patient'])
            ->orderBy('appointment_date', 'desc')
            ->get();


        $data["title"] = "Retail Sale";
        /*$data['products'] = Product::orderBy("ProductName", "ASC")
             ->when(session('store_id'),function ($q){
                 $q->where('store_id',session('store_id'));
             })
            ->get();*/
        $data['products'] = [];
        //$data['customers'] = Customer::where(["Type" => 2])->orderBy("Name", "ASC")->get();
        $data['admitted_patients'] = Patient::where("patient_type", "walking_customer")->get();

        $data['invoiceNo'] = $this->returnInvoiceNumber();
        $data['retail_print_mode'] = session('retail_print_mode', 'a4');
        $data['default_row_taxes'] = $this->getDefaultRowTaxPercentages();

        return view("sale.retial_sale", $data);
    }

    public function set_retail_print_mode()
    {
        $mode = request()->input('mode', 'a4');
        if (!in_array($mode, ['thermal', 'a4'], true)) {
            $mode = 'a4';
        }

        session(['retail_print_mode' => $mode]);

        return response()->json([
            'status' => true,
            'mode' => $mode,
        ]);
    }

    public function pharmacy_transfer()
    {
        $store = Store::where("id", "!=", env('SEHAT_CARD_PHARMACY_STORE_ID'))->first();
        session(['store_id' => 2]);
        session(['store_name' => "Retail Pharmacy Sale"]);
        session(['is_free' => 0]);
        /*if($store){
            session(['store_id' => $store->id]);
            session(['store_name' => $store->store_name]);
            session(['is_free' => $store->use_purchase_price_as_sale_price]);
        }*/

        $type = $_GET['type'] ?? "Home";
        $data['type'] = $type;
        $data["ward_request"] = $_GET["ward_request"] ?? "";
        $data['patient_id'] = "";
        $data['list_products'] = [];

        $data['appointments'] = [];


        $data["title"] = "Retail Sale";
        /*$data['products'] = Product::orderBy("ProductName", "ASC")
             ->when(session('store_id'),function ($q){
                 $q->where('store_id',session('store_id'));
             })
            ->get();*/
        //Cache::forget('products_store_2');
        $data["products"] =  Product::with('generic_name')
            ->when(session('store_id'), function ($q) {
                $q->where('store_id', session('store_id'));
            })
            ->orderBy("ProductName", "ASC")
            ->where("IsActive", 1)
            ->where("ProductName", "!=", '')
            ->where("pack_size", "!=", 0)
            ->where("pack_price", "!=", 0)

            ->get();
        foreach ($data['products'] as $key => $value) {
            $value->avaliable_qty = $this->getProductAvailableQuantity((int) $value->ProductID);
        }
        //$data['customers'] = Customer::where(["Type" => 2])->orderBy("Name", "ASC")->get();
        $data['admitted_patients'] = Patient::where("patient_type", "walking_customer")->get();

        $data['invoiceNo'] = $this->returnTransferInvoiceNumber();

        return view("sale.pharmacy_transfer", $data);
    }

    public function search_appointment(Request $request)
    {
        $term = $request->get('q');

        $appointments = Appointment::where('is_active', 1)
            ->when($term, function ($q) use ($term) {
                $q->whereHas('patient', function ($sub) use ($term) {
                    $sub->where('name', 'like', "%{$term}%")
                        ->orWhere('mr_no', 'like', "%{$term}%");
                })
                    ->orWhere('appointment_number', 'like', "%{$term}%");
            })
            ->with('patient')
            ->orderBy('appointment_date', 'desc')
            ->limit(20)
            ->get();

        return response()->json($appointments->map(function ($a) {
            return [
                'id'   => $a->id,
                'text' => $a->patient->name .
                    " | Appointment# " . $a->appointment_number .
                    " | MR#: " . $a->patient->mr_no,
            ];
        }));
    }

    public function in_patient_pharmacy_sale()
    {
        $store = Store::where("id", "!=", env('SEHAT_CARD_PHARMACY_STORE_ID'))->first();
        /*if($store){
            session(['store_id' => $store->id]);
            session(['store_name' => $store->store_name]);
            session(['is_free' => $store->use_purchase_price_as_sale_price]);
        }*/
        session(['store_id' => 2]);
        session(['store_name' => "In Patient Retail Pharmacy Sale"]);
        session(['is_free' => 0]);

        $type = $_GET['type'] ?? '';
        $data['type'] = $type;
        $data["ward_request"] = $_GET["ward_request"] ?? "";
        $data['patient_id'] = "";
        $data['list_products'] = [];

        $data["title"] = "Add New Sale";
        $data['products'] = [];

        $data['medicine_types'] = MedicineType::orderBy('name')->get();
        //$data['customers'] = Customer::where(["Type" => 2])->orderBy("Name", "ASC")->get();
        $data['admitted_patients'] = Patient::where("is_active",1)->get();
           

           // dd( $data['admitted_patients']);

        $data['invoiceNo'] = $this->returnInvoiceNumber();
        $data['retail_print_mode'] = session('retail_print_mode', 'a4');
        $data['default_row_taxes'] = $this->getDefaultRowTaxPercentages();

        return view("sale.in_patient_pharmacy_sale", $data);
    }

    // public function ware_house_stock()
    // {

    //     $data["title"] = "Add New Supplier or Customer";
    //     $data['market'] = Market::where(["IsActive" => 1])->get()->sortBy('Name');

    //     return view("configuration.sup_cus_registration", $data);
    // }

    // public function list_customer()
    // {
    //     $res = Customer::with("market")->where(["IsActive" => 1]);

    //     return DataTables::of($res)
    //         ->addColumn('action', function ($cert) {
    //             $details = json_encode($cert);
    //             if (in_array(auth()->user()->roles->pluck('name')[0], ["Super Admin", "District Super Admin"])) {
    //                 $html = '<a href="javascript:void(0)" class="btn btn-warning btn-icon btn-sm edit_record" data-details=\'' . $details . '\'  data-id="' . $cert->SCID . '"><i class="tf-icons bx bx-pencil"></i></a>';
    //                 $html .= '<button class="btn btn-danger btn-icon btn-sm delete_record" data-id="' . $cert->SCID . '" type="submit"><i class="bx bx-trash tf-icons"></i></button>';
    //             } else {
    //                 $html = "";
    //             }

    //             return $html;
    //         })
    //         ->addColumn('customType', function ($cert) {
    //             $cert->customType;
    //             if ($cert->Type == 2) {
    //                 return $cert->customType = "Customer";
    //             } else {
    //                 return $cert->customType = "Supplier";
    //             }
    //         })
    //         ->rawColumns(["customType", "action"])
    //         ->make(true);
    // }

    // public function save_customer()
    // {


    //     Customer::updateOrCreate(
    //         ["SCID" => request()->id],
    //         request()->except(["id", "_token"])
    //     );
    //     return ["status" => true, "message" => "Record saved successfully"];
    // }


    public function save_sale()
    {

        /*return response()->json([
            "data" => $request->all()
        ]);*/
        $patient_id = request()->patient_id;
        $admission_id = request()->patient_admission_id ?? 0;
        $customer = Patient::where(["id" => $patient_id])->first();
        //-------------------------------------------//
        $Invoice = $this->returnInvoiceNumber();
        $SupplierID = $patient_id;
        $Freight = 0;
        $PDate = date("Y-m-d", strtotime(request()->bill_date));
        $Description = request()->BillDiscription;
        $medicine_type = request()->medicine_type;
        $bill_description = request()->BillDiscription;
        $discount_percentage = request()->discount_percentage ?? 0;
        $Discount = request()->discount_amount ?? 0;
        $demage = 0;
        $ReceivedAmount = request()->ReceivedAmount;
        $userID = auth()->user()->id;
        $totalTax = 0;
        $TotalSale = request()->BillAmount;
        $SalemanID = 0;
        $Commesion = 0;
        if ($customer) {
            $CustomerName = $customer->name . " - " . $customer->mr_no;
        } else {
            $CustomerName = "Walking Customer";
            $patient_id = 0;
        }

        /*foreach(request()->ProductList as $row){
            $totalTax = ($totalTax) + ($row['taxAmount']);
        }*/
        $total = ($TotalSale);

        $SaleArray = array(
            'SCID'     => (session('store_id') == env('SEHAT_CARD_PHARMACY_STORE_ID')) ? 1 : 2, // 1 sehat card user,2 walking customer of retail store , table use sup_cus_details
            'store_id'     => session('store_id'), // sehat card user
            'wr_id'     => request()->ward_request_id ?? 0, // sehat card user
            'patient_id'   => $patient_id,
            'admission_id'   => $admission_id,
            'InvoiceNo' => $Invoice,
            'medicine_type' => $medicine_type,
            'Date'  => $PDate,
            'Description'   =>  $CustomerName,
            'TotalSale'     => $total,
            'received_amount'     => $ReceivedAmount,
            'Discount'     =>  $Discount,
            'discount_percentage'     =>  $discount_percentage,
            'sale_descriptions' => $bill_description,
            'CreatedBy'     => $userID,
            'CreatedAt'     => date('Y-m-d')
        );
        if ($SalemanID != '') {
            $SaleArray['SalemanCommesion'] = $Commesion;
            $SaleArray['SalemanID'] = $SalemanID;
        }

        $SaleArray['bill_details'] = json_encode(request()->ProductList);
        $sale = Sale::create($SaleArray);
        $last_id = $sale->SaleID;

        $is_free = session('is_free');
        foreach (request()->ProductList as $row) {
            $soldQuantity = $row['Quantity'];
            $result = GrnDetails::where(["ProductID" => $row['ProductID'], "ProductStatus" => 1])->get();
            $Detail_array = array(
                'store_id'   => session('store_id'),
                'SaleID'   => $last_id,
                'patient_id'   => $patient_id,
                'admission_id'   => $admission_id,
                'ProductID' => $row['ProductID'],
                'UnitePrice'  => $row['UnitePrice'],
                'taxPercentage'  => $row['taxPercentage'],
                'dose_type'  => $row['dose_type'],
            );
            $applyTax = $row['taxPercentage'] / 100;
            foreach ($result as $key => $value) {
                if ($soldQuantity <= $value->RemainingQuantity && $soldQuantity != 0) {
                    //echo "yes";
                    $total = ($soldQuantity) * ($row['UnitePrice']);
                    $taxAmount = ($total) * $applyTax;

                    if ($is_free) {  // if free then sale price will be same as purchase price
                        $Detail_array['UnitePrice'] = $value->UnitPrice;
                    }

                    $Detail_array['PurchasePrice'] = $value->UnitPrice;
                    $Detail_array['Quantity'] = $soldQuantity;
                    $Detail_array['GDID'] = $value->GDID;
                    $Detail_array['taxAmount'] = $taxAmount;
                    $remainingQuantity = $value->RemainingQuantity - $soldQuantity;
                    SaleDetails::create($Detail_array);
                    GrnDetails::where(["GDID" => $value->GDID])->update(['RemainingQuantity' => $remainingQuantity, 'SoldQuantity' => ($value->SoldQuantity + $soldQuantity)]);
                    if ($remainingQuantity == 0) {
                        GrnDetails::where(['GDID' => $value->GDID])->update(['ProductStatus' => 0]);
                    }
                    $soldQuantity = 0;
                } else {
                    if ($soldQuantity > $value->RemainingQuantity && $soldQuantity != 0) {
                        $total = ($value->RemainingQuantity) * ($row['UnitePrice']);
                        $taxAmount = ($total) * $applyTax;

                        if ($is_free) { // if free then sale price will be same as purchase price
                            $Detail_array['UnitePrice'] = $value->UnitPrice;
                        }

                        $Detail_array['PurchasePrice'] = $value->UnitPrice;
                        $Detail_array['Quantity'] = $value->RemainingQuantity;
                        $Detail_array['GDID'] = $value->GDID;
                        $Detail_array['taxAmount'] = $taxAmount;
                        $soldQuantity = ($soldQuantity) - ($value->RemainingQuantity);
                        //echo $soldQuantity;
                        SaleDetails::create($Detail_array);
                        GrnDetails::where(['GDID' => $value->GDID])->update(['RemainingQuantity' => 0, 'SoldQuantity' => ($value->SoldQuantity + $value->RemainingQuantity), 'ProductStatus' => 0]);
                    }
                }
            } //.... end of foreach
            //---- if stock is zero then also enter products in sale   -----//
            /*if($soldQuantity > 0){
                            $product = GrnDetails::where("UnitPrice",">",0)->where(["ProductID"=>$row['ProductID']])->orderBy("GDID","DESC")->first();

                            // dd($product,$row['ProductID']);
                            $total = ($soldQuantity) * ($row['UnitePrice']);
                            $taxAmount = ($total) * $applyTax;

                            $Detail_array['PurchasePrice']= $product->UnitPrice;
                            $Detail_array['Quantity']=$soldQuantity;
                            $Detail_array['GDID']=$product->GDID;
                            $Detail_array['taxAmount']=$taxAmount;
                            $remainingQuantity = ($product->RemainingQuantity) - ($soldQuantity);

                            SaleDetails::create($Detail_array);
                            GrnDetails::where(['GDID'=>$value->GDID])->update(['RemainingQuantity'=>$remainingQuantity]);
                        }*/
            //--------- end if stock is zero   ----------//
        } //------------ end of main foreach   -----------//

        if (request()->ward_request_id) {
            WardRequest::whereId(request()->ward_request_id)->update(["issued_by" => auth()->user()->id, "issued_at" => date("Y-m-d H:i:s"), "status" => 1]);
        }

        if (session('store_id') == 1) {
            (new PatientAdmissionController())->updateAdmissionDetails($admission_id);
        }

        $this->updateSaleTaxAmount($last_id);

        return ["status" => true, "message" => "Sale Completed Successfully", "id" => $last_id];
    }

    public function save_retail_sale()
    {
        $printMode = request()->retail_print_mode;
        if (in_array($printMode, ['thermal', 'a4'], true)) {
            session(['retail_print_mode' => $printMode]);
        }

        $TotalSale = 0;

        foreach (request()->ProductList as $row) {
            $TotalSale = ($TotalSale) + (($row['Quantity'] * ($row['UnitePrice'])));
        }

        $patient_id = request()->patient_id;
        $invoice_discount = request()->invoice_discount;
        $admission_id = request()->patient_admission_id ?? 0;
        $customer = Patient::where(["id" => $patient_id])->first();
        $ReceivedAmountFromCustomer = 0;
        if (request()->ReceivedAmountFromCustomer) {
            $ReceivedAmountFromCustomer = request()->ReceivedAmountFromCustomer;
        }
        //-------------------------------------------//
        $Invoice = $this->returnInvoiceNumber();
        $SupplierID = $patient_id;
        $Freight = 0;
        $PDate = date("Y-m-d", strtotime(request()->bill_date));
        $Description = request()->BillDiscription;
        $appointment_id = request()->appointment_id;
        $medicine_type = request()->medicine_type;
        $bill_description = request()->BillDiscription;
        $Discount = request()->discount_amount ?? 0;
        $discount_percentage = 0;
        $demage = 0;
        $ReceivedAmount = request()->ReceivedAmount;

        $userID = auth()->user()->id;
        $totalTax = 0;

        $SalemanID = 0;
        $Commesion = 0;
        if ($customer) {
            $CustomerName = $customer->name . " - " . $customer->mr_no;
        } else {
            $CustomerName = "Walking Customer";
            $patient_id = 0;
        }

        if ($appointment_id) {
            $app = Appointment::where('is_active', 1)
                ->with(['patient'])
                ->where("id", $appointment_id)
                ->first();
            $CustomerName = $app->patient->name ?? "";
            $patient_id = $app->patient_id ?? 0;
            $admission_id = 0;
        }

        /*foreach(request()->ProductList as $row){
            $totalTax = ($totalTax) + ($row['taxAmount']);
        }*/
        $total = ($TotalSale) + $totalTax;

        $bill_amount = ($total) - ($Discount);

        $SaleArray = array(
            'SCID'     => (session('store_id') == env('SEHAT_CARD_PHARMACY_STORE_ID')) ? 1 : 2, // 1 sehat card user,2 walking customer of retail store , table use sup_cus_details
            'store_id'     => session('store_id'), // sehat card user
            'wr_id'     => request()->ward_request_id ?? 0, // sehat card user
            'ReceivedAmountFromCustomer'   => $ReceivedAmountFromCustomer,
            'patient_id'   => $patient_id,
            'admission_id'   => 0,
            'InvoiceNo' => $Invoice,
            'appointment_id' => $appointment_id,
            'medicine_type' => $medicine_type,
            'Date'  => $PDate,
            'Description'   =>  $CustomerName,
            'TotalSale'     => $total,
            'received_amount'     => $ReceivedAmount,
            'Discount'     =>  $Discount,
            'invoice_discount'     =>  $invoice_discount,
            'discount_percentage'     =>  $discount_percentage,
            'sale_descriptions' => $bill_description,
            'CreatedBy'     => $userID,
            'CreatedAt'     => date('Y-m-d')
        );
        if ($SalemanID != '') {
            $SaleArray['SalemanCommesion'] = $Commesion;
            $SaleArray['SalemanID'] = $SalemanID;
        }

        $SaleArray['bill_details'] = json_encode(request()->ProductList);

        try {
            DB::beginTransaction();

            $sale = Sale::create($SaleArray);
            $last_id = $sale->SaleID;
            $SaleArray['SaleID'] = $last_id;
            $temp_sale = TempSale::create($SaleArray);
            $item_details = [];
            foreach (request()->ProductList as $row) {
                $TotalSale = ($TotalSale) + (($row['Quantity'] * ($row['UnitePrice'])));

                // Get product to validate discount
                $product = Product::find($row['ProductID']);
                $allowPercentage = 100;
                $requestedDiscount = $row['discount_percentage'] ?? 0;
                $finalDiscountPercentage = 0;

                if ($allowPercentage === 0) {
                    // If allow_percentage is zero, don't apply any discount
                    $finalDiscountPercentage = 0;
                } else if ($allowPercentage > 0) {
                    // If product has allow_percentage limit
                    $finalDiscountPercentage = ($requestedDiscount > $allowPercentage) ? $allowPercentage : $requestedDiscount;
                } else {
                    // No allow_percentage field set (null) - apply user's discount
                    $finalDiscountPercentage = $requestedDiscount;
                }

                $discountAmount = ($row['Quantity'] * $row['UnitePrice'] * $finalDiscountPercentage) / 100;
                $row['discount_percentage_amount'] = $discountAmount;
                $rowTax = $this->calculateProductRowTaxAmounts($row, (float) $row['Quantity']);

                $item_details[] = array(
                    'store_id'   => session('store_id'),
                    'SaleID'   => $last_id, //$sale->SaleID,
                    'temp_sale_id'   => $temp_sale->id,
                    'patient_id'   => $patient_id,
                    'admission_id'   => $admission_id,
                    'ProductID' => $row['ProductID'],
                    'UnitePrice'  => $row['UnitePrice'],
                    'taxPercentage'  => $row['taxPercentage'] ?? 0,
                    'taxAmount'  => $rowTax['tax_amount'],
                    'sale_tax'  => $rowTax['sale_tax'],
                    'income_tax'  => $rowTax['income_tax'],
                    'dose_type'  => $row['dose_type'],
                    'Quantity'  => $row['Quantity'],
                    'discount_percentage' => $finalDiscountPercentage,
                    'discount_percentage_amount' => $discountAmount,
                );
            }

            TempSaleDetails::insert($item_details);

            DB::commit(); // ✅ commit if all good
        } catch (\Exception $e) {
            DB::rollBack(); // ❌ rollback on error
            throw $e;       // optional: rethrow for logging
        }


        if ($ReceivedAmount >= 1) {
            SalePayment::create(["sale_id" => $last_id, "patient_id" => $patient_id, "admission_id" => $admission_id, "amount" => $ReceivedAmount, "created_by" => auth()->user()->id, "created_at" => date("Y-m-d H:i:s")]);
        }



        $is_free = session('is_free');
        foreach (request()->ProductList as $row) {
            $soldQuantity = $row['Quantity'];
            $result = GrnDetails::where(["ProductID" => $row['ProductID'], "ProductStatus" => 1])->get();

            // Get product to check allow_percentage
            $product = Product::find($row['ProductID']);
            $allowPercentage = 100;

            // Apply discount based on allow_percentage logic
            $requestedDiscount = $row['discount_percentage'] ?? 0;
            $finalDiscountPercentage = 0;

            if ($allowPercentage === 0) {
                // If allow_percentage is zero, don't apply any discount
                $finalDiscountPercentage = 0;
            } else if ($allowPercentage > 0) {
                // If product has allow_percentage limit
                if ($requestedDiscount <= $allowPercentage) {
                    // User's discount is within limit - apply user's discount
                    $finalDiscountPercentage = $requestedDiscount;
                } else {
                    // User's discount exceeds limit - apply product's allow_percentage
                    $finalDiscountPercentage = $allowPercentage;
                }
            } else {
                // No allow_percentage field set (null) - apply user's discount
                $finalDiscountPercentage = $requestedDiscount;
            }

            $discountAmount = ($row['Quantity'] * $row['UnitePrice'] * $finalDiscountPercentage) / 100;

            $Detail_array = array(
                'store_id'   => session('store_id'),
                'SaleID'   => $last_id,
                'patient_id'   => $patient_id,
                'admission_id'   => $admission_id,
                'ProductID' => $row['ProductID'],
                'UnitePrice'  => $row['UnitePrice'],
                'taxPercentage'  => $row['taxPercentage'] ?? 0,
                'dose_type'  => $row['dose_type'],
                'discount_percentage' => $finalDiscountPercentage,
                'discount_percentage_amount' => $discountAmount,
                'sale_tax' => (float) ($row['sale_tax'] ?? 0),
                'income_tax' => (float) ($row['income_tax'] ?? 0),
            );
            $originalSoldQty = (float) $row['Quantity'];
            foreach ($result as $key => $value) {
                if ($soldQuantity <= $value->RemainingQuantity && $soldQuantity != 0) {
                    //echo "yes";
                    $chunkQty = (float) $soldQuantity;
                    $Detail_array['PurchasePrice'] = $value->UnitPrice;
                    $Detail_array['Quantity'] = $soldQuantity;
                    $Detail_array['GDID'] = $value->GDID;
                    $Detail_array['taxAmount'] = $this->calculateDetailChunkTaxAmount(
                        $chunkQty,
                        (float) $row['UnitePrice'],
                        (float) $discountAmount,
                        $originalSoldQty,
                        $Detail_array
                    );
                    $remainingQuantity = $value->RemainingQuantity - $soldQuantity;
                    SaleDetails::create($Detail_array);
                    GrnDetails::where(["GDID" => $value->GDID])->update(['RemainingQuantity' => $remainingQuantity, 'SoldQuantity' => ($value->SoldQuantity + $soldQuantity)]);
                    if ($remainingQuantity == 0) {
                        GrnDetails::where(['GDID' => $value->GDID])->update(['ProductStatus' => 0]);
                    }
                    $soldQuantity = 0;
                } else {
                    if ($soldQuantity > $value->RemainingQuantity && $soldQuantity != 0) {
                        $chunkQty = (float) $value->RemainingQuantity;

                        if ($is_free) { // if free then sale price will be same as purchase price
                            $Detail_array['UnitePrice'] = $value->UnitPrice;
                        }

                        $Detail_array['PurchasePrice'] = $value->UnitPrice;
                        $Detail_array['Quantity'] = $value->RemainingQuantity;
                        $Detail_array['GDID'] = $value->GDID;
                        $Detail_array['taxAmount'] = $this->calculateDetailChunkTaxAmount(
                            $chunkQty,
                            (float) $row['UnitePrice'],
                            (float) $discountAmount,
                            $originalSoldQty,
                            $Detail_array
                        );
                        $soldQuantity = ($soldQuantity) - ($value->RemainingQuantity);
                        //echo $soldQuantity;
                        SaleDetails::create($Detail_array);
                        GrnDetails::where(['GDID' => $value->GDID])->update(['RemainingQuantity' => 0, 'SoldQuantity' => ($value->SoldQuantity + $value->RemainingQuantity), 'ProductStatus' => 0]);
                    }
                }
            } //.... end of foreach
            //---- if stock is zero then also enter products in sale   -----//
            /*if($soldQuantity > 0){
                $product = GrnDetails::where("UnitPrice",">",0)->where(["ProductID"=>$row['ProductID']])->orderBy("GDID","DESC")->first();

                // dd($product,$row['ProductID']);
                $total = ($soldQuantity) * ($row['UnitePrice']);
                $taxAmount = ($total) * $applyTax;

                $Detail_array['PurchasePrice']= $product->UnitPrice;
                $Detail_array['Quantity']=$soldQuantity;
                $Detail_array['GDID']=$product->GDID;
                $Detail_array['taxAmount']=$taxAmount;
                $remainingQuantity = ($product->RemainingQuantity) - ($soldQuantity);

                SaleDetails::create($Detail_array);
                GrnDetails::where(['GDID'=>$value->GDID])->update(['RemainingQuantity'=>$remainingQuantity]);
            }*/
            //--------- end if stock is zero   ----------//
        } //------------ end of main foreach   -----------//

        if (request()->ward_request_id) {
            WardRequest::whereId(request()->ward_request_id)->update(["issued_by" => auth()->user()->id, "issued_at" => date("Y-m-d H:i:s"), "status" => 1]);
        }

        if (session('store_id') == 1) {
            (new PatientAdmissionController())->updateAdmissionDetails($admission_id);
        }

        $this->updateSaleTaxAmount($last_id);
        sleep(1);

        return [
            "status" => true,
            "message" => "Sale Completed Successfully",
            "id" => $last_id,
            "invoice_no" => $this->returnInvoiceNumber(),
        ];
    }
    
    public function save_in_patient_retail_sale()
    {
        $printMode = request()->retail_print_mode;
        if (in_array($printMode, ['thermal', 'a4'], true)) {
            session(['retail_print_mode' => $printMode]);
        }

        $TotalSale = 0;

        foreach (request()->ProductList as $row) {
            $TotalSale = ($TotalSale) + (($row['Quantity'] * ($row['UnitePrice'])));
        }

        $patient_id = request()->patient_id;
        $invoice_discount = request()->invoice_discount;
        $admission_id = request()->patient_admission_id ?? 0;
        $customer = Patient::where(["id" => $patient_id])->first();
        $ReceivedAmountFromCustomer = 0;
        if (request()->ReceivedAmountFromCustomer) {
            $ReceivedAmountFromCustomer = request()->ReceivedAmountFromCustomer;
        }
        //-------------------------------------------//
        $Invoice = $this->returnInvoiceNumber();
        $SupplierID = $patient_id;
        $Freight = 0;
        $PDate = date("Y-m-d", strtotime(request()->bill_date));
        $Description = request()->BillDiscription;
        $sale_descriptions = request()->sale_descriptions;
        $appointment_id = request()->appointment_id;
        $medicine_type = request()->medicine_type;
        $bill_description = request()->BillDiscription;
        $Discount = request()->discount_amount ?? 0;
        $discount_percentage = 0;
        $demage = 0;
        $ReceivedAmount = request()->ReceivedAmount;

        $userID = auth()->user()->id;
        $totalTax = 0;

        $SalemanID = 0;
        $Commesion = 0;
        if ($customer) {
            $CustomerName = $customer->name . " - " . $customer->mr_no;
        } else {
            $CustomerName = "Walking Customer";
            $patient_id = 0;
        }

        if ($appointment_id) {
            $app = Appointment::where('is_active', 1)
                ->with(['patient'])
                ->where("id", $appointment_id)
                ->first();
            $CustomerName = $app->patient->name ?? "";
            $patient_id = $app->patient_id ?? 0;
            $admission_id = 0;
        }

        /*foreach(request()->ProductList as $row){
            $totalTax = ($totalTax) + ($row['taxAmount']);
        }*/
        $total = ($TotalSale) + $totalTax;

        $bill_amount = ($total) - ($Discount);

        $SaleArray = array(
            'SCID'     => (session('store_id') == env('SEHAT_CARD_PHARMACY_STORE_ID')) ? 1 : 2, // 1 sehat card user,2 walking customer of retail store , table use sup_cus_details
            'store_id'     => session('store_id'), // sehat card user
            'wr_id'     => request()->ward_request_id ?? 0, // sehat card user
            'ReceivedAmountFromCustomer'   => $ReceivedAmountFromCustomer,
            'patient_id'   => $patient_id,
            'admission_id'   => 1,
            'InvoiceNo' => $Invoice,
            'appointment_id' => $appointment_id,
            'medicine_type' => $medicine_type,
            'Date'  => $PDate,
            'Description'   =>  $CustomerName,
            'sale_descriptions'   =>  $sale_descriptions,
            'TotalSale'     => $total,
            'received_amount'     => $ReceivedAmount,
            'Discount'     =>  $Discount,
            'invoice_discount'     =>  $invoice_discount,
            'discount_percentage'     =>  $discount_percentage,
            'CreatedBy'     => $userID,
            'CreatedAt'     => date('Y-m-d')
        );
        if ($SalemanID != '') {
            $SaleArray['SalemanCommesion'] = $Commesion;
            $SaleArray['SalemanID'] = $SalemanID;
        }

        $SaleArray['bill_details'] = json_encode(request()->ProductList);

        try {
            DB::beginTransaction();

            $sale = Sale::create($SaleArray);
            $last_id = $sale->SaleID;
            $SaleArray['SaleID'] = $last_id;
            $temp_sale = TempSale::create($SaleArray);
            $item_details = [];
            foreach (request()->ProductList as $row) {
                $TotalSale = ($TotalSale) + (($row['Quantity'] * ($row['UnitePrice'])));

                // Get product to validate discount
                $product = Product::find($row['ProductID']);
                $allowPercentage = 100;
                $requestedDiscount = $row['discount_percentage'] ?? 0;
                $finalDiscountPercentage = 0;

                if ($allowPercentage === 0) {
                    // If allow_percentage is zero, don't apply any discount
                    $finalDiscountPercentage = 0;
                } else if ($allowPercentage > 0) {
                    // If product has allow_percentage limit
                    $finalDiscountPercentage = ($requestedDiscount > $allowPercentage) ? $allowPercentage : $requestedDiscount;
                } else {
                    // No allow_percentage field set (null) - apply user's discount
                    $finalDiscountPercentage = $requestedDiscount;
                }

                $discountAmount = ($row['Quantity'] * $row['UnitePrice'] * $finalDiscountPercentage) / 100;
                $row['discount_percentage_amount'] = $discountAmount;
                $rowTax = $this->calculateProductRowTaxAmounts($row, (float) $row['Quantity']);

                $item_details[] = array(
                    'store_id'   => session('store_id'),
                    'SaleID'   => $last_id, //$sale->SaleID,
                    'temp_sale_id'   => $temp_sale->id,
                    'patient_id'   => $patient_id,
                    'admission_id'   => $admission_id,
                    'ProductID' => $row['ProductID'],
                    'UnitePrice'  => $row['UnitePrice'],
                    'taxPercentage'  => $row['taxPercentage'] ?? 0,
                    'taxAmount'  => $rowTax['tax_amount'],
                    'sale_tax'  => $rowTax['sale_tax'],
                    'income_tax'  => $rowTax['income_tax'],
                    'dose_type'  => $row['dose_type'],
                    'Quantity'  => $row['Quantity'],
                    'discount_percentage' => $finalDiscountPercentage,
                    'discount_percentage_amount' => $discountAmount,
                );
            }

            TempSaleDetails::insert($item_details);

            DB::commit(); // ✅ commit if all good
        } catch (\Exception $e) {
            DB::rollBack(); // ❌ rollback on error
            throw $e;       // optional: rethrow for logging
        }


        if ($ReceivedAmount >= 1) {
            SalePayment::create(["sale_id" => $last_id, "patient_id" => $patient_id, "admission_id" => $admission_id, "amount" => $ReceivedAmount, "created_by" => auth()->user()->id, "created_at" => date("Y-m-d H:i:s")]);
        }



        $is_free = session('is_free');
        foreach (request()->ProductList as $row) {
            $soldQuantity = $row['Quantity'];
            $result = GrnDetails::where(["ProductID" => $row['ProductID'], "ProductStatus" => 1])->get();

            // Get product to check allow_percentage
            $product = Product::find($row['ProductID']);
            $allowPercentage = 100;

            // Apply discount based on allow_percentage logic
            $requestedDiscount = $row['discount_percentage'] ?? 0;
            $finalDiscountPercentage = 0;

            if ($allowPercentage === 0) {
                // If allow_percentage is zero, don't apply any discount
                $finalDiscountPercentage = 0;
            } else if ($allowPercentage > 0) {
                // If product has allow_percentage limit
                if ($requestedDiscount <= $allowPercentage) {
                    // User's discount is within limit - apply user's discount
                    $finalDiscountPercentage = $requestedDiscount;
                } else {
                    // User's discount exceeds limit - apply product's allow_percentage
                    $finalDiscountPercentage = $allowPercentage;
                }
            } else {
                // No allow_percentage field set (null) - apply user's discount
                $finalDiscountPercentage = $requestedDiscount;
            }

            $discountAmount = ($row['Quantity'] * $row['UnitePrice'] * $finalDiscountPercentage) / 100;

            $Detail_array = array(
                'store_id'   => session('store_id'),
                'SaleID'   => $last_id,
                'patient_id'   => $patient_id,
                'admission_id'   => $admission_id,
                'ProductID' => $row['ProductID'],
                'UnitePrice'  => $row['UnitePrice'],
                'taxPercentage'  => $row['taxPercentage'] ?? 0,
                'dose_type'  => $row['dose_type'],
                'discount_percentage' => $finalDiscountPercentage,
                'discount_percentage_amount' => $discountAmount,
                'sale_tax' => (float) ($row['sale_tax'] ?? 0),
                'income_tax' => (float) ($row['income_tax'] ?? 0),
            );
            $originalSoldQty = (float) $row['Quantity'];
            foreach ($result as $key => $value) {
                if ($soldQuantity <= $value->RemainingQuantity && $soldQuantity != 0) {
                    //echo "yes";
                    $chunkQty = (float) $soldQuantity;
                    $Detail_array['PurchasePrice'] = $value->UnitPrice;
                    $Detail_array['Quantity'] = $soldQuantity;
                    $Detail_array['GDID'] = $value->GDID;
                    $Detail_array['taxAmount'] = $this->calculateDetailChunkTaxAmount(
                        $chunkQty,
                        (float) $row['UnitePrice'],
                        (float) $discountAmount,
                        $originalSoldQty,
                        $Detail_array
                    );
                    $remainingQuantity = $value->RemainingQuantity - $soldQuantity;
                    SaleDetails::create($Detail_array);
                    GrnDetails::where(["GDID" => $value->GDID])->update(['RemainingQuantity' => $remainingQuantity, 'SoldQuantity' => ($value->SoldQuantity + $soldQuantity)]);
                    if ($remainingQuantity == 0) {
                        GrnDetails::where(['GDID' => $value->GDID])->update(['ProductStatus' => 0]);
                    }
                    $soldQuantity = 0;
                } else {
                    if ($soldQuantity > $value->RemainingQuantity && $soldQuantity != 0) {
                        $chunkQty = (float) $value->RemainingQuantity;

                        if ($is_free) { // if free then sale price will be same as purchase price
                            $Detail_array['UnitePrice'] = $value->UnitPrice;
                        }

                        $Detail_array['PurchasePrice'] = $value->UnitPrice;
                        $Detail_array['Quantity'] = $value->RemainingQuantity;
                        $Detail_array['GDID'] = $value->GDID;
                        $Detail_array['taxAmount'] = $this->calculateDetailChunkTaxAmount(
                            $chunkQty,
                            (float) $row['UnitePrice'],
                            (float) $discountAmount,
                            $originalSoldQty,
                            $Detail_array
                        );
                        $soldQuantity = ($soldQuantity) - ($value->RemainingQuantity);
                        //echo $soldQuantity;
                        SaleDetails::create($Detail_array);
                        GrnDetails::where(['GDID' => $value->GDID])->update(['RemainingQuantity' => 0, 'SoldQuantity' => ($value->SoldQuantity + $value->RemainingQuantity), 'ProductStatus' => 0]);
                    }
                }
            } //.... end of foreach
            //---- if stock is zero then also enter products in sale   -----//
            /*if($soldQuantity > 0){
                $product = GrnDetails::where("UnitPrice",">",0)->where(["ProductID"=>$row['ProductID']])->orderBy("GDID","DESC")->first();

                // dd($product,$row['ProductID']);
                $total = ($soldQuantity) * ($row['UnitePrice']);
                $taxAmount = ($total) * $applyTax;

                $Detail_array['PurchasePrice']= $product->UnitPrice;
                $Detail_array['Quantity']=$soldQuantity;
                $Detail_array['GDID']=$product->GDID;
                $Detail_array['taxAmount']=$taxAmount;
                $remainingQuantity = ($product->RemainingQuantity) - ($soldQuantity);

                SaleDetails::create($Detail_array);
                GrnDetails::where(['GDID'=>$value->GDID])->update(['RemainingQuantity'=>$remainingQuantity]);
            }*/
            //--------- end if stock is zero   ----------//
        } //------------ end of main foreach   -----------//

        if (request()->ward_request_id) {
            WardRequest::whereId(request()->ward_request_id)->update(["issued_by" => auth()->user()->id, "issued_at" => date("Y-m-d H:i:s"), "status" => 1]);
        }

        if (session('store_id') == 1) {
            (new PatientAdmissionController())->updateAdmissionDetails($admission_id);
        }
        sleep(1);

         //====== finance entry will goes here   -------//
        $voucher = generateVoucherNumber("sale",auth()->user()->id);

        $voucher_data = [
            "voucher_number" =>$voucher,
            "voucher_type"   => "sale",
            'user_id' => auth()->user()->id,
            'created_by' => auth()->user()->id,
            "voucher_date"   => date("Y-m-d"),
            "total_amount"   => $bill_amount,
            "remarks"   => "Customer Sale by ".auth()->user()->name ?? '',
            "created_by"   => auth()->user()->id,
            "created_at"   => date("Y-m-d H:i:s"),
        ];
        $voucher = FinanceVoucher::create($voucher_data);
        $voucher_id = $voucher->id;

                $cogs_amount = $this->cogs_purchase($sale->SaleID);
                $customer_finance_head_id = $this->get_customer_finance_id($patient_id);

                $amount = $bill_amount;
                $remarks = "Customer Sale Invoice#".$Invoice;
                // Customer debuit   pharmacy income credit
                make_entry($voucher_id,$customer_finance_head_id,$amount,0,"sale",$sale->SaleID,auth()->user()->id,$remarks);
                make_entry($voucher_id,financeHeadId('pharmacy_income'),0,$amount,"sale",$sale->SaleID,auth()->user()->id,$remarks);
                
                $remarks = "pharmacy_sale_cogs";
                //cogs debuit   pharmacy_purchase credit
                make_entry($voucher_id,financeHeadId('cogs'),$cogs_amount,0,"pharmacy_sale_cogs",$value->id,auth()->user()->id,$remarks);
                make_entry($voucher_id,financeHeadId('pharmacy_purchase'),0,$cogs_amount,"pharmacy_sale_cogs",$value->id,auth()->user()->id,$remarks);

        $this->updateSaleTaxAmount($last_id);

        return ["status" => true, "message" => "Sale Completed Successfully", "id" => $last_id];
    }

     public function cogs_purchase($sale_id)
    {
        $cogs = DB::table('sale_details')
            ->selectRaw('SaleID, SUM((Quantity - ReturnQuantity) * PurchasePrice) as cogs')
            ->where('SaleID', $sale_id)
            ->groupBy('SaleID')
            ->first();
        return $cogs->cogs ?? 0;
    }

     public function get_customer_finance_id($patient_id)
    {
        $data = DB::table('patients')
            ->where('id', $patient_id)
            ->first();
        return $data->finance_head_id ?? 0;
    }

    public function save_pharmacy_transfer()
    {
        $TotalSale = 0;

        foreach (request()->ProductList as $row) {
            $TotalSale = ($TotalSale) + (($row['Quantity'] * ($row['UnitePrice'])));
        }

        $patient_id = request()->patient_id;
        $invoice_discount = request()->invoice_discount;
        $admission_id = request()->patient_admission_id ?? 0;
        $customer = Patient::where(["id" => $patient_id])->first();
        $ReceivedAmountFromCustomer = 0;
        if (request()->ReceivedAmountFromCustomer) {
            $ReceivedAmountFromCustomer = request()->ReceivedAmountFromCustomer;
        }
        //-------------------------------------------//
        $Invoice = $this->returnTransferInvoiceNumber();


        $SupplierID = $patient_id;
        $Freight = 0;
        $PDate = date("Y-m-d", strtotime(request()->bill_date));
        $Description = request()->BillDiscription;
        $appointment_id = request()->appointment_id;
        $medicine_type = request()->medicine_type;
        $bill_description = request()->previous_balance ?? '';
        $discount_percentage = request()->discount_percentage ?? 0;
        $Discount = request()->discount_amount ?? 0;
        $demage = 0;
        $ReceivedAmount = request()->ReceivedAmount;

        $userID = auth()->user()->id;
        $totalTax = 0;

        $SalemanID = 0;
        $Commesion = 0;
        if ($customer) {
            $CustomerName = $customer->name . " - " . $customer->mr_no;
        } else {
            $CustomerName = "Walking Customer";
            $patient_id = 0;
        }

        if ($appointment_id) {
            $app = Appointment::where('is_active', 1)
                ->with(['patient'])
                ->where("id", $appointment_id)
                ->first();
            $CustomerName = $app->patient->name ?? "";
            $patient_id = $app->patient_id ?? 0;
            $admission_id = 0;
        }

        /*foreach(request()->ProductList as $row){
            $totalTax = ($totalTax) + ($row['taxAmount']);
        }*/
        $total = ($TotalSale) + $totalTax;

        $SaleArray = array(
            'SCID'     => 0, // 1 sehat card user,2 walking customer of retail store , table use sup_cus_details
            'store_id'     => 1, // sehat card user
            'wr_id'     =>  0, // sehat card user
            'transfer_type'     =>  request()->SID, // sehat card user
            'ReceivedAmountFromCustomer'   => $ReceivedAmountFromCustomer,
            'patient_id'   => 0,
            'admission_id'   => 0,
            'SaleID'   => 0,
            'InvoiceNo' => $Invoice,
            'appointment_id' => 0,
            'medicine_type' => $medicine_type,
            'Date'  => $PDate,
            'Description'   =>  $CustomerName,
            'TotalSale'     => $total,
            'received_amount'     => $ReceivedAmount,
            'Discount'     =>  $Discount,
            'invoice_discount'     =>  $invoice_discount,
            'discount_percentage'     =>  $discount_percentage,
            'sale_descriptions' => $bill_description,
            'CreatedBy'     => $userID,
            'CreatedAt'     => date('Y-m-d')
        );




        $SaleArray['bill_details'] = json_encode(request()->ProductList);

        try {
            DB::beginTransaction();
            $temp_sale = PharmacyTransfer::create($SaleArray);
            $item_details = [];
            foreach (request()->ProductList as $row) {
                $TotalSale = ($TotalSale) + (($row['Quantity'] * ($row['UnitePrice'])));
                $item_details[] = array(
                    'store_id'   => session('store_id'),
                    'SaleID'   => 0, //$sale->SaleID,
                    'temp_sale_id'   => $temp_sale->id,
                    'patient_id'   => $patient_id,
                    'admission_id'   => $admission_id,
                    'ProductID' => $row['ProductID'],
                    'UnitePrice'  => $row['UnitePrice'],
                    'taxPercentage'  => $row['taxPercentage'],
                    'dose_type'  => $row['dose_type'],
                    'Quantity'  => $row['Quantity'],
                    'PurchasePrice'  => $row['UnitePrice'],
                );
            }

            PharmacyTransferDetails::insert($item_details);

            DB::commit(); // ✅ commit if all good
        } catch (\Exception $e) {
            DB::rollBack(); // ❌ rollback on error
            throw $e;       // optional: rethrow for logging
        }



        $is_free = session('is_free');
        foreach (request()->ProductList as $row) {
            $soldQuantity = $row['Quantity'];
            $result = GrnDetails::where(["ProductID" => $row['ProductID'], "ProductStatus" => 1])->get();

            $applyTax = $row['taxPercentage'] / 100;
            foreach ($result as $key => $value) {
                if ($soldQuantity <= $value->RemainingQuantity && $soldQuantity != 0) {
                    $remainingQuantity = $value->RemainingQuantity - $soldQuantity;
                    GrnDetails::where(["GDID" => $value->GDID])->update(['RemainingQuantity' => $remainingQuantity, 'SoldQuantity' => ($value->SoldQuantity + $soldQuantity)]);
                    if ($remainingQuantity == 0) {
                        GrnDetails::where(['GDID' => $value->GDID])->update(['ProductStatus' => 0]);
                    }
                    $soldQuantity = 0;
                } else {
                    if ($soldQuantity > $value->RemainingQuantity && $soldQuantity != 0) {
                        GrnDetails::where(['GDID' => $value->GDID])->update(['RemainingQuantity' => 0, 'SoldQuantity' => ($value->SoldQuantity + $value->RemainingQuantity), 'ProductStatus' => 0]);
                    }
                }
            } //.... end of foreach
            //--------- end if stock is zero   ----------//
        } //------------ end of main foreach   -----------//


        sleep(1);

        return ["status" => true, "message" => "Sale Completed Successfully", "id" => $temp_sale->id];
    }


    public function temp_save_sale()
    {
        TempSale::truncate();
        TempSaleDetails::truncate();
        /*return response()->json([
            "data" => $request->all()
        ]);*/
        $customer = Customer::where(["SCID" => request()->SID])->first();
        //-------------------------------------------//
        $Invoice = $this->returnInvoiceNumber();
        $SupplierID = request()->SID;
        $Freight = 0;
        $PDate = date("Y-m-d", strtotime(request()->bill_date));
        $Description = request()->BillDiscription;
        $bill_description = request()->BillDiscription;
        $Discount = 0;
        $demage = 0;
        $ReceivedAmount = request()->ReceivedAmount;
        $userID = auth()->user()->id;
        $totalTax = 0;
        $TotalSale = request()->BillAmount;
        $SalemanID = 0;
        $Commesion = 0;
        $CustomerName = $customer->Name;
        /*foreach(request()->ProductList as $row){
            $totalTax = ($totalTax) + ($row['taxAmount']);
        }*/
        $total = ($TotalSale) + $totalTax;

        $invoice_discount = request()->invoice_discount ?? 0;

        $SaleArray = array(
            'SCID'     => $SupplierID,
            'InvoiceNo' => $Invoice,
            'Date'  => $PDate,
            'Description'   =>  $CustomerName,
            'TotalSale'     => $total,
            'received_amount'     => $ReceivedAmount,
            'Discount'     =>  0,
            'invoice_discount' => $invoice_discount,
            'sale_descriptions' => $bill_description,
            'CreatedBy'     => $userID,
            'CreatedAt'     => date('Y-m-d')
        );
        if ($SalemanID != '') {
            $SaleArray['SalemanCommesion'] = $Commesion;
            $SaleArray['SalemanID'] = $SalemanID;
        }

        $SaleArray['bill_details'] = json_encode(request()->ProductList);
        $sale = TempSale::create($SaleArray);
        $last_id = $sale->SaleID;





        foreach (request()->ProductList as $row) {
            $soldQuantity = $row['Quantity'];

            // Validate product discount (same as retail sale)
            $product = Product::find($row['ProductID']);
            $discount_percentage = isset($row['discount_percentage']) ? $row['discount_percentage'] : 0;
            $discount_percentage_amount = isset($row['discount_percentage_amount']) ? $row['discount_percentage_amount'] : 0;

            $returnQuantity = isset($row['ReturnQuantity']) ? $row['ReturnQuantity'] : 0;

            // Calculate actual quantity after returns
            $actualQuantity = $soldQuantity - $returnQuantity;

            // Apply same rules as retail sale:
            // 1. If allow_percentage is 0, don't apply any discount
            // 2. If requested discount > allowed, apply the allowed percentage
            // 3. If requested discount <= allowed, apply the requested percentage
            // 4. If quantity after return is zero, then discount_percentage_amount = 0
            if ($actualQuantity <= 0) {
                // Rule 4: If quantity after return is zero, then discount_percentage_amount = 0
                $discount_percentage_amount = 0;
            } else if ($product) {
                $allowPercentage = 100;

                if ($allowPercentage === 0) {
                    // Rule 3: If allow_percentage is zero, don't apply percentage
                    $discount_percentage = 0;
                    $discount_percentage_amount = 0;
                } else if ($discount_percentage > $allowPercentage) {
                    // Rule 1: If requested > allowed, apply the allowed percentage
                    $discount_percentage = $allowPercentage;
                    $unitPrice = $row['UnitePrice'];
                    // Bill Amount = (quantity - returnquantity) * UnitePrice
                    $itemTotal = $unitPrice * $actualQuantity;
                    $discount_percentage_amount = ($itemTotal * $allowPercentage) / 100;
                } else {
                    // Rule 2: If requested <= allowed, apply the requested percentage
                    $unitPrice = $row['UnitePrice'];
                    $itemTotal = $unitPrice * $actualQuantity;
                    $discount_percentage_amount = ($itemTotal * $discount_percentage) / 100;
                }
            }
            $Detail_array = array(
                'SaleID'   => $last_id,
                'ProductID' => $row['ProductID'],
                'UnitePrice'  => $row['UnitePrice'],
                'taxPercentage'  => $row['taxPercentage'],
                'taxAmount'  => $row['taxAmount'],
                'discount_percentage' => $discount_percentage,
                'discount_percentage_amount' => $discount_percentage_amount,
                'ReturnQuantity' => $returnQuantity,
            );
            $Detail_array['Quantity'] = $soldQuantity;
            TempSaleDetails::create($Detail_array);
        }

        return ["status" => true, "message" => "Temp Sale Completed Successfully", "id" => $last_id];
    }

    public function print_temp_sale($SaleID = '', $customer_id = '', $date = '', $received_amount = '')
    {

        $data['record'] = TempSale::where(['SaleID' => $SaleID])->get();
        $data['customer'] = Customer::where(["SCID" => $customer_id])->get();
        $data['receiveable'] = $received_amount;
        $data['PreviousBalance'] = (new CustomerPayments())->customer_previous_balance($customer_id, $date);

        $data['data'] = TempSaleDetails::with('product')->get();
        $data['show_customer_contact'] = "yes";
        $data['title'] = 'Sale Details Report';
        $return = "No";
        /*echo "<pre>";
        print_r($data);
        exit();*/
        foreach ($data['data'] as $rec) {
            $rec->AvaliableQuantity = ($rec->Quantity) - ($rec->ReturnQuantity);
            $rec->totalAmount = ($rec->AvaliableQuantity) * ($rec->UnitePrice);
            if ($rec->ReturnQuantity > 0) {
                $return = "Yes";
            }
        }
        if ($return == "Yes") {
            $data['return'] = "Yes";
        } else {
            $data['return'] = "No";
        }

        TempSale::truncate();
        TempSaleDetails::truncate();
        return view('reports/print_new_invoice', $data);
        // exit();
    } //--- End of function print_purchase_detail() ---//

    function returnInvoiceNumber()
    {
        $result = Sale::orderBy("SaleID", "DESC")->first();
        if ($result) {
            return ($result->SaleID) + 1;
        } else {
            return 1;
        }
    }

    function returnTransferInvoiceNumber()
    {
        $result = PharmacyTransfer::orderBy("id", "DESC")->first();
        if ($result) {
            return ($result->id) + 1;
        } else {
            return 1;
        }
    }

    public function getTransectionNo()
    {
        $rec = ReceiveablesDetail::orderBy("RDID", "DESC")->first();

        if (!empty($rec)) {
            return (($rec->RDID) + 1);
        } else {
            return (1);
        }
    }

    public function print_purchase_detail($SaleID = '', $date = '')
    {

        if ($date == '') {
            $date = date("Y-m-d");
        }
        $pTable = "sale";
        $columns = array('*');
        $where = array();
        $joins = '';

        $data['record'] = Sale::where(['SaleID' => $SaleID])->get();
        $customer_id = $data['record'][0]->SCID;
        $billDate = date("d-m-Y", strtotime($data['record'][0]->Date));

        //$data['PreviousBalance']=(new CustomerPayments())->customer_previous_balance($customer_id,$date);

        $data['data'] = SaleDetails::with('product')->where(['SaleID' => $SaleID])->get();
        $data['title'] = 'Sale Details Report';
        $return = "No";
        $totalAmount = 0;
        $data['prev_balance'] = (new CustomerPayments())->customer_previous_balance($customer_id, '');

        foreach ($data['data'] as $rec) {
            $rec->AvaliableQuantity = ($rec->Quantity) - ($rec->ReturnQuantity);
            $rec->totalAmount = ($rec->AvaliableQuantity) * ($rec->UnitePrice);
            $totalAmount = ($totalAmount) + ($rec->totalAmount);
            if ($rec->ReturnQuantity > 0) {
                $return = "Yes";
            }
        }

        $result = [];

        // Iterate through the array remove duplicate items . sum the quantity ,totalamount, taxamount and remove duplication for bill print only...//
        foreach ($data['data'] as $item) {
            $productId = $item->ProductID;

            // If ProductID already exists in the result, sum up the Quantity and UnitePrice
            if (isset($result[$productId])) {
                $result[$productId]->Quantity += $item->Quantity;
                $result[$productId]->totalAmount += $item->totalAmount;
                $result[$productId]->taxAmount += $item->taxAmount;
            } else {
                // Add new ProductID to result
                $result[$productId] = clone $item;
            }
        }
        $result = array_values($result);
        $data['data'] = $result;


        if ($return == "Yes") {
            $data['return'] = "Yes";
        } else {
            $data['return'] = "No";
        }


        $data['TotalAmount'] = $totalAmount;
        $data['show_customer_contact'] = "true";

        $data['customer'] = Customer::where("SCID", $customer_id)->get();

        return view('reports/customer_purchase_report_new', $data);
        //$this->load->view('reports/customer_purchase_report',$data);


        //
        // exit();
    } //--- End of function print_purchase_detail() ---//


    public function return_item()
    {
        $sale_details = SaleDetails::where(["SDID" => request()->SDID])->first();
        $sale = Sale::where(["SaleID" => $sale_details->SaleID])->first();
        $admission_id = $sale->admission_id;


        $retrun_qty = request()->ReturnQuantity;
        $total_return_price = ($sale_details->UnitePrice) * ($retrun_qty);
        $total_return_qty = ($sale_details->ReturnQuantity) + ($retrun_qty);

        $total_sale_amount = ($sale->TotalSale) - ($total_return_price);
        $received_amount = ($total_sale_amount) - ($sale->Discount);


        //------- sale related operations  ------------//
        Sale::where(["SaleID" => $sale_details->SaleID])->update(["TotalSale" => $total_sale_amount, "received_amount" => $received_amount]);
        SaleDetails::where(["SDID" => request()->SDID])->update(['ReturnQuantity' => $total_return_qty, 'return_by' => auth()->user()->id]);

        //---------- end of sale related operations   ---------//

        //------------- now grn detals .........//
        $result = GrnDetails::where(["GDID" => $sale_details->GDID])->first();
        $remainingQuanity = ($result->RemainingQuantity) + ($retrun_qty);
        $soldQuantity = ($result->SoldQuantity) - ($retrun_qty);
        $TotalReturn = ($result->TotalReturn) + ($retrun_qty);
        $grn_detailDate = array(
            "SoldQuantity" => $soldQuantity,
            "TotalReturn"  => $TotalReturn,
            "RemainingQuantity" => $remainingQuanity,
            "ProductStatus"     => 1
        );
        GrnDetails::where(['GDID' => $sale_details->GDID])->update($grn_detailDate);
        (new PatientAdmissionController())->updateAdmissionDetails($admission_id);
        return response()->json(["status" => true, "message" => "done"]);
        //$this->Zk_Common_Model->update_records('grn_details',$grn_detailDate,array('GDID'=>$GDID));
    }

    public function return_pharmacy_item()
    {
        $sale_details = SaleDetails::where(["SDID" => request()->SDID])->first();
        $sale = Sale::where(["SaleID" => $sale_details->SaleID])->first();

        $admission_id = $sale->admission_id;
        $sale_id = $sale->SaleID;
        $is_admitted_patient = "no";

        //---- check if patient is admitt or not  ---//
        if ($admission_id) {
            $check_patient_admission = InPatientAdmission::whereId($admission_id)->where("admission_status", "Admit")->first();
            if ($check_patient_admission) {
                $is_admitted_patient = "yes";
            } else {
                $is_admitted_patient = "no";
            }
        }
        //----- end of check -----//

        $retrun_qty = request()->ReturnQuantity;
        $total_return_qty = ($sale_details->ReturnQuantity) + ($retrun_qty);

        // Update the sale_details return quantity and recalculate discount amounts
        $active_quantity_after_return = max(0, $sale_details->Quantity - $total_return_qty);

        // Calculate new proportional discount amount for the returned item
        $new_discount_percentage_amount = 0;
        if ($active_quantity_after_return > 0 && $sale_details->Quantity > 0) {
            $proportion = $active_quantity_after_return / $sale_details->Quantity;
            if (isset($sale_details->discount_percentage_amount) && $sale_details->discount_percentage_amount > 0) {
                $new_discount_percentage_amount = $sale_details->discount_percentage_amount * $proportion;
            } else if (isset($sale_details->discount_percentage) && $sale_details->discount_percentage > 0) {
                $line_amount_before_discount = $active_quantity_after_return * $sale_details->UnitePrice;
                $new_discount_percentage_amount = ($line_amount_before_discount * $sale_details->discount_percentage) / 100;
            }
        }

        // Update the specific returned item first
        SaleDetails::where(["SDID" => request()->SDID])->update([
            'ReturnQuantity' => $total_return_qty,
            'discount_percentage_amount' => $new_discount_percentage_amount,
            'return_by' => auth()->user()->id
        ]);
        TempSaleDetails::where(["SaleID" => $sale_details->SaleID, "ProductID" => $sale_details->ProductID])->update([
            'ReturnQuantity' => $total_return_qty,
            'discount_percentage_amount' => $new_discount_percentage_amount,
            'return_by' => auth()->user()->id
        ]);

        // Now update discount_percentage_amount for ALL items in the sale based on their current active quantities
        $all_sale_details = SaleDetails::where(["SaleID" => $sale_details->SaleID])->get();

        foreach ($all_sale_details as $detail) {
            $active_quantity = max(0, $detail->Quantity - $detail->ReturnQuantity);
            $updated_discount_amount = 0;

            if ($active_quantity > 0 && $detail->Quantity > 0) {
                $proportion = $active_quantity / $detail->Quantity;

                // Get the original discount amount (before any returns)
                $original_discount_amount = 0;
                if (isset($detail->discount_percentage) && $detail->discount_percentage > 0) {
                    $original_line_amount = $detail->Quantity * $detail->UnitePrice;
                    $original_discount_amount = ($original_line_amount * $detail->discount_percentage) / 100;
                } else if (isset($detail->discount_percentage_amount)) {
                    // If we already have a stored amount, use it as reference for proportion
                    $original_discount_amount = $detail->discount_percentage_amount / ($detail->Quantity > 0 ? ($detail->Quantity - $detail->ReturnQuantity) / $detail->Quantity : 1);
                }

                $updated_discount_amount = $original_discount_amount * $proportion;
            }

            // Update the discount amount in database
            SaleDetails::where(["SDID" => $detail->SDID])->update([
                'discount_percentage_amount' => $updated_discount_amount
            ]);
            TempSaleDetails::where(["SaleID" => $detail->SaleID, "ProductID" => $detail->ProductID])->update([
                'discount_percentage_amount' => $updated_discount_amount
            ]);
        }

        // Fetch fresh data after updating ALL discount amounts
        $all_sale_details = SaleDetails::where(["SaleID" => $sale_details->SaleID])->get();

        $recalculated_total_before_discount = 0; // Sum of (Quantity - ReturnQuantity) * UnitePrice for all items
        $recalculated_total_discount_amount = 0; // Sum of all ITEM discount amounts (no invoice_discount here)

        foreach ($all_sale_details as $detail) {
            $active_quantity = max(0, $detail->Quantity - $detail->ReturnQuantity); // Ensure non-negative
            $line_amount_before_discount = $active_quantity * $detail->UnitePrice;
            $recalculated_total_before_discount += $line_amount_before_discount;

            // Calculate ITEM discount amount for this line - only if active_quantity > 0
            // NOTE: invoice_discount is NOT applied to individual items, only to final bill
            if ($active_quantity > 0) {
               // if (isset($detail->discount_percentage_amount) && $detail->discount_percentage_amount > 0) {
                    // Proportional item discount for active quantity
                   // $proportion = $active_quantity / max(1, $detail->Quantity); // Avoid division by zero
                  //  $line_discount_amount = $detail->discount_percentage_amount * $proportion;
                  //  $recalculated_total_discount_amount += max(0, $line_discount_amount); // Ensure non-negative
               // } else if (isset($detail->discount_percentage) && $detail->discount_percentage > 0) {
                    // Calculate item discount percentage
                    $line_discount_amount = ($line_amount_before_discount * $detail->discount_percentage) / 100;
                    $recalculated_total_discount_amount += max(0, $line_discount_amount); // Ensure non-negative
                //}
            }
            // If active_quantity is 0, item discount is automatically 0 (6% of 0 = 0)
        }

        // Calculate final amounts according to specifications
        // TotalSale = amount before discount of sum of per item (no invoice_discount applied here)
        $new_total_sale = max(0, $recalculated_total_before_discount); // Amount before discount, ensure non-negative

        // received_amount = sum of per item sale amount - total discount per item - invoice_discount (applied to final bill only)
        $amount_after_item_discounts = max(0, $recalculated_total_before_discount - $recalculated_total_discount_amount);
        $new_received_amount = max(0, $amount_after_item_discounts - ($sale->invoice_discount ?? 0)); // Invoice discount applied to final bill only


        //---- check if patient is admitt then correct the bill otherwise make entry in pharmacy_return_items table for user closing balance.---//
        //--- close balance will adjust from pharmacy return table only amount will be minus from total sale amount of user during closing  ---//

        if (($is_admitted_patient == "yes" && $sale->received_amount == 0)) {
            if ($sale->admission_id == 0) {  // if walking customer sale then also make changes in salepayment table
                SalePayment::where(["sale_id" => $sale_details->SaleID])->update(["amount" => $new_received_amount]);
            }
            Sale::where(["SaleID" => $sale_details->SaleID])->update([
                "is_return_made" => 1,
                "ModifiedAt" => date("Y-m-d H:i:s"),
                "ModifiedBy" => auth()->user()->id,
                "TotalSale" => $new_total_sale,
                "received_amount" => $new_received_amount,
                "Discount" => $recalculated_total_discount_amount
            ]);
            TempSale::where(["SaleID" => $sale_details->SaleID])->update([
                "is_return_made" => 1,
                "ModifiedAt" => date("Y-m-d H:i:s"),
                "ModifiedBy" => auth()->user()->id,
                "TotalSale" => $new_total_sale,
                "received_amount" => $new_received_amount,
                "Discount" => $recalculated_total_discount_amount
            ]);
        } else {
            PharmacyRetrun::create([
                "sale_id" => $sale_details->SaleID,
                "sale_detail_id" => request()->SDID,
                "product_id" => $sale_details->ProductID,
                "quantity" => request()->ReturnQuantity,
                "amount" => request()->return_amount,
                "created_by" => auth()->user()->id,
                "created_at" => date("Y-m-d H:i:s"),
            ]);

            Sale::where(["SaleID" => $sale_details->SaleID])->update([
                "is_return_made" => 1,
                "ModifiedAt" => date("Y-m-d H:i:s"),
                "ModifiedBy" => auth()->user()->id,
                "TotalSale" => $new_total_sale,
                "received_amount" => $new_received_amount,
                "Discount" => $recalculated_total_discount_amount
            ]);
            TempSale::where(["SaleID" => $sale_details->SaleID])->update([
                "is_return_made" => 1,
                "ModifiedAt" => date("Y-m-d H:i:s"),
                "ModifiedBy" => auth()->user()->id,
                "TotalSale" => $new_total_sale,
                "received_amount" => $new_received_amount,
                "Discount" => $recalculated_total_discount_amount
            ]);

            if ($admission_id != 0) {
                SalePayment::where(["admission_id" => $admission_id])->update(["amount" => $new_received_amount]);
            } else {
                SalePayment::where(["sale_id" => $sale_details->SaleID])->update(["amount" => $new_received_amount]);
            }
        }
        //-------- end of check  ------//

        //------------- now grn detals .........//
        $result = GrnDetails::where(["GDID" => $sale_details->GDID])->first();
        $remainingQuanity = ($result->RemainingQuantity) + ($retrun_qty);
        $soldQuantity = ($result->SoldQuantity) - ($retrun_qty);
        $TotalReturn = ($result->TotalReturn) + ($retrun_qty);
        $grn_detailDate = array(
            "SoldQuantity" => $soldQuantity,
            "TotalReturn"  => $TotalReturn,
            "RemainingQuantity" => $remainingQuanity,
            "ProductStatus"     => 1
        );
        GrnDetails::where(['GDID' => $sale_details->GDID])->update($grn_detailDate);

        $this->updateSaleTaxAmount($sale_id);

        return response()->json(["status" => true, "message" => "done"]);
        //$this->Zk_Common_Model->update_records('grn_details',$grn_detailDate,array('GDID'=>$GDID));
    }
    public function return_customer_parmacy_item()
    {
        
        $sale_details = SaleDetails::where(["SDID" => request()->SDID])->first();
        $sale = Sale::where(["SaleID" => $sale_details->SaleID])->first();
        $product = Product::where(["ProductID" => $sale_details->ProductID])->first();
        $customer_finance_head_id = $this->get_customer_finance_id($sale->patient_id);
        $return_amount = request()->return_amount;
        $user_id = auth()->user()->id;

        $admission_id = $sale->admission_id;
        $sale_id = $sale->SaleID;
        $is_admitted_patient = "no";

        //---- check if patient is admitt or not  ---//
        if ($admission_id) {
            $check_patient_admission = InPatientAdmission::whereId($admission_id)->where("admission_status", "Admit")->first();
            if ($check_patient_admission) {
                $is_admitted_patient = "yes";
            } else {
                $is_admitted_patient = "no";
            }
        }
        //----- end of check -----//

        $retrun_qty = request()->ReturnQuantity;
        $total_return_qty = ($sale_details->ReturnQuantity) + ($retrun_qty);

        // Update the sale_details return quantity and recalculate discount amounts
        $active_quantity_after_return = max(0, $sale_details->Quantity - $total_return_qty);

        // Calculate new proportional discount amount for the returned item
        $new_discount_percentage_amount = 0;
        if ($active_quantity_after_return > 0 && $sale_details->Quantity > 0) {
            //$proportion = $active_quantity_after_return / $sale_details->Quantity;
            //if (isset($sale_details->discount_percentage_amount) && $sale_details->discount_percentage_amount > 0) {
             //   $new_discount_percentage_amount = $sale_details->discount_percentage_amount * $proportion;
           // } else if (isset($sale_details->discount_percentage) && $sale_details->discount_percentage > 0) {
                $line_amount_before_discount = $active_quantity_after_return * $sale_details->UnitePrice;
                $new_discount_percentage_amount = ($line_amount_before_discount * $sale_details->discount_percentage) / 100;
           // }
        }

        // Update the specific returned item first
        SaleDetails::where(["SDID" => request()->SDID])->update([
            'ReturnQuantity' => $total_return_qty,
            'discount_percentage_amount' => $new_discount_percentage_amount,
            'return_by' => auth()->user()->id
        ]);
        TempSaleDetails::where(["SaleID" => $sale_details->SaleID, "ProductID" => $sale_details->ProductID])->update([
            'ReturnQuantity' => $total_return_qty,
            'discount_percentage_amount' => $new_discount_percentage_amount,
            'return_by' => auth()->user()->id
        ]);

        // Now update discount_percentage_amount for ALL items in the sale based on their current active quantities
        $all_sale_details = SaleDetails::where(["SaleID" => $sale_details->SaleID])->get();

        foreach ($all_sale_details as $detail) {
            $active_quantity = max(0, $detail->Quantity - $detail->ReturnQuantity);
            $updated_discount_amount = 0;

            if ($active_quantity > 0 && $detail->Quantity > 0) {
                $proportion = $active_quantity / $detail->Quantity;

                // Get the original discount amount (before any returns)
                $original_discount_amount = 0;
                if (isset($detail->discount_percentage) && $detail->discount_percentage > 0) {
                    $original_line_amount = $detail->Quantity * $detail->UnitePrice;
                    $original_discount_amount = ($original_line_amount * $detail->discount_percentage) / 100;
                } else if (isset($detail->discount_percentage_amount)) {
                    // If we already have a stored amount, use it as reference for proportion
                    $original_discount_amount = $detail->discount_percentage_amount / ($detail->Quantity > 0 ? ($detail->Quantity - $detail->ReturnQuantity) / $detail->Quantity : 1);
                }

                $updated_discount_amount = $original_discount_amount * $proportion;
            }

            // Update the discount amount in database
            SaleDetails::where(["SDID" => $detail->SDID])->update([
                'discount_percentage_amount' => $updated_discount_amount
            ]);
            TempSaleDetails::where(["SaleID" => $detail->SaleID, "ProductID" => $detail->ProductID])->update([
                'discount_percentage_amount' => $updated_discount_amount
            ]);
        }

        // Fetch fresh data after updating ALL discount amounts
        $all_sale_details = SaleDetails::where(["SaleID" => $sale_details->SaleID])->get();

        $recalculated_total_before_discount = 0; // Sum of (Quantity - ReturnQuantity) * UnitePrice for all items
        $recalculated_total_discount_amount = 0; // Sum of all ITEM discount amounts (no invoice_discount here)

        foreach ($all_sale_details as $detail) {
            $active_quantity = max(0, $detail->Quantity - $detail->ReturnQuantity); // Ensure non-negative
            $line_amount_before_discount = $active_quantity * $detail->UnitePrice;
            $recalculated_total_before_discount += $line_amount_before_discount;

            // Calculate ITEM discount amount for this line - only if active_quantity > 0
            // NOTE: invoice_discount is NOT applied to individual items, only to final bill
            if ($active_quantity > 0) {
               // if (isset($detail->discount_percentage_amount) && $detail->discount_percentage_amount > 0) {
                    // Proportional item discount for active quantity
                   // $proportion = $active_quantity / max(1, $detail->Quantity); // Avoid division by zero
                 //   $line_discount_amount = $detail->discount_percentage_amount * $proportion;
                 //   $recalculated_total_discount_amount += max(0, $line_discount_amount); // Ensure non-negative
                //} else if (isset($detail->discount_percentage) && $detail->discount_percentage > 0) {
                    // Calculate item discount percentage
                    $line_discount_amount = ($line_amount_before_discount * $detail->discount_percentage) / 100;
                    $recalculated_total_discount_amount += max(0, $line_discount_amount); // Ensure non-negative
                //}
            }
            // If active_quantity is 0, item discount is automatically 0 (6% of 0 = 0)
        }

        // Calculate final amounts according to specifications
        // TotalSale = amount before discount of sum of per item (no invoice_discount applied here)
        $new_total_sale = max(0, $recalculated_total_before_discount); // Amount before discount, ensure non-negative

        // received_amount = sum of per item sale amount - total discount per item - invoice_discount (applied to final bill only)
        $amount_after_item_discounts = max(0, $recalculated_total_before_discount - $recalculated_total_discount_amount);
        $new_received_amount = max(0, $amount_after_item_discounts - ($sale->invoice_discount ?? 0)); // Invoice discount applied to final bill only


        //---- check if patient is admitt then correct the bill otherwise make entry in pharmacy_return_items table for user closing balance.---//
        //--- close balance will adjust from pharmacy return table only amount will be minus from total sale amount of user during closing  ---//

          $pharmacy_return=  PharmacyRetrun::create([
                "sale_id" => $sale_details->SaleID,
                "sale_detail_id" => request()->SDID,
                "product_id" => $sale_details->ProductID,
                "quantity" => request()->ReturnQuantity,
                "amount" => request()->return_amount,
                "created_by" => auth()->user()->id,
                "created_at" => date("Y-m-d H:i:s"),
            ]);

            Sale::where(["SaleID" => $sale_details->SaleID])->update([
                "is_return_made" => 1,
                "ModifiedAt" => date("Y-m-d H:i:s"),
                "ModifiedBy" => auth()->user()->id,
                "TotalSale" => $new_total_sale,
                "received_amount" => $new_received_amount,
                "Discount" => $recalculated_total_discount_amount
            ]);
            TempSale::where(["SaleID" => $sale_details->SaleID])->update([
                "is_return_made" => 1,
                "ModifiedAt" => date("Y-m-d H:i:s"),
                "ModifiedBy" => auth()->user()->id,
                "TotalSale" => $new_total_sale,
                "received_amount" => $new_received_amount,
                "Discount" => $recalculated_total_discount_amount
            ]);

            if ($admission_id != 0) {
                SalePayment::where(["admission_id" => $admission_id])->update(["amount" => $new_received_amount]);
            } else {
                SalePayment::where(["sale_id" => $sale_details->SaleID])->update(["amount" => $new_received_amount]);
            }
        
        //-------- end of check  ------//

        //------------- now grn detals .........//
        $result = GrnDetails::where(["GDID" => $sale_details->GDID])->first();
        $remainingQuanity = ($result->RemainingQuantity) + ($retrun_qty);
        $soldQuantity = ($result->SoldQuantity) - ($retrun_qty);
        $TotalReturn = ($result->TotalReturn) + ($retrun_qty);
        $grn_detailDate = array(
            "SoldQuantity" => $soldQuantity,
            "TotalReturn"  => $TotalReturn,
            "RemainingQuantity" => $remainingQuanity,
            "ProductStatus"     => 1
        );
        GrnDetails::where(['GDID' => $sale_details->GDID])->update($grn_detailDate);

        //------ finance will take effect here  -----//

         $voucher = generateVoucherNumber("sale_return",auth()->user()->id);

        $voucher_data = [
            "voucher_number" =>$voucher,
            "voucher_type"   => "sale_return",
            'user_id' => auth()->user()->id,
            'created_by' => auth()->user()->id,
            "voucher_date"   => date("Y-m-d"),
            "total_amount"   => $return_amount,
            "remarks"   => $product->ProductName."  Return by ".auth()->user()->name ?? '',
            "created_by"   => auth()->user()->id,
            "created_at"   => date("Y-m-d H:i:s"),
        ];
        $voucher = FinanceVoucher::create($voucher_data);
        $voucher_id = $voucher->id;
        $remarks =  "( ".$product->ProductName." ) Return by ".auth()->user()->name ?? '';

                make_entry($voucher_id,financeHeadId('pharmacy_return'),$return_amount,0,"pharmacy_return",$pharmacy_return->id,auth()->user()->id,$remarks);
                make_entry($voucher_id,$customer_finance_head_id,0,$return_amount,"pharmacy_return",$pharmacy_return->id,auth()->user()->id,$remarks);

                $cost_of_good_sales_of_return_item = $this->cogs_after_return($pharmacy_return->id);
                // pharmacy_purchase debit    cogs credit
                make_entry($voucher_id,financeHeadId('pharmacy_purchase'),$cost_of_good_sales_of_return_item,0,"cogs_pharmacy_sale_return",$pharmacy_return->id,auth()->user()->id,$remarks);
                make_entry($voucher_id,financeHeadId('cogs'),0,$cost_of_good_sales_of_return_item,"cogs_pharmacy_sale_return",$pharmacy_return->id,auth()->user()->id,$remarks);


        //----- end of fincane  ---------//
        $this->updateSaleTaxAmount($sale_id);
        return response()->json(["status" => true, "message" => "done"]);
        //$this->Zk_Common_Model->update_records('grn_details',$grn_detailDate,array('GDID'=>$GDID));
    }

 public function cogs_after_return($pharmacy_return_id)
    {
        $return = PharmacyRetrun::where("id",$pharmacy_return_id)->first();
        $return_qty = $return->quantity;
        $cogs = DB::table('sale_details')
            ->where('SaleID', $return->sale_id)
            ->where('ProductID', $return->product_id)
            ->first();

        return ($return_qty ?? 0) * ($cogs->PurchasePrice ?? 0);

    }
    public function return_pharmacy_item_backup()
    {


        $sale_details = SaleDetails::where(["SDID" => request()->SDID])->first();
        $sale = Sale::where(["SaleID" => $sale_details->SaleID])->first();
        $discount_percentage =  $sale->discount_percentage;
        $admission_id = $sale->admission_id;


        $retrun_qty = request()->ReturnQuantity;
        $total_return_price = ($sale_details->UnitePrice) * ($retrun_qty);
        $total_return_qty = ($sale_details->ReturnQuantity) + ($retrun_qty);
        $discount_percentage_amount = round(($total_return_price * $discount_percentage) / 100);


        $total_sale_amount = ($sale->TotalSale) - ($total_return_price);
        //----- collect discount percentage from coustomer on return -----//
        $total_return_price = round($total_return_price - $discount_percentage_amount);
        $received_amount = ($sale->received_amount) - ($total_return_price);


        //------- sale related operations  ------------//
        Sale::where(["SaleID" => $sale_details->SaleID])->update(["TotalSale" => $total_sale_amount, "received_amount" => $received_amount]);
        SaleDetails::where(["SDID" => request()->SDID])->update(['ReturnQuantity' => $total_return_qty, 'return_by' => auth()->user()->id]);

        //---------- end of sale related operations   ---------//

        //------------- now grn detals .........//
        $result = GrnDetails::where(["GDID" => $sale_details->GDID])->first();
        $remainingQuanity = ($result->RemainingQuantity) + ($retrun_qty);
        $soldQuantity = ($result->SoldQuantity) - ($retrun_qty);
        $TotalReturn = ($result->TotalReturn) + ($retrun_qty);
        $grn_detailDate = array(
            "SoldQuantity" => $soldQuantity,
            "TotalReturn"  => $TotalReturn,
            "RemainingQuantity" => $remainingQuanity,
            "ProductStatus"     => 1
        );
        GrnDetails::where(['GDID' => $sale_details->GDID])->update($grn_detailDate);

        return response()->json(["status" => true, "message" => "done"]);
        //$this->Zk_Common_Model->update_records('grn_details',$grn_detailDate,array('GDID'=>$GDID));
    }



    public function get_bill_details($sale_id)
    {
        $patients = SaleDetails::with("product", "sale")
            ->when($sale_id, function ($query) use ($sale_id) {
                $query->where('SaleID', $sale_id);
            })
            ->when(request()->medicine_type, function ($query) {
                $query->whereHas('sale', function ($q) {

                    $q->where('medicine_type', request()->medicine_type);
                });
            });
        return DataTables::of($patients)
            ->addColumn("actions", function ($patient) {
                if ($patient->ReturnQuantity == $patient->Quantity) {
                    return "";
                } else {
                    return '<a href="javascript:void(0)" data-sdid="' . (int) $patient->SDID . '" class="btn btn-sm btn-primary return_product">Return</a>';
                }
            })
            ->addColumn("total_amount", function ($value) {
                $total = ($value->UnitePrice) * ($value->Quantity);
                return number_format($total, 2);
            })
            ->addColumn("total_consumed", function ($value) {
                $total = ($value->Quantity) - ($value->ReturnQuantity);
                return $total;
            })

            ->rawColumns(["actions", "total_amount", "total_consumed"])
            ->make(true);
    }

    public function get_sale_detail_for_return($sdid)
    {
        $detail = SaleDetails::with('sale')->where('SDID', (int) $sdid)->first();

        if (!$detail) {
            return response()->json([
                'success' => false,
                'message' => 'Sale detail not found.',
            ], 404);
        }

        if ($detail->ReturnQuantity >= $detail->Quantity) {
            return response()->json([
                'success' => false,
                'message' => 'This item has already been fully returned.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'SDID' => $detail->SDID,
                'patient_id' => $detail->sale->patient_id ?? 0,
                'admission_id' => $detail->sale->admission_id ?? 0,
                'Quantity' => $detail->Quantity,
                'ReturnQuantity' => $detail->ReturnQuantity,
                'available_quantity' => $detail->Quantity - $detail->ReturnQuantity,
                'UnitePrice' => $detail->UnitePrice,
                'discount_percentage' => $detail->discount_percentage ?? 0,
            ],
        ]);
    }

    public function retail_sale_point()
    {
        $store = Store::where("id", "!=", env('SEHAT_CARD_PHARMACY_STORE_ID'))->first();
        session(['store_id' => 2]);
        session(['store_name' => "Retail Pharmacy Sale Point"]);
        session(['is_free' => 0]);
        /*if($store){
            session(['store_id' => $store->id]);
            session(['store_name' => $store->store_name]);
            session(['is_free' => $store->use_purchase_price_as_sale_price]);
        }*/

        $type = $_GET['type'] ?? "Home";
        $data['type'] = $type;
        $data["ward_request"] = $_GET["ward_request"] ?? "";
        $data['patient_id'] = "";
        $data['list_products'] = [];

        $data['appointments'] = Appointment::where('is_active', 1)
            ->where('created_at', '>=', Carbon::now()->subDays(2)) // last 5 days
            ->with(['patient'])
            ->orderBy('appointment_date', 'desc')
            ->get();


        $data["title"] = "Retail Sale Point";
        /*$data['products'] = Product::orderBy("ProductName", "ASC")
             ->when(session('store_id'),function ($q){
                 $q->where('store_id',session('store_id'));
             })
            ->get();*/
        //Cache::forget('products_store_2');
        $data["products"] =  Product::with('generic_name')
            ->when(session('store_id'), function ($q) {
                $q->where('store_id', session('store_id'));
            })
            ->orderBy("ProductName", "ASC")
            ->where("IsActive", 1)
            ->where("ProductName", "!=", '')
            ->where("pack_size", "!=", 0)
            ->where("pack_price", "!=", 0)

            ->get();
        foreach ($data['products'] as $key => $value) {
            $value->avaliable_qty = $this->getProductAvailableQuantity((int) $value->ProductID);
        }
        //$data['customers'] = Customer::where(["Type" => 2])->orderBy("Name", "ASC")->get();
        $data['admitted_patients'] = Patient::where("patient_type", "walking_customer")->get();

        $data['invoiceNo'] = $this->returnInvoiceNumber();

        return view("sale.retail_sale_point", $data);
    }

    /**
     * Row tax: ((Qty - ReturnQty) * Price - discount) then sale_tax% + income_tax%.
     */
    protected function calculateSaleDetailRowTaxAmount(
        float $quantity,
        float $returnQuantity,
        float $unitPrice,
        float $discountAmount,
        float $saleTaxPct,
        float $incomeTaxPct
    ): float {
        $amount = max(0, ($quantity - $returnQuantity) * $unitPrice);
        $newAmount = max(0, $amount - $discountAmount);
        $saleTaxAmt = round(($newAmount * $saleTaxPct) / 100, 2);
        $incomeTaxAmt = round(($newAmount * $incomeTaxPct) / 100, 2);

        return round($saleTaxAmt + $incomeTaxAmt, 2);
    }

    protected function calculateProductRowTaxAmounts(array $row, float $quantity): array
    {
        $returnQty = (float) ($row['ReturnQuantity'] ?? 0);
        $saleTaxPct = (float) ($row['sale_tax'] ?? 0);
        $incomeTaxPct = (float) ($row['income_tax'] ?? 0);
        $taxAmount = $this->calculateSaleDetailRowTaxAmount(
            $quantity,
            $returnQty,
            (float) ($row['UnitePrice'] ?? 0),
            (float) ($row['discount_percentage_amount'] ?? 0),
            $saleTaxPct,
            $incomeTaxPct
        );

        return [
            'sale_tax' => $saleTaxPct,
            'income_tax' => $incomeTaxPct,
            'tax_amount' => $taxAmount,
        ];
    }

    protected function calculateDetailChunkTaxAmount(
        float $chunkQty,
        float $unitPrice,
        float $rowDiscountAmount,
        float $originalSoldQty,
        array $detailArray
    ): float {
        $discountShare = $originalSoldQty > 0
            ? ($rowDiscountAmount * ($chunkQty / $originalSoldQty))
            : 0;

        return $this->calculateSaleDetailRowTaxAmount(
            $chunkQty,
            0,
            $unitPrice,
            $discountShare,
            (float) ($detailArray['sale_tax'] ?? 0),
            (float) ($detailArray['income_tax'] ?? 0)
        );
    }

    /**
     * Default row tax % from active taxes table (matched by name).
     */
    protected function getDefaultRowTaxPercentages(): array
    {
        $saleTax = 0.0;
        $incomeTax = 0.0;

        foreach (Tax::active()->get() as $tax) {
            $pct = (float) ($tax->tax_percentage ?? 0);
            if ($pct <= 0) {
                continue;
            }

            $name = strtolower(trim((string) ($tax->name ?? '')));
            if ($name === '') {
                continue;
            }

            if (str_contains($name, 'income')) {
                $incomeTax = $pct;
            } elseif (str_contains($name, 'sale')) {
                $saleTax = $pct;
            }
        }

        return [
            'sale_tax' => $saleTax,
            'income_tax' => $incomeTax,
        ];
    }

    protected function updateSaleTaxAmount(int $saleId): void
    {
        $sale = Sale::where('SaleID', $saleId)->first();
        if (!$sale) {
            return;
        }

        $totalBill = (float) ($sale->TotalSale ?? 0)
            - (float) ($sale->Discount ?? 0)
            - (float) ($sale->invoice_discount ?? 0);

        $totalTaxAmount = 0.0;
        $details = SaleDetails::where('SaleID', $saleId)->get();

        foreach ($details as $detail) {
            $rowTax = $this->calculateSaleDetailRowTaxAmount(
                (float) ($detail->Quantity ?? 0),
                (float) ($detail->ReturnQuantity ?? 0),
                (float) ($detail->UnitePrice ?? 0),
                (float) ($detail->discount_percentage_amount ?? 0),
                (float) ($detail->sale_tax ?? 0),
                (float) ($detail->income_tax ?? 0)
            );

            if ((float) ($detail->taxAmount ?? 0) !== $rowTax) {
                SaleDetails::where('SDID', $detail->SDID)->update(['taxAmount' => $rowTax]);
            }

            $totalTaxAmount += $rowTax;
        }

        $totalTaxAmount = round($totalTaxAmount, 2);
        $netAmount = round($totalBill + $totalTaxAmount, 2);

        Sale::where('SaleID', $saleId)->update([
            'tax_amount' => $totalTaxAmount,
            'net_amount' => $netAmount,
        ]);
        TempSale::where('SaleID', $saleId)->update([
            'tax_amount' => $totalTaxAmount,
            'net_amount' => $netAmount,
        ]);

        $tempDetails = TempSaleDetails::where('SaleID', $saleId)->get();
        foreach ($tempDetails as $detail) {
            $rowTax = $this->calculateSaleDetailRowTaxAmount(
                (float) ($detail->Quantity ?? 0),
                (float) ($detail->ReturnQuantity ?? 0),
                (float) ($detail->UnitePrice ?? 0),
                (float) ($detail->discount_percentage_amount ?? 0),
                (float) ($detail->sale_tax ?? 0),
                (float) ($detail->income_tax ?? 0)
            );

            if ((float) ($detail->taxAmount ?? 0) !== $rowTax) {
                TempSaleDetails::where('id', $detail->id)->update(['taxAmount' => $rowTax]);
            }
        }
    }
}

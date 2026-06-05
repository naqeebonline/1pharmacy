<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get("add_is_sync_field", [\App\Http\Controllers\Admin\StockController::class, 'add_is_sync_field']);
Route::get("import_all_products_to_bill", [\App\Http\Controllers\Admin\StockController::class, 'import_all_products_to_bill'])->name('pos.import_all_products_to_bill');
Route::get("syncData", [\App\Http\Controllers\SyncController::class, 'load_sync_view'])->name('pos.syncData');
Route::post("syncDataLive", [\App\Http\Controllers\SyncController::class, 'syncLoclDataWithLive'])->name('pos.syncDataLive');

Route::get('/testData', function () {});

Route::get("lab-results/{invoice_no?}", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "out_user_print_investigation"])->name("pos.out_user_print_investigation");
Route::get("print_out_user_inv_result/{inv_id}",  [\App\Http\Controllers\Investigation\InvestigationResult::class, 'print_out_user_inv_result'])->name("pos.print_out_user_inv_result");

Route::get("/", [\App\Http\Controllers\HomeController::class, 'index'])->name('app.landing-screen');
Route::get("dashboardAnalytics", [\App\Http\Controllers\HomeController::class, 'dashboardAnalytics'])->name('app.dashboardAnalytics');
Route::get('/printReceipt', [\App\Http\Controllers\ThermalPrintController::class, 'printReceipt']);
Route::get('/printReceipt2', [\App\Http\Controllers\ThermalPrintController::class, 'printReceipt2']);
Route::post('/syncLiveDataFromLocal', [\App\Http\Controllers\DataSyncController::class, 'syncLiveDataFromLocal']);
Route::get('/sendDataToLive', [\App\Http\Controllers\DataSyncController::class, 'sendDataToLive']);


Route::get('/testQue', [App\Http\Controllers\HomeController::class, 'testQue']);
Route::get('/telenorGatway', [\App\Http\Controllers\API\SmsController::class, 'telenorGatway']);

\Illuminate\Support\Facades\Auth::routes();

Route::post('custom-authenticate', [\App\Http\Controllers\Auth\LoginController::class, 'customAuthenticate'])->name('custom-authenticate');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get("sehatcard_patients_statistics", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "sehatcard_patients_statistics"])->name("pos.sehatcard_patients_statistics");

// Test route for sales dashboard (temporary - remove after testing)
Route::get('/test-sale-dashboard', [\App\Http\Controllers\SaleDashboardController::class, 'index'])->name('test.sale.dashboard');
Route::prefix('no-auth')->group(function () {

    Route::post('districts-by-province-id', [\App\Http\Controllers\NoAuthActionsControllers::class, 'districtsByProvinceId'])->name('noauth.districts-by-province-id');

    Route::post('districts-by-division-id', [\App\Http\Controllers\NoAuthActionsControllers::class, 'districtsByDivisionId'])->name('noauth.districts-by-division-id');

    Route::post('tehsils-by-district-id', [\App\Http\Controllers\NoAuthActionsControllers::class, 'tehsilsByDistrictId'])->name('noauth.tehsils-by-district-id');

    Route::post('sections-by-company-id', [\App\Http\Controllers\NoAuthActionsControllers::class, 'sectionsByCompanyId'])->name('noauth.sections-by-company-id');

    Route::post('users-hod-id-check', [\App\Http\Controllers\NoAuthActionsControllers::class, 'usersHodIdCheck'])->name('noauth.users-hod-id-check');

    Route::post('users-hod-check', [\App\Http\Controllers\NoAuthActionsControllers::class, 'usersHodCheck'])->name('noauth.users-hod-check');

    Route::post('users-list-by-company-id', [\App\Http\Controllers\NoAuthActionsControllers::class, 'usersListByCompanyId'])->name('noauth.users-list-by-company-id');

    Route::post('companies-by-type-id', [\App\Http\Controllers\NoAuthActionsControllers::class, 'companiesByTypeId'])->name('noauth.companies-by-type-id');

    Route::get('companies-list', [\App\Http\Controllers\NoAuthActionsControllers::class, 'companiesList'])->name('noauth.companies-list');
});

Route::middleware(['auth'])->prefix('dropDown')->group(function () {
    Route::get('/', [\App\Http\Controllers\DynamicController::class, 'dropDown'])->name('dynamic.dropDown');
});

Route::get('/psw-generate', function () {
    return \Illuminate\Support\Facades\Hash::make('12345678');
});

/*Route::post('create-meeting', function () {


})->name("create-meeting");*/

Route::group(['middleware' => 'auth'], function () {
    Route::get("sehatCardDashboard", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "sehatCardDashboard"])->name("pos.sehatCardDashboard")->middleware('store.selected');
    Route::get('pharmacy_dashboard', [\App\Http\Controllers\HomeController::class, 'pharmacy_dashboard'])->name('user.pharmacy_dashboard')->middleware('store.selected');
    Route::get('/retail-dashboard', [App\Http\Controllers\Dashboard\DashboardController::class, 'retailPharmacyDashboard'])->name('pos.retailPharmacyDashboard');
    Route::get('user-dashboard', [\App\Http\Controllers\HomeController::class, 'userDashboard'])->name('user.dashboard');

    Route::get('view-history', [\App\Http\Controllers\Admin\SmsHistoryController::class, 'index'])->name('view-history');
    Route::get('list-sms', [\App\Http\Controllers\Admin\SmsHistoryController::class, 'listSms'])->name('list-sms');
    Route::get('add_new_market', [\App\Http\Controllers\Admin\MarketController::class, 'add_new_market'])->name('pos.add_new_market');
    Route::get('list_market', [\App\Http\Controllers\Admin\MarketController::class, 'list_market'])->name('pos.list_market');
    Route::post('save-market', [\App\Http\Controllers\Admin\MarketController::class, 'save_market'])->name('pos.save_market');
    Route::post('delete-table-data', [\App\Http\Controllers\Admin\MarketController::class, 'delete_table_data'])->name('pos.delete-table-data');
    Route::post('delete_table_data_2', [\App\Http\Controllers\Admin\MarketController::class, 'delete_table_data_2'])->name('pos.delete_table_data_2');
    Route::post('delete_IsActive', [\App\Http\Controllers\Admin\MarketController::class, 'delete_IsActive'])->name('pos.delete_IsActive');

    Route::get('add_new_product', [\App\Http\Controllers\Admin\ProductController::class, 'add_new_product'])->name('pos.add_new_product')->middleware('store.selected');
    Route::get('grn_request', [\App\Http\Controllers\Admin\StockController::class, 'grn_request'])->name('pos.grn_request')->middleware('store.selected');

    Route::get('ware-house-stock', [\App\Http\Controllers\Admin\StockController::class, 'ware_house_stock'])->name('pos.ware_house_stock')->middleware('store.selected');
    //Route::get('sehat-card-pharmacy-sale', [\App\Http\Controllers\Admin\SaleController::class, 'sehat_card_pharmacy_sale'])->name('pos.add_new_sale')->middleware('store.selected');
    Route::get('sehat-card-pharmacy-sale', [\App\Http\Controllers\Admin\SaleController::class, 'sehat_card_pharmacy_sale'])->name('pos.add_new_sale');
    // Route::get('retail-sale', [\App\Http\Controllers\Admin\SaleController::class, 'retail_pharmacy_sale'])->name('pos.retail_pharmacy_sale')->middleware('store.selected');
    Route::get('retail-sale', [\App\Http\Controllers\Admin\SaleController::class, 'retail_pharmacy_sale'])->name('pos.retail_pharmacy_sale');
    Route::get('search_appointment', [\App\Http\Controllers\Admin\SaleController::class, 'search_appointment'])->name('pos.search_appointment');
    Route::get('refresh_retail_products', [\App\Http\Controllers\Admin\SaleController::class, 'refresh_retail_products'])->name('pos.refresh_retail_products');

    Route::get('pharmacy-transfer', [\App\Http\Controllers\Admin\SaleController::class, 'pharmacy_transfer'])->name('pos.pharmacy_transfer');
    Route::post('save_pharmacy_transfer', [\App\Http\Controllers\Admin\SaleController::class, 'save_pharmacy_transfer'])->name('pos.save_pharmacy_transfer');
    Route::get('print_pharmacy_transfer_bill/{id?}', [\App\Http\Controllers\Admin\SupplierPayments::class, 'print_pharmacy_transfer_bill'])->name('pos.print_pharmacy_transfer_bill');
    Route::get('pharmacy_transfer_bills', [\App\Http\Controllers\Admin\SupplierPayments::class, 'pharmacy_transfer_bills'])->name('pos.pharmacy_transfer_bills');

    Route::get('list_product', [\App\Http\Controllers\Admin\ProductController::class, 'list_product'])->name('pos.list_product');
    Route::post('save-product', [\App\Http\Controllers\Admin\ProductController::class, 'save_product'])->name('pos.save_product');
    Route::post('getProduct', [\App\Http\Controllers\Admin\ProductController::class, 'getProduct'])->name('pos.getProduct');
    Route::post('get_items_by_product_id', [\App\Http\Controllers\Admin\ProductController::class, 'get_items_by_product_id'])->name('pos.get_items_by_product_id');
    Route::post('get_items_by_barcode', [\App\Http\Controllers\Admin\ProductController::class, 'get_items_by_barcode'])->name('pos.get_items_by_barcode');

    Route::get('customers', [\App\Http\Controllers\Admin\CustomerController::class, 'add_new_customer'])->name('pos.add_new_customer');
    Route::get('list_customer', [\App\Http\Controllers\Admin\CustomerController::class, 'list_customer'])->name('pos.list_customer');
    Route::post('save-customer', [\App\Http\Controllers\Admin\CustomerController::class, 'save_customer'])->name('pos.save_customer');

    Route::get('set_user_store_id/{store_id?}', [\App\Http\Controllers\Admin\StockController::class, 'set_user_store_id'])->name('pos.set_user_store_id');
    Route::get('select_store', [\App\Http\Controllers\Admin\StockController::class, 'select_store'])->name('pos.select_store');
    Route::get('add-stock', [\App\Http\Controllers\Admin\StockController::class, 'add_new_stock'])->name('pos.add_new_add_new_stock');

    Route::get('get-ware-house-stock', [\App\Http\Controllers\Admin\StockController::class, 'get_ware_house_stock'])->name('pos.get_ware_house_stock');
    Route::get('product_purchase_details/{id}', [\App\Http\Controllers\Admin\StockController::class, 'product_purchase_details'])->name('pos.product_purchase_details');
    Route::post("save-stock", [\App\Http\Controllers\Admin\StockController::class, 'save_stock'])->name('pos.save_stock');
    Route::get("edit-purchase-bill/{id?}", [\App\Http\Controllers\Admin\StockController::class, 'edit_purchase_bill'])->name('pos.edit_purchase_bill');


    Route::get('supplier-payments', [\App\Http\Controllers\Admin\SupplierPayments::class, 'supplier_payments'])->name('pos.supplier_payments');

    Route::post('save-payments', [\App\Http\Controllers\Admin\SupplierPayments::class, 'save_payments'])->name('pos.save_payments');
    Route::get('get-payments/{id?}', [\App\Http\Controllers\Admin\SupplierPayments::class, 'get_payments'])->name('pos.get_payments');
    Route::get('purchase-details/{id?}', [\App\Http\Controllers\Admin\SupplierPayments::class, 'purchase_details'])->name('pos.purchase_details');
    Route::get('get_purchase_bill_items/{id?}', [\App\Http\Controllers\Admin\SupplierPayments::class, 'get_purchase_bill_items'])->name('pos.get_purchase_bill_items');
    Route::get('add-bill-items/{id?}', [\App\Http\Controllers\Admin\SupplierPayments::class, 'add_bill_items'])->name('pos.add_bill_items');
    Route::get('print_purchase/{SCID?}/{GRNID?}', [\App\Http\Controllers\Admin\SupplierPayments::class, 'print_purchase'])->name('pos.print_purchase');
    Route::get('print_purchase_request/{SCID?}/{GRNID?}', [\App\Http\Controllers\Admin\SupplierPayments::class, 'print_purchase_request'])->name('pos.print_purchase_request');
    Route::get('print_thermel_purchase_details/{id?}', [\App\Http\Controllers\Admin\SupplierPayments::class, 'print_thermel_purchase_details'])->name('pos.print_thermel_purchase');
    Route::get('print_retail_themal/{id?}', [\App\Http\Controllers\Admin\SupplierPayments::class, 'print_retail_thermel_purchase_details'])->name('pos.print_retail_thermal_bill');
    Route::get('print_retail_thermel_purchase_details/{id?}', [\App\Http\Controllers\Admin\SupplierPayments::class, 'print_retail_thermel_purchase_details'])->name('pos.print_retail_thermel_purchase_details');
    //Route::get('print-customer-bill/{id?}', [\App\Http\Controllers\Admin\SupplierPayments::class, 'print_customer_bill'])->name('pos.print_customer_bill');
    Route::get('print-customer-bill-a4/{id?}', [\App\Http\Controllers\Admin\SupplierPayments::class, 'print_customer_bill_a4'])->name('pos.print_customer_bill_a4');
    Route::get('print-customer-bill-excel/{id}', [\App\Http\Controllers\Admin\SupplierPayments::class, 'print_customer_bill_excel'])->name('pos.print_customer_bill_excel');
    Route::get('print_customer_bill/{id?}', [\App\Http\Controllers\Admin\SupplierPayments::class, 'print_customer_bill_a4'])->name('pos.print_customer_bill');
    Route::get('previous_bills', [\App\Http\Controllers\Admin\SupplierPayments::class, 'previous_bills'])->name('pos.previous_bills');
    Route::get('retail_previous_bills', [\App\Http\Controllers\Admin\SupplierPayments::class, 'retail_previous_bills'])->name('pos.retail_previous_bills');
    Route::get('in_patient_retail_previous_bills', [\App\Http\Controllers\Admin\SupplierPayments::class, 'in_patient_retail_previous_bills'])->name('pos.in_patient_retail_previous_bills');



    Route::post('save_sale', [\App\Http\Controllers\Admin\SaleController::class, 'save_sale'])->name('pos.save_sale');
    Route::post('save_retail_sale', [\App\Http\Controllers\Admin\SaleController::class, 'save_retail_sale'])->name('pos.save_retail_sale');
    Route::post('set-retail-print-mode', [\App\Http\Controllers\Admin\SaleController::class, 'set_retail_print_mode'])->name('pos.set_retail_print_mode');
    Route::post('save_in_patient_retail_sale', [\App\Http\Controllers\Admin\SaleController::class, 'save_in_patient_retail_sale'])->name('pos.save_in_patient_retail_sale');
    Route::post('temp_save_sale', [\App\Http\Controllers\Admin\SaleController::class, 'temp_save_sale'])->name('pos.temp_save_sale');
    Route::get('print_temp_sale/{sale_id?}/{customer_id?}/{date?}/{received_amount?}', [\App\Http\Controllers\Admin\SaleController::class, 'print_temp_sale'])->name('pos.print_temp_sale');
    Route::get('print_purchase_detail/{sale_id?}/{date?}', [\App\Http\Controllers\Admin\SaleController::class, 'print_purchase_detail'])->name('pos.print_purchase_detail');
    Route::get('customer-payments', [\App\Http\Controllers\Admin\CustomerPayments::class, 'customer_payments'])->name('pos.customer_payments');

    Route::post('save-customer-payments', [\App\Http\Controllers\Admin\CustomerPayments::class, 'save_customer_payments'])->name('pos.save_customer_payments');

    Route::get('receiveable/{id?}', [\App\Http\Controllers\Admin\CustomerPayments::class, 'receiveables'])->name('pos.receiveables');
    Route::get('sale-details/{id?}', [\App\Http\Controllers\Admin\CustomerPayments::class, 'sale_details'])->name('pos.sale_details');
    Route::get('customer_previous_balance/{id?}/{date?}', [\App\Http\Controllers\Admin\CustomerPayments::class, 'customer_previous_balance'])->name('pos.customer_previous_balance');
    Route::get('supplier_previous_balance/{id?}/{date?}', [\App\Http\Controllers\Admin\SupplierPayments::class, 'supplier_previous_balance'])->name('pos.supplier_previous_balance');


    Route::get("expenses", [\App\Http\Controllers\Admin\ExpensesController::class, 'add_new_expenses'])->name('pos.add_new_expenses');
    Route::get("expenses-list", [\App\Http\Controllers\Admin\ExpensesController::class, 'expenses_list'])->name('pos.expenses_list');
    Route::get("sub_expenses_list", [\App\Http\Controllers\Admin\ExpensesController::class, 'sub_expenses_list'])->name('pos.sub_expenses_list');
    Route::post("save-expanses", [\App\Http\Controllers\Admin\ExpensesController::class, 'save_expanses'])->name('pos.save_expanses');
    Route::get("sub-expanses", [\App\Http\Controllers\Admin\ExpensesController::class, 'sub_expanses'])->name('pos.sub_expanses');
    Route::post("save-sub-expanses", [\App\Http\Controllers\Admin\ExpensesController::class, 'save_sub_expanses'])->name('pos.save_sub_expanses');


    Route::get("general-expanses", [\App\Http\Controllers\Admin\ExpensesController::class, 'general_expanses'])->name('pos.general_expanses');
    Route::get("general-expanses-list", [\App\Http\Controllers\Admin\ExpensesController::class, 'general_expanses_list'])->name('pos.general_expanses_list');
    Route::post("get_sub_expanses", [\App\Http\Controllers\Admin\ExpensesController::class, 'get_sub_expanses'])->name('pos.get_sub_expanses');
    Route::post("save-general-expanses", [\App\Http\Controllers\Admin\ExpensesController::class, 'save_general_expanses'])->name('pos.save_general_expanses');

    Route::get("get-daily-sale", [\App\Http\Controllers\HomeController::class, 'getDailySaleData'])->name('pos.getDailySaleData');

    Route::post('deactivate_record', [\App\Http\Controllers\ProductConfiguration\CategoryController::class, 'deactivate_record'])->name('pos.deactivate_record');
    Route::post('delete_appointment', [\App\Http\Controllers\ProductConfiguration\CategoryController::class, 'delete_appointment'])->name('pos.delete_appointment');
    Route::post('deactivate_in_patient_service_charges', [\App\Http\Controllers\ProductConfiguration\CategoryController::class, 'deactivate_in_patient_service_charges'])->name('pos.deactivate_in_patient_service_charges');

    Route::get('main-category', [\App\Http\Controllers\ProductConfiguration\CategoryController::class, 'add_main_category'])->name('pos.add_main_category');
    Route::get('list_main_category', [\App\Http\Controllers\ProductConfiguration\CategoryController::class, 'list_main_category'])->name('pos.list_main_category');
    Route::post('save_main_category', [\App\Http\Controllers\ProductConfiguration\CategoryController::class, 'save_main_category'])->name('pos.save_main_category');

    Route::get('item-sub-category', [\App\Http\Controllers\ProductConfiguration\CategoryController::class, 'add_sub_category'])->name('pos.add_sub_category');
    Route::get('list_sub_category', [\App\Http\Controllers\ProductConfiguration\CategoryController::class, 'list_sub_category'])->name('pos.list_sub_category');
    Route::post('save_sub_category', [\App\Http\Controllers\ProductConfiguration\CategoryController::class, 'save_sub_category'])->name('pos.save_sub_category');
    Route::post('get_sub_category', [\App\Http\Controllers\ProductConfiguration\CategoryController::class, 'get_sub_category'])->name('pos.get_sub_category');

    Route::get('item-form', [\App\Http\Controllers\ProductConfiguration\CategoryController::class, 'add_item_form'])->name('pos.add_item_form');
    Route::get('list_item_form', [\App\Http\Controllers\ProductConfiguration\CategoryController::class, 'list_item_form'])->name('pos.list_item_form');
    Route::post('save_item_form', [\App\Http\Controllers\ProductConfiguration\CategoryController::class, 'save_item_form'])->name('pos.save_item_form');

    Route::get('item-make', [\App\Http\Controllers\ProductConfiguration\CategoryController::class, 'add_make'])->name('pos.add_make');
    Route::get('list_make', [\App\Http\Controllers\ProductConfiguration\CategoryController::class, 'list_make'])->name('pos.list_make');
    Route::post('save_make', [\App\Http\Controllers\ProductConfiguration\CategoryController::class, 'save_make'])->name('pos.save_make');

    Route::get('item-generic-name', [\App\Http\Controllers\ProductConfiguration\CategoryController::class, 'add_generic_name'])->name('pos.add_generic_name');
    Route::get('list_generic_name', [\App\Http\Controllers\ProductConfiguration\CategoryController::class, 'list_generic_name'])->name('pos.list_generic_name');
    Route::post('save_generic_name', [\App\Http\Controllers\ProductConfiguration\CategoryController::class, 'save_generic_name'])->name('pos.save_generic_name');

    Route::get('taxes', [\App\Http\Controllers\ProductConfiguration\CategoryController::class, 'add_taxes'])->name('pos.add_taxes');
    Route::get('list_taxes', [\App\Http\Controllers\ProductConfiguration\CategoryController::class, 'list_taxes'])->name('pos.list_taxes');
    Route::post('save_taxes', [\App\Http\Controllers\ProductConfiguration\CategoryController::class, 'save_taxes'])->name('pos.save_taxes');
    Route::post('zero_tax_percentage', [\App\Http\Controllers\ProductConfiguration\CategoryController::class, 'zero_tax_percentage'])->name('pos.zero_tax_percentage');


    Route::get('list-wards', [\App\Http\Controllers\GeneralConfigration\GeneralConfigController::class, 'add_ward'])->name('pos.add_ward');
    Route::get('list_ward', [\App\Http\Controllers\GeneralConfigration\GeneralConfigController::class, 'list_ward'])->name('pos.list_ward');
    Route::post('save_ward', [\App\Http\Controllers\GeneralConfigration\GeneralConfigController::class, 'save_ward'])->name('pos.save_ward');

    Route::get('list-beds', [\App\Http\Controllers\GeneralConfigration\GeneralConfigController::class, 'add_bed'])->name('pos.add_bed');
    Route::get('list_bed', [\App\Http\Controllers\GeneralConfigration\GeneralConfigController::class, 'list_bed'])->name('pos.list_bed');
    Route::post('save_bed', [\App\Http\Controllers\GeneralConfigration\GeneralConfigController::class, 'save_bed'])->name('pos.save_bed');

    Route::get('list-service-type', [\App\Http\Controllers\GeneralConfigration\GeneralConfigController::class, 'add_service_type'])->name('pos.add_service_type');
    Route::get('list_service_type', [\App\Http\Controllers\GeneralConfigration\GeneralConfigController::class, 'list_service_type'])->name('pos.list_service_type');
    Route::get('print_service_type', [\App\Http\Controllers\GeneralConfigration\GeneralConfigController::class, 'print_service_type'])->name('pos.print_service_type');
    Route::post('save_service_type', [\App\Http\Controllers\GeneralConfigration\GeneralConfigController::class, 'save_service_type'])->name('pos.save_service_type');


    Route::get("customer-registration", [\App\Http\Controllers\PatientController\PatientController::class, "customer_regisration"])->name("pos.patient_regisration");
    Route::post("get_patient_by_cnic", [\App\Http\Controllers\PatientController\PatientController::class, "get_patient_by_cnic"])->name("pos.get_patient_by_cnic");
    Route::post("store-customer-registration", [\App\Http\Controllers\PatientController\PatientController::class, "store_customer_regisration"])->name("pos.store_patient_regisration");
    Route::get("list-patient", [\App\Http\Controllers\PatientController\PatientController::class, "list_patient"])->name("pos.list_patient");

    // Customer payments (patients table is used as customers)
    Route::get("customer-receive-payment", [\App\Http\Controllers\PatientController\PatientController::class, "customer_receive_payment"])->name("pos.customer_receive_payment");
    Route::post("store-customer-receive-payment", [\App\Http\Controllers\PatientController\PatientController::class, "store_customer_receive_payment"])->name("pos.store_customer_receive_payment");
    Route::get("customer-receive-payment/history/{patient_id}", [\App\Http\Controllers\PatientController\PatientController::class, "customer_payment_history"])->name("pos.customer_payment_history");
    Route::get("customer-receive-payment/list", [\App\Http\Controllers\PatientController\PatientController::class, "customer_receive_payment_list"])->name("pos.customer_receive_payment_list");

    Route::get("customer-receive-payment/payment/{payment_id}", [\App\Http\Controllers\PatientController\PatientController::class, "get_customer_receive_payment"])->name("pos.get_customer_receive_payment");
    Route::post("customer-receive-payment/payment/update", [\App\Http\Controllers\PatientController\PatientController::class, "update_customer_receive_payment"])->name("pos.update_customer_receive_payment");
    Route::post("customer-receive-payment/payment/delete", [\App\Http\Controllers\PatientController\PatientController::class, "delete_customer_receive_payment"])->name("pos.delete_customer_receive_payment");
    Route::post("customer-receive-payment/payment/approve", [\App\Http\Controllers\PatientController\PatientController::class, "approve_customer_receive_payment"])->name("pos.approve_customer_receive_payment");

    // Supplier payments (suppliers are in sup_cus_details where Type=1)
    Route::get("supplier-receive-payment", [\App\Http\Controllers\Admin\SupplierPayments::class, "supplier_receive_payment"])->name("pos.supplier_receive_payment");
    Route::post("store-supplier-receive-payment", [\App\Http\Controllers\Admin\SupplierPayments::class, "store_supplier_receive_payment"])->name("pos.store_supplier_receive_payment");
    Route::get("supplier-receive-payment/list", [\App\Http\Controllers\Admin\SupplierPayments::class, "supplier_receive_payment_list"])->name("pos.supplier_receive_payment_list");
    Route::get("supplier-receive-payment/payment/{payment_id}", [\App\Http\Controllers\Admin\SupplierPayments::class, "get_supplier_receive_payment"])->name("pos.get_supplier_receive_payment");
    Route::post("supplier-receive-payment/payment/update", [\App\Http\Controllers\Admin\SupplierPayments::class, "update_supplier_receive_payment"])->name("pos.update_supplier_receive_payment");
    Route::post("supplier-receive-payment/payment/delete", [\App\Http\Controllers\Admin\SupplierPayments::class, "delete_supplier_receive_payment"])->name("pos.delete_supplier_receive_payment");
    Route::post("supplier-receive-payment/payment/approve", [\App\Http\Controllers\Admin\SupplierPayments::class, "approve_supplier_receive_payment"])->name("pos.approve_supplier_receive_payment");

    // Customer bills from Sale table
    Route::get("customer-bills", [\App\Http\Controllers\PatientController\PatientController::class, "customer_bills"])->name("pos.customer_bills");
    Route::get("customer-bills/list", [\App\Http\Controllers\PatientController\PatientController::class, "customer_bills_list"])->name("pos.customer_bills_list");
    Route::get("customer-bills/export-excel", [\App\Http\Controllers\PatientController\PatientController::class, "customer_bills_export_excel"])->name("pos.customer_bills_export_excel");
    Route::get("customer-bills/ledger", [\App\Http\Controllers\PatientController\PatientController::class, "customer_ledger_report"])->name("pos.customer_ledger_report");
    Route::get("customer-bills/profit-loss", [\App\Http\Controllers\PatientController\PatientController::class, "customer_profit_loss_report"])->name("pos.customer_profit_loss_report");




    Route::get("patient-admission", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "patient_admission"])->name("pos.patient_admission");
    Route::get("list-admission", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "list_admission"])->name("pos.list_admission");
    Route::post("ward-bed", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "ward_bed"])->name("pos.ward_bed");
    Route::post("store-patient-admission", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "store_patient_admission"])->name("pos.store_patient_admission");
    Route::post("admission-cancelation", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "admission_cancelation"])->name("pos.admission_cancelation");
    Route::get("print_patient_admission/{admission_id}", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "print_patient_admission"])->name("pos.print_patient_admission");
    Route::get("print_in_patient_admission/{admission_id}", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "print_in_patient_admission"])->name("pos.print_in_patient_admission");
    Route::get("discharged_patient", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "discharged_patient"])->name("pos.discharged_patient");
    Route::get("updatePatientData/{from_id}/{to_id}", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "updatePatientData"])->name("pos.updatePatientData");
    Route::get("discharged_patient_list", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "discharged_patient_list"])->name("pos.discharged_patient_list");
    Route::post("cencel_patient_admission", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "cencel_patient_admission"])->name("pos.cencel_patient_admission");
    Route::get("list-admission-statistics", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "list_admission_statistics"])->name("pos.list_admission_statistics");

    Route::get("patient-account/{patient_id?}/{admission_id?}", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "patient_account"])->name("pos.patient_account");
    Route::get("get_admission_investigations/{patient_id?}/{admission_id?}", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "get_admission_investigations"])->name("pos.get_admission_investigations");
    Route::post("save_patient_investigation", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "save_patient_investigation"])->name("pos.save_patient_investigation");
    Route::post("save_in_patient_investigation", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "save_in_patient_investigation"])->name("pos.save_in_patient_investigation");

    Route::get("get_admission_service_charges/{patient_id?}/{admission_id?}", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "get_admission_service_charges"])->name("pos.get_admission_service_charges");
    Route::post("save_patient_service_charges", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "save_patient_service_charges"])->name("pos.save_patient_service_charges");
    Route::post("save_in_patient_service_charges", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "save_in_patient_service_charges"])->name("pos.save_in_patient_service_charges");
    Route::get("get_admitted_patient_treatments/{patient_id?}/{admission_id?}", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "get_admitted_patient_treatments"])->name("pos.get_admitted_patient_treatments");

    Route::get('add_procedure_type', [\App\Http\Controllers\GeneralConfigration\GeneralConfigController::class, 'add_procedure_type'])->name('pos.add_procedure_type');
    Route::get('list_procedure_type', [\App\Http\Controllers\GeneralConfigration\GeneralConfigController::class, 'list_procedure_type'])->name('pos.list_procedure_type');
    Route::post('save_procedure_type', [\App\Http\Controllers\GeneralConfigration\GeneralConfigController::class, 'save_procedure_type'])->name('pos.save_procedure_type');

    Route::get('add_investigation_category', [\App\Http\Controllers\GeneralConfigration\InvestigationController::class, 'add_investigation_category'])->name('pos.add_investigation_category');
    Route::get('list_investigation_category', [\App\Http\Controllers\GeneralConfigration\InvestigationController::class, 'list_investigation_category'])->name('pos.list_investigation_category');
    Route::post('save_investigation_category', [\App\Http\Controllers\GeneralConfigration\InvestigationController::class, 'save_investigation_category'])->name('pos.save_investigation_category');

    Route::get('investigation_sub_category', [\App\Http\Controllers\GeneralConfigration\InvestigationController::class, 'add_investigation_sub_category'])->name('pos.add_investigation_sub_category');
    Route::get('list_investigation_sub_category', [\App\Http\Controllers\GeneralConfigration\InvestigationController::class, 'list_investigation_sub_category'])->name('pos.list_investigation_sub_category');
    Route::post('save_investigation_sub_category', [\App\Http\Controllers\GeneralConfigration\InvestigationController::class, 'save_investigation_sub_category'])->name('pos.save_investigation_sub_category');
    Route::post('save_report_heading', [\App\Http\Controllers\GeneralConfigration\InvestigationController::class, 'save_report_heading'])->name('pos.save_report_heading');

    Route::get('investigation_parameter/{id?}', [\App\Http\Controllers\GeneralConfigration\InvestigationController::class, 'add_investigation_parameter'])->name('pos.add_investigation_parameter');
    Route::get('list_investigation_parameter/{id?}', [\App\Http\Controllers\GeneralConfigration\InvestigationController::class, 'list_investigation_parameter'])->name('pos.list_investigation_parameter');
    Route::post('save_investigation_parameter', [\App\Http\Controllers\GeneralConfigration\InvestigationController::class, 'save_investigation_parameter'])->name('pos.save_investigation_parameter');

    Route::get('add_consultant', [\App\Http\Controllers\GeneralConfigration\ConsultantController::class, 'add_consultant'])->name('pos.add_consultant');
    Route::get('list_consultant', [\App\Http\Controllers\GeneralConfigration\ConsultantController::class, 'list_consultant'])->name('pos.list_consultant');
    Route::post('save_consultant', [\App\Http\Controllers\GeneralConfigration\ConsultantController::class, 'save_consultant'])->name('pos.save_consultant');
    Route::get('export_consultants_pdf', [\App\Http\Controllers\GeneralConfigration\ConsultantController::class, 'exportConsultantsPdf'])->name('pos.export_consultants_pdf');
    Route::get('preview_consultants_pdf', [\App\Http\Controllers\GeneralConfigration\ConsultantController::class, 'previewConsultantsPdf'])->name('pos.preview_consultants_pdf');

    Route::get('add_consultant_department', [\App\Http\Controllers\GeneralConfigration\ConsultantController::class, 'add_consultant_department'])->name('pos.add_consultant_department');
    Route::get('list_consultant_department', [\App\Http\Controllers\GeneralConfigration\ConsultantController::class, 'list_consultant_department'])->name('pos.list_consultant_department');
    Route::post('save_consultant_department', [\App\Http\Controllers\GeneralConfigration\ConsultantController::class, 'save_consultant_department'])->name('pos.save_consultant_department');

    Route::get('add_consultant_speciality', [\App\Http\Controllers\GeneralConfigration\ConsultantController::class, 'add_consultant_speciality'])->name('pos.add_consultant_speciality');
    Route::get('list_consultant_speciality', [\App\Http\Controllers\GeneralConfigration\ConsultantController::class, 'list_consultant_speciality'])->name('pos.list_consultant_speciality');
    Route::post('save_consultant_speciality', [\App\Http\Controllers\GeneralConfigration\ConsultantController::class, 'save_consultant_speciality'])->name('pos.save_consultant_speciality');

    Route::get('add_consultant_type', [\App\Http\Controllers\GeneralConfigration\ConsultantController::class, 'add_consultant_type'])->name('pos.add_consultant_type');
    Route::get('list_consultant_type', [\App\Http\Controllers\GeneralConfigration\ConsultantController::class, 'list_consultant_type'])->name('pos.list_consultant_type');
    Route::post('save_consultant_type', [\App\Http\Controllers\GeneralConfigration\ConsultantController::class, 'save_consultant_type'])->name('pos.save_consultant_type');



    Route::get("discharge-patient", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "discharge_patient"])->name("pos.discharge_patient");
    Route::get("list-admited-patients", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "list_admitted_patients"])->name("pos.list_admitted_patients");
    Route::post("save_discharge_checklist", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "save_discharge_checklist"])->name("pos.save_discharge_checklist");
    Route::get("discharge-patient-from-ward/{patient_admission_id?}", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "discharge_patient_from_ward"])->name("pos.discharge_patient_from_ward");

    Route::get("investigation_result",  [\App\Http\Controllers\Investigation\InvestigationResult::class, 'investigation_result'])->name("post.investigation_result");
    Route::get("investigation_list",  [\App\Http\Controllers\Investigation\InvestigationResult::class, 'investigation_result_list'])->name("pos.investigation_result_list");
    Route::get("investigation_completed_list",  [\App\Http\Controllers\Investigation\InvestigationResult::class, 'investigation_completed_list'])->name("pos.investigation_completed_list");
    Route::get("investigation_add_result/{inv_id?}/{inv_cat_id?}",  [\App\Http\Controllers\Investigation\InvestigationResult::class, 'investigation_add_result'])->name("pos.investigation_add_result");
    Route::post("store_inv_result",  [\App\Http\Controllers\Investigation\InvestigationResult::class, 'store_inv_result'])->name("pos.store_inv_result");
    Route::get("print_inv_result/{inv_id}",  [\App\Http\Controllers\Investigation\InvestigationResult::class, 'print_inv_result'])->name("pos.print_inv_result");
    Route::get("delete_investigation_result/{inv_id?}",  [\App\Http\Controllers\Investigation\InvestigationResult::class, 'delete_investigation_result'])->name("pos.delete_investigation_result");


    Route::get("patient_summary", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "patient_summary"])->name("pos.patient_summary");

    Route::get("patient_investigation", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "patient_investigation"])->name("pos.patient_investigation");
    Route::get("patient_service_charges", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "patient_service_charges"])->name("pos.patient_service_charges");

    Route::get("patient_ot_notes", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "ot_notes"])->name("pos.patient_ot_notes");
    Route::post("save_patient_ot_notes", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "save_patient_ot_notes"])->name("pos.save_patient_ot_notes");
    Route::get("get_patient_ot_notes/{patient_id?}/{admission_id?}", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "get_patient_ot_notes"])->name("pos.get_patient_ot_notes");


    Route::get("patient_nurse_notes", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "patient_nurse_notes"])->name("pos.patient_nurse_notes");
    Route::post("save_patient_nurse_notes", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "save_patient_nurse_notes"])->name("pos.save_patient_nurse_notes");
    Route::get("get_patient_nurse_notes/{patient_id?}/{admission_id?}", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "get_patient_nurse_notes"])->name("pos.get_patient_nurse_notes");


    Route::get("patient_refunds", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "patient_refunds"])->name("pos.patient_refunds");
    Route::post("save_patient_refunds", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "save_patient_refunds"])->name("pos.save_patient_refunds");
    Route::get("get_patient_refunds/{patient_id?}/{admission_id?}", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "get_patient_refunds"])->name("pos.get_patient_refunds");
    Route::get("view_patient_summary/{patient_id?}/{admission_id?}", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "view_patient_summary"])->name("pos.view_patient_summary");



    Route::get("print_lab_invoice/{invoice_no?}", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "print_lab_invoice"])->name("pos.print_lab_invoice");
    Route::get("print_hospital_lab_invoice/{invoice_no?}", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "print_hospital_lab_invoice"])->name("pos.print_hospital_lab_invoice");
    Route::post("get-ward-investigations/{ward_id?}", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "get_ward_investigations"])->name("pos.get_ward_investigations");


    Route::get("machine_patient", [\App\Http\Controllers\PatientController\MachinePatientController::class, "machine_patient"])->name("pos.machine_patient");
    Route::get("machine_patient_list", [\App\Http\Controllers\PatientController\MachinePatientController::class, "machine_patient_list"])->name("pos.machine_patient_list");
    Route::post("get_machine_category", [\App\Http\Controllers\PatientController\MachinePatientController::class, "get_machine_category"])->name("post.get_machine_category");
    Route::post("store_machine_patient", [\App\Http\Controllers\PatientController\MachinePatientController::class, "store_machine_patient"])->name("pos.store_machine_patient");


    Route::get("return_product", [\App\Http\Controllers\ProductConfiguration\ReturnProductController::class, "return_product"])->name("pos.return_product");
    Route::get("return_pharmacy_product/{sale_id?}", [\App\Http\Controllers\ProductConfiguration\ReturnProductController::class, "return_pharmacy_product"])->name("pos.return_pharmacy_product");
    Route::get("get_bill_details/{sale_id?}", [\App\Http\Controllers\Admin\SaleController::class, "get_bill_details"])->name("pos.get_bill_details");
    Route::get("get_sale_detail_for_return/{sdid}", [\App\Http\Controllers\Admin\SaleController::class, "get_sale_detail_for_return"])->name("pos.get_sale_detail_for_return");
    Route::get("return_customer_product/{sale_id?}", [\App\Http\Controllers\ProductConfiguration\ReturnProductController::class, "return_customer_product"])->name("pos.return_customer_product");
    


    Route::get("product_kit/{id?}", [\App\Http\Controllers\ProductConfiguration\ProductKitController::class, "product_kit"])->name("pos.product_kit");
    Route::post("product_kit_save", [\App\Http\Controllers\ProductConfiguration\ProductKitController::class, "product_kit_save"])->name("pos.save_product_kit");
    Route::get("product_kit_list/{product_main_id?}", [\App\Http\Controllers\ProductConfiguration\ProductKitController::class, "product_kit_list"])->name("pos.product_kit_list");

    // OPD Type Configuration Routes
    Route::get("opd_types", [\App\Http\Controllers\Admin\OpdTypeController::class, "index"])->name("pos.opd_types");
    Route::get("list_opd_types", [\App\Http\Controllers\Admin\OpdTypeController::class, "list_opd_types"])->name("pos.list_opd_types");
    Route::get("create_opd_type", [\App\Http\Controllers\Admin\OpdTypeController::class, "create"])->name("pos.create_opd_type");
    Route::post("store_opd_type", [\App\Http\Controllers\Admin\OpdTypeController::class, "store"])->name("pos.store_opd_type");
    Route::get("edit_opd_type/{id}", [\App\Http\Controllers\Admin\OpdTypeController::class, "edit"])->name("pos.edit_opd_type");
    Route::post("update_opd_type/{id}", [\App\Http\Controllers\Admin\OpdTypeController::class, "update"])->name("pos.update_opd_type");
    Route::post("delete_opd_type/{id}", [\App\Http\Controllers\Admin\OpdTypeController::class, "destroy"])->name("pos.delete_opd_type");
    Route::get("get_opd_type/{id}", [\App\Http\Controllers\Admin\OpdTypeController::class, "get_opd_type"])->name("pos.get_opd_type");

    Route::get("patient_vitals", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "patient_vitals"])->name("pos.patient_vitals");
    Route::get("patient_vitals_list/{patient_id?}/{admission_id?}", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "patient_vitals_list"])->name("pos.patient_vitals_list");
    Route::post("save_patient_vitals", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "save_patient_vitals"])->name("pos.save_patient_vitals");


    Route::get('get_products', [\App\Http\Controllers\Admin\StockController::class, 'get_products'])->name('pos.get_products');

    Route::post('update_grn', [\App\Http\Controllers\Admin\StockController::class, 'update_grn'])->name('pos.update_grn');
    Route::post('add_item_to_bill', [\App\Http\Controllers\Admin\StockController::class, 'add_item_to_bill'])->name('pos.add_item_to_bill');

    Route::get('delete_item_from_bill/{id?}', [\App\Http\Controllers\Admin\StockController::class, 'delete_item_from_bill'])->name('pos.delete_item_from_bill');

    // Low Sale Price Products Report
    Route::get('product-price-management', [\App\Http\Controllers\Admin\StockController::class, 'low_sale_price_products'])->name('pos.low_sale_price_products');
    Route::post('update-product-price', [\App\Http\Controllers\Admin\StockController::class, 'update_product_price'])->name('pos.update_product_price');

    Route::get('list_grn_request/{id?}', [\App\Http\Controllers\Admin\StockController::class, 'list_grn_request'])->name('pos.list_grn_request');
    Route::post('approve_grn_bill', [\App\Http\Controllers\Admin\StockController::class, 'approve_grn_bill'])->name('pos.approve_grn_bill');
    Route::post('return_item', [\App\Http\Controllers\Admin\SaleController::class, 'return_item'])->name('pos.return_item');
    Route::post('return_pharmacy_item', [\App\Http\Controllers\Admin\SaleController::class, 'return_pharmacy_item'])->name('pos.return_pharmacy_item');
    Route::post('return_customer_parmacy_item', [\App\Http\Controllers\Admin\SaleController::class, 'return_customer_parmacy_item'])->name('pos.return_customer_parmacy_item');

    Route::get("print_admitted_patient_treatment_report/{patient_id?}/{admission_id?}", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "print_admitted_patient_treatment_report"])->name("pos.print_admitted_patient_treatment_report");
    Route::get("patient_treatment_chart_report/{patient_id?}/{admission_id?}/{medicine_type_id?}", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "patient_treatment_chart_report"])->name("pos.patient_treatment_chart_report");
    Route::post("updat-e_patient_admission", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "update_patient_admission"])->name("pos.update_patient_admission");
    Route::post("update_patient_admission_info", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "update_patient_admission_info"])->name("pos.update_patient_admission_info");
    Route::get("canceled_patients", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "canceled_patients"])->name("pos.canceled_patients");
    Route::get("canceled_patient_list", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "canceled_patient_list"])->name("pos.canceled_patient_list");
    Route::get("calculatePatientMedecineAmount/{admission_id?}", [\App\Http\Controllers\PatientController\PatientExpenseController::class, "calculatePatientMedecineAmount"])->name("pos.calculatePatientMedecineAmount");
    Route::get("totalCases/{from_date?}/{to_date?}/{procedure_type_id?}/{consultant_id?}", [\App\Http\Controllers\PatientController\PatientExpenseController::class, "totalCases"])->name("pos.totalCases");
    Route::get("totalCasesD/{from_date?}/{to_date?}/{procedure_type_id?}", [\App\Http\Controllers\PatientController\PatientExpenseController::class, "totalCasesD"])->name("pos.totalCasesD");
    Route::get("birth-certificate/{baby_id?}", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "birth_certificate"])->name("pos.birth_certificate");

    Route::get("patient_baby/{patient_id?}/{admission_id?}", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "patient_baby"])->name("pos.patient_baby");
    Route::get("list_patient_baby/{patient_id?}/{admission_id?}", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "list_patient_baby"])->name("pos.list_patient_baby");
    Route::post("save_baby", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "save_baby"])->name("pos.save_baby");
    Route::get("getMachinePatientReport", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "getMachinePatientReport"])->name("pos.getMachinePatientReport");

    Route::get("list_all_patients", [\App\Http\Controllers\PatientReports\PatientReportController::class, "list_all_patients"])->name("pos.list_all_patients");
    Route::get("patient-admission-report", [\App\Http\Controllers\PatientReports\PatientReportController::class, "patient_admission_report"])->name("pos.patient_admission_report");
    Route::get("print_patient_admission_report/{from_date?}/{to_date?}/{consultant_id?}/{procedure_type_id?}", [\App\Http\Controllers\PatientReports\PatientReportController::class, "print_patient_admission_report"])->name("pos.print_patient_admission_report");
    Route::get("print_sehat_card_claim/{report_type?}", [\App\Http\Controllers\PatientReports\PatientReportController::class, "print_sehat_card_claim"])->name("pos.print_sehat_card_claim");



    Route::get("print_pharmacy_audit_form", [\App\Http\Controllers\Admin\StockController::class, 'print_pharmacy_audit_form'])->name('pos.print_pharmacy_audit_form');
    Route::post("update_audit", [\App\Http\Controllers\Admin\StockController::class, 'update_audit'])->name('pos.update_audit');
    Route::post("store_audit", [\App\Http\Controllers\Admin\StockController::class, 'store_audit'])->name('pos.store_audit');
    Route::get("list_audit", [\App\Http\Controllers\Admin\StockController::class, 'list_audit'])->name('pos.list_audit');
    Route::get("start_pharmacy_audit/{audit_id?}", [\App\Http\Controllers\Admin\StockController::class, 'start_pharmacy_audit'])->name('pos.start_pharmacy_audit');
    Route::post("add_product_to_audit", [\App\Http\Controllers\Admin\StockController::class, 'add_product_to_audit'])->name('pos.add_product_to_audit');

    Route::get("delete_product_from_audit/{id?}", [\App\Http\Controllers\Admin\StockController::class, 'delete_product_from_audit'])->name('pos.delete_product_from_audit');
    Route::get("approve_close_audit/{id?}", [\App\Http\Controllers\Admin\StockController::class, 'approve_close_audit'])->name('pos.approve_close_audit');


    Route::get('add-locations', [\App\Http\Controllers\GeneralConfigration\GeneralConfigController::class, 'add_locations'])->name('pos.add_locations');
    Route::get('list_locations', [\App\Http\Controllers\GeneralConfigration\GeneralConfigController::class, 'list_locations'])->name('pos.list_locations');
    Route::post('save_locations', [\App\Http\Controllers\GeneralConfigration\GeneralConfigController::class, 'save_locations'])->name('pos.save_locations');


    Route::get("generate-doctor-invoice", [\App\Http\Controllers\Accounts\DoctorPaymentsController::class, "index"])->name("pos.generate-doctor-invoice");
    Route::get("load_discharge_patients_for_doctor_payments", [\App\Http\Controllers\Accounts\DoctorPaymentsController::class, "load_discharge_patients_for_doctor_payments"])->name("pos.load_discharge_patients_for_doctor_payments");
    Route::get("generateDoctorPayments/{from_date?}/{to_date?}/{consultant_id?}", [\App\Http\Controllers\Accounts\DoctorPaymentsController::class, "generatePayment"])->name("pos.generatePayment");

    Route::get("doctor-sehat-card-payments", [\App\Http\Controllers\Accounts\DoctorPaymentsController::class, "doctor_sehat_card_payments"])->name("pos.doctor-sehat-card-payments");


    Route::get("doctor-sc-invoices/{id?}", [\App\Http\Controllers\Accounts\DoctorPaymentsController::class, "doctor_sc_invoices"])->name("pos.doctor_sc_invoices");
    Route::get("consultant_sc_balance/{id?}", [\App\Http\Controllers\GeneralConfigration\ConsultantController::class, "consultant_sc_balance"])->name("pos.consultant_sc_balance");
    Route::post("save_sc_payments/{id?}", [\App\Http\Controllers\Accounts\DoctorPaymentsController::class,  "save_sc_payments"])->name("pos.save_sc_payments");
    Route::get("get_sc_payments_to_doctors/{id?}", [\App\Http\Controllers\Accounts\DoctorPaymentsController::class,  "get_sc_payments_to_doctors"])->name("pos.get_sc_payments_to_doctors");
    Route::get("print_doctor_invoice/{id?}", [\App\Http\Controllers\Accounts\DoctorPaymentsController::class,  "print_doctor_invoice"])->name("pos.print_doctor_invoice");


    Route::get('/import-csv', [\App\Http\Controllers\CsvImportController::class, 'showForm'])->name('pos.import_form');
    Route::post('/import-csv', [\App\Http\Controllers\CsvImportController::class, 'import'])->name('pos.import');
    Route::post('/saveSehatCardPayment', [\App\Http\Controllers\CsvImportController::class, 'saveSehatCardPayment'])->name('pos.saveSehatCardPayment');

    //------------------ in patient details   -----------//
    Route::get("view_in_patient_summary/{patient_id?}/{admission_id?}", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "view_in_patient_summary"])->name("pos.view_in_patient_summary");
    Route::get("get_in_admission_investigations/{patient_id?}/{admission_id?}", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "get_in_admission_investigations"])->name("pos.get_in_admission_investigations");
    Route::get("in_patient_treatment_chart_report/{patient_id?}/{admission_id?}/{medicine_type_id?}", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "in_patient_treatment_chart_report"])->name("pos.in_patient_treatment_chart_report");
    Route::post("cancel_in_patient_admission", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "cancel_in_patient_admission"])->name("pos.cancel_in_patient_admission");
    Route::get("in_patient_service_charges", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "in_patient_service_charges"])->name("pos.in_patient_service_charges");




    Route::get('/daily-closing', [\App\Http\Controllers\Finance\FinanceController::class, 'daily_closing'])->name('pos.daily_closing');
    Route::post('/post_daily_closing', [\App\Http\Controllers\Finance\FinanceController::class, 'post_daily_closing'])->name('pos.post_daily_closing');
    Route::get('/balanceReport', [\App\Http\Controllers\Finance\Reports\FinanceReportController::class, 'balanceReport'])->name('pos.balanceReport');
    Route::get('/view_details', [\App\Http\Controllers\Finance\FinanceController::class, 'view_details'])->name('pos.view_details');


    Route::post('/getBalance', [\App\Http\Controllers\Finance\FinanceController::class, 'getBalance'])->name('pos.getBalance');
    Route::get('/cash-payment-voucher', [\App\Http\Controllers\Finance\FinanceController::class, 'cash_payment_voucher'])->name('pos.cash_payment_voucher');
    Route::post('/save_cash_payment_voucher', [\App\Http\Controllers\Finance\FinanceController::class, 'save_cash_payment_voucher'])->name('pos.save_cash_payment_voucher');

    Route::get('/cash-receipt-voucher', [\App\Http\Controllers\Finance\FinanceController::class, 'cash_receipt_voucher'])->name('pos.cash_receipt_voucher');
    Route::post('/save_cash_receipt_voucher', [\App\Http\Controllers\Finance\FinanceController::class, 'save_cash_receipt_voucher'])->name('pos.save_cash_receipt_voucher');
    Route::post('/approve_transaction_entry', [\App\Http\Controllers\Finance\FinanceController::class, 'approve_transaction_entry'])->name('pos.approve_transaction_entry');
    Route::post('delete_transaction_entry', [\App\Http\Controllers\Finance\FinanceController::class, 'delete_transaction_entry'])->name('pos.delete_transaction_entry');

    /* Route::get('/journal-voucher', [\App\Http\Controllers\Finance\FinanceController::class, 'journal_voucher'])->name('pos.journal_voucher');
    Route::post('/save_journal-voucher', [\App\Http\Controllers\Finance\FinanceController::class, 'save_journal_voucher'])->name('pos.save_journal_voucher');*/


    Route::get('journal-voucher', [\App\Http\Controllers\Finance\FinanceController::class, 'create'])->name('pos.journal_voucher');
    Route::post('journal-voucher', [\App\Http\Controllers\Finance\FinanceController::class, 'store'])->name('pos.save_journal_voucher');


    //--------------   in/out patient for hospital  --------------------//

    Route::get("hospital-patient-admission", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "hospital_patient_admission"])->name("pos.hospital_patient_admission");
    Route::get("hospital-list-admission", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "hospital_list_admission"])->name("pos.hospital_list_admission");
    Route::get("in_patient_investigation", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "in_patient_investigation"])->name("pos.in_patient_investigation");

    Route::get("in_patient_payment", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "in_patient_payment"])->name("pos.in_patient_payment");
    Route::get("in_patient_admission_list", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "in_patient_admission_list"])->name("pos.in_patient_admission_list");
    Route::get("primary_discharge/{admission_id?}", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "primary_discharge"])->name("pos.primary_discharge");
    Route::get("print_admitted_patient_medicine_list/{admission_id?}", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "print_admitted_patient_medicine_list"])->name("pos.print_admitted_patient_medicine_list");
    Route::get("in_patient_payments_receiving_bill/{admission_id?}", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "in_patient_payments_receiving_bill"])->name("pos.in_patient_payments_receiving_bill");
    Route::get('in_patient_pharmacy_sale', [\App\Http\Controllers\Admin\SaleController::class, 'in_patient_pharmacy_sale'])->name('pos.in_patient_pharmacy_sale')->middleware('store.selected');
    Route::get('add_consultant_procedure', [\App\Http\Controllers\GeneralConfigration\GeneralConfigController::class, 'add_consultant_procedure'])->name('pos.add_consultant_procedure');
    Route::get('consultant_procedure_pricing/{consultant_procedure_id?}', [\App\Http\Controllers\GeneralConfigration\GeneralConfigController::class, 'consultant_procedure_pricing'])->name('pos.consultant_procedure_pricing');
    Route::get("in_patient_discharged_list", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "in_patient_discharged_list"])->name("pos.in_patient_discharged_list");
    //-----         reports   ------//
    Route::get('/printDailyClosingVoucher/{id}', [\App\Http\Controllers\Finance\Reports\FinanceReportController::class, 'printDailyClosingVoucher'])->name('pos.printDailyClosingVoucher');
    Route::get('/printJournalVoucher/{id}', [\App\Http\Controllers\Finance\Reports\FinanceReportController::class, 'printJournalVoucher'])->name('pos.printJournalVoucher');
    Route::get('/get_user_base_daily_closing_report', [\App\Http\Controllers\Finance\Reports\FinanceReportController::class, 'get_user_base_daily_closing_report'])->name('pos.get_user_base_daily_closing_report');
    Route::get('/get_user_base_daily_closing_report2', [\App\Http\Controllers\Finance\Reports\FinanceReportController::class, 'get_user_base_daily_closing_report2'])->name('pos.get_user_base_daily_closing_report2');
    Route::get('/trail_balance_report', [\App\Http\Controllers\Finance\Reports\FinanceReportController::class, 'trail_balance_report'])->name('pos.trail_balance_report');
    Route::get('/finance_vouchers_report', [\App\Http\Controllers\Finance\Reports\FinanceReportController::class, 'finance_vouchers_report'])->name('pos.finance_vouchers_report');
    Route::get('/finance_ledger_report', [\App\Http\Controllers\Finance\Reports\FinanceReportController::class, 'finance_ledger_report'])->name('pos.finance_ledger_report');
    Route::get('/outstanding_balances_report', [\App\Http\Controllers\Finance\Reports\FinanceReportController::class, 'outstanding_balances_report'])->name('pos.outstanding_balances_report');
    Route::get('/twoLevelBalanceReport', [\App\Http\Controllers\Finance\Reports\FinanceReportController::class, 'twoLevelBalanceReport'])->name('pos.twoLevelBalanceReport');
    Route::get('/profitAndLossReport', [\App\Http\Controllers\Finance\Reports\FinanceReportController::class, 'profitAndLossReport'])->name('pos.profitAndLossReport');
    Route::get('/profitAndLossReport/excel', [\App\Http\Controllers\Finance\Reports\FinanceReportController::class, 'profitAndLossReportExcel'])->name('pos.profitAndLossReportExcel');

    //----------------- end of new menues  ------------//


    Route::post("save_patient_payment", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "save_patient_payment"])->name("pos.save_patient_payment");
    Route::post("calculate_patient_discharge_amount", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "calculate_patient_discharge_amount"])->name("pos.calculate_patient_discharge_amount");
    Route::post("primary_discharge_in_patient", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "primary_discharge_in_patient"])->name("pos.primary_discharge_in_patient");
    Route::get("list-in-admited-patients", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "list_in_admitted_patients"])->name("pos.list_in_admitted_patients");
    Route::post("hospital_store_patient_admission", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "hospital_store_patient_admission"])->name("pos.hospital_store_patient_admission");

    Route::get('list_consultant_procedure', [\App\Http\Controllers\GeneralConfigration\GeneralConfigController::class, 'list_consultant_procedure'])->name('pos.list_consultant_procedure');
    Route::post('save_consultant_procedures', [\App\Http\Controllers\GeneralConfigration\GeneralConfigController::class, 'save_consultant_procedures'])->name('pos.save_consultant_procedures');
    Route::post('save_consultant_procedure_pricing', [\App\Http\Controllers\GeneralConfigration\GeneralConfigController::class, 'save_consultant_procedure_pricing'])->name('pos.save_consultant_procedure_pricing');
    Route::post('get_consultant_procedures', [\App\Http\Controllers\GeneralConfigration\GeneralConfigController::class, 'get_consultant_procedures'])->name('pos.get_consultant_procedures');
    Route::post("update_in_patient_admission", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "update_in_patient_admission"])->name("pos.update_in_patient_admission");
    Route::get("in_patient_discharged_listing", [\App\Http\Controllers\PatientController\PatientAdmissionController::class, "in_patient_discharged_listing"])->name("pos.in_patient_discharged_listing");

    // Finance Head named routes
    Route::get('finance-heads', [App\Http\Controllers\FinanceHeadController::class, 'index'])->name('finance-heads.index');
    Route::post('finance-heads', [App\Http\Controllers\FinanceHeadController::class, 'store'])->name('finance-heads.store');
    Route::get('finance-heads/{finance_head}', [App\Http\Controllers\FinanceHeadController::class, 'show'])->name('finance-heads.show');
    Route::put('finance-heads/{finance_head}', [App\Http\Controllers\FinanceHeadController::class, 'update'])->name('finance-heads.update');
    Route::delete('finance-heads/{finance_head}', [App\Http\Controllers\FinanceHeadController::class, 'destroy'])->name('finance-heads.destroy');

    Route::get('reports', function () {
        return view('reports.dashboard');
    })->name('reports.dashboard');
    Route::get('grn-supplier-report', [\App\Http\Controllers\Reports\GrnReportController::class, 'supplierPurchaseReport'])->name('reports.grn_supplier_report');
    Route::get('grn-supplier-report-pdf', [\App\Http\Controllers\Reports\GrnReportController::class, 'supplierPurchaseReportPdf'])->name('reports.grn_supplier_report_pdf');
    Route::get('inventory-summary', [\App\Http\Controllers\Reports\GrnReportController::class, 'inventorySummary'])->name('reports.inventory_summary');
    Route::get('export-inventory-excel', [\App\Http\Controllers\Reports\GrnReportController::class, 'exportInventoryToExcel'])->name('reports.export_inventory_excel');
    Route::get('export-low-stock-excel', [\App\Http\Controllers\Reports\GrnReportController::class, 'exportLowStockToExcel'])->name('reports.export_low_stock_excel');

    // Procedure Dashboard Routes
    Route::get('procedure-dashboard', [\App\Http\Controllers\Reports\ProcedureDashboardController::class, 'index'])->name('reports.procedure_dashboard');
    Route::get('procedure-dashboard/data', [\App\Http\Controllers\Reports\ProcedureDashboardController::class, 'getProcedureData'])->name('reports.procedure_dashboard.data');
    Route::get('procedure-dashboard/stats', [\App\Http\Controllers\Reports\ProcedureDashboardController::class, 'getDashboardStats'])->name('reports.procedure_dashboard.stats');
    Route::get('procedure-dashboard/pdf', [\App\Http\Controllers\Reports\ProcedureDashboardController::class, 'exportPdf'])->name('reports.procedure_dashboard.pdf');
    Route::get('procedure-dashboard/print', [\App\Http\Controllers\Reports\ProcedureDashboardController::class, 'printProcedures'])->name('reports.procedure_dashboard.print');

    // Analytics Routes
    Route::get('analytics', [\App\Http\Controllers\Reports\AnalyticsController::class, 'index'])->name('reports.analytics');
    Route::get('analytics/sales-chart', [\App\Http\Controllers\Reports\AnalyticsController::class, 'getSalesChart'])->name('analytics.sales_chart');
    Route::get('analytics/product-sales-chart', [\App\Http\Controllers\Reports\AnalyticsController::class, 'getProductSalesChart'])->name('analytics.product_sales_chart');
    Route::get('analytics/monthly-sales-chart', [\App\Http\Controllers\Reports\AnalyticsController::class, 'getMonthlySalesChart'])->name('analytics.monthly_sales_chart');
    Route::get('analytics/sales-stats-chart', [\App\Http\Controllers\Reports\AnalyticsController::class, 'getSalesStatsChart'])->name('analytics.sales_stats_chart');

    Route::get('/sale-dashboard', [\App\Http\Controllers\SaleDashboardController::class, 'index'])->name('sale.dashboard');
    Route::get('/appointment-dashboard', [\App\Http\Controllers\AppointmentDashboardController::class, 'index'])->name('appointment.dashboard');

    Route::get('/appointment-dashboard/day-appointments', [\App\Http\Controllers\AppointmentDashboardController::class, 'dayAppointments'])->name('appointment.dashboard.day');

    // Daily Product Sales Dashboard Routes
    Route::get('daily-product-sales', [\App\Http\Controllers\Reports\DailyProductSalesController::class, 'index'])->name('reports.daily_product_sales');
    Route::get('daily-product-sales/data', [\App\Http\Controllers\Reports\DailyProductSalesController::class, 'getProductSalesData'])->name('reports.daily_product_sales.data');
    Route::get('daily-product-sales/with-cost', [\App\Http\Controllers\Reports\DailyProductSalesController::class, 'getProductSalesWithPurchasePrice'])->name('reports.daily_product_sales.with_cost');
    Route::get('daily-product-sales/chart', [\App\Http\Controllers\Reports\DailyProductSalesController::class, 'getDailySalesChart'])->name('reports.daily_product_sales.chart');
    Route::get('daily-product-sales/top-products', [\App\Http\Controllers\Reports\DailyProductSalesController::class, 'getTopSellingProducts'])->name('reports.daily_product_sales.top_products');
    Route::get('daily-product-sales/statistics', [\App\Http\Controllers\Reports\DailyProductSalesController::class, 'getSalesStatistics'])->name('reports.daily_product_sales.statistics');
    Route::get('daily-product-sales/print', [\App\Http\Controllers\Reports\DailyProductSalesController::class, 'exportPrintableReport'])->name('reports.daily_product_sales.print');
    Route::get('daily-product-sales/export-excel', [\App\Http\Controllers\Reports\DailyProductSalesController::class, 'exportTableExcel'])->name('reports.daily_product_sales.export_excel');
    Route::get('daily-product-sales/export-pdf', [\App\Http\Controllers\Reports\DailyProductSalesController::class, 'exportTablePdf'])->name('reports.daily_product_sales.export_pdf');

    Route::get('sale-purchase-price-compare', [\App\Http\Controllers\Reports\SalePurchasePriceCompareController::class, 'index'])->name('reports.sale_purchase_price_compare');
    Route::get('sale-purchase-price-compare/count', [\App\Http\Controllers\Reports\SalePurchasePriceCompareController::class, 'count'])->name('reports.sale_purchase_price_compare.count');
    Route::post('sale-purchase-price-compare/bulk-update', [\App\Http\Controllers\Reports\SalePurchasePriceCompareController::class, 'bulkUpdateSalePrices'])->name('reports.sale_purchase_price_compare.bulk_update');

    Route::get('stock-purchase-value', [\App\Http\Controllers\Reports\StockPurchaseValueReportController::class, 'index'])->name('reports.stock_purchase_value');

    // New Retail Sale Point Route
    Route::get('retail-sale-point', [\App\Http\Controllers\Admin\SaleController::class, 'retail_sale_point'])->name('pos.retail_sale_point');
});

// Short Expiry Report Routes
Route::middleware(['auth'])->prefix('reports')->group(function () {
    // Short Expiry Report Routes
    Route::get('short-expiry', [\App\Http\Controllers\Pos\ShortExpiryReportController::class, 'index'])->name('reports.short_expiry');
    Route::get('short-expiry-count', [\App\Http\Controllers\Pos\ShortExpiryReportController::class, 'getShortExpiryCount'])->name('reports.short_expiry_count');
    Route::get('short-expiry-data', [\App\Http\Controllers\Pos\ShortExpiryReportController::class, 'getShortExpiryData'])->name('reports.short_expiry_data');
    Route::get('short-expiry-stats', [\App\Http\Controllers\Pos\ShortExpiryReportController::class, 'getShortExpiryStats'])->name('reports.short_expiry_stats');
    Route::get('short-expiry-export', [\App\Http\Controllers\Pos\ShortExpiryReportController::class, 'exportShortExpiry'])->name('reports.short_expiry_export');
});

// HX Complaints Routes - Nursing Counter
Route::middleware(['auth'])->group(function () {
    Route::get('nursing-hx-complaints', [\App\Http\Controllers\Admin\HxComplaintController::class, 'nursing_hx_complaints'])->name('hx.nursing_hx_complaints');
    Route::get('hx/get-today-appointments', [\App\Http\Controllers\Admin\HxComplaintController::class, 'get_today_appointments'])->name('hx.get_today_appointments');
    Route::get('hx/get-appointment-details', [\App\Http\Controllers\Admin\HxComplaintController::class, 'get_appointment_details'])->name('hx.get_appointment_details');
    Route::post('hx/save-hx-complaint', [\App\Http\Controllers\Admin\HxComplaintController::class, 'save_hx_complaint'])->name('hx.save_hx_complaint');
    Route::get('hx/get-hx-complaint', [\App\Http\Controllers\Admin\HxComplaintController::class, 'get_hx_complaint'])->name('hx.get_hx_complaint');
    Route::get('hx/get-list-hx-complaints', [\App\Http\Controllers\Admin\HxComplaintController::class, 'get_list_hx_complaints'])->name('hx.get_list_hx_complaints');
    Route::post('hx/delete-hx-complaint', [\App\Http\Controllers\Admin\HxComplaintController::class, 'delete_hx_complaint'])->name('hx.delete_hx_complaint');
});

// Expiry Date Update Routes
Route::middleware(['auth'])->group(function () {
    Route::get('expiry-update', [\App\Http\Controllers\Admin\ExpiryUpdateController::class, 'expiry_update_view'])->name('expiry.expiry_update_view');
    Route::get('expiry/get-grn-details', [\App\Http\Controllers\Admin\ExpiryUpdateController::class, 'get_grn_details'])->name('expiry.get_grn_details');
    Route::post('expiry/update-expiry-date', [\App\Http\Controllers\Admin\ExpiryUpdateController::class, 'update_expiry_date'])->name('expiry.update_expiry_date');
    Route::get('expiry/print-report', [\App\Http\Controllers\Admin\ExpiryUpdateController::class, 'print_expiry_report'])->name('expiry.print_expiry_report');

    // Return Expired Items Routes
    Route::get('return-expired-items', [\App\Http\Controllers\Admin\ExpiryUpdateController::class, 'return_expired_items_view'])->name('expiry.return_expired_items');
    Route::get('expiry/get-returnable-items', [\App\Http\Controllers\Admin\ExpiryUpdateController::class, 'get_returnable_items'])->name('expiry.get_returnable_items');
    Route::post('expiry/save-return-to-supplier', [\App\Http\Controllers\Admin\ExpiryUpdateController::class, 'save_return_to_supplier'])->name('expiry.save_return_to_supplier');

    // GRN Returns List & Management Routes
    Route::get('grn-returns-list', [\App\Http\Controllers\Admin\ExpiryUpdateController::class, 'grn_returns_list'])->name('expiry.grn_returns_list');
    Route::get('expiry/get-grn-returns-list', [\App\Http\Controllers\Admin\ExpiryUpdateController::class, 'get_grn_returns_list'])->name('expiry.get_grn_returns_list');
    Route::post('expiry/approve-grn-return', [\App\Http\Controllers\Admin\ExpiryUpdateController::class, 'approve_grn_return'])->name('expiry.approve_grn_return');
    Route::post('expiry/reject-grn-return', [\App\Http\Controllers\Admin\ExpiryUpdateController::class, 'reject_grn_return'])->name('expiry.reject_grn_return');
    Route::post('expiry/delete-grn-return', [\App\Http\Controllers\Admin\ExpiryUpdateController::class, 'delete_grn_return'])->name('expiry.delete_grn_return');
    Route::get('expiry/view-grn-return/{id}', [\App\Http\Controllers\Admin\ExpiryUpdateController::class, 'view_grn_return'])->name('expiry.view_grn_return');
    Route::get('expiry/edit-grn-return/{id}', [\App\Http\Controllers\Admin\ExpiryUpdateController::class, 'edit_grn_return'])->name('expiry.edit_grn_return');
    Route::get('expiry/print-grn-return/{id}', [\App\Http\Controllers\Admin\ExpiryUpdateController::class, 'print_grn_return'])->name('expiry.print_grn_return');
    Route::post('expiry/update-grn-return', [\App\Http\Controllers\Admin\ExpiryUpdateController::class, 'update_grn_return'])->name('expiry.update_grn_return');


    Route::get('medicine-types', [\App\Http\Controllers\ProductConfiguration\CategoryController::class, 'add_medicine_types'])->name('pos.add_medicine_types');
    Route::get('list_medicine_types', [\App\Http\Controllers\ProductConfiguration\CategoryController::class, 'list_medicine_types'])->name('pos.list_medicine_types');
    Route::post('save_medicine_types', [\App\Http\Controllers\ProductConfiguration\CategoryController::class, 'save_medicine_types'])->name('pos.save_medicine_types');
    Route::post('delete_medicine_type', [\App\Http\Controllers\ProductConfiguration\CategoryController::class, 'delete_medicine_type'])->name('pos.delete_medicine_type');

});

// Patient History Search Routes
Route::middleware(['auth'])->group(function () {
    Route::get('patient-history-by-mobile', [\App\Http\Controllers\PatientReports\PatientHistoryController::class, 'index'])->name('patient.history_by_mobile');
    Route::post('patient/search-by-mobile', [\App\Http\Controllers\PatientReports\PatientHistoryController::class, 'searchByMobile'])->name('patient.search_by_mobile');
    Route::get('patient/print-history-by-mobile', [\App\Http\Controllers\PatientReports\PatientHistoryController::class, 'printHistoryByMobile'])->name('patient.print_history_by_mobile');
});

// Package Sale Routes
Route::middleware(['auth'])->group(function () {
    Route::get('package-sale', [\App\Http\Controllers\Admin\PackageSaleController::class, 'package_sale'])->name('pos.package_sale');
    Route::post('save_package_sale', [\App\Http\Controllers\Admin\PackageSaleController::class, 'save_package_sale'])->name('pos.save_package_sale');
});

// Package Appointments Routes
Route::middleware(['auth'])->group(function () {
    Route::get('package-appointments', [\App\Http\Controllers\Appointments\PackageAppointmentController::class, 'index'])->name('pos.package_appointments');
    Route::get('get-package-appointments', [\App\Http\Controllers\Appointments\PackageAppointmentController::class, 'get_package_appointments'])->name('pos.get_package_appointments');
    Route::get('get-appointment-details', [\App\Http\Controllers\Appointments\PackageAppointmentController::class, 'get_appointment_details'])->name('pos.get_appointment_details');
});

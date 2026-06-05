<?php

use Illuminate\Support\Facades\Route;


Route::group(['middleware' => 'auth'], function () {
    Route::get('appointment', [\App\Http\Controllers\Appointments\AppointmentController::class, 'appointment'])->name('pos.appointments');
    Route::get('general_patient_investigation', [\App\Http\Controllers\PatientController\PatientInvestigationController::class, 'general_patient_investigation'])->name('pos.general_patient_investigation');
    Route::post('delete_patient_investigation', [\App\Http\Controllers\PatientController\PatientInvestigationController::class, 'delete_patient_investigation'])->name('pos.delete_patient_investigation');

    Route::get("pharmacy_audit", [\App\Http\Controllers\Admin\StockController::class, 'pharmacy_audit'])->name('pos.pharmacy_audit');
    Route::get('print_all_investigations/{from_date?}/{to_date?}/{investigation_sub_category_id?}/{created_by?}', [\App\Http\Controllers\PatientController\PatientInvestigationController::class, 'print_all_investigations'])->name('pos.print_all_investigations');


    Route::get('list_appointments', [\App\Http\Controllers\Appointments\AppointmentController::class, 'list_appointments'])->name('pos.list_appointments');
    Route::get('print_appointment/{id?}', [\App\Http\Controllers\Appointments\AppointmentController::class, 'print_appointment'])->name('pos.print_appointment');
    Route::get('print_package_appointment/{id?}', [\App\Http\Controllers\Appointments\AppointmentController::class, 'print_package_appointment'])->name('pos.print_package_appointment');
    Route::get('print_e_prescription/{id?}', [\App\Http\Controllers\Appointments\AppointmentController::class, 'print_e_prescription'])->name('pos.print_e_prescription');
    Route::get('print_all_appointments/{from_date?}/{to_date?}/{opd_type?}/{consultant_id?}/{user_id?}', [\App\Http\Controllers\Appointments\AppointmentController::class, 'print_all_appointments'])->name('pos.print_all_appointments');

    Route::post('save_appointments', [\App\Http\Controllers\Appointments\AppointmentController::class, 'save_appointments'])->name('pos.save_appointments');
    Route::post('update_appointment', [\App\Http\Controllers\Appointments\AppointmentController::class, 'update_appointment'])->name('pos.update_appointment');
    Route::post('get_opd_investigations', [\App\Http\Controllers\Appointments\AppointmentController::class, 'get_opd_investigations'])->name('pos.get_opd_investigations');
    Route::post('save_patient_investigations', [\App\Http\Controllers\Appointments\AppointmentController::class, 'save_patient_investigations'])->name('pos.save_patient_investigations');

    Route::post('save_general_patient_investigation', [\App\Http\Controllers\PatientController\PatientInvestigationController::class, 'save_general_patient_investigation'])->name('pos.save_general_patient_investigation');
    Route::get('get_list_investigations', [\App\Http\Controllers\PatientController\PatientInvestigationController::class, 'get_list_investigations'])->name('pos.get_list_investigations');


    Route::get('ward_request', [\App\Http\Controllers\PatientController\WardController::class, 'ward_request'])->name('pos.ward_request');
    Route::post('save_ward_request', [\App\Http\Controllers\PatientController\WardController::class, 'save_ward_request'])->name('pos.save_ward_request');
    Route::get('get_list_ward_request', [\App\Http\Controllers\PatientController\WardController::class, 'get_list_ward_request'])->name('pos.get_list_ward_request');
    Route::get('get_list_ward_request_in_pharmacy', [\App\Http\Controllers\PatientController\WardController::class, 'get_list_ward_request_in_pharmacy'])->name('pos.get_list_ward_request_in_pharmacy');
    Route::get('print_ward_request/{id?}', [\App\Http\Controllers\PatientController\WardController::class, 'print_ward_request'])->name('pos.print_ward_request');
    Route::get('view_ward_request/{id?}', [\App\Http\Controllers\PatientController\WardController::class, 'view_ward_request'])->name('pos.view_ward_request');
    Route::post('add_product_to_ward_request', [\App\Http\Controllers\PatientController\WardController::class, 'add_product_to_ward_request'])->name('pos.add_product_to_ward_request');
    Route::post('update_ward_request_items', [\App\Http\Controllers\PatientController\WardController::class, 'update_ward_request_items'])->name('pos.update_ward_request_items');
    Route::get('delete_ward_request_item/{id}', [\App\Http\Controllers\PatientController\WardController::class, 'delete_ward_request_item'])->name('pos.delete_ward_request_item');
    Route::get('sync_patient_data', [\App\Http\Controllers\PatientController\WardController::class, 'sync_patient_data'])->name('pos.sync_patient_data');
});

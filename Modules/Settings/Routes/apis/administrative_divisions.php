<?php

namespace Modules\Settings\Routes;

use Illuminate\Support\Facades\Route;
use Modules\Settings\Http\Controllers\Apis\V1\AdministrativeDivisionsController;

Route::prefix('countries')->group(function () {
    Route::get('/', [AdministrativeDivisionsController::class, 'countries'])->name('api.v1.settings.countries');
    Route::get('/lite/{lite?}', [AdministrativeDivisionsController::class, 'countries'])->name('api.v1.settings.countries.lite')->defaults('lite', 'lite');
    Route::prefix('{id}')->group(function () {
        Route::get('/', [AdministrativeDivisionsController::class, 'country'])->name('api.v1.settings.country');
        Route::get('provinces/{lite?}', [AdministrativeDivisionsController::class, 'countryProvinces'])->name('api.v1.settings.country.provinces');
        Route::get('divisions/{lite?}', [AdministrativeDivisionsController::class, 'countryDivisions'])->name('api.v1.settings.country.divisions');
        Route::get('districts/{lite?}', [AdministrativeDivisionsController::class, 'countryDistricts'])->name('api.v1.settings.country.districts');
        Route::get('tehsils/{lite?}', [AdministrativeDivisionsController::class, 'countryTehsils'])->name('api.v1.settings.country.tehsils');
        Route::get('union_councils/{lite?}', [AdministrativeDivisionsController::class, 'countryUnionCouncils'])->name('api.v1.settings.country.unionCouncils');
    });
});

Route::prefix('provinces')->group(function () {
    Route::get('/', [AdministrativeDivisionsController::class, 'provinces'])->name('api.v1.settings.provinces');
    Route::get('/lite/{lite?}', [AdministrativeDivisionsController::class, 'provinces'])->name('api.v1.settings.provinces.lite')->defaults('lite', 'lite');
    Route::prefix('{id}')->group(function () {
        Route::get('/', [AdministrativeDivisionsController::class, 'province'])->name('api.v1.settings.province');
        Route::get('divisions/{lite?}', [AdministrativeDivisionsController::class, 'provinceDivisions'])->name('api.v1.settings.province.divisions');
        Route::get('districts/{lite?}', [AdministrativeDivisionsController::class, 'provinceDistricts'])->name('api.v1.settings.province.districts');
        Route::get('tehsils/{lite?}', [AdministrativeDivisionsController::class, 'provinceTehsils'])->name('api.v1.settings.province.tehsils');
        Route::get('union_councils/{lite?}', [AdministrativeDivisionsController::class, 'provinceUnionCouncils'])->name('api.v1.settings.province.unionCouncils');
    });
});

Route::prefix('divisions')->group(function () {
    Route::get('/', [AdministrativeDivisionsController::class, 'divisions'])->name('api.v1.settings.divisions');
    Route::get('/lite/{lite?}', [AdministrativeDivisionsController::class, 'divisions'])->name('api.v1.settings.divisions.lite')->defaults('lite', 'lite');
    Route::prefix('{id}')->group(function () {
        Route::get('/', [AdministrativeDivisionsController::class, 'division'])->name('api.v1.settings.division');
        Route::get('districts/{lite?}', [AdministrativeDivisionsController::class, 'divisionDistricts'])->name('api.v1.settings.division.districts');
        Route::get('tehsils/{lite?}', [AdministrativeDivisionsController::class, 'divisionTehsils'])->name('api.v1.settings.division.tehsils');
        Route::get('union_councils/{lite?}', [AdministrativeDivisionsController::class, 'divisionUnionCouncils'])->name('api.v1.settings.division.unionCouncils');
    });
});

Route::prefix('districts')->group(function () {
    Route::get('/', [AdministrativeDivisionsController::class, 'districts'])->name('api.v1.settings.districts');
    Route::get('/lite/{lite?}', [AdministrativeDivisionsController::class, 'districts'])->name('api.v1.settings.districts.lite')->defaults('lite', 'lite');
    Route::prefix('{id}')->group(function () {
        Route::get('/', [AdministrativeDivisionsController::class, 'district'])->name('api.v1.settings.district');
        Route::get('tehsils/{lite?}', [AdministrativeDivisionsController::class, 'districtTehsils'])->name('api.v1.settings.district.tehsils');
        Route::get('union_councils/{lite?}', [AdministrativeDivisionsController::class, 'districtUnionCouncils'])->name('api.v1.settings.district.unionCouncils');
    });
});

Route::prefix('tehsils')->group(function () {
    Route::get('/', [AdministrativeDivisionsController::class, 'tehsils'])->name('api.v1.settings.tehsils');
    Route::get('/lite/{lite?}', [AdministrativeDivisionsController::class, 'tehsils'])->name('api.v1.settings.tehsils.lite')->defaults('lite', 'lite');
    Route::prefix('{id}')->group(function () {
        Route::get('/', [AdministrativeDivisionsController::class, 'tehsil'])->name('api.v1.settings.tehsil');
        Route::get('union_councils/{lite?}', [AdministrativeDivisionsController::class, 'tehsilUnionCouncils'])->name('api.v1.settings.tehsil.unionCouncils');
    });
});

Route::prefix('union_councils')->group(function () {
    Route::get('/', [AdministrativeDivisionsController::class, 'unionCouncils'])->name('api.v1.settings.unionCouncils');
    Route::get('/lite/{lite?}', [AdministrativeDivisionsController::class, 'unionCouncils'])->name('api.v1.settings.unionCouncils.lite')->defaults('lite', 'lite');
    Route::get('/{id}', [AdministrativeDivisionsController::class, 'unionCouncil'])->name('api.v1.settings.unionCouncil');
});
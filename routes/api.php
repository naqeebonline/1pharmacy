<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\CommonActionController;
use App\Http\Controllers\API\LoginController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
/*
| Desktop sync — exempt from default api throttle (60/min) to avoid HTTP 429.
*/
Route::middleware('throttle:sync')->group(function () {
    Route::post('/sync', [\App\Http\Controllers\SyncController::class, 'syncData']);
    Route::get('/sync/pull', [\App\Http\Controllers\SyncController::class, 'pullData']);
    Route::get('/sync/tables', [\App\Http\Controllers\SyncController::class, 'syncTables']);
})->withoutMiddleware(['throttle:api']);
//Route::get('/sendDataToLive', [\App\Http\Controllers\DataSyncController::class, 'sendDataToLive']);
Route::prefix('v1')->group(function (){
    //Route::post('/syncLiveDataFromLocal', [\App\Http\Controllers\DataSyncController::class, 'syncLiveDataFromLocal']);

    Route::post('updatePatient', [\App\Http\Controllers\API\SmsController::class,'updatePatient']);
    Route::post('sendSms', [\App\Http\Controllers\API\SmsController::class,'sendSms']);



    Route::middleware('auth:sanctum')->group(function(){
        Route::get('/user', function (Request $request) {
            return $request->user();
        });


        Route::post('findNearestMobile', [CommonActionController::class,'findNearestMobile']);
    });//---- end of auth sanctum


    Route::get('sensitivity/{id?}', [\App\Http\Controllers\CommonApiController::class,'sensitivity']);
    Route::get('facilityType/{id?}', [\App\Http\Controllers\CommonApiController::class,'facilityType']);
    Route::get('policeStations/{id?}', [\App\Http\Controllers\CommonApiController::class,'policeStations']);
    Route::get('districts/{id?}', [\App\Http\Controllers\CommonApiController::class,'districts']);
    Route::get('getVehicleType/{id?}', [\App\Http\Controllers\CommonApiController::class,'getVehicleType']);

    Route::post('circles/{id?}', [\App\Http\Controllers\CommonApiController::class,'circles']);
    Route::get('rank', [\App\Http\Controllers\CommonApiController::class,'getRank']);
    Route::get('tehsils/{district_id?}/{tehsil_id?}', [\App\Http\Controllers\CommonApiController::class,'tehsils']);
    Route::post('user-login', [LoginController::class,'eGovUserlogin']);



});




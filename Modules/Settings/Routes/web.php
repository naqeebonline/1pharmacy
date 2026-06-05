<?php

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
use Modules\Settings\Http\Controllers\MyRolesController;
use Modules\Settings\Http\Controllers\MyPermissionsController;
use Modules\Settings\Http\Controllers\MyAppsController;
use Modules\Settings\Http\Controllers\MenusController;
use Modules\Settings\Http\Controllers\CompaniesController;
use Modules\Settings\Http\Controllers\CompanyTypesController;
use Modules\Settings\Http\Controllers\SectionsController;
use Modules\Settings\Http\Controllers\UsersController;

Route::prefix('settings')->middleware(['auth'])->group(function() {

   // Route::get('/', [\App\Http\Controllers\HomeController::class,'dashboard'])->name('settings.home');
    //Route::get('/', [MyAppsController::class,'index'])->name('settings.home');
    Route::get('/', [App\Http\Controllers\Dashboard\DashboardController::class, 'retailPharmacyDashboard'])->name('settings.home');
    
    Route::prefix('my-apps')->group(function (){
        Route::controller(MyAppsController::class)->group(function (){
            Route::get('/', 'index')->name('settings.my-apps.list')->middleware('permission:apps.view');

            Route::group(['middleware' => ['permission:apps.create,apps.view,apps.store']], function(){
                Route::get('create', 'create')->name('settings.my-apps.create');
                Route::post('store', 'store')->name('settings.my-apps.store');
            });

            Route::group(['middleware' => ['permission:apps.edit']], function(){
                Route::get('edit/{id}', 'edit')->name('settings.my-apps.edit');
                Route::put('update/{id}', 'update')->name('settings.my-apps.update');
            });

            Route::delete('delete/{id}', 'destroy')->name('settings.my-apps.delete')->middleware('permission:apps.delete');
        });
    });


    Route::prefix('menus')->group(function (){
        Route::controller(MenusController::class)->group(function (){
            Route::get('/', 'index')->name('settings.menus.list');
            Route::get('create', 'create')->name('settings.menus.create');
            Route::post('store', 'store')->name('settings.menus.store');
            Route::get('edit/{id}', 'edit')->name('settings.menus.edit');
            Route::put('update/{id}', 'update')->name('settings.menus.update');
            Route::delete('delete/{id}', 'destroy')->name('settings.menus.delete');
            Route::post('menus-by-app-id', 'menuByAppId')->name('settings.menus.menus-by-app-id');
        });
    });


    Route::prefix('my-permissions')->group(function (){
        Route::controller(MyPermissionsController::class)->group(function (){
            Route::get('/', 'index')->name('settings.my-permissions.list');
            Route::get('create', 'create')->name('settings.my-permissions.create');
            Route::post('store', 'store')->name('settings.my-permissions.store');
            Route::get('edit/{id}', 'edit')->name('settings.my-permissions.edit');
            Route::put('update/{id}', 'update')->name('settings.my-permissions.update');
            Route::delete('delete/{id}', 'destroy')->name('settings.my-permissions.delete');
        });
    });


    Route::prefix('my-roles')->group(function (){
        Route::controller(MyRolesController::class)->group(function (){
            Route::get('/', 'index')->name('settings.my-roles.list');
            Route::get('role-permissions/{id}', 'show')->name('settings.my-roles.show');
            Route::post('role-permissions-save/{id}', 'rolePermissionsSave')->name('settings.my-roles.role-permissions-save');
            Route::get('create', 'create')->name('settings.my-roles.create');
            Route::post('store', 'store')->name('settings.my-roles.store');
            Route::get('edit/{id}', 'edit')->name('settings.my-roles.edit');
            Route::put('update/{id}', 'update')->name('settings.my-roles.update');
            Route::delete('delete/{id}', 'destroy')->name('settings.my-roles.delete');
        });
    });


    Route::prefix('companies')->group(function (){
        Route::controller(CompaniesController::class)->group(function (){
            Route::get('/', 'index')->name('settings.companies.list');
            Route::get('create', 'create')->name('settings.companies.create');
            Route::post('store', 'store')->name('settings.companies.store');
            Route::get('edit/{id}', 'edit')->name('settings.companies.edit');
            Route::put('update/{id}', 'update')->name('settings.companies.update');
            Route::delete('delete/{id}', 'destroy')->name('settings.companies.delete');
        });
    });


    Route::prefix('company-types')->group(function (){
        Route::controller(CompanyTypesController::class)->group(function (){
            Route::get('/', 'index')->name('settings.company-types.list');
            Route::get('create', 'create')->name('settings.company-types.create');
            Route::post('store', 'store')->name('settings.company-types.store');
            Route::get('edit/{id}', 'edit')->name('settings.company-types.edit');
            Route::put('update/{id}', 'update')->name('settings.company-types.update');
            Route::delete('delete/{id}', 'destroy')->name('settings.company-types.delete');
        });
    });


    Route::prefix('sections')->group(function (){
        Route::controller(SectionsController::class)->group(function (){
            Route::get('/', 'index')->name('settings.sections.list');
            Route::get('create', 'create')->name('settings.sections.create');
            Route::post('store', 'store')->name('settings.sections.store');
            Route::get('edit/{id}', 'edit')->name('settings.sections.edit');
            Route::put('update/{id}', 'update')->name('settings.sections.update');
            Route::delete('delete/{id}', 'destroy')->name('settings.sections.delete');
        });
    });


    Route::prefix('users-mgt')->group(function (){
        Route::controller(UsersController::class)->group(function (){
            Route::get('/', 'index')
                ->name('settings.users-mgt.list')
                ->middleware(['permission:users.mgt.list']);

            Route::get('all-user-datatable', 'allUsers')
                ->name('all.user.datatable')
                ->middleware(['permission:users.mgt.list']);

            Route::prefix('user-permissions')->group(function (){
                Route::get('/{id}', 'show')->name('settings.users-mgt.show');
                Route::post('/{id}', 'userPermissionsSave')->name('settings.users-mgt.user-permissions-save');
            })->middleware(['permission:users.mgt.assign.permissions']);

            Route::get('create', 'create')
                ->name('settings.users-mgt.create')
                ->middleware(['permission:users.mgt.create']);

            Route::post('store', 'store')
                ->name('settings.users-mgt.store')
                ->middleware(['permission:users.mgt.create']);

            Route::get('edit/{id}', 'edit')->name('settings.users-mgt.edit') ->middleware('permission:users.mgt.edit');;
            Route::put('update/{id}', 'update')
                ->name('settings.users-mgt.update')
                ->middleware(['permission:users.mgt.edit']);

            Route::delete('delete/{id}', 'destroy')->name('settings.users-mgt.delete')
                ->middleware(['permission:users.mgt.delete']);

            Route::get('my-profile', 'myProfile')->name('settings.users-mgt.my-profile');
            Route::post('my-profile-save', 'myProfileAct')
                ->name('settings.users-mgt.my-profile-save')
                ->middleware(['permission:users.mgt.delete']);

            Route::get('change-password', 'changePassword')
                ->name('settings.users-mgt.change-password')
                ->middleware(['permission:users.mgt.delete']);
            Route::post('change-password-save', 'changePasswordAct')
                ->name('settings.users-mgt.change-password-save')
                ->middleware(['permission:users.mgt.delete']);
        });
    });


});

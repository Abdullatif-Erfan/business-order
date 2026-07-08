<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RoleController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Home\HomeController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\RateController;




// Route::get('/', function () {
//     return 'Laravel is working!';
// });

// Route::get('/', function () {
//     return view('welcome'); // Or return a response
// });

/**
 * Create a demo user by visiting this route
 * http://127.0.0.1:8000/createUser
 */


Route::get('set-locale/{locale}', [LocaleController::class, 'setLocale'])->name('set-locale');

Route::get('/createUser',[UserController::class,'createUser'])->name('createUser');

Route::get('/',[LoginController::class,'login'])->name('login');
Route::post('/loginMe',[LoginController::class,'loginMe'])->name('loginMe');

Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('login.logout',[LoginController::class,'logout'])->name('login.logout');
    Route::get('login/relogin/{id}',[LoginController::class,'relogin'])->name('login.relogin')->middleware('access:users,list');
    Route::post('login/changeBranch',[LoginController::class,'changeBranch'])->name('login.changeBranch');


    Route::prefix('roles')->group(function() {
        Route::get('/', [RoleController::class, 'index'])->name('roles.index')->middleware('access:users,list');
        Route::get('/data', [RoleController::class, 'getData'])->name('roles.data'); 
        Route::get('/create', [RoleController::class, 'create'])->name('roles.create')->middleware('access:users,create_records');
        Route::post('/store', [RoleController::class, 'store'])->name('roles.store'); 
        Route::get('/edit/{roleId}', [RoleController::class, 'edit'])->name('roles.edit')->middleware('access:users,edit_records');
        Route::patch('/update/{roleId}', [RoleController::class, 'update'])->name('roles.update')->middleware('access:users,edit_records');
        Route::get('/destroy/{roleId}', [RoleController::class, 'destroy'])->name('roles.destroy')->middleware('access:users,delete_records');

        // permissions
        Route::get('/permissions/{roleId}',[RoleController::class, 'permissions'])->name('roles.permissions')->middleware('access:users,list');
        Route::post('/permissions/store_permission', [RoleController::class, 'store_permission'])->name('roles.permissions.store_permission');
    });


    Route::prefix('user')->group(function(){
        Route::get('/',[UserController::class, 'index'])->name('user.index')->middleware('access:users,list');
        Route::get('/data',[UserController::class, 'getData'])->name('user.data');
        Route::get('/create',[UserController::class, 'create'])->name('user.create')->middleware('access:users,create_records');
        Route::get('/edit/{id}',[UserController::class, 'edit'])->name('user.edit')->middleware('access:users,edit_records');
        Route::post('/store',[UserController::class, 'store'])->name('user.store');
        Route::patch('/update/{id}',[UserController::class, 'update'])->name('user.update')->middleware('access:users,edit_records');
        Route::get('/delete/{id}',[UserController::class, 'delete'])->name('user.delete')->middleware('access:users,delete_records');
    });


    Route::prefix('home')->group(function () {
        Route::get('/',[HomeController::class,'index'])->name('home');
        Route::get('/orders', [HomeController::class, 'getDashboardOrders'])->name('home.orders');
        Route::get('/bought', [HomeController::class, 'getDashboardBoughts'])->name('home.bought');
        Route::get('/sales', [HomeController::class, 'getDashboardSales'])->name('home.sales');


        Route::post('/search',[HomeController::class,'index'])->name('home.search');
        Route::post('/currencyConverter',[HomeController::class,'currencyConverter'])->name('home.currencyConverter');
        Route::post('/getBalance',[HomeController::class,'getBalance'])->name('home.getBalance');
        Route::get('/cleanAll',[HomeController::class,'cleanAll'])->name('home.cleanAll')->middleware('access:users,list');
        Route::post('warehouse_item_notify_amount', [HomeController::class, 'warehouseItemNotifyAmount'])->name('home.warehouse_item_notify_amount');
        Route::post('expired_date_notify_amount',[HomeController::class, 'expiredDateNotifyAmount'])->name('home.expired_date_notify_amount');
        Route::post('warehouse_item_list',[HomeController::class,'warehouseItemList'])->name('home.warehouse_item_list');
        Route::post('get_expire_date_list',[HomeController::class,'expiredWarehouseItems'])->name('home.get_expire_date_list');
    });


    // backup
    Route::prefix('backups')->group(function(){
        Route::get('/', [BackupController::class, 'index'])->name('backups.index');
        Route::get('/data', [BackupController::class, 'getData'])->name('backups.data');
        Route::post('/', [BackupController::class, 'createBackup'])->name('backups.create');
        Route::post('/restore/{id}', [BackupController::class, 'restoreBackup'])->name('backups.restore');
        Route::get('/download/{id}', [BackupController::class, 'download'])->name('backups.download');
        Route::delete('/destroy/{id}', [BackupController::class, 'deleteBackup'])->name('backups.destroy');
    });
    
    // Rate
    Route::prefix('rate')->group(function(){
        Route::get('/', [RateController::class, 'index'])->name('rate.index')->middleware('access:rates,list');
        Route::get('/data', [RateController::class, 'getData'])->name('rate.data');
        Route::get('/create', [RateController::class, 'create'])->name('rate.create')->middleware('access:rates,create_records');
        Route::post('/store', [RateController::class, 'store'])->name('rate.store');
        Route::get('/edit/{id}', [RateController::class, 'edit'])->name('rate.edit')->middleware('access:rates,edit_records');
        Route::put('/update', [RateController::class, 'update'])->name('rate.update')->middleware('access:rates,edit_records');
        Route::delete('/destroy/{id}', [RateController::class, 'destroy'])->name('rate.destroy')->middleware('access:rates,delete_records');
    });

    // setting
    require __DIR__ . '/v1/setting.php';
   
    // order
    require __DIR__ . '/v1/order.php';

    // buy
    require __DIR__ . '/v1/buy.php';
 
    // sales
    require __DIR__ . '/v1/sales.php';

     // warehouse
     require __DIR__ . '/v1/warehouse.php';


    // clearance
    require __DIR__ . '/v1/clearance.php';
    

    // transaction
    require __DIR__ . '/v1/transaction.php';

    // reports
    require __DIR__ . '/v1/reports.php';
    
     // hr
     require __DIR__ . '/v1/hr.php';

     // production
     require __DIR__ . '/v1/production.php';


   
});
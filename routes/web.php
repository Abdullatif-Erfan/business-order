<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RoleController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Home\HomeController;

use App\Http\Controllers\Warehouse\WarehouseListController;
use App\Http\Controllers\Sales\SalesController;

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
Route::get('/createUser',[UserController::class,'createUser'])->name('createUser');



Route::get('/',[LoginController::class,'login'])->name('login');
Route::post('/loginMe',[LoginController::class,'loginMe'])->name('loginMe');

Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('login.logout',[LoginController::class,'logout'])->name('login.logout');
    Route::get('login/relogin/{id}',[LoginController::class,'relogin'])->name('login.relogin');

    Route::prefix('roles')->group(function() {
        Route::get('/', [RoleController::class, 'index'])->name('roles.index'); 
        Route::get('/data', [RoleController::class, 'getData'])->name('roles.data'); 
        Route::get('/create', [RoleController::class, 'create'])->name('roles.create'); 
        Route::post('/store', [RoleController::class, 'store'])->name('roles.store'); 
        Route::get('/edit/{roleId}', [RoleController::class, 'edit'])->name('roles.edit'); 
        Route::patch('/update/{roleId}', [RoleController::class, 'update'])->name('roles.update');
        Route::get('/destroy/{roleId}', [RoleController::class, 'destroy'])->name('roles.destroy'); 

        // permissions
        Route::get('/permissions/{roleId}',[RoleController::class, 'permissions'])->name('roles.permissions');
        Route::post('/permissions/store_permission', [RoleController::class, 'store_permission'])->name('roles.permissions.store_permission');
    });


    Route::prefix('user')->group(function(){
        Route::get('/',[UserController::class, 'index'])->name('user.index');
        Route::get('/data',[UserController::class, 'getData'])->name('user.data');
        Route::get('/create',[UserController::class, 'create'])->name('user.create');
        Route::get('/edit/{id}',[UserController::class, 'edit'])->name('user.edit');
        Route::post('/store',[UserController::class, 'store'])->name('user.store');
        Route::patch('/update/{id}',[UserController::class, 'update'])->name('user.update');
        Route::get('/delete/{id}',[UserController::class, 'delete'])->name('user.delete');
    });


    Route::prefix('home')->group(function () {
        Route::get('/',[HomeController::class,'index'])->name('home');
        Route::post('/search',[HomeController::class,'index'])->name('home.search');
        Route::post('warehouse_item_notify_amount', [HomeController::class, 'warehouseItemNotifyAmount'])->name('home.warehouse_item_notify_amount');
        Route::post('expired_date_notify_amount',[HomeController::class, 'expiredDateNotifyAmount'])->name('home.expired_date_notify_amount');
        Route::post('warehouse_item_list',[HomeController::class,'warehouseItemList'])->name('home.warehouse_item_list');
        Route::post('get_expire_date_list',[HomeController::class,'expiredWarehouseItems'])->name('home.get_expire_date_list');
    });


     // WarehouseList
     Route::prefix('warehousesList')->group(function(){
        Route::get('/', [WarehouseListController::class, 'index'])->name('warehousesList.index');
        Route::get('/data', [WarehouseListController::class, 'getData'])->name('warehousesList.data');
        Route::get('/details/{id}', [WarehouseListController::class, 'details'])->name('warehousesList.details');
        Route::patch('/update', [WarehouseListController::class, 'update'])->name('warehousesList.update');
        Route::get('/getWarehouseItemForTransfer/{id}', [WarehouseListController::class, 'getWarehouseItemForTransfer'])->name('warehousesList.getWarehouseItemForTransfer');
        Route::post('/updateTransfer', [WarehouseListController::class, 'updateTransfer'])->name('warehousesList.updateTransfer');
        Route::get('/create', [WarehouseListController::class, 'create'])->name('warehousesList.create');
        Route::post('/store', [WarehouseListController::class, 'store'])->name('warehousesList.store');
        Route::delete('/delete/{id}', [WarehouseListController::class, 'destroy'])->name('warehousesList.delete');
    });

   

    // Sales
    Route::prefix('sales')->group(function(){
        Route::get('/',[SalesController::class,'index'])->name('sales.index');
        Route::get('/data',[SalesController::class,'getData'])->name('sales.data');
        Route::get('/create',[SalesController::class,'create'])->name('sales.create');
        Route::post('/store',[SalesController::class,'store'])->name('sales.store');
        Route::get('/details/{billno}',[SalesController::class,'details'])->name('sales.details');
        Route::get('/edit/{billno}',[SalesController::class,'edit'])->name('sales.edit');
        Route::get('/getSingleRecordForEdit/{id}',[SalesController::class,'getSingleRecordForEdit'])->name('sales.getSingleRecordForEdit');
        Route::post('/updateSalesAndWarehouseItems',[SalesController::class, 'updateSalesAndWarehouseItems'])->name('sales.updateSalesAndWarehouseItems');
        Route::post('/update',[SalesController::class,'update'])->name('sales.update');
        Route::post('/deleteSingleItem/{id}',[SalesController::class,'deleteSingleItem'])->name('sales.deleteSingleItem');
        Route::get('/destroy/{times}',[SalesController::class,'destroy'])->name('sales.destroy');
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
        Route::get('/', [RateController::class, 'index'])->name('rate.index');
        Route::get('/data', [RateController::class, 'getData'])->name('rate.data');
        Route::get('/create', [RateController::class, 'create'])->name('rate.create');
        Route::post('/store', [RateController::class, 'store'])->name('rate.store');
        Route::get('/edit/{id}', [RateController::class, 'edit'])->name('rate.edit');
        Route::put('/update', [RateController::class, 'update'])->name('rate.update');
        Route::delete('/destroy/{id}', [RateController::class, 'destroy'])->name('rate.destroy');
    });

    // setting
    require __DIR__ . '/v1/setting.php';
   
    // buy
    require __DIR__ . '/v1/buy.php';
 
    // clearance
    require __DIR__ . '/v1/clearance.php';
    

    // transaction
    require __DIR__ . '/v1/transaction.php';

    // reports
    require __DIR__ . '/v1/reports.php';
    
     // hr
     require __DIR__ . '/v1/hr.php';

   
});
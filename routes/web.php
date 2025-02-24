<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RoleController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Home\HomeController;

use App\Http\Controllers\Journal\JournalController;

use App\Http\Controllers\Warehouse\WarehouseListController;
use App\Http\Controllers\Sales\SalesController;



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
        Route::get('/',[HomeController::class,'index'])->name('home.index');
        Route::post('warehouse_item_notify_amount', [HomeController::class, 'warehouseItemNotifyAmount'])->name('home.warehouse_item_notify_amount');
        Route::post('expired_date_notify_amount',[HomeController::class, 'expiredDateNotifyAmount'])->name('home.expired_date_notify_amount');
        Route::post('warehouse_item_list',[HomeController::class,'warehouseItemList'])->name('home.warehouse_item_list');
        Route::post('get_expire_date_list',[HomeController::class,'expiredWarehouseItems'])->name('home.get_expire_date_list');
    });

    

    // setting
    require __DIR__ . '/v1/setting.php';
   
    // buy
    require __DIR__ . '/v1/buy.php';
 

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

    // Journal
    Route::prefix('journal')->group(function(){
        Route::get('/',[JournalController::class, 'index'])->name('journal.index');
        Route::get('/data', [JournalController::class, 'getData'])->name('journal.data');
        Route::get('/create',[JournalController::class, 'create'])->name('journal.create');
        Route::post('/store', [JournalController::class, 'store'])->name('journal.store');
        Route::get('/details/{times}', [JournalController::class, 'details'])->name('journal.details');
        Route::patch('/update', [JournalController::class, 'update'])->name('journal.update');
        Route::patch('/update_document', [JournalController::class, 'update_document'])->name('journal.update_document');
        Route::get('/print/{times}', [JournalController::class, 'print'])->name('journal.print');
        Route::get('/edit/{times}', [JournalController::class, 'edit'])->name('journal.edit');
        Route::delete('/destroy/{times}', [JournalController::class, 'destroy'])->name('journal.destroy');
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
        Route::get('/destroy/{id}',[SalesController::class,'destroy'])->name('sales.destroy');
    });



    
});
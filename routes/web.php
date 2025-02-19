<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RoleController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Home\HomeController;
use App\Http\Controllers\Setting\SettingController;
use App\Http\Controllers\Setting\BranchController;
use App\Http\Controllers\Setting\WarehouseController;
use App\Http\Controllers\Setting\UnitController;
use App\Http\Controllers\Setting\CurrencyController;
use App\Http\Controllers\Setting\AccountController;
use App\Http\Controllers\Journal\JournalController;
use App\Http\Controllers\Buy\BuyPreListController;
use App\Http\Controllers\Buy\BoughtController;
use App\Http\Controllers\Buy\BoughtDetailsController;
use App\Http\Controllers\Warehouse\WarehouseListController;






// Route::get('/', function () {
//     return 'Laravel is working!';
// });

// Route::get('/', function () {
//     return view('welcome'); // Or return a response
// });

Route::get('/',[LoginController::class,'login'])->name('login');
Route::post('/loginMe',[LoginController::class,'loginMe'])->name('loginMe');
/**
 * Create a demo user by visiting this route
 * http://127.0.0.1:8000/createUser
 */
Route::get('/createUser',[UserController::class,'createUser'])->name('createUser');


Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/role',[RoleController::class,'index'])->name('role');
    Route::get('/home',[HomeController::class,'index'])->name('home');
    Route::prefix('home')->group(function () {
        Route::post('warehouse_item_notify_amount', [HomeController::class, 'warehouseItemNotifyAmount'])->name('home.warehouse_item_notify_amount');
        Route::post('expired_date_notify_amount',[HomeController::class, 'expiredDateNotifyAmount'])->name('home.expired_date_notify_amount');
        Route::post('warehouse_item_list',[HomeController::class,'warehouseItemList'])->name('home.warehouse_item_list');
        Route::post('get_expire_date_list',[HomeController::class,'expiredWarehouseItems'])->name('home.get_expire_date_list');
    });

    Route::post('login.logout',[LoginController::class,'logout'])->name('login.logout');

    // setting
    Route::get('setting',[SettingController::class,'index'])->name('setting');

    // Branch
    Route::get('/branches', [BranchController::class, 'index'])->name('branches.list');
    Route::post('/branches', [BranchController::class, 'store'])->name('branches');
    Route::get('/branches/{id}', [BranchController::class, 'show'])->name('branches.show');
    Route::patch('/branches/{id}', [BranchController::class, 'update'])->name('branches.update'); 
    Route::delete('/branches/{id}', [BranchController::class, 'destroy'])->name('branches.destroy');

    // Warehouse
    Route::get('/warehouses', [WarehouseController::class, 'index'])->name('warehouses.index');
    Route::get('/warehouses/create', [WarehouseController::class, 'create'])->name('warehouses.create');
    Route::post('/warehouses/store', [WarehouseController::class, 'store'])->name('warehouses.store');
    Route::get('/warehouses/{warehouse}', [WarehouseController::class, 'show'])->name('warehouses.show');
    Route::patch('/warehouses/update', [WarehouseController::class, 'update'])->name('warehouses.update');
    Route::delete('/warehouses/{id}', [WarehouseController::class, 'destroy'])->name('warehouses.destroy');

    // WarehouseList
    // Route::get('/warehousesList/{id?}', [WarehouseListController::class, 'index'])->name('warehousesList.index');
    Route::get('/warehousesList', [WarehouseListController::class, 'index'])->name('warehousesList.index');
    Route::get('/warehousesList/data', [WarehouseListController::class, 'getData'])->name('warehousesList.data');
    Route::get('/warehousesList/details/{id}', [WarehouseListController::class, 'details'])->name('warehousesList.details');
    Route::patch('/warehousesList/update', [WarehouseListController::class, 'update'])->name('warehousesList.update');
    Route::get('/warehousesList/getWarehouseItemForTransfer/{id}', [WarehouseListController::class, 'getWarehouseItemForTransfer'])->name('warehousesList.getWarehouseItemForTransfer');
    Route::post('/warehousesList/updateTransfer', [WarehouseListController::class, 'updateTransfer'])->name('warehousesList.updateTransfer');
    Route::get('/warehousesList/create', [WarehouseListController::class, 'create'])->name('warehousesList.create');
    Route::post('/warehousesList/store', [WarehouseListController::class, 'store'])->name('warehousesList.store');
    Route::delete('/warehousesList/delete/{id}', [WarehouseListController::class, 'destroy'])->name('warehousesList.delete');




    // unit
    Route::get('/units', [UnitController::class, 'index'])->name('units.list');
    Route::get('/units/create', [UnitController::class, 'create'])->name('units.create');
    Route::post('/units/store', [UnitController::class, 'store'])->name('units.store');
    Route::get('/units/{id}', [UnitController::class, 'show'])->name('units.show');
    Route::patch('/units/update', [UnitController::class, 'update'])->name('units.update'); 
    Route::delete('/units/{id}', [UnitController::class, 'destroy'])->name('units.destroy');

    // Currency
    Route::get('/currency', [CurrencyController::class, 'index'])->name('currency.list');
    Route::get('/currency/create', [CurrencyController::class, 'create'])->name('currency.create');
    Route::post('/currency/store', [CurrencyController::class, 'store'])->name('currency.store');
    Route::get('/currency/{id}', [CurrencyController::class, 'show'])->name('currency.show');
    Route::patch('/currency/update', [CurrencyController::class, 'update'])->name('currency.update'); 
    Route::delete('/currency/{id}', [CurrencyController::class, 'destroy'])->name('currency.destroy');

    // Account
    Route::get('/account', [AccountController::class, 'index'])->name('account.list');
    Route::get('/account/create', [AccountController::class, 'create'])->name('account.create');
    Route::post('/account/store', [AccountController::class, 'store'])->name('account.store');
    Route::get('/account/{id}', [AccountController::class, 'edit'])->name('account.edit');
    Route::get('/account/show/{id}', [AccountController::class, 'show'])->name('account.show');
    Route::patch('/account/update', [AccountController::class, 'update'])->name('account.update'); 
    Route::delete('/account/{id}', [AccountController::class, 'destroy'])->name('account.destroy');

    // Journal
    Route::get('/journal',[JournalController::class, 'index'])->name('journal.index');
    Route::get('/journal/data', [JournalController::class, 'getData'])->name('journal.data');
    Route::get('/journal/create',[JournalController::class, 'create'])->name('journal.create');
    Route::post('/journal/store', [JournalController::class, 'store'])->name('journal.store');
    Route::get('/journal/details/{times}', [JournalController::class, 'details'])->name('journal.details');
    Route::patch('/journal/update', [JournalController::class, 'update'])->name('journal.update');
    Route::patch('/journal/update_document', [JournalController::class, 'update_document'])->name('journal.update_document');
    Route::get('/journal/print/{times}', [JournalController::class, 'print'])->name('journal.print');
    Route::get('/journal/edit/{times}', [JournalController::class, 'edit'])->name('journal.edit');
    Route::delete('/journal/destroy/{times}', [JournalController::class, 'destroy'])->name('journal.destroy');


    // BuyPreList 
    Route::get('/buyprelist',[BuyPreListController::class, 'index'])->name('buyprelist.index');
    Route::get('/buyprelist/data',[BuyPreListController::class, 'getData'])->name('buyprelist.data');
    Route::get('/buyprelist/{id}',[BuyPreListController::class, 'show'])->name('buyprelist.show');
    Route::post('/buyprelist/store',[BuyPreListController::class, 'store'])->name('buyprelist.store');
    Route::patch('/buyprelist/update',[BuyPreListController::class, 'update'])->name('buyprelist.update');
    Route::delete('/buyprelist/destroy/{id}',[BuyPreListController::class, 'destroy'])->name('buyprelist.destroy');

    // bought
    Route::get('/bought',[BoughtController::class,'index'])->name('bought.index');
    Route::get('/bought/data',[BoughtController::class,'getData'])->name('bought.data');
    Route::get('/bought/create',[BoughtController::class,'create'])->name('bought.create');
    Route::get('/bought/show/{times}',[BoughtController::class,'show'])->name('bought.show');
    Route::post('/bought/store',[BoughtController::class,'store'])->name('bought.store');
    Route::patch('/bought/update',[BoughtController::class,'update'])->name('bought.update');
    Route::delete('/bought/destroy/{times}',[BoughtController::class,'destroy'])->name('bought.destroy');

    // BoughtDetailsController
    Route::get('/boughtList',[BoughtDetailsController::class,'index'])->name('boughtList.index');
    Route::get('/boughtList/data',[BoughtDetailsController::class,'getData'])->name('boughtList.data');
    Route::get('/boughtList/create',[BoughtDetailsController::class,'create'])->name('boughtList.create');
    Route::post('/boughtList/store',[BoughtDetailsController::class,'store'])->name('boughtList.store');
    Route::post('/boughtList/submit',[BoughtDetailsController::class,'submit'])->name('boughtList.submit');
    Route::post('/boughtList/update',[BoughtDetailsController::class,'update'])->name('boughtList.update');
    Route::get('/boughtList/checkBillNoDuplication',[BoughtDetailsController::class, 'checkBillNoDuplication'])->name('boughtList.checkBillNoDuplication');
    Route::get('/boughtList/getSingleRecordForEdit/{id}',[BoughtDetailsController::class,'getSingleRecordForEdit'])->name('boughtList.getSingleRecordForEdit');
    Route::post('/boughtList/updateItemAndWarehouseItems',[BoughtDetailsController::class, 'updateItemAndWarehouseItems'])->name('boughtList.updateItemAndWarehouseItems');
    Route::get('/boughtList/getWarehouseListForDelete/{id}',[BoughtDetailsController::class,'getWarehouseListForDelete'])->name('boughtList.getWarehouseListForDelete');
    
    Route::get('/boughtList/details/{times}',[BoughtDetailsController::class,'details'])->name('boughtList.details');
    Route::get('/boughtList/destroy/{billno}',[BoughtDetailsController::class,'destroy'])->name('boughtList.destroy');
    Route::post('/boughtList/deleteSingleItem',[BoughtDetailsController::class,'deleteSingleItem'])->name('boughtList.deleteSingleItem');
    Route::get('/boughtList/edit/{times}',[BoughtDetailsController::class,'edit'])->name('boughtList.edit');

});
<?php

use App\Http\Controllers\Setting\SettingController;
use App\Http\Controllers\Setting\BranchController;
use App\Http\Controllers\Setting\WarehouseController;
use App\Http\Controllers\Setting\UnitController;
use App\Http\Controllers\Setting\CurrencyController;
use App\Http\Controllers\Setting\AccountController;
use App\Http\Controllers\Setting\IncomeTypeController;
use App\Http\Controllers\Setting\ExpenseTypeController;
use App\Http\Controllers\Setting\OrgProfileController;


Route::get('setting',[SettingController::class,'index'])->name('setting')->middleware('access:settings,list');

// branch
Route::prefix('branches')->group(function(){
    Route::get('/', [BranchController::class, 'index'])->name('branches.list')->middleware('access:settings,list');
    Route::post('/', [BranchController::class, 'store'])->name('branches')->middleware('access:settings,create_records');
    Route::get('/{id}', [BranchController::class, 'show'])->name('branches.show')->middleware('access:settings,edit_records');
    Route::patch('/{id}', [BranchController::class, 'update'])->name('branches.update');
    Route::delete('/{id}', [BranchController::class, 'destroy'])->name('branches.destroy')->middleware('access:settings,delete_records');
});

   // Warehouse
Route::prefix('warehouses')->group(function(){
    Route::get('/', [WarehouseController::class, 'index'])->name('warehouses.index');
    Route::get('/create', [WarehouseController::class, 'create'])->name('warehouses.create')->middleware('access:settings,create_records');
    Route::post('/store', [WarehouseController::class, 'store'])->name('warehouses.store');
    Route::get('/{warehouse}', [WarehouseController::class, 'show'])->name('warehouses.show')->middleware('access:settings,edit_records');
    Route::patch('/update', [WarehouseController::class, 'update'])->name('warehouses.update');
    Route::delete('/{id}', [WarehouseController::class, 'destroy'])->name('warehouses.destroy')->middleware('access:settings,delete_records');
});

// unit
Route::prefix('units')->group(function(){
    Route::get('/', [UnitController::class, 'index'])->name('units.list');
    Route::get('/create', [UnitController::class, 'create'])->name('units.create')->middleware('access:settings,create_records');
    Route::post('/store', [UnitController::class, 'store'])->name('units.store');
    Route::get('/{id}', [UnitController::class, 'show'])->name('units.show')->middleware('access:settings,edit_records');
    Route::patch('/update', [UnitController::class, 'update'])->name('units.update'); 
    Route::delete('/{id}', [UnitController::class, 'destroy'])->name('units.destroy')->middleware('access:settings,delete_records');
});

// Currency
Route::prefix('currency')->group(function(){
    Route::get('/', [CurrencyController::class, 'index'])->name('currency.list');
    Route::get('/create', [CurrencyController::class, 'create'])->name('currency.create')->middleware('access:settings,create_records');
    Route::post('/store', [CurrencyController::class, 'store'])->name('currency.store');
    Route::get('/{id}', [CurrencyController::class, 'show'])->name('currency.show')->middleware('access:settings,edit_records');
    Route::patch('/update', [CurrencyController::class, 'update'])->name('currency.update'); 
    Route::delete('/{id}', [CurrencyController::class, 'destroy'])->name('currency.destroy')->middleware('access:settings,delete_records');
});

// Account
Route::prefix('account')->group(function(){
    Route::get('/', [AccountController::class, 'index'])->name('account.list');
    Route::get('/create', [AccountController::class, 'create'])->name('account.create')->middleware('access:settings,create_records');
    Route::post('/store', [AccountController::class, 'store'])->name('account.store');
    Route::get('/{id}', [AccountController::class, 'edit'])->name('account.edit')->middleware('access:settings,edit_records');
    Route::get('/show/{id}', [AccountController::class, 'show'])->name('account.show')->middleware('access:settings,edit_records');
    Route::patch('/update', [AccountController::class, 'update'])->name('account.update'); 
    Route::delete('/{id}', [AccountController::class, 'destroy'])->name('account.destroy')->middleware('access:settings,delete_records');
});

// income_type
Route::prefix('itype')->group(function(){
    Route::get('/', [IncomeTypeController::class, 'index'])->name('itype.list');
    Route::get('/create', [IncomeTypeController::class, 'create'])->name('itype.create')->middleware('access:settings,create_records');
    Route::post('/store', [IncomeTypeController::class, 'store'])->name('itype.store');
    Route::get('/{id}', [IncomeTypeController::class, 'show'])->name('itype.show')->middleware('access:settings,edit_records');
    Route::patch('/update', [IncomeTypeController::class, 'update'])->name('itype.update'); 
    Route::delete('/{id}', [IncomeTypeController::class, 'destroy'])->name('itype.destroy')->middleware('access:settings,delete_records');
});


// expense_type
Route::prefix('etype')->group(function(){
    Route::get('/', [ExpenseTypeController::class, 'index'])->name('etype.list');
    Route::get('/create', [ExpenseTypeController::class, 'create'])->name('etype.create')->middleware('access:settings,create_records');
    Route::post('/store', [ExpenseTypeController::class, 'store'])->name('etype.store');
    Route::get('/{id}', [ExpenseTypeController::class, 'show'])->name('etype.show')->middleware('access:settings,edit_records');
    Route::patch('/update', [ExpenseTypeController::class, 'update'])->name('etype.update'); 
    Route::delete('/{id}', [ExpenseTypeController::class, 'destroy'])->name('etype.destroy')->middleware('access:settings,delete_records');
});

// Organization Profile
Route::prefix('orgprofile')->group(function(){
    Route::get('/', [OrgProfileController::class, 'index'])->name('orgprofile.list');
    Route::get('/create', [OrgProfileController::class, 'create'])->name('orgprofile.create')->middleware('access:settings,create_records');
    Route::post('/store', [OrgProfileController::class, 'store'])->name('orgprofile.store');
    Route::get('/{id}', [OrgProfileController::class, 'edit'])->name('orgprofile.edit')->middleware('access:settings,edit_records');
    Route::post('/update', [OrgProfileController::class, 'update'])->name('orgprofile.update'); 
    Route::delete('/{id}', [OrgProfileController::class, 'destroy'])->name('orgprofile.destroy')->middleware('access:settings,delete_records');
});


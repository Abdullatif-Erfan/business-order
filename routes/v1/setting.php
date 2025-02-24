<?php

use App\Http\Controllers\Setting\SettingController;
use App\Http\Controllers\Setting\BranchController;
use App\Http\Controllers\Setting\WarehouseController;
use App\Http\Controllers\Setting\UnitController;
use App\Http\Controllers\Setting\CurrencyController;
use App\Http\Controllers\Setting\AccountController;
use App\Http\Controllers\Setting\IncomeTypeController;
use App\Http\Controllers\Setting\ExpenseTypeController;



Route::get('setting',[SettingController::class,'index'])->name('setting');

// branch
Route::prefix('branches')->group(function(){
    Route::get('/', [BranchController::class, 'index'])->name('branches.list');
    Route::post('/', [BranchController::class, 'store'])->name('branches');
    Route::get('/{id}', [BranchController::class, 'show'])->name('branches.show');
    Route::patch('/{id}', [BranchController::class, 'update'])->name('branches.update');
    Route::delete('/{id}', [BranchController::class, 'destroy'])->name('branches.destroy');
});

   // Warehouse
Route::prefix('warehouses')->group(function(){
    Route::get('/', [WarehouseController::class, 'index'])->name('warehouses.index');
    Route::get('/create', [WarehouseController::class, 'create'])->name('warehouses.create');
    Route::post('/store', [WarehouseController::class, 'store'])->name('warehouses.store');
    Route::get('/{warehouse}', [WarehouseController::class, 'show'])->name('warehouses.show');
    Route::patch('/update', [WarehouseController::class, 'update'])->name('warehouses.update');
    Route::delete('/{id}', [WarehouseController::class, 'destroy'])->name('warehouses.destroy');
});

// unit
Route::prefix('units')->group(function(){
    Route::get('/', [UnitController::class, 'index'])->name('units.list');
    Route::get('/create', [UnitController::class, 'create'])->name('units.create');
    Route::post('/store', [UnitController::class, 'store'])->name('units.store');
    Route::get('/{id}', [UnitController::class, 'show'])->name('units.show');
    Route::patch('/update', [UnitController::class, 'update'])->name('units.update'); 
    Route::delete('/{id}', [UnitController::class, 'destroy'])->name('units.destroy');
});

// Currency
Route::prefix('currency')->group(function(){
    Route::get('/', [CurrencyController::class, 'index'])->name('currency.list');
    Route::get('/create', [CurrencyController::class, 'create'])->name('currency.create');
    Route::post('/store', [CurrencyController::class, 'store'])->name('currency.store');
    Route::get('/{id}', [CurrencyController::class, 'show'])->name('currency.show');
    Route::patch('/update', [CurrencyController::class, 'update'])->name('currency.update'); 
    Route::delete('/{id}', [CurrencyController::class, 'destroy'])->name('currency.destroy');
});

// Account
Route::prefix('account')->group(function(){
    Route::get('/', [AccountController::class, 'index'])->name('account.list');
    Route::get('/create', [AccountController::class, 'create'])->name('account.create');
    Route::post('/store', [AccountController::class, 'store'])->name('account.store');
    Route::get('/{id}', [AccountController::class, 'edit'])->name('account.edit');
    Route::get('/show/{id}', [AccountController::class, 'show'])->name('account.show');
    Route::patch('/update', [AccountController::class, 'update'])->name('account.update'); 
    Route::delete('/{id}', [AccountController::class, 'destroy'])->name('account.destroy');
});

// income_type
Route::prefix('itype')->group(function(){
    Route::get('/', [IncomeTypeController::class, 'index'])->name('itype.list');
    Route::get('/create', [IncomeTypeController::class, 'create'])->name('itype.create');
    Route::post('/store', [IncomeTypeController::class, 'store'])->name('itype.store');
    Route::get('/{id}', [IncomeTypeController::class, 'show'])->name('itype.show');
    Route::patch('/update', [IncomeTypeController::class, 'update'])->name('itype.update'); 
    Route::delete('/{id}', [IncomeTypeController::class, 'destroy'])->name('itype.destroy');
});


// expense_type
Route::prefix('etype')->group(function(){
    Route::get('/', [ExpenseTypeController::class, 'index'])->name('etype.list');
    Route::get('/create', [ExpenseTypeController::class, 'create'])->name('etype.create');
    Route::post('/store', [ExpenseTypeController::class, 'store'])->name('etype.store');
    Route::get('/{id}', [ExpenseTypeController::class, 'show'])->name('etype.show');
    Route::patch('/update', [ExpenseTypeController::class, 'update'])->name('etype.update'); 
    Route::delete('/{id}', [ExpenseTypeController::class, 'destroy'])->name('etype.destroy');
});

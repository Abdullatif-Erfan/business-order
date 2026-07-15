<?php

use App\Http\Controllers\Setting\SettingController;
use App\Http\Controllers\Setting\BranchController;
use App\Http\Controllers\Setting\WarehouseController;
use App\Http\Controllers\Setting\UnitController;
use App\Http\Controllers\Setting\CurrencyController;
use App\Http\Controllers\Setting\CategoryController;
use App\Http\Controllers\Setting\AccountController;
use App\Http\Controllers\Setting\IncomeTypeController;
use App\Http\Controllers\Setting\ExpenseTypeController;
use App\Http\Controllers\Setting\OrgProfileController;
use App\Http\Controllers\Setting\CarController;


Route::get('setting',[SettingController::class,'index'])->name('setting')->middleware('access:settings,list');

// categorie
Route::prefix('category')->group(function(){
    Route::get('/', [CategoryController::class, 'index'])->name('category.list');
    Route::get('/create', [CategoryController::class, 'create'])->name('category.create')->middleware('access:settings,create_records');
    Route::post('/store', [CategoryController::class, 'store'])->name('category.store');
    Route::get('/{id}', [CategoryController::class, 'show'])->name('category.show')->middleware('access:settings,edit_records');
    Route::patch('/update', [CategoryController::class, 'update'])->name('category.update'); 
    Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('category.destroy')->middleware('access:settings,delete_records');
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

// car
Route::prefix('cars')->group(function(){
    Route::get('/', [CarController::class, 'index'])->name('cars.list');
    Route::get('/create', [CarController::class, 'create'])->name('cars.create')->middleware('access:settings,create_records');
    Route::post('/store', [CarController::class, 'store'])->name('cars.store');
    Route::get('/{id}', [CarController::class, 'show'])->name('cars.show')->middleware('access:settings,edit_records');
    Route::patch('/update', [CarController::class, 'update'])->name('cars.update'); 
    Route::delete('/{id}', [CarController::class, 'destroy'])->name('cars.destroy')->middleware('access:settings,delete_records');
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
    Route::post('/update', [OrgProfileController::class, 'update'])->name('orgprofile.update')->middleware('access:settings,edit_records'); 
    Route::delete('/{id}', [OrgProfileController::class, 'destroy'])->name('orgprofile.destroy')->middleware('access:settings,delete_records');
});


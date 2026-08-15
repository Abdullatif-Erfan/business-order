<?php

use App\Http\Controllers\Hr\EmployeeController;
use App\Http\Controllers\Hr\SalaryController;
use App\Http\Controllers\Hr\SalaryReportController;
use App\Http\Controllers\Hr\CustomerController;
use App\Http\Controllers\Hr\SupplierController;

Route::prefix('employee')->group(function() {
    Route::get('/',[EmployeeController::class, 'index'])->name('employee.index')->middleware('access:hr,list');
    Route::get('/data',[EmployeeController::class, 'getData'])->name('employee.data');
    Route::get('/create',[EmployeeController::class, 'create'])->name('employee.create')->middleware('access:hr,create_records');
    Route::post('/store',[EmployeeController::class, 'store'])->name('employee.store');
    Route::get('/edit/{id}',[EmployeeController::class, 'edit'])->name('employee.edit')->middleware('access:hr,edit_records');
    Route::put('/update',[EmployeeController::class, 'update'])->name('employee.update');
    Route::get('/destroy/{id}',[EmployeeController::class, 'destroy'])->name('employee.destroy')->middleware('access:hr,delete_records');
});

Route::prefix('supplier')->group(function() {
    Route::get('/',[SupplierController::class, 'index'])->name('supplier.index')->middleware('access:hr,list');
    Route::get('/data',[SupplierController::class, 'getData'])->name('supplier.data');
    Route::get('/create',[SupplierController::class, 'create'])->name('supplier.create')->middleware('access:hr,create_records');
    Route::post('/store',[SupplierController::class, 'store'])->name('supplier.store');
    Route::get('/edit/{id}',[SupplierController::class, 'edit'])->name('supplier.edit')->middleware('access:hr,edit_records');
    Route::put('/update',[SupplierController::class, 'update'])->name('supplier.update');
    Route::get('/destroy/{id}',[SupplierController::class, 'destroy'])->name('supplier.destroy')->middleware('access:hr,delete_records');
});

Route::prefix('customer')->group(function() {
    Route::get('/',[CustomerController::class, 'index'])->name('customer.index')->middleware('access:hr,list');
    Route::get('/data',[CustomerController::class, 'getData'])->name('customer.data');
    Route::get('/create',[CustomerController::class, 'create'])->name('customer.create')->middleware('access:hr,create_records');
    Route::post('/store',[CustomerController::class, 'store'])->name('customer.store');
    Route::get('/edit/{id}',[CustomerController::class, 'edit'])->name('customer.edit')->middleware('access:hr,edit_records');
    Route::put('/update',[CustomerController::class, 'update'])->name('customer.update');
    Route::get('/destroy/{id}',[CustomerController::class, 'destroy'])->name('customer.destroy')->middleware('access:hr,delete_records');
});


Route::prefix('salary')->group(function() {
    Route::get('/',[SalaryController::class, 'index'])->name('salary.index')->middleware('access:hr,list');
    Route::get('/data',[SalaryController::class, 'getData'])->name('salary.data');
    Route::get('/create',[SalaryController::class, 'create'])->name('salary.create')->middleware('access:hr,create_records');
    Route::post('/store',[SalaryController::class, 'store'])->name('salary.store');
    Route::get('/edit/{id}',[SalaryController::class, 'edit'])->name('salary.edit')->middleware('access:hr,edit_records');
    Route::put('/update',[SalaryController::class, 'update'])->name('salary.update');
    Route::get('/destroy/{times}',[SalaryController::class, 'destroy'])->name('salary.destroy')->middleware('access:hr,delete_records');
});

// Salary Report
Route::get('/salary/report',[SalaryReportController::class, 'index'])->name('salary.report.index')->middleware('access:hr,list');
Route::get('/salary/report/data',[SalaryReportController::class, 'getData'])->name('salary.report.data');



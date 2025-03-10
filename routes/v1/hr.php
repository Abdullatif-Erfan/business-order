<?php

use App\Http\Controllers\Hr\EmployeeController;
use App\Http\Controllers\Hr\SalaryController;
use App\Http\Controllers\Hr\SalaryReportController;




Route::prefix('employee')->group(function() {
    Route::get('/',[EmployeeController::class, 'index'])->name('employee.index')->middleware('access:hr,list');
    Route::get('/data',[EmployeeController::class, 'getData'])->name('employee.data');
    Route::get('/create',[EmployeeController::class, 'create'])->name('employee.create')->middleware('access:hr,create_records');
    Route::post('/store',[EmployeeController::class, 'store'])->name('employee.store');
    Route::get('/edit/{id}',[EmployeeController::class, 'edit'])->name('employee.edit')->middleware('access:hr,edit_records');
    Route::put('/update',[EmployeeController::class, 'update'])->name('employee.update');
    Route::get('/destroy/{id}',[EmployeeController::class, 'destroy'])->name('employee.destroy')->middleware('access:hr,delete_records');
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



<?php

use App\Http\Controllers\Hr\EmployeeController;
use App\Http\Controllers\Hr\SalaryController;
use App\Http\Controllers\Hr\SalaryReportController;




Route::prefix('employee')->group(function() {
    Route::get('/',[EmployeeController::class, 'index'])->name('employee.index');
    Route::get('/data',[EmployeeController::class, 'getData'])->name('employee.data');
    Route::get('/create',[EmployeeController::class, 'create'])->name('employee.create');
    Route::post('/store',[EmployeeController::class, 'store'])->name('employee.store');
    Route::get('/edit/{id}',[EmployeeController::class, 'edit'])->name('employee.edit');
    Route::put('/update',[EmployeeController::class, 'update'])->name('employee.update');
    Route::get('/destroy/{id}',[EmployeeController::class, 'destroy'])->name('employee.destroy');
});


Route::prefix('salary')->group(function() {
    Route::get('/',[SalaryController::class, 'index'])->name('salary.index');
    Route::get('/data',[SalaryController::class, 'getData'])->name('salary.data');
    Route::get('/create',[SalaryController::class, 'create'])->name('salary.create');
    Route::post('/store',[SalaryController::class, 'store'])->name('salary.store');
    Route::get('/edit/{id}',[SalaryController::class, 'edit'])->name('salary.edit');
    Route::put('/update',[SalaryController::class, 'update'])->name('salary.update');
    Route::get('/destroy/{times}',[SalaryController::class, 'destroy'])->name('salary.destroy');
});

// Salary Report
Route::get('/salary/report',[SalaryReportController::class, 'index'])->name('salary.report.index');
Route::get('/salary/report/data',[SalaryReportController::class, 'getData'])->name('salary.report.data');



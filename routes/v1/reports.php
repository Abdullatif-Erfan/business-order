<?php

use App\Http\Controllers\Report\ItemController;
use App\Http\Controllers\Report\ChartOfAccount;


Route::prefix('reports')->group(function() {
    Route::get('/home',[ItemController::class, 'home'])->name('reports.home');
    Route::post('/daily',[ItemController::class, 'daily'])->name('reports.daily');
    Route::post('/monthly',[ItemController::class, 'monthly'])->name('reports.monthly');
    Route::post('/yearly',[ItemController::class, 'yearly'])->name('reports.yearly');
});

Route::prefix('chartOfAccount')->group(function() {
    Route::get('/{id?}',[ChartOfAccount::class, 'index'])->name('chartOfAccount.index');
    // Route::post('/daily',[ChartOfAccount::class, 'daily'])->name('reports.daily');
    // Route::post('/monthly',[ChartOfAccount::class, 'monthly'])->name('reports.monthly');
    // Route::post('/yearly',[ChartOfAccount::class, 'yearly'])->name('reports.yearly');
});
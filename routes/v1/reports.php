<?php

use App\Http\Controllers\Report\ItemController;
use App\Http\Controllers\Report\ChartOfAccount;
use App\Http\Controllers\Report\CacheFlowController;
use App\Http\Controllers\Report\BalanceSheetController;
use App\Http\Controllers\Report\LawController;
use App\Http\Controllers\Report\ProfitAndLossController;






Route::prefix('reports')->group(function() {
    Route::get('/home',[ItemController::class, 'home'])->name('reports.home');
    Route::post('/daily',[ItemController::class, 'daily'])->name('reports.daily');
    Route::post('/monthly',[ItemController::class, 'monthly'])->name('reports.monthly');
    Route::post('/yearly',[ItemController::class, 'yearly'])->name('reports.yearly');
});

Route::get('/chartOfAccount/{id?}',[ChartOfAccount::class, 'index'])->name('chartOfAccount.index');
Route::get('/cacheflow',[CacheFlowController::class, 'index'])->name('cacheflow.index');
Route::get('/cacheflow/data',[CacheFlowController::class, 'getData'])->name('cacheflow.data');
Route::get('/balancesheet',[BalanceSheetController::class,'index'])->name('balancesheet.index');
Route::get('/balancesheet/data',[BalanceSheetController::class,'getData'])->name('balancesheet.data');
Route::get('/laws',[LawController::class,'index'])->name('laws.index');
Route::get('/profitloss',[ProfitAndLossController::class,'index'])->name('profitloss.index');










<?php

use App\Http\Controllers\Report\ItemController;
use App\Http\Controllers\Report\ChartOfAccount;
use App\Http\Controllers\Report\CacheFlowController;
use App\Http\Controllers\Report\CacheFlowWithBalanceController;
use App\Http\Controllers\Report\BalanceSheetController;
use App\Http\Controllers\Report\LawController;
use App\Http\Controllers\Report\ProfitAndLossController;


Route::prefix('reports')->group(function() {
    Route::get('/home',[ItemController::class, 'home'])->name('reports.home')->middleware('access:reports,list');
    Route::post('/daily',[ItemController::class, 'daily'])->name('reports.daily')->middleware('access:reports,list');
    Route::post('/monthly',[ItemController::class, 'monthly'])->name('reports.monthly')->middleware('access:reports,list');
    Route::post('/yearly',[ItemController::class, 'yearly'])->name('reports.yearly')->middleware('access:reports,list');
});

Route::get('/chartOfAccount/{id?}',[ChartOfAccount::class, 'index'])->name('chartOfAccount.index')->middleware('access:reports,list');
// Route::get('/cacheflow',[CacheFlowController::class, 'index'])->name('cacheflow.index')->middleware('access:reports,list');
// Route::get('/cacheflow/data',[CacheFlowController::class, 'getData'])->name('cacheflow.data');

Route::get('/cacheflow',[CacheFlowWithBalanceController::class, 'index'])->name('cacheflow.index')->middleware('access:reports,list');
Route::get('/cacheflow/data',[CacheFlowWithBalanceController::class, 'getData'])->name('cacheflow.data');

Route::get('/balancesheet',[BalanceSheetController::class,'index'])->name('balancesheet.index')->middleware('access:reports,list');
Route::get('/balancesheet/data',[BalanceSheetController::class,'getData'])->name('balancesheet.data');
Route::get('/laws',[LawController::class,'index'])->name('laws.index')->middleware('access:reports,list');
Route::get('/profitloss',[ProfitAndLossController::class,'index'])->name('profitloss.index')->middleware('access:reports,list');










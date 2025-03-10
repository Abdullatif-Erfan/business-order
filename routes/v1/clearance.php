<?php

use App\Http\Controllers\Clearance\ClearanceController;

// Clearance 
Route::prefix('clearance')->group(function(){

    // buy clearance
    Route::get('/',[ClearanceController::class, 'index'])->name('clearance.index')->middleware('access:clearance,list');
    Route::get('/data',[ClearanceController::class, 'getData'])->name('clearance.data');
    Route::get('/buy/create/{currency_id}/{buy_to_account_id}', [ClearanceController::class, 'create_for_buy'])
    ->name('clearance.buy.create')->middleware('access:clearance,create_records');
    Route::post('/buy/store', [ClearanceController::class, 'store_for_buy'])->name('clearance.buy.store')->middleware('access:clearance,create_records');


    // Sales Clearance
    Route::get('/sales',[ClearanceController::class, 'sales_index'])->name('clearance.sales.index')->middleware('access:clearance,list');
    Route::get('/sales/data',[ClearanceController::class, 'getSalesData'])->name('clearance.sales.data');
    Route::get('/sales/create/{currency_id}/{sales_to_account_id}', [ClearanceController::class, 'create_for_sales'])
    ->name('clearance.sales.create')->middleware('access:clearance,create_records');
    Route::post('/sales/store', [ClearanceController::class, 'store_for_sales'])->name('clearance.sales.store')->middleware('access:clearance,create_records');
});
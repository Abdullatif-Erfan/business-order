<?php

use App\Http\Controllers\Sales\SalesController;
use App\Http\Controllers\Sales\PosSalesController;

   // Sales
   Route::prefix('sales')->group(function(){
    Route::get('/',[SalesController::class,'index'])->name('sales.index')->middleware('access:sales,list');
    Route::get('/data',[SalesController::class,'getData'])->name('sales.data');
    Route::get('/create',[SalesController::class,'create'])->name('sales.create')->middleware('access:sales,create_records');
    Route::post('/store',[SalesController::class,'store'])->name('sales.store');
    Route::get('/details/{billno}',[SalesController::class,'details'])->name('sales.details');
    Route::get('/edit/{billno}',[SalesController::class,'edit'])->name('sales.edit')->middleware('access:sales,edit_records');
    Route::get('/getSingleRecordForEdit/{id}',[SalesController::class,'getSingleRecordForEdit'])->name('sales.getSingleRecordForEdit');
    Route::post('/updateSalesAndWarehouseItems',[SalesController::class, 'updateSalesAndWarehouseItems'])->name('sales.updateSalesAndWarehouseItems');
    Route::post('/update',[SalesController::class,'update'])->name('sales.update');
    Route::post('/deleteSingleItem/{id}',[SalesController::class,'deleteSingleItem'])->name('sales.deleteSingleItem')->middleware('access:sales,delete_records');
    Route::get('/destroy/{times}',[SalesController::class,'destroy'])->name('sales.destroy')->middleware('access:sales,delete_records');
    // POS
    Route::get('/pos_create',[PosSalesController::class,'pos_create'])->name('sales.pos_create')->middleware('access:sales,create_records');
    Route::post('/pos_store',[PosSalesController::class,'pos_store'])->name('sales.pos_store')->middleware('access:sales,create_records');

});

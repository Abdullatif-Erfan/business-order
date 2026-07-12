<?php

use App\Http\Controllers\Sales\SalesController;
use App\Http\Controllers\Sales\PosSalesController;
use App\Http\Controllers\Sales\SalesByItemController;

   // Sales
   Route::prefix('sales')->group(function(){
    Route::get('/',[SalesController::class,'index'])->name('sales.index')->middleware('access:sales,list');
    Route::get('/data',[SalesController::class,'getData'])->name('sales.data');
    Route::get('/create',[SalesController::class,'create'])->name('sales.create')->middleware('access:sales,create_records');
    Route::get('/createWithOtherCurrency',[SalesController::class,'createWithOtherCurrency'])->name('sales.createWithOtherCurrency')->middleware('access:sales,create_records');
    Route::post('/store',[SalesController::class,'store'])->name('sales.store');
    Route::post('/storeWithOtherCurrency',[SalesController::class,'storeWithOtherCurrency'])->name('sales.storeWithOtherCurrency');
    Route::get('/details/{billno}',[SalesController::class,'details'])->name('sales.details');
    Route::get('/edit/{billno}',[SalesController::class,'edit'])->name('sales.edit')->middleware('access:sales,edit_records');
    Route::get('/getSingleRecordForEdit/{id}',[SalesController::class,'getSingleRecordForEdit'])->name('sales.getSingleRecordForEdit');
    Route::post('/updateSalesAndWarehouseItems',[SalesController::class, 'updateSalesAndWarehouseItems'])->name('sales.updateSalesAndWarehouseItems');
    Route::post('/update',[SalesController::class,'update'])->name('sales.update');
    Route::post('/deleteSingleItem/{id}',[SalesController::class,'deleteSingleItem'])->name('sales.deleteSingleItem')->middleware('access:sales,delete_records');
    //  Route::post('/deleteSingleItem',[SalesController::class,'deleteSingleItem'])->name('sales.deleteSingleItem')->middleware('access:sales,delete_records');
    Route::get('/destroy/{times}',[SalesController::class,'destroy'])->name('sales.destroy')->middleware('access:sales,delete_records');
    
    // Invoice routes
    Route::get('/invoices', [SalesController::class, 'invoiceList'])->name('sales.invoices')->middleware('access:sales,list');
    Route::get('/invoice-data', [SalesController::class, 'getInvoiceData'])->name('sales.invoiceData');
    Route::post('/generate-invoice', [SalesController::class, 'generateInvoice'])->name('sales.generateInvoice')->middleware('access:sales,create_records');
    Route::get('/invoice/{id}', [SalesController::class, 'showInvoice'])->name('sales.showInvoice')->middleware('access:sales,list');
    Route::post('/invoice-payment', [SalesController::class, 'addPayment'])->name('sales.addPayment')->middleware('access:sales,create_records');
});

Route::prefix('soldItemList')->group(function(){
    Route::get('/',[SalesByItemController::class,'index'])->name('soldItemList.index')->middleware('access:sales,list');
    Route::get('/data',[SalesByItemController::class,'getData'])->name('soldItemList.data');
});

<?php

use App\Http\Controllers\Buy\BuyPreListController;
use App\Http\Controllers\Buy\BoughtController;
use App\Http\Controllers\Buy\BoughtDetailsController;

// BuyPreList 
Route::prefix('buyprelist')->group(function(){
    Route::get('/',[BuyPreListController::class, 'index'])->name('buyprelist.index')->middleware('access:buy,list');
    Route::get('/data',[BuyPreListController::class, 'getData'])->name('buyprelist.data');
    Route::get('/pos_data',[BuyPreListController::class, 'getPosData'])->name('buyprelist.pos_data');
    Route::get('/print_barcode', [BuyPreListController::class, 'print_barcode'])->name('buyprelist.print_barcode');
    Route::get('/{id}',[BuyPreListController::class, 'show'])->name('buyprelist.show')->middleware('access:buy,create_records');
    Route::post('/store',[BuyPreListController::class, 'store'])->name('buyprelist.store')->middleware('access:buy,create_records');
    Route::post('/pos_store',[BuyPreListController::class, 'pos_store'])->name('buyprelist.pos_store')->middleware('access:buy,create_records');
    Route::post('/storeWithBarcodeGeneration',[BuyPreListController::class, 'storeWithBarcodeGeneration'])->name('buyprelist.storeWithBarcodeGeneration')->middleware('access:buy,create_records');
   
    
    Route::post('/update',[BuyPreListController::class, 'update'])->name('buyprelist.update')->middleware('access:buy,edit_records');
    Route::delete('/destroy/{id}',[BuyPreListController::class, 'destroy'])->name('buyprelist.destroy')->middleware('access:buy,delete_records');
});

// bought
Route::prefix('bought')->group(function(){
    Route::get('/',[BoughtController::class,'index'])->name('bought.index')->middleware('access:buy,list');
    Route::get('/data',[BoughtController::class,'getData'])->name('bought.data');
    Route::get('/create',[BoughtController::class,'create'])->name('bought.create')->middleware('access:buy,create_records');
    Route::get('/show/{times}',[BoughtController::class,'show'])->name('bought.show')->middleware('access:buy,create_records');
    Route::post('/store',[BoughtController::class,'store'])->name('bought.store')->middleware('access:buy,create_records');
    Route::patch('/update',[BoughtController::class,'update'])->name('bought.update')->middleware('access:buy,edit_records');
    Route::delete('/destroy/{times}',[BoughtController::class,'destroy'])->name('bought.destroy')->middleware('access:buy,delete_records');
});

// BoughtDetailsController
Route::prefix('boughtList')->group(function(){
    Route::get('/',[BoughtDetailsController::class,'index'])->name('boughtList.index')->middleware('access:buy,list');
    Route::get('/data',[BoughtDetailsController::class,'getData'])->name('boughtList.data');
    Route::get('/create',[BoughtDetailsController::class,'create'])->name('boughtList.create')->middleware('access:buy,create_records');
    Route::post('/store',[BoughtDetailsController::class,'store'])->name('boughtList.store')->middleware('access:buy,create_records');
    Route::post('/submit',[BoughtDetailsController::class,'submit'])->name('boughtList.submit')->middleware('access:buy,create_records');
    Route::post('/update',[BoughtDetailsController::class,'update'])->name('boughtList.update')->middleware('access:buy,edit_records');
    Route::get('/checkBillNoDuplication',[BoughtDetailsController::class, 'checkBillNoDuplication'])->name('boughtList.checkBillNoDuplication');
    Route::get('/getSingleRecordForEdit/{id}',[BoughtDetailsController::class,'getSingleRecordForEdit'])->name('boughtList.getSingleRecordForEdit');
    Route::post('/updateItemAndWarehouseItems',[BoughtDetailsController::class, 'updateItemAndWarehouseItems'])->name('boughtList.updateItemAndWarehouseItems');
    Route::get('/getWarehouseListForDelete/{id}',[BoughtDetailsController::class,'getWarehouseListForDelete'])->name('boughtList.getWarehouseListForDelete');
    Route::get('/details/{times}',[BoughtDetailsController::class,'details'])->name('boughtList.details');
    Route::get('/destroy/{times}',[BoughtDetailsController::class,'destroy'])->name('boughtList.destroy')->middleware('access:buy,delete_records');
    Route::post('/deleteSingleItem',[BoughtDetailsController::class,'deleteSingleItem'])
           ->name('boughtList.deleteSingleItem')->middleware('access:buy,delete_records');
    Route::get('/edit/{times}',[BoughtDetailsController::class,'edit'])->name('boughtList.edit')->middleware('access:buy,edit_records');
});


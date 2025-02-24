<?php

use App\Http\Controllers\Buy\BuyPreListController;
use App\Http\Controllers\Buy\BoughtController;
use App\Http\Controllers\Buy\BoughtDetailsController;

// BuyPreList 
Route::prefix('buyprelist')->group(function(){
    Route::get('/',[BuyPreListController::class, 'index'])->name('buyprelist.index');
    Route::get('/data',[BuyPreListController::class, 'getData'])->name('buyprelist.data');
    Route::get('/{id}',[BuyPreListController::class, 'show'])->name('buyprelist.show');
    Route::post('/store',[BuyPreListController::class, 'store'])->name('buyprelist.store');
    Route::patch('/update',[BuyPreListController::class, 'update'])->name('buyprelist.update');
    Route::delete('/destroy/{id}',[BuyPreListController::class, 'destroy'])->name('buyprelist.destroy');
});

// bought
Route::prefix('bought')->group(function(){
    Route::get('/',[BoughtController::class,'index'])->name('bought.index');
    Route::get('/data',[BoughtController::class,'getData'])->name('bought.data');
    Route::get('/create',[BoughtController::class,'create'])->name('bought.create');
    Route::get('/show/{times}',[BoughtController::class,'show'])->name('bought.show');
    Route::post('/store',[BoughtController::class,'store'])->name('bought.store');
    Route::patch('/update',[BoughtController::class,'update'])->name('bought.update');
    Route::delete('/destroy/{times}',[BoughtController::class,'destroy'])->name('bought.destroy');
});

// BoughtDetailsController
Route::prefix('boughtList')->group(function(){
    Route::get('/',[BoughtDetailsController::class,'index'])->name('boughtList.index')->middleware('access:gen_buy,list');
    Route::get('/data',[BoughtDetailsController::class,'getData'])->name('boughtList.data');
    Route::get('/create',[BoughtDetailsController::class,'create'])->name('boughtList.create')->middleware('access:gen_buy,create_records');
    Route::post('/store',[BoughtDetailsController::class,'store'])->name('boughtList.store')->middleware('access:gen_buy,create_records');
    Route::post('/submit',[BoughtDetailsController::class,'submit'])->name('boughtList.submit')->middleware('access:gen_buy,create_records');
    Route::post('/update',[BoughtDetailsController::class,'update'])->name('boughtList.update')->middleware('access:gen_buy,edit_records');
    Route::get('/checkBillNoDuplication',[BoughtDetailsController::class, 'checkBillNoDuplication'])->name('boughtList.checkBillNoDuplication');
    Route::get('/getSingleRecordForEdit/{id}',[BoughtDetailsController::class,'getSingleRecordForEdit'])->name('boughtList.getSingleRecordForEdit');
    Route::post('/updateItemAndWarehouseItems',[BoughtDetailsController::class, 'updateItemAndWarehouseItems'])->name('boughtList.updateItemAndWarehouseItems');
    Route::get('/getWarehouseListForDelete/{id}',[BoughtDetailsController::class,'getWarehouseListForDelete'])->name('boughtList.getWarehouseListForDelete');
    Route::get('/details/{times}',[BoughtDetailsController::class,'details'])->name('boughtList.details');
    Route::get('/destroy/{billno}',[BoughtDetailsController::class,'destroy'])->name('boughtList.destroy')->middleware('access:gen_buy,delete_records');;
    Route::post('/deleteSingleItem',[BoughtDetailsController::class,'deleteSingleItem'])
           ->name('boughtList.deleteSingleItem')->middleware('access:gen_buy,delete_records');
    Route::get('/edit/{times}',[BoughtDetailsController::class,'edit'])->name('boughtList.edit')->middleware('access:gen_buy,edit_records');;
});


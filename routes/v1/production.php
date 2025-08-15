<?php

use App\Http\Controllers\Production\ModelController;
use App\Http\Controllers\Production\ModelDetailsController;
use App\Http\Controllers\Production\QalamController;

// model
Route::prefix('model')->group(function(){
    Route::get('/',[ModelController::class,'index'])->name('model.index')->middleware('access:production,list');
    Route::get('/data',[ModelController::class,'getData'])->name('model.data');
    Route::get('/create',[ModelController::class,'create'])->name('model.create')->middleware('access:production,create_records');
    Route::get('/{id}',[ModelController::class, 'show'])->name('model.show')->middleware('access:production,create_records');
    Route::post('/store',[ModelController::class,'store'])->name('model.store')->middleware('access:production,create_records');
    Route::patch('/update',[ModelController::class,'update'])->name('model.update');
    Route::delete('/destroy/{id}',[ModelController::class,'destroy'])->name('model.destroy')->middleware('access:production,delete_records');
});

Route::prefix('modelDetails')->group(function(){

    Route::get('/create/{modelId}',[ModelDetailsController::class,'create'])->name('modelDetails.create');
    Route::post('/store',[ModelDetailsController::class,'store'])->name('modelDetails.store');
    Route::patch('/update',[ModelDetailsController::class,'update'])->name('modelDetails.update');

});



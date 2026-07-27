<?php

use App\Http\Controllers\Warehouse\WarehouseReturnController;

// Return routes
Route::prefix('return')->group(function(){
    Route::get('/list', [WarehouseReturnController::class, 'returnList'])->name('return.list')->middleware('access:gudam,list');
    Route::get('/data', [WarehouseReturnController::class, 'getData'])->name('return.getData');
    Route::get('/view/{id}', [WarehouseReturnController::class, 'viewReturn'])->name('return.view')->middleware('access:gudam,list');
    Route::get('/edit/{id}', [WarehouseReturnController::class, 'editReturn'])->name('return.edit')->middleware('access:gudam,edit_records');
    Route::post('/update', [WarehouseReturnController::class, 'updateReturn'])->name('return.updateReturn')->middleware('access:gudam,edit_records');
});


     
<?php

use App\Http\Controllers\Warehouse\WarehouseListController;
use App\Http\Controllers\Warehouse\WarehouseWastageController;


    // WarehouseList
    Route::prefix('warehousesList')->group(function(){
        Route::get('/', [WarehouseListController::class, 'index'])->name('warehousesList.index')->middleware('access:gudam,list');
        Route::get('/data', [WarehouseListController::class, 'getData'])->name('warehousesList.data');
        Route::get('/details/{id}', [WarehouseListController::class, 'details'])->name('warehousesList.details');
        Route::patch('/update', [WarehouseListController::class, 'update'])->name('warehousesList.update')->middleware('access:gudam,edit_records');
        Route::get('/getWarehouseItemForTransfer/{id}', [WarehouseListController::class, 'getWarehouseItemForTransfer'])->name('warehousesList.getWarehouseItemForTransfer')->middleware('access:gudam,edit_records');
        Route::get('/getWarehouseItemForConversion/{id}', [WarehouseListController::class, 'getWarehouseItemForConversion'])->name('warehousesList.getWarehouseItemForConversion')->middleware('access:gudam,edit_records');
        Route::post('/updateTransfer', [WarehouseListController::class, 'updateTransfer'])->name('warehousesList.updateTransfer');
        Route::post('/updateConversion', [WarehouseListController::class, 'updateConversion'])->name('warehousesList.updateConversion');
        Route::get('/create', [WarehouseListController::class, 'create'])->name('warehousesList.create')->middleware('access:gudam,create_records');
        Route::post('/store', [WarehouseListController::class, 'store'])->name('warehousesList.store');
        Route::delete('/delete/{id}', [WarehouseListController::class, 'destroy'])->name('warehousesList.delete')->middleware('access:gudam,delete_records');

        // all lists route
        Route::get('/all', [WarehouseListController::class, 'all'])->name('warehousesList.all')->middleware('access:gudam,list');
        Route::get('/allData', [WarehouseListController::class, 'allData'])->name('warehousesList.allData')->middleware('access:gudam,list');

        
        // wastage
        Route::get('/wastage', [WarehouseWastageController::class, 'index'])->name('warehousesList.wastage')->middleware('access:gudam,list');
        Route::get('/wastage_data', [WarehouseWastageController::class, 'getData'])->name('warehousesList.wastage_data');
        Route::get('/wastage/create', [WarehouseWastageController::class, 'create'])->name('warehousesList.wastage.create');
        Route::post('/wastage/store', [WarehouseWastageController::class, 'store'])->name('warehousesList.wastage.store');

    });

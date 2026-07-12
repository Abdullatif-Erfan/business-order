<?php

use App\Http\Controllers\Order\OrderController;

// =========================================
// ORDER ROUTES
// =========================================
Route::prefix('orders')->name('orders.')->group(function(){
    // List and Data
    Route::get('/', [OrderController::class, 'index'])->name('index')->middleware('access:order,list');
    Route::get('/data', [OrderController::class, 'getData'])->name('data');
    
    // Create
    Route::get('/create', [OrderController::class, 'create'])->name('create')->middleware('access:order,create_records');
    Route::post('/store', [OrderController::class, 'store'])->name('store')->middleware('access:order,create_records');
    
    // Read (Show)
    Route::get('/show/{ord_num}', [OrderController::class, 'show'])->name('show')->middleware('access:order,list');
    
    // Edit/Update
    Route::get('/edit/{ord_num}', [OrderController::class, 'edit'])->name('edit')->middleware('access:order,edit_records');
    Route::put('/update/{ord_num}', [OrderController::class, 'update'])->name('update')->middleware('access:order,edit_records');
    
    // Delete
    Route::delete('/destroy/{ord_num}', [OrderController::class, 'destroy'])->name('destroy')->middleware('access:order,delete_records');
    
    // Status Management
    Route::patch('/{ord_num}/status', [OrderController::class, 'updateStatus'])->name('updateStatus')->middleware('access:order,edit_records');
    Route::post('/update-status/{ord_num}', [OrderController::class, 'updateStatus'])->name('updateStatus')->middleware('access:order,edit_records');
    Route::get('/counts', [OrderController::class, 'getCounts'])->name('counts')->middleware('access:order,list');

    // AJAX Routes
    Route::post('/add-item', [OrderController::class, 'addItem'])->name('addItem')->middleware('access:order,create_records');
    Route::get('/check-duplication', [OrderController::class, 'checkDuplication'])->name('checkDuplication')->middleware('access:order,list');
});
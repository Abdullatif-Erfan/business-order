<?php

use App\Http\Controllers\Order\OrderController;

// =========================================
// ORDER ROUTES
// =========================================
Route::prefix('orders')->name('orders.')->group(function(){
    // List and Data
    Route::get('/', [OrderController::class, 'index'])->name('index')->middleware('access:order,list');
    Route::get('/data', [OrderController::class, 'getData'])->name('data')->middleware('access:order,list');
    
    // Create
    Route::get('/create', [OrderController::class, 'create'])->name('create')->middleware('access:order,create_records');
    Route::post('/store', [OrderController::class, 'store'])->name('store')->middleware('access:order,create_records');
    
    // Read (Show)
    Route::get('/{id}', [OrderController::class, 'show'])->name('show')->middleware('access:order,list');
    
    // Edit/Update
    Route::get('/{id}/edit', [OrderController::class, 'edit'])->name('edit')->middleware('access:order,edit_records');
    Route::put('/{id}', [OrderController::class, 'update'])->name('update')->middleware('access:order,edit_records');
    Route::patch('/{id}', [OrderController::class, 'update'])->name('update.patch')->middleware('access:order,edit_records');
    
    // Delete
    Route::delete('/{id}', [OrderController::class, 'destroy'])->name('destroy')->middleware('access:order,delete_records');
    
    // =========================================
    // ADDITIONAL ROUTES
    // =========================================
    // Status Management
    Route::patch('/{id}/status', [OrderController::class, 'updateStatus'])->name('updateStatus')->middleware('access:order,edit_records');
    Route::get('/status/{status}', [OrderController::class, 'getByStatus'])->name('byStatus')->middleware('access:order,list');
    
    // Statistics & Dashboard
    Route::get('/statistics', [OrderController::class, 'getStatistics'])->name('statistics')->middleware('access:order,list');
    Route::get('/dashboard', [OrderController::class, 'getDashboardOrders'])->name('dashboard')->middleware('access:order,list');
    
    // Bulk Operations
    Route::post('/bulk-delete', [OrderController::class, 'bulkDelete'])->name('bulkDelete')->middleware('access:order,delete_records');
    Route::post('/bulk-status-update', [OrderController::class, 'bulkStatusUpdate'])->name('bulkStatusUpdate')->middleware('access:order,edit_records');
    
    // Export & Print
    Route::get('/export', [OrderController::class, 'export'])->name('export')->middleware('access:order,list');
    Route::get('/export/pdf', [OrderController::class, 'exportPDF'])->name('exportPDF')->middleware('access:order,list');
    Route::get('/print/{id}', [OrderController::class, 'print'])->name('print')->middleware('access:order,list');
    
    // AJAX Routes for Create Form
    Route::post('/add-item', [OrderController::class, 'addItem'])->name('addItem')->middleware('access:order,create_records');
    Route::get('/check-duplication', [OrderController::class, 'checkDuplication'])->name('checkDuplication')->middleware('access:order,list');
    Route::get('/search', [OrderController::class, 'search'])->name('search')->middleware('access:order,list');
});
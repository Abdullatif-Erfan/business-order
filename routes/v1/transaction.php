<?php

use App\Http\Controllers\Transactions\JournalController;
use App\Http\Controllers\Transactions\ExpenseController;



 // Journal
 Route::prefix('journal')->group(function(){
    Route::get('/',[JournalController::class, 'index'])->name('journal.index')->middleware('access:journal,list');
    Route::get('/data', [JournalController::class, 'getData'])->name('journal.data');
    Route::get('/create',[JournalController::class, 'create'])->name('journal.create')->middleware('access:journal,create_records');
    Route::post('/store', [JournalController::class, 'store'])->name('journal.store');
    Route::get('/details/{times}', [JournalController::class, 'details'])->name('journal.details');
    Route::patch('/update', [JournalController::class, 'update'])->name('journal.update')->middleware('access:journal,edit_records');
    Route::patch('/update_document', [JournalController::class, 'update_document'])->name('journal.update_document')->middleware('access:journal,edit_records');
    Route::get('/print/{times}', [JournalController::class, 'print'])->name('journal.print');
    Route::get('/edit/{times}', [JournalController::class, 'edit'])->name('journal.edit')->middleware('access:journal,edit_records');
    Route::delete('/destroy/{times}', [JournalController::class, 'destroy'])->name('journal.destroy')->middleware('access:journal,delete_records');
});

// expense
Route::prefix('expense')->group(function(){
    Route::get('/',[ExpenseController::class, 'index'])->name('expense.index')->middleware('access:expense,list');
    Route::get('/data', [ExpenseController::class, 'getData'])->name('expense.data');
    Route::get('/create',[ExpenseController::class, 'create'])->name('expense.create')->middleware('access:expense,create_records');
    Route::post('/store', [ExpenseController::class, 'store'])->name('expense.store');
    Route::get('/edit/{id}', [ExpenseController::class, 'edit'])->name('expense.edit')->middleware('access:expense,edit_records');
    Route::patch('/update/{id}', [ExpenseController::class, 'update'])->name('expense.update'); 
    Route::get('/destroy/{id}', [ExpenseController::class, 'destroy'])->name('expense.destroy')->middleware('access:expense,delete_records');
});
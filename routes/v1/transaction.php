<?php

use App\Http\Controllers\Transactions\JournalController;
use App\Http\Controllers\Transactions\IncomeController;
use App\Http\Controllers\Transactions\ExpenseController;



 // Journal
 Route::prefix('journal')->group(function(){
    Route::get('/',[JournalController::class, 'index'])->name('journal.index');
    Route::get('/data', [JournalController::class, 'getData'])->name('journal.data');
    Route::get('/create',[JournalController::class, 'create'])->name('journal.create');
    Route::post('/store', [JournalController::class, 'store'])->name('journal.store');
    Route::get('/details/{times}', [JournalController::class, 'details'])->name('journal.details');
    Route::patch('/update', [JournalController::class, 'update'])->name('journal.update');
    Route::patch('/update_document', [JournalController::class, 'update_document'])->name('journal.update_document');
    Route::get('/print/{times}', [JournalController::class, 'print'])->name('journal.print');
    Route::get('/edit/{times}', [JournalController::class, 'edit'])->name('journal.edit');
    Route::delete('/destroy/{times}', [JournalController::class, 'destroy'])->name('journal.destroy');
});

// income
Route::prefix('income')->group(function(){
    Route::get('/',[IncomeController::class, 'index'])->name('income.index');
    Route::get('/data', [IncomeController::class, 'getData'])->name('income.data');
    Route::get('/create',[IncomeController::class, 'create'])->name('income.create'); // show insert form
    Route::post('/store', [IncomeController::class, 'store'])->name('income.store');
    Route::get('/edit/{id}', [IncomeController::class, 'edit'])->name('income.edit');  // show edit form
    Route::patch('/update/{id}', [IncomeController::class, 'update'])->name('income.update'); 
    Route::get('/destroy/{id}', [IncomeController::class, 'destroy'])->name('income.destroy');
});

// expense
Route::prefix('expense')->group(function(){
    Route::get('/',[ExpenseController::class, 'index'])->name('expense.index');
    Route::get('/data', [ExpenseController::class, 'getData'])->name('expense.data');
    Route::get('/create',[ExpenseController::class, 'create'])->name('expense.create'); // show insert form
    Route::post('/store', [ExpenseController::class, 'store'])->name('expense.store');
    Route::get('/edit/{id}', [ExpenseController::class, 'edit'])->name('expense.edit');  // show edit form
    Route::patch('/update/{id}', [ExpenseController::class, 'update'])->name('expense.update'); 
    Route::get('/destroy/{id}', [ExpenseController::class, 'destroy'])->name('expense.destroy');
});
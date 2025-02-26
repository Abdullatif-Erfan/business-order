<?php

use App\Http\Controllers\Transactions\JournalController;
use App\Http\Controllers\Transactions\IncomeController;


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
    Route::get('/create',[IncomeController::class, 'create'])->name('income.create');
    Route::post('/store', [IncomeController::class, 'store'])->name('income.store');
    Route::get('/details/{id}', [IncomeController::class, 'details'])->name('income.details');
    Route::patch('/update/{id}', [IncomeController::class, 'update'])->name('income.update');
    Route::get('/print/{id}', [IncomeController::class, 'print'])->name('income.print');
    Route::get('/edit/{id}', [IncomeController::class, 'edit'])->name('income.edit');
    Route::get('/destroy/{id}', [IncomeController::class, 'destroy'])->name('income.destroy');
});
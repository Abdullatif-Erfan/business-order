<?php

use App\Http\Controllers\Report\ItemController;

Route::prefix('reports')->group(function() {
    Route::get('/home',[ItemController::class, 'home'])->name('reports.home');
    Route::post('/daily',[ItemController::class, 'daily'])->name('reports.daily');
    Route::post('/monthly',[ItemController::class, 'monthly'])->name('reports.monthly');
    Route::post('/yearly',[ItemController::class, 'yearly'])->name('reports.yearly');

});
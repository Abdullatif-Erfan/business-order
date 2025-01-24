<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;


// Route::get('/', function () {
//     return view('login.login');
// });

Route::get('/',[LoginController::class,'login'])->name('login');
// Route::get('/login',[LoginController::class,'login'])->name('login');




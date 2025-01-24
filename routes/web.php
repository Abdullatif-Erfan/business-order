<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RoleController;
use App\Http\Controllers\Auth\UserController;


// Route::get('/', function () {
//     return 'Laravel is working!';
// });

// Route::get('/', function () {
//     return view('welcome'); // Or return a response
// });

Route::get('/',[LoginController::class,'login'])->name('login');
Route::middleware('auth:sanctum')->group(function () { 
    Route::get('/role',[RoleController::class,'index'])->name('role');
});




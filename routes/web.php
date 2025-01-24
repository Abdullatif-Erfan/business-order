<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RoleController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Home\HomeController;


// Route::get('/', function () {
//     return 'Laravel is working!';
// });

// Route::get('/', function () {
//     return view('welcome'); // Or return a response
// });

Route::get('/',[LoginController::class,'login'])->name('login');
Route::post('/loginMe',[LoginController::class,'loginMe'])->name('loginMe');
/**
 * Create a demo user by visiting this route
 * http://127.0.0.1:8000/createUser
 */
Route::get('/createUser',[UserController::class,'createUser'])->name('createUser');


Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/role',[RoleController::class,'index'])->name('role');
    Route::get('/home',[HomeController::class,'index'])->name('home');
});
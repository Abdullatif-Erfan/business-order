<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller; 
 
use App\Models\Auth\Login; 
use Carbon\Carbon; 
use Illuminate\Support\Facades\Cookie; 
use Illuminate\Support\Facades\Crypt; 
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Str; 
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Date; 
use Laravel\Sanctum\HasApiTokens; 

class LoginController extends Controller
{
    public function login()
    {
        return view('login.login');
    }
}

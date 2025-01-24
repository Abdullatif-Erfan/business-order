<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function login()
    {
        // return response()->json(['message' => 'working']);
        return view('login.login');
    }
}

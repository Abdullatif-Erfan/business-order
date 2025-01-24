<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session; // Import Session facade
use Illuminate\Support\Facades\Auth; // Import Auth facade


class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         // Check if user is logged in
        //  $isLoggedIn = auth()->check();

        //  // Get the currently logged-in user (if any)
        //  $user = auth()->user();
 
        //  // Retrieve session data
        //  $sessionData = Session::all();
 
        //  // Debugging: Display login status, user, and session data
        //  dd([
        //      'isLoggedIn' => $isLoggedIn,
        //      'user' => $user,
        //      'sessionData' => $sessionData,
        //  ]);

        return response()->json(['msg' => 'Home Controller is working']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

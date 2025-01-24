<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash; 

use App\Models\Auth\User; 

class UserController extends Controller
{

    public function createUser()
    {
        // Create a sample user record
        $user = User::create([
            'full_name' => 'Abdul Latif',
            'user_name' => 'erfan',
            'email' => 'erfan@gmail.com',
            'password' => Hash::make('password123'), // Hashing the password
            'roleId' => 1, // Example role ID
            'isAdmin' => 1, // Example for admin user
            'isDeleted' => 0, // Not deleted
            'isHidden' => 0, // Not hidden
            'photo' => 'profile_pic.jpg', // Example photo filename
            'createdBy' => 1, // Example user ID who created this user
        ]);

        // Return a response indicating that the user was created
        return response()->json(['message' => 'User created successfully', 'user' => $user]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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

<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller; 
 
use Carbon\Carbon; 
use Illuminate\Support\Facades\Cookie; 
use Illuminate\Support\Facades\Crypt; 
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Str; 
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Date; 
use Laravel\Sanctum\HasApiTokens; 

use App\Models\User; 
use App\Models\Auth\Role; 
use App\Models\Auth\AccessMetrics; 
use App\Models\Auth\Login; 


class LoginController extends Controller
{
    public function __construct()
    {
        // Middleware can be added for authentication checks
    }

    public function login()
    {
        return view('login.login');
    }

     /**
     * Check if the user is logged in
     */
    public function isLoggedIn()
    {
        if (!Session::has('isLoggedIn') || !Session::get('isLoggedIn')) {
            return view('login');
        } else {
            return redirect()->route('home');
        }
    }

    /**
     * Handle login logic
     */
    public function loginMe(Request $request)
    {
        $request->validate([
            'user_name' => 'required|string',
            'password' => 'required|string|min:6',
        ], [
            'user_name.required' => 'نام کاربری ضروری میباشد',
            'password.required' => 'رمز عبور ضروری میباشد',
            // 'password.min' => 'رمز عبور باید حداقل 6 کاراکتر باشد',
        ]);

        $user_name = $request->input('user_name');
        $password = $request->input('password');

    
        $user = User::with(['roleRelationName' => function($query) {
            $query->select('roleId', 'role', 'status', 'isDeleted');
        }])->where('user_name', $user_name)->first();

        // return response()->json(['data' => $user]);
        // dd($user->roleRelationName->status);
        // dd($user->roleRelationName);
        // return ['roleRelationName' => $user->roleRelationName];
        // return ['user' => $user];


        if ($user && Hash::check($password, $user->password)) {
            if ($user->isAdmin != 1 && ($user->roleRelationName->status == 2 || $user->roleRelationName->isDeleted == 1)) {
                Session::flash('not_exist', 'not_exist');
                return redirect()->route('login');
            }
           
            $accessInfo = $this->accessInfo($user->roleId);
            // return response()->json(['accessInfo' => $accessInfo]);

            Session::put([
                'userId' => $user->id,
                'role' => $user->roleId,
                'roleText' => $user->roleRelationName->role,
                'name' => $user->full_name,
                'isAdmin' => $user->isAdmin,
                'accessInfo' => $accessInfo,
                'isLoggedIn' => true,
            ]);

            // Session::put('lang', 'dr');
            // Authenticate the user using Laravel's auth system
            auth()->login($user);
            return redirect()->route('home');
        } else {
            Session::flash('failed', 'failed');
            return redirect()->route('login');
        }
    }

    /**
     * Show the forgot password view
     */
    public function forgotPassword()
    {
        if (!Session::has('isLoggedIn') || !Session::get('isLoggedIn')) {
            return view('users.forgotPassword');
        } else {
            return redirect()->route('dashboard');
        }
    }

    /**
     * Handle password reset link generation
     */
    public function resetPasswordUser(Request $request)
    {
        $request->validate([
            'login_email' => 'required|email',
        ]);

        $email = strtolower($request->input('login_email'));
        $user = User::where('email', $email)->first();

        if ($user) {
            $activationId = Str::random(15);
            $user->update([
                'activation_id' => $activationId,
                'created_at' => now(),
            ]);

            $resetLink = route('resetPasswordConfirmUser', ['activationId' => $activationId, 'email' => urlencode($email)]);

            // Here, send the email logic should be implemented
            // Example: Mail::to($email)->send(new ResetPasswordMail($resetLink));

            Session::flash('success', 'Reset password link sent successfully.');
        } else {
            Session::flash('error', 'This email is not registered with us.');
        }

        return redirect()->route('forgotPassword');
    }

    /**
     * Show the reset password form
     */
    public function resetPasswordConfirmUser($activationId, $email)
    {
        $email = urldecode($email);
        $user = User::where('email', $email)->where('activation_id', $activationId)->first();

        if ($user) {
            return view('users.newPassword', compact('email', 'activationId'));
        } else {
            return redirect()->route('login');
        }
    }

    /**
     * Handle password creation
     */
    public function createPasswordUser(Request $request)
    {
        $request->validate([
            'password' => 'required|string|confirmed|max:20',
        ]);

        $email = $request->input('email');
        $activationId = $request->input('activation_code');
        $password = $request->input('password');

        $user = User::where('email', $email)->where('activation_id', $activationId)->first();

        if ($user) {
            $user->update([
                'password' => Hash::make($password),
                'activation_id' => null, // Clear activation ID after reset
            ]);

            Session::flash('success', 'Password reset successfully.');
        } else {
            Session::flash('error', 'Password reset failed.');
        }

        return redirect()->route('login');
    }

    /**
     * Build access information for roles
     */
    public function accessInfo($roleId)
    {
        // Fetch the role access matrix from the database using Eloquent
        $matrix = AccessMetrics::query()->select('roleId','access')->where('roleId', $roleId)->first();
        
        // Initialize the final matrix array
        $finalMatrixArray = [];

        if ($matrix && !empty($matrix->access)) {
            // Decode the JSON access matrix
            $accessMatrix = json_decode($matrix->access);

            // Loop through each module matrix and build the final matrix array
            foreach ($accessMatrix as $moduleMatrix) {
                $finalMatrixArray[$moduleMatrix->module] = (array) $moduleMatrix;
            }
        }

        // Return the final access matrix array
        return $finalMatrixArray;
    }

    /**
     * Handle relogin logic
     */
    public function relogin($userId)
    {
        $user = User::find($userId);

        if ($user) {
            if ($user->isAdmin != 1 && ($user->role->status == 2 || $user->role->isDeleted == 1)) {
                abort(403, "The role doesn't exist or is inactive");
            }

            $accessInfo = $this->accessInfo($user->role_id);

            Session::put([
                'userId' => $user->id,
                'role' => $user->role_id,
                'roleText' => $user->role->name,
                'name' => $user->name,
                'isAdmin' => $user->isAdmin,
                'accessInfo' => $accessInfo,
                'isLoggedIn' => true,
            ]);

            // Session::put('lang', 'dr');
            // Session::put('comein', 'business@kawoshgaran');

            return redirect()->route('home');
        } else {
            abort(403, "The role doesn't exist or is inactive");
        }
    }

    public function logout(Request $request)
    {
         // Logout the user based on the guard being used
        Auth::guard('web')->logout(); // Replace 'web' with your actual guard if different

        // Clear all session data
        Session::flush();

        // Regenerate session token
        $request->session()->regenerateToken();

        // Redirect to login page with a success message
        return redirect()->route('login')->with('success', 'شما با موفقیت خارج شدید');
    }

}

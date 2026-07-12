<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller; 
 
use Carbon\Carbon; 
use Illuminate\Support\Facades\Cookie; 
use Illuminate\Support\Facades\Crypt; 
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str; 
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Date; 
use Laravel\Sanctum\HasApiTokens; 
use App\Models\Setting\OrgBio;
use App\Models\User; 
use Illuminate\Support\Facades\Schema;
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
        $columns = [];

        if (Schema::hasColumn('org_bios', 'is_expired')) {
            $columns[] = 'is_expired';
        }

        if (Schema::hasColumn('org_bios', 'expired_date')) {
            $columns[] = 'expired_date';
        }

        // If no columns exist → skip safely
        if (empty($columns)) {
            return view('login.login');
        }

        $orgData = OrgBio::select('is_expired', 'expired_date')->first();
    
        if (!$orgData) {
            return view('login.login');
        }
    
        $today = Carbon::now();
        $daysLeft = null;
    
        //  Only parse date if exists
        if (!empty($orgData->expired_date)) {
            $expiredDate = Carbon::parse($orgData->expired_date);
            $daysLeft = (int) ceil($today->diffInDays($expiredDate, false)); // can be negative
        }
    
        //  EXPIRED (by flag OR by date)
        if ($orgData->is_expired == 1 || ($daysLeft !== null && $daysLeft < 0)) {
    
            $daysPassed = $daysLeft !== null ? abs($daysLeft) : null;
    
            Session::flash('expired_days', $daysPassed);
            Session::flash('expired_text', $daysPassed === null ? 'مدت زیادی گذشته است' : null);
    
            return view('login.expiredLoginMessage');
        }
    
        // ✅ NEAR EXPIRY
        if ($daysLeft !== null && $daysLeft <= 20) {
            Session::flash('nearExpired', $daysLeft);
        }
    
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
        // Add debug logging
    //    \Log::debug('Session ID at start: ' . session()->getId());

        $request->validate([
            'user_name' => 'required|string',
            'password' => 'required|string|min:5',
        ], [
            'user_name.required' => __('validate.user_name_required'),
            'password.required'  => __('validate.password_required'),
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

             // Add session verification before putting data
             if (!session()->isStarted()) {
                session()->start();
            }

            // \Log::debug('Session data before put:', session()->all());

            if ($user->isAdmin != 1 && ($user->roleRelationName->status == 2 || $user->roleRelationName->isDeleted == 1)) {
                Session::flash('not_exist', 'not_exist');
                return redirect()->route('login');
            }
           
            $accessInfo = $this->accessInfo($user->roleId);
            // return response()->json(['accessInfo' => $accessInfo]);


            Session::put([
                'userId' => $user->id,
                'accountId' => $user->account_id,
                'role' => $user->roleId,
                'roleText' => $user->roleRelationName->role,
                'name' => $user->full_name,
                'isAdmin' => $user->isAdmin,
                'accessInfo' => $accessInfo,
                'isLoggedIn' => true,
            ]);

            // Session::put('lang', 'dr');
            // Authenticate the user using Laravel's auth system
            // auth()->login($user);

            // \Log::debug('Session data after put:', session()->all());
            session()->save();
            // \Log::debug('Session saved, ID: ' . session()->getId());
            Auth::guard('web')->login($user);
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
        $user = User::with('roleRelationName')->find($userId);

        if ($user) {
            if ($user->isAdmin != 1 && ($user->roleRelationName->status == 2 || $user->roleRelationName->isDeleted == 1)) {
                abort(403, "The role doesn't exist or is inactive");
            }

            $accessInfo = $this->accessInfo($user->roleId);

            Session::put([
                'userId' => $user->id,
                'accountId' => $user->account_id,
                'role' => $user->roleId,
                'roleText' => $user->roleRelationName->name,
                'name' => $user->full_name,
                'isAdmin' => $user->isAdmin,
                'accessInfo' => $accessInfo,
                'isLoggedIn' => true,
            ]);

            // Session::put('lang', 'dr');
            // Session::put('comein', 'business@kawoshgaran');
            // Authenticate the user
            // Auth::login($user);
            //  auth()->login($user);
            Auth::guard('web')->login($user);
            return redirect()->route('home');
        } else {
            abort(403, "The role doesn't exist or is inactive");
        }
    }

    // public function changeBranch(Request $request)
    // {
    //     $user = auth()->user();
    //     // Set new branch_id and isAdmin in session
    //     session()->put('branch_id', $request->branch_id);
    //     // session()->put('isAdmin', 0);
    //     session()->save(); // Force save session

    //     return response()->json(['status' => 'success', 'message' => 'Branch changed successfully!']);
    // }

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

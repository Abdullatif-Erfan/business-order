<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Setting\Account;
use App\Models\Setting\Car;
use App\Models\User; 
use App\Models\Auth\Role; 
use App\Models\Setting\OrgBio;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    protected $isAdmin, $accountId;
    public function __construct()
    {
        if (auth()->check()) {
            $this->isAdmin = session('isAdmin', auth()->user()->isAdmin == 1);
            $this->accountId = session('accountId', auth()->id());
        } 
        else 
        {
            $this->isAdmin = false;
            $this->accountId = 0;
        }
    }

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
        // $users = User::with(['roleRelationName'])->orderBy('created_at','DESC')->get();
        // return ['users' => $users];
        // return $this->userId;
        $orgbios = OrgBio::all();
        return view('management.users.list',compact('orgbios'));
    }


    public function getData(Request $request)
    {
        if ($this->isAdmin) {
            $users = User::with(['roleRelationName', 'account'])
                ->where('isHidden', 0)
                ->orderBy('created_at', 'DESC');
        } else {
            $users = User::with(['roleRelationName', 'account'])
                ->where('account_id', $this->accountId)
                ->where('isHidden', 0)
                ->orderBy('created_at', 'DESC');
        }
        
        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('photo', function ($user) {
                $imagePath = !empty($user->photo) && file_exists(storage_path('app/public/' . $user->photo))
                    ? asset('storage/' . $user->photo)
                    : asset('storage/user_photos/no_image.png');
                
                return '<img src="' . $imagePath . '" alt="image" class="avatar-img rounded" style="width:35px; height:35px; object-fit:cover; border-radius:50%;">';
            })
            ->addColumn('link', function ($user) {
                return $user->account_id && $user->account_id > 0 
                    ? '<span class="badge badge-success"><i class="fas fa-check-circle"></i> ' . __('user.has_account') . '</span>' 
                    : '<span class="badge badge-secondary"><i class="fas fa-times-circle"></i> ' . __('user.no_account') . '</span>';
            })
            ->addColumn('priviledge', function ($user) {
                if ($user->isAdmin) {
                    return '<span class="badge badge-danger">' . __('common.admin') . '</span>';
                }
                return '<span class="badge badge-info">' . ($user->roleRelationName->role ?? __('user.no_role')) . '</span>';
            })
            ->addColumn('relogin', function ($user) {
                return $this->isAdmin 
                    ? '<a href="login/relogin/' . $user->id . '" class="hidden-print" title="' . __('user.relogin') . '">
                        <i class="fas fa-retweet" style="font-size:18px; color:#17a2b8;"></i>
                    </a>' 
                    : ''; 
            })
             ->addColumn('view', function ($user) {
                return '<i class="fas fa-eye viewUser" 
                            data-id="' . $user->id . '" 
                            style="font-size:20px; color: #0d8dc1">
                            </i>';
            })

            ->addColumn('edit', function ($user) {
                return '<a href="user/edit/' . $user->id . '" class="hidden-print" title="' . __('common.edit') . '">
                        <i class="fas fa-pen" style="font-size:18px; color:#007bff;"></i>
                    </a>'; 
            })
            ->addColumn('delete', function ($user) {
                return $this->isAdmin 
                    ? '<a href="user/delete/' . $user->id . '" onclick="return doConfirm();" class="hidden-print" title="' . __('common.delete') . '">
                        <i class="fa fa-trash" style="font-size:18px; color:#dc3545;"></i>
                    </a>' 
                    : ''; 
            })
            ->rawColumns(['photo', 'relogin', 'edit','view', 'delete', 'link', 'priviledge'])
            ->make(true);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if(!$this->isAdmin)
        {
            echo "Just Admin can create user";
            die();
        }
        $roles = Role::all();
        $orgbios = OrgBio::all();
        $isAdmin = $this->isAdmin ?? 0;
        // get list of employess
        $accounts = Account::select('id', 'name')->where('account_type_id',2)->get();

        // get list of customers 
        $customers  = Account::select('id', 'name')->where('account_type_id',3)->get(); 
        $cars = Car::select('id','name')->get();
        return view('management.users.create',compact('roles','orgbios','isAdmin','accounts','customers','cars'));
    }

    /**
    * Store a newly created resource in storage.
    */

     public function store(Request $request)
    {
        // Validate the request with conditional rules
        $validated = $request->validate([
            'full_name' => 'required|string|min:5|max:128',
            'user_name' => 'required|string|min:5|max:128|unique:users,user_name',
            'email' => 'nullable|email|max:128|unique:users,email',
            'password' => 'required|string|min:5|max:20|confirmed',
            'roleId' => 'required|exists:roles,roleId',
            'isAdmin' => 'required|boolean',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'account_id' => $request->isAdmin == 0 ? 'required|exists:accounts,id' : 'nullable|exists:accounts,id',
            'car_ids' => $request->isAdmin == 0 ? 'required|array|min:1' : 'nullable|array',
            'car_ids.*' => 'exists:cars,id',
            'customer_ids' => $request->isAdmin == 0 ? 'required|array|min:1' : 'nullable|array',
            'customer_ids.*' => 'exists:accounts,id',
        ]);

        try {
            DB::beginTransaction();

            // Create user
            $user = User::create([
                'full_name' => $validated['full_name'],
                'user_name' => $validated['user_name'],
                'email' => $validated['email'] ?? null,
                'password' => Hash::make($validated['password']),
                'roleId' => $validated['roleId'],
                'isAdmin' => $validated['isAdmin'],
                'account_id' => $validated['account_id'] ?? null,
                'car_ids' => $validated['car_ids'] ?? [],
                'customer_ids' => $validated['customer_ids'] ?? [],
                'createdBy' => auth()->id(),
            ]);

            // Handle photo upload
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('user_photos', 'public');
                $user->photo = $photoPath;
                $user->save();
            }

            // Update account with user reference
            if (!empty($validated['account_id'])) {
                Account::where('id', $validated['account_id'])
                    ->update(['user_account_id' => $user->id]);
            }

            DB::commit();

            return redirect()->route('user.index')->with('notification', [
                'message' => __('common.added_successfully'), 
                'type' => 'success'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating user: ' . $e->getMessage());

            return redirect()->back()->withInput()->with('notification', [
                'message' => __('common.add_failed') . ': ' . $e->getMessage(), 
                'type' => 'danger'
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::with(['account', 'roleRelationName'])->findOrFail($id);
        $orgbios = OrgBio::all();
        
        // Load assigned cars and customers
        $assignedCars = [];
        $assignedCustomers = [];
        
        if (!empty($user->car_ids) && is_array($user->car_ids)) {
            $assignedCars = Car::whereIn('id', $user->car_ids)->get();
        }
        
        if (!empty($user->customer_ids) && is_array($user->customer_ids)) {
            $assignedCustomers = Account::whereIn('id', $user->customer_ids)
                ->where('account_type_id', 3)
                ->get();
        }
        // return ['user' => $user,  'assignedCars' => $assignedCars, 'assignedCustomers' => $assignedCustomers];

        return view('management.users.show', compact('user', 'orgbios', 'assignedCars', 'assignedCustomers'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $roles = Role::all();
        $orgbios = OrgBio::all();
        
        // Eager load the account relationship
        $user = User::with('account')->findOrFail($id);
        
        $isAdmin = $this->isAdmin ?? 0;
        
        // Get list of employees, customers, and cars
        $accounts = Account::select('id', 'name')
            ->whereIn('account_type_id', [2, 3])
            ->get();
        
        $customers = Account::select('id', 'name')
            ->where('account_type_id', 3)
            ->get();
        
        $cars = Car::select('id', 'name')->get();
        
        // Get the user's current account
        $userAccount = $user->account; // Returns Account model or null
        
        return view('management.users.edit', compact(
            'roles', 
            'orgbios', 
            'user', 
            'isAdmin', 
            'accounts', 
            'userAccount',
            'customers',
            'cars'
        ));
    }
    


     /**
     * Update the specified resource in storage.
     */
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        
        // Build validation rules conditionally
        $rules = [
            'full_name' => 'required|string|min:5|max:128',
            'user_name' => 'required|string|min:5|max:128|unique:users,user_name,' . $id,
            'email' => 'nullable|email|max:128|unique:users,email,' . $id,
            'password' => 'nullable|string|min:5|max:20|confirmed',
            'roleId' => 'required|exists:roles,roleId',
            'isAdmin' => 'required|boolean',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'old_account_id' => 'nullable|exists:accounts,id',
        ];

        //  Add conditional validation for simple users
        if ($request->isAdmin == 0) {
            $rules['account_id'] = 'required|exists:accounts,id';
            $rules['car_ids'] = 'required|array|min:1';
            $rules['car_ids.*'] = 'exists:cars,id';
            $rules['customer_ids'] = 'required|array|min:1';
            $rules['customer_ids.*'] = 'exists:accounts,id';
        } else {
            $rules['account_id'] = 'nullable|exists:accounts,id';
            $rules['car_ids'] = 'nullable|array';
            $rules['car_ids.*'] = 'exists:cars,id';
            $rules['customer_ids'] = 'nullable|array';
            $rules['customer_ids.*'] = 'exists:accounts,id';
        }

        // Validate the request
        $validated = $request->validate($rules);

        try {
            DB::beginTransaction();

            //  Update user data
            $user->full_name = $validated['full_name'];
            $user->user_name = $validated['user_name'];
            $user->email = $validated['email'] ?? null;
            $user->roleId = $validated['roleId'];
            $user->isAdmin = $validated['isAdmin'];
            
            //  Update password only if provided
            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }
            
            //  Update car_ids and customer_ids as JSON
            $user->car_ids = $validated['car_ids'] ?? [];
            $user->customer_ids = $validated['customer_ids'] ?? [];
            
            // Update account_id
            $user->account_id = $validated['account_id'] ?? null;

            //  Handle photo upload
            if ($request->hasFile('photo')) {
                // Delete old photo if exists
                if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                    Storage::disk('public')->delete($user->photo);
                }
                $photoPath = $request->file('photo')->store('user_photos', 'public');
                $user->photo = $photoPath;
            }

            $user->save();

            // Update account with user reference
            $oldAccountId = $validated['old_account_id'] ?? null;
            $newAccountId = $validated['account_id'] ?? null;

            // Remove user reference from old account
            if (!empty($oldAccountId) && $oldAccountId != $newAccountId) {
                Account::where('id', $oldAccountId)
                    ->where('user_account_id', $user->id)
                    ->update(['user_account_id' => null]);
            }

            // Add user reference to new account
            if (!empty($newAccountId)) {
                Account::where('id', $newAccountId)
                    ->update(['user_account_id' => $user->id]);
            }

            DB::commit();

            return redirect()->route('user.index')->with('notification', [
                'message' => __('common.updated_successfully'), 
                'type' => 'success'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating user: ' . $e->getMessage());

            return redirect()->back()->withInput()->with('notification', [
                'message' => __('common.update_failed') . ': ' . $e->getMessage(), 
                'type' => 'danger'
            ]);
        }
    }

    /**
     * Update account association for user
     */
    private function updateAccountAssociation($userId, $accountId)
    {
        // if (!empty($accountId)) {
            // Remove old reference from previous account
            Account::where('user_account_id', $userId)
                ->where('id', '!=', $accountId)
                ->update(['user_account_id' => null]);
            
            // Set new reference
            Account::where('id', $accountId)
                ->update(['user_account_id' => $userId]);
        // } else {
        //     // Remove all references
        //     Account::where('user_account_id', $userId)
        //         ->update(['user_account_id' => null]);
        // }
    }

    

    /**
     * Remove the specified resource from storage.
     */
    public function delete(string $id)
    {
        try {
            // Find the user
            $user = User::find($id);
            
            if (!$user) {
                Session::put('notification', [
                    'message' => __('common.record_not_found'), 
                    'type' => 'danger'
                ]);
                return redirect()->route('user.index');
            }

            // First, update accounts to remove user reference
            Account::where('user_account_id', $id)->update(['user_account_id' => null]);

            // Then delete the user
            $user->delete();

            Session::put('notification', [
                'message' => __('common.deleted_successfully'), 
                'type' => 'success'
            ]);
            return redirect()->route('user.index');

        } catch (\Exception $e) {
            \Log::error('Error deleting user: ' . $e->getMessage());
            
            Session::put('notification', [
                'message' => __('common.delete_failed') . ': ' . $e->getMessage(), 
                'type' => 'danger'
            ]);
            return redirect()->route('user.index');
        }
    }
}

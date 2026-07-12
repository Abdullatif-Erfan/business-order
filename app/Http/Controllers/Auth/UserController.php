<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Setting\Account;
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
          if($this->isAdmin)
          {
              $users = User::with(['roleRelationName'])->where('isHidden',0)->orderBy('created_at','DESC');
          }
          else 
          {
             $users = User::with(['roleRelationName'])->where('users.account_id',$this->accountId)->where('isHidden',0)->orderBy('created_at','DESC');
          }
           
            
            return DataTables::of($users)
            
            ->addIndexColumn()
           
            ->addColumn('photo', function ($user) {
                $imagePath = !empty($user->photo) && file_exists(storage_path('app/public/' . $user->photo))
                    ? asset('storage/' . $user->photo)
                    : asset('storage/user_photos/no_image.png');
            
                return '<img src="' . $imagePath . '" alt="image" class="avatar-img rounded" style="width:30px;margin:2px 0px;">';
            })


            ->addColumn('link', function ($user) {
                return $user->account_id && $user->account_id > 0 ? '<i class="fas fa-check-circle success"></>' : 
                '<i class="fas fa-times default"></>';
            })

            ->addColumn('priviledge', function ($user) {
                return $user->isAdmin ? __('common.admin') : $user->roleRelationName->role;
            })

            ->addColumn('relogin', function ($user) {
                return $this->isAdmin ? '<a href="login/relogin/'.$user->id.'" class="hidden-print"><i class="fas fa-retweet" 
                data-id="' . $user->id . '" style="font-size:20px;"></i></a>' : ''; 
            })

            ->addColumn('edit', function ($user) {
                return '<a href="user/edit/'.$user->id.'" class="hidden-print"><i class="fas fa-pen" 
                data-id="' . $user->id . '" style="font-size:20px;"></i></a>'; 
            })

            ->addColumn('delete', function ($user) {
                return $this->isAdmin ? '<a href="user/delete/'.$user->id.'" onclick="doConfirm()" class="hidden-print"><i class="fa fa-trash" 
                data-id="' . $user->id . '" style="font-size:20px; color:red"></i></a>': ''; 
            })
            ->rawColumns(['photo','relogin','edit','delete','link'])
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
        // get list of customers and employess or drivers
        $accounts = Account::select('id', 'name')->whereIn('account_type_id', [2, 3])->get(); 
        return view('management.users.create',compact('roles','orgbios','isAdmin','accounts'));
    }

    /**
    * Store a newly created resource in storage.
    */

    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'full_name' => 'required|string|min:5|max:128',
            'user_name' => 'required|string|min:5|max:128|unique:users,user_name',
            'email' => 'nullable|email|max:128|unique:users,email',
            'password' => 'required|string|min:5|max:20|confirmed',
            'roleId' => 'required|exists:roles,roleId',
            'isAdmin' => 'required|boolean',
            'account_id' => 'nullable|exists:accounts,id', 
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
            DB::beginTransaction();

            // Create user
            $user = new User();
            $user->full_name = $validated['full_name'];
            $user->user_name = $validated['user_name'];
            $user->email = $validated['email'] ?? null;
            $user->password = Hash::make($validated['password']);
            $user->roleId = $validated['roleId'];
            $user->isAdmin = $validated['isAdmin'];
            $user->account_id = $validated['account_id'] ?? null;
            $user->createdBy = auth()->id();

            // Handle photo upload
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('user_photos', 'public');
                $user->photo = $photoPath;
            }

            $user->save();

            // Update account with user reference
            if (!empty($validated['account_id'])) {
                Account::where('id', $validated['account_id'])
                    ->update(['user_account_id' => $user->id]);
            }

            DB::commit();

            Session::put('notification', [
                'message' => __('common.added_successfully'), 
                'type' => 'success'
            ]);
            return redirect()->route('user.index');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating user: ' . $e->getMessage());

            Session::put('notification', [
                'message' => __('common.add_failed') . ': ' . $e->getMessage(), 
                'type' => 'danger'
            ]);
            return redirect()->route('user.index')->withInput();
        }
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
        $roles = Role::all();
        $orgbios = OrgBio::all();
        
        // Eager load the account relationship
        $user = User::with('account')->findOrFail($id);
        
        $isAdmin = $this->isAdmin ?? 0;
        
        // Get list of customers, employees, or drivers
        $accounts = Account::select('id', 'name')
            ->whereIn('account_type_id', [2, 3])
            ->get();
        
        // Get the user's current account
        $userAccount = $user->account; // Returns Account model or null
        
        return view('management.users.edit', compact('roles', 'orgbios', 'user', 'isAdmin', 'accounts', 'userAccount'));
    }


     /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Base validation rules
        $rules = [
            'full_name' => 'required|string|min:5|max:128',
            'user_name' => 'required|string|min:5|max:128|unique:users,user_name,' . $id,
            'email' => 'nullable|email|max:128|unique:users,email,' . $id,
            'password' => 'nullable|string|min:5|max:20|confirmed',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'account_id' => 'nullable',
            'old_account_id' => 'nullable',
        ];

        // Only admins can update these fields
        if (auth()->user()->isAdmin) {
            $rules['roleId'] = 'nullable|exists:roles,roleId';
            $rules['isAdmin'] = 'nullable|boolean';
        }

        $validated = $request->validate($rules);

        try {
            DB::beginTransaction();

            $user = User::findOrFail($id);

            // Update basic fields
            $user->full_name = $validated['full_name'];
            $user->user_name = $validated['user_name'];
            $user->email = $validated['email'] ?? null;
            $user->account_id = $validated['account_id'] ?? null;

            // Only update admin fields if user is admin
            if (auth()->user()->isAdmin) {
                if (isset($validated['roleId'])) {
                    $user->roleId = $validated['roleId'];
                }
                if (isset($validated['isAdmin'])) {
                    $user->isAdmin = $validated['isAdmin'];
                }
            }

            // Update password if provided
            if ($request->filled('password')) {
                $user->password = Hash::make($request->input('password'));
            }

            // Handle photo upload
            if ($request->hasFile('photo')) {
                // Delete old photo
                if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                    Storage::disk('public')->delete($user->photo);
                }
                
                $photoPath = $request->file('photo')->store('user_photos', 'public');
                $user->photo = $photoPath;
            }

            $user->save();

            // Update account association
            // قبلا حساب انتخاب شده بود وحالا یا حساب دیگر ویا پاک ساختیم باید اکاونت تیبل نیز آپدیت و نل شود
            if(!empty($validated['old_account_id'])) {
                $this->updateAccountAssociation($user->id, $validated['account_id'] ?? null);
            }

            DB::commit();

            Session::put('notification', [
                'message' => __('common.updated_successfully'), 
                'type' => 'success'
            ]);
            return redirect()->route('user.index');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            Session::put('notification', [
                'message' => __('common.record_not_found'), 
                'type' => 'danger'
            ]);
            return redirect()->route('user.index');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating user: ' . $e->getMessage());

            Session::put('notification', [
                'message' => __('common.update_failed') . ': ' . $e->getMessage(), 
                'type' => 'danger'
            ]);
            return redirect()->route('user.index')->withInput();
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

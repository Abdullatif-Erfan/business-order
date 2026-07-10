<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

use App\Models\User; 
use App\Models\Auth\Role; 
use App\Models\Setting\OrgBio;
use App\Models\Setting\Branch;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    protected $branch_id, $isAdmin;
    public function __construct()
    {
        if (auth()->check()) {
            $this->branch_id = session('branch_id', auth()->user()->branch_id ?? 0);
            $this->isAdmin = session('isAdmin', auth()->user()->isAdmin == 1);
        } else {
            $this->branch_id = 0;
            $this->isAdmin = false;
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
        // $users = User::with(['roleRelationName','branchRelation'])->orderBy('created_at','DESC')->get();
        // return ['users' => $users];
        $orgbios = OrgBio::all();
        return view('management.users.list',compact('orgbios'));
    }


    public function getData(Request $request)
    {
          if($this->isAdmin)
          {
              $users = User::with(['roleRelationName','branchRelation'])->where('isHidden',0)->orderBy('created_at','DESC');
          }
          else 
          {
             $users = User::with(['roleRelationName','branchRelation'])->where('isHidden',0)->where('branch_id', $this->branch_id)->orderBy('created_at','DESC');
          }
           
            
            return DataTables::of($users->get())
            
            ->addIndexColumn()
           
            ->addColumn('photo', function ($user) {
                $imagePath = !empty($user->photo) && file_exists(storage_path('app/public/' . $user->photo))
                    ? asset('storage/' . $user->photo)
                    : asset('storage/user_photos/no_image.png');
            
                return '<img src="' . $imagePath . '" alt="image" class="avatar-img rounded" style="width:30px;margin:2px 0px;">';
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
            ->rawColumns(['photo','relogin','edit','delete'])
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
        $branches = Branch::all();
        $isAdmin = $this->isAdmin ?? 0;
        return view('management.users.create',compact('roles','orgbios','branches','isAdmin'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // return ['data' => $request->all()];
        $validated = $request->validate([
            'full_name' => 'required|string|min:5|max:128',
            'user_name' => 'required|string|min:5|max:128|unique:users,user_name',
            'email' => 'nullable|email|max:128|unique:users,email',
            'password' => 'required|string|min:5|max:20|confirmed',
            'roleId' => 'required|exists:roles,roleId',
            'branch_id' => 'required|exists:branches,id',
            'isAdmin' => 'required|boolean',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = new User();
        $user->full_name = $validated['full_name'];
        $user->user_name = $validated['user_name'];
        $user->email = $validated['email'];
        $user->password = Hash::make($validated['password']);
        $user->roleId = $validated['roleId'];
        $user->isAdmin = $validated['isAdmin'];
        $user->branch_id = $validated['branch_id'];
        $user->createdBy = auth()->id();

        if ($request->hasFile('photo')) {
            $user->photo = $request->file('photo')->store('user_photos', 'public');
        }

        $user->save();
        
        Session::put('notification', ['message' => __('common.admin'), 'type' => 'success']);
        return redirect()->route('user.index');
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
        $user = User::findOrFail($id);
        $branches = Branch::all();
        $isAdmin = $this->isAdmin ?? 0;

        return view('management.users.edit',compact('roles','orgbios','user','branches','isAdmin'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {  
        $id = $id ?? 0;

        // Validate the incoming data
        $validated = $request->validate([
            'full_name' => 'required|string|min:5|max:128',
            'user_name' => 'required|string|min:5|max:128|unique:users,user_name,' . $id,
            'email' => 'nullable|email|max:128|unique:users,email,' . $id,
            'password' => 'nullable|string|min:5|max:20|confirmed',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Find the user
        $user = User::findOrFail($id);

        // Update only allowed fields
        $user->full_name = $validated['full_name'];
        $user->user_name = $validated['user_name'];
        $user->email = $validated['email'] ?? $user->email;

        // Only an admin can update these fields
        if (auth()->user()->isAdmin) {
            $validatedAdminFields = $request->validate([
                'roleId' => 'nullable|exists:roles,roleId',
                'branch_id' => 'nullable|exists:branches,id',
                'isAdmin' => 'nullable|boolean',
            ]);

            $user->roleId = $validatedAdminFields['roleId'] ?? $user->roleId;
            $user->branch_id = $validatedAdminFields['branch_id'] ?? $user->branch_id;
            $user->isAdmin = $validatedAdminFields['isAdmin'] ?? $user->isAdmin;
        }

        // Update password if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        // Handle photo upload
        if ($request->hasFile('photo')) {
            if ($user->photo && Storage::exists('public/user_photos/' . $user->photo)) {
                Storage::delete('public/user_photos/' . $user->photo);
            }

            $user->photo = $request->file('photo')->store('user_photos', 'public');
        }

        // Save the updated user data
        $user->save();

        Session::put('notification', ['message' => __('common.updated_successfully'), 'type' => 'success']);
        return redirect()->route('user.index');
    }

    

    /**
     * Remove the specified resource from storage.
     */
    public function delete(string $id)
    {
        $user = User::findOrFail($id);
        if($user)
        {
            $user->delete();
            Session::put('notification', ['message' => __('deleted_successfully'), 'type' => 'danger']);
            return redirect()->route('user.index');
        }

        Session::put('notification', ['message' => __('delete_failed'), 'type' => 'danger']);
        return redirect()->route('user.index');

    }
}

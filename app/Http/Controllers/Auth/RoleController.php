<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Auth\Role;
use App\Models\Auth\AccessMetrics;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    /**
     * Display a listing of the roles with pagination.
     */
    public function index()
    {
        $roles = Role::orderBy('created_at', 'desc')->paginate(10);
        return view('management.roles.list', compact('roles'));
    }

    public function getData(Request $request)
    {
          
            
          $roles = Role::orderBy('created_at','DESC');
           
            
            return DataTables::of($roles->get())
            
            ->addIndexColumn()

            ->addColumn('status', function ($role) {
                return $role->status ? 'فعال' : 'غیرفعال';
            })
             
            ->addColumn('add', function ($role) {
                return '<a href="roles/permissions/'.$role->roleId.'" class="hidden-print"><i class="btn btn-sm btn-success" 
                data-id="' . $role->roleId . '" style=""> صلاحیت ( + / - )</i></a>'; 
            })

            ->addColumn('edit', function ($role) {
                return '<a href="roles/edit/'.$role->roleId.'" class="hidden-print"><i class="fas fa-pen-square" 
                data-id="' . $role->roleId . '" style="font-size:20px;"></i></a>'; 
            })

            ->addColumn('delete', function ($role) {
                return '<a href="roles/destroy/'.$role->roleId.'" onclick="doConfirm()" class="hidden-print"><i class="fas fa-trash-alt danger" 
                data-id="' . $role->roleId . '" style="font-size:20px; color:red"></i></a>'; 
            })
            ->rawColumns(['add','edit','delete'])
            ->make(true);

    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        return view('management.roles.create');
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'role' => 'required|string|max:255|unique:roles,role',
            'status' => 'required|boolean',
        ]);

        Role::create([
            'role' => $request->role,
            'status' => $request->status,
            'createdBy' => auth()->id(),
            'isDeleted' => false,
        ]);

        Session::flash('notification', ['message' => 'رول موفقانه اضافه شد', 'type' => 'success']);
        return redirect()->route('roles.index');
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit($roleId)
    {
        $role = Role::where('roleId', $roleId)->firstOrFail();
        // return ['roles' => $role];
        return view('management.roles.edit', compact('role'));
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, $roleId) // Use $roleId instead of $id
    {
        // Find the role by roleId (which is the primary key)
        // $role = Role::findOrFail($roleId);
        $role = Role::where('roleId', $roleId)->first();
    
        // Validate the input
        $request->validate([
            'role' => 'required|string',
            'status' => 'required|boolean',
        ]);
    
        // Update the role
        $role->update([
            'role' => $request->role,
            'status' => $request->status,
        ]);
    
        // Flash success message
        Session::flash('notification', ['message' => 'رول موفقانه ویرایش شد', 'type' => 'success']);
    
        // Redirect to roles index
        return redirect()->route('roles.index');
    }
    

    /**
     * Remove the specified role from storage.
     */
    public function destroy($roleId)
    {
        $role = Role::findOrFail($roleId);
        $role->delete();

        Session::flash('notification', ['message' => 'رول موفقانه حذف شد', 'type' => 'success']);
        return redirect()->route('roles.index');
    }


    /**
     * Add permission
     */
    public function permissions(Request $request, string $roleId)
    {
       $roleInfo = $this->getRoleInfo($roleId);
       $roleAccessMatrix = $this->getRoleAccessMatrix($roleId);
    //    $moduleList  = config('modules.moduleList');

    //    return ['roleInfo' => $roleInfo];
    //    return ['roleInfo' => $roleInfo,'roleAccessMatrix' => $roleAccessMatrix,'moduleList' => $moduleList];
       return view('management.permission.list', compact('roleInfo', 'roleAccessMatrix','roleId'));
    }

     /**
     * This function used to get role information by id
     * @param number $roleId : This is role id
     * @return array $result : This is role information
     */
    private function getRoleInfo($roleId)
    {
        $roleInfo = Role::select('roleId','role','status')->where('roleId',$roleId)->where('isDeleted', 0)->first();
        return $roleInfo;
    }
     /**
     * This function used to get access matrix of a role by roleId.
     * If the access matrix entry doesn't exists then it creates the matrix.
     * @param number $roleId : This is roleId of role
     */
    private function getRoleAccessMatrix($roleId)
    {
        $result = $this->getRoleAccessMatrixQuery($roleId);

        if(is_null($result)) 
        {
            // Access the module list from the config file
            $modules = config('modules.moduleList');

            // Prepare the access matrix
            $accessMatrix = [
                'roleId' => $roleId,
                'access' => json_encode($modules),
                'createdBy' => auth()->id(),
                'createdDtm' => now(),
            ];

             // Insert the new access matrix
            $this->insertAccessMatrix($accessMatrix);

            $result = $this->getRoleAccessMatrixQuery($roleId);
        }

        return json_decode($result->access, true);
        // return $result;

    }

    /**
     * This function used to get role access matrix by role id
     * @param number $roleId : This is roleId of role
     */
    private function getRoleAccessMatrixQuery($roleId)
    {
        $AccessMetrics = AccessMetrics::select('roleId','access')
        ->where('roleId',$roleId)
        ->first();
        return $AccessMetrics;
    }

    /**
     * This method is used to insert default access rights when a role gets created
     */
    private function insertAccessMatrix($accessMatrix)
    {
        AccessMetrics::create($accessMatrix);
    }

    function store_permission(Request $request)
    {
        // return ['data' => $request->all()];

        // Get role ID from request
        $roleId = $request->input('roleId');

        // Get access permissions from request
        $postParams = $request->input('access');

        // Define all possible permission fields
        $defaultPermissions = [
            'label' => 0,
            'total_access' => 0,
            'list' => 0,
            'create_records' => 0,
            'edit_records' => 0,
            'delete_records' => 0,
        ];

        // Process each module's permissions
        $modules2 = [];
        foreach ($postParams as $module => $permissions) {
            $singleModule = ['module' => $module];

            // Ensure all fields are present, setting defaults if missing
            foreach ($defaultPermissions as $key => $defaultValue) {
                $singleModule[$key] = isset($permissions[$key]) && $permissions[$key] == 'on' ? 1 : $defaultValue;
            }

            $modules2[] = $singleModule;
        }

        // Prepare the access matrix for storage
        $accessMatrix = [
            'roleId' => $roleId,
            'access' => json_encode($modules2),
            'isDeleted' => 0,
            'createdBy' => auth()->id() ?? '',  
            'createdDtm' => now(),
            'updatedBy' => auth()->id() ?? '',
            'updatedDtm' => now(),
        ];

        // Store or update access matrix in database
        $updated = AccessMetrics::updateOrCreate(['roleId' => $roleId], $accessMatrix);

        if ($updated) {

            Session::flash('notification', [
                'message' => 'موفقانه ویرایش گردید',
                'type' => 'success',
            ]);
    
            return redirect()->route('roles.index');

        } else {
            
            Session::flash('notification', [
                'message' => ' ویرایش نگردید',
                'type' => 'danger',
            ]);
            return redirect()->route('roles.index');
        }

    }

    
}

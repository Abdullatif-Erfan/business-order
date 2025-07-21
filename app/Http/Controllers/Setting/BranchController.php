<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Setting\Branch;
use Yajra\DataTables\Facades\DataTables;

use App\Models\Buy\BoughtItem;
use App\Models\Buy\BuyPreList;
use App\Models\Warehouse\WarehouseItem;
use App\Models\Setting\Warehouse;
use App\Models\Warehouse\WarehouseSales;
use App\Models\Transaction\Journal;

class BranchController extends Controller
{
    // protected $module;
    // public function __construct()
    // {
    //     $this->isLoggedIn();
    //     $this->module = 'settings';	
    // }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $branches = Branch::latest()->paginate(10); // Adjust pagination size as needed
        // return response()->json($branches);
        if($request->ajax())
        {
            $user = auth()->user();
            $branch_id = $user->branch_id ?? 0;
            $isAdmin = $user->isAdmin == 1; 

            if(!$isAdmin)
            {
                $branchs = Branch::query()->where('id',$branch_id)->orderBy('id', 'DESC');
            } 
            else 
            {
                $branchs = Branch::query()->orderBy('id', 'DESC');
            }


            return DataTables::eloquent($branchs)

            // ->addColumn('edit', function($branch) {
            //     return '<a href="'.route('branch.edit', $branch->id).'" data-id="'.$branch->id.'">
            //        <i class="fas fa-pen-square editBranch" style="font-size:20px;"></i>
            //     </a>';
            // })

            // Add Index Column
            ->addIndexColumn()

            ->addColumn('edit', function($branch) use ($isAdmin) {
                return $isAdmin ? '<i class="fas fa-pen-square editBranch" data-id="'.$branch->id.'" style="font-size:20px;"></i>': '';
            })
            ->addColumn('delete', function($branch) use ($isAdmin) {
                return $isAdmin && $branch->is_disabled == 0 ? '<i class="fas fa-trash-alt deleteBranch" data-id="'.$branch->id.'" style="font-size:20px; color:red;"></i>' : '';
            })
            ->rawColumns(['edit','delete'])
            ->make(true);
            // dd($branch); 
        }


    }

    public function create()
    {
        return view('settings.branch.addForm');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Define custom validation messages
        $messages = [
            'name.required'        => __('validate.branch_name_required'),
            'name.string'          => __('validate.branch_name_string'),
            'name.max'             => __('validate.branch_name_max'),
            'name.min'             => __('validate.branch_name_min'),
            'responsible.required' => __('validate.branch_responsible_required'),
            'phone.required'       => __('validate.branch_phone_required'),
            'address.required'     => __('validate.branch_address_required'),
        ];

        // Validate the request
         $validated = $request->validate([
            'name' => 'required|string|max:255|min:5',
            'responsible' => 'required|min:4',
            'phone'       => 'required',
            'email'       => 'nullable',
            'address'     => 'required|string|min:4|max:255'
         ], $messages);

        // Create new branch
        Branch::create($validated);

        // Return success response
        return response()->json(['status' => 'success', 'message' => __('common.added_successfully')]);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $branch = Branch::where('id',$id)->first(); 
        if (!$branch) {
            return response()->json(['status' => 'failed', 'message' => __('common.not_found')], 404);
        }
        return view('settings.branch.editForm', compact('branch'));
         
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $messages = [
            'name.required'        => __('validate.branch_name_required'),
            'name.string'          => __('validate.branch_name_string'),
            'name.max'             => __('validate.branch_name_max'),
            'name.min'             => __('validate.branch_name_min'),
            'responsible.required' => __('validate.branch_responsible_required'),
            'phone.required'       => __('validate.branch_phone_required'),
            'address.required'     => __('validate.branch_address_required'),
        ];

        // Validate the request
        $validated = $request->validate([
            'id'          => 'required|exists:branches,id',
            'name'        => 'required|string|max:255|min:5',
            'responsible' => 'required|string|min:4',
            'phone'       => 'required',
            'email'       => 'nullable|email',
            'address'     => 'required|string|min:4|max:255'
        ], $messages);

        $branch = Branch::find($request->id);

        // Update the branch's information
        $branch->update($request->except('id'));

        return response()->json(['status' => 'success', 'message' => __('validate.updated_successfully')], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $branch = Branch::findOrFail($id);
        
        // Check if any related record exists
        $journalExists = Journal::where('branch_id', $id)->exists();
        $boughtItemExists = BoughtItem::where('branch_id', $id)->exists();
        $buyPreListExists = BuyPreList::where('branch_id', $id)->exists();
        $warehouseExists = Warehouse::where('branch_id', $id)->exists();
        $warehouseSalesExists = WarehouseSales::where('branch_id', $id)->exists();
    
        // If any record exists, prevent deletion
        if ($journalExists || $boughtItemExists  || $buyPreListExists || $warehouseExists || $warehouseSalesExists) 
        {
            return response()->json(['status' => 'failed', 'message' => __('validate.has_records_in_tables')]);
        }
    
        // If no related records exist, delete the currency
        $branch->delete();
        return response()->json(['status' => 'success', 'message' => __('common.deleted_successfully')]);
    }
    

}

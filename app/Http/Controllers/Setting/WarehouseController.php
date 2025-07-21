<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\Setting\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

use App\Models\Warehouse\WarehouseItem;
use App\Models\Setting\Warehouse;
use App\Models\Warehouse\WarehouseSales;


class WarehouseController extends Controller
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

    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Base query based on user role
            if (!$this->isAdmin) {
                $warehousesQuery = Warehouse::with('branch')->where('branch_id', $this->branch_id);
            } else {
                $warehousesQuery = Warehouse::with('branch');
            }
    
            // Get IDs of the two oldest records
            $oldestRecords = (clone $warehousesQuery)->orderBy('id', 'ASC')->limit(2)->pluck('id')->toArray();
    
            // Apply ordering for DataTables
            $warehouses = $warehousesQuery->orderBy('id', 'DESC');
    
            return DataTables::eloquent($warehouses)
                ->addIndexColumn()
                ->addColumn('edit', function ($warehouse) {
                    return '<i class="fas fa-pen-square editWarehouse" data-id="' . $warehouse->id . '" style="font-size:20px; cursor: pointer;"></i>';
                })
                ->addColumn('delete', function ($warehouse) use ($oldestRecords) {
                    // Prevent deletion of the two oldest records
                    if (in_array($warehouse->id, $oldestRecords)) {
                        return '<br>'; // Do not show delete icon
                    }
                    return '<i class="fas fa-trash-alt deleteWarehouse" data-id="' . $warehouse->id . '" style="font-size:20px; color:red; cursor: pointer;"></i>';
                })
                ->rawColumns(['edit', 'delete'])
                ->make(true);
        }
    }
    

    


    public function create()
    {
        if(!$this->isAdmin)
        {
            $branchs = Branch::where('id',$this->branch_id)->get();
        } 
        else 
        {
            $branchs = Branch::all();
        }
        return view('settings.warehouse.addForm',compact('branchs'));
    }

    public function store(Request $request)
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

        $request->validate([
            'name' => 'required|max:255|min:5',
            'branch_id' => 'required|exists:branches,id',
            'responsible' => 'required|max:100',
            'address' => 'required|max:255',
        ], $messages);

        $warehouse = Warehouse::create($request->all());

        return response()->json(['status' => 'success']);
    }



    public function show($id)
    {
        $warehouse = Warehouse::with('branch')->find($id);

        if (!$warehouse) {
            return response()->json(['message' => 'Warehouse not found'], 404);
        }

        // get branch for dropdown
        if(!$this->isAdmin)
        {
            $branchs = Branch::where('id',$this->branch_id)->get();
        } 
        else 
        {
            $branchs = Branch::all();
        }
        // return response()->json([$warehouse, $branchs]);
        return view('settings.warehouse.editForm',compact('branchs','warehouse'));

        return response()->json($warehouse);
    }




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

        $request->validate([
            'name' => 'required|max:255|min:5',
            'branch_id' => 'required|exists:branches,id',
            'responsible' => 'required|max:100',
            'address' => 'required|max:255',
        ], $messages);


        $warehouse = Warehouse::find($request->id);

        if (!$warehouse) {
            return response()->json(['message' => 'Warehouse not found'], 404);
        }

         // Exclude 'id' from the request data
        $data = $request->except('id');

        $warehouse->update($data);

        return response()->json(['status' => 'success']);
    }



    public function destroy($id)
    {
        $warehouse = Warehouse::find($id);
        
        // Check if any related record exists
        $warehouseItemExists = WarehouseItem::where('warehouse_id', $id)->exists();
    
        // If any record exists, prevent deletion
        if ($warehouseItemExists) 
        {
            return response()->json(['status' => 'failed', 'message' => __('validated.has_records_in_tables')]);
        }
    
        // If no related records exist, delete the currency
        $warehouse->delete();
        return response()->json(['status' => 'success', 'message' => __('common.deleted_successfully')]);
    }
    
}
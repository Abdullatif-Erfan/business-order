<?php

namespace App\Http\Controllers\Setting;

// use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting\Unit;

use App\Models\Buy\BoughtItemDetails;
use App\Models\Warehouse\WarehouseItem;

use Yajra\DataTables\Facades\DataTables;



class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $unites = Unit::latest()->paginate(10); // Adjust pagination size as needed
        // return response()->json($unites);
        if($request->ajax())
        {
            $unit = Unit::query()->orderBy('id', 'DESC');
            return  DataTables::eloquent($unit)

            // ->addColumn('edit', function($unit) {
            //     return '<a href="'.route('unit.edit', $unit->id).'" data-id="'.$unit->id.'">
            //        <i class="fas fa-pen-square editUnit" style="font-size:20px;"></i>
            //     </a>';
            // })

            // Add Index Column
            ->addIndexColumn()

            ->addColumn('edit', function($unit) {
                return '<i class="fas fa-pen-square editUnit" data-id="'.$unit->id.'" style="font-size:20px;"></i>';
            })
            ->addColumn('delete', function($unit) {
                return '<i class="fas fa-trash-alt deleteUnit" data-id="'.$unit->id.'" style="font-size:20px; color:red;"></i>';
            })
            ->rawColumns(['edit','delete'])
            ->make(true);
            // dd($unit); 
        }


    }

    public function create()
    {
        $units = Unit::all();
        return view('settings.unit.addForm',compact('units'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Define custom validation messages  
        $messages = [
            'name.required' => __('validate.pre_list_name_required'),
            'name.string' => __('validate.pre_list_name_string'),
            'name.max' => __('validate.pre_list_name_max'),
            'name.min' => __('validate.pre_list_name_min'),
            'name.unique' => __('validate.pre_list_name_unique'),
        ];

        // Validate the request
         $validated = $request->validate([
            'name' => 'required|string|max:255|min:2|unique:units,name',
         ], $messages);

        // Create new unit
        Unit::create([
            'name' => $validated['name'],
        ]);

        // Return success response
        return response()->json(['status' => 'success','message' => __('common.added_successfully')]);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {   
        $unit = Unit::where('id',$id)->first(); 
        if($unit) {
             return view('settings.unit.editForm',compact('unit'));
         }
        return response()->json(['message' => __('common.not_found')],404);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        // Find the unit first
        $unit = Unit::find($request->id);
    
        if (!$unit) {
            return response()->json(['message' => __('common.not_found')], 404);
        }
    
        // Define custom validation messages
        $messages = [
            'name.required' => __('validate.pre_list_name_required'),
            'name.string' => __('validate.pre_list_name_string'),
            'name.max' => __('validate.pre_list_name_max'),
            'name.min' => __('validate.pre_list_name_min'),
            'name.unique' => __('validate.pre_list_name_unique'),
        ];
    
        // Validate the request and ignore the current unit's ID in unique rule
        $validated = $request->validate([
            'name' => 'required|string|max:255|min:2|unique:units,name,' . $unit->id,
        ], $messages);
    
        // Update the unit's name
        $unit->name = $request->input('name');
        $unit->save();
    
        return response()->json(['status' => 'success', 'message' => __('common.updated_successfully')], 200);
    }
    

    /**
     * Remove the specified resource from storage.
     */
    

    public function destroy($id)
    {
        $unit = Unit::findOrFail($id);
        
        // Check if any related record exists
        $boughtItemDetailsExists = BoughtItemDetails::where('unit_id', $id)->exists();
        $warehouseItemExists = WarehouseItem::exists();
    
        // If any record exists, prevent deletion
        if ($boughtItemDetailsExists || $warehouseItemExists ) 
        {
            return response()->json(['status' => 'failed', 'message' => __('validate.has_records_in_tables')]);
        }
    
        // If no related records exist, delete the currency
        $unit->delete();
        return response()->json(['status' => 'success', 'message' => __('common.deleted_successfully')]);
    }
}

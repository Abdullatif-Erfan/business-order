<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting\Car;
use App\Models\Buy\BoughtItemDetails;
use App\Models\Warehouse\WarehouseItem;
use App\Models\Order\Order;
use Yajra\DataTables\Facades\DataTables;

class CarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->ajax())
        {
            $car = Car::query()->orderBy('id', 'DESC');
            return  DataTables::eloquent($car)

            // ->addColumn('edit', function($car) {
            //     return '<a href="'.route('car.edit', $car->id).'" data-id="'.$car->id.'">
            //        <i class="fas fa-pen-square editCar" style="font-size:20px;"></i>
            //     </a>';
            // })

            // Add Index Column
            ->addIndexColumn()

            ->addColumn('edit', function($car) {
                return '<i class="fas fa-pen-square editCar" data-id="'.$car->id.'" style="font-size:20px;"></i>';
            })
            ->addColumn('delete', function($car) {
                return '<i class="fas fa-trash-alt deleteCar" data-id="'.$car->id.'" style="font-size:20px; color:red;"></i>';
            })
            ->rawColumns(['edit','delete'])
            ->make(true);
            // dd($car); 
        }


    }

    public function create()
    {
        $cars = Car::all();
        return view('settings.car.addForm',compact('cars'));
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
            'name' => 'required|string|max:255|min:2|unique:cars,name',
         ], $messages);

        // Create new car
        Car::create([
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
        $car = Car::where('id',$id)->first(); 
        if($car) {
             return view('settings.car.editForm',compact('car'));
         }
        return response()->json(['message' => __('common.not_found')],404);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        // Find the car first
        $car = Car::find($request->id);
    
        if (!$car) {
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
    
        // Validate the request and ignore the current car's ID in unique rule
        $validated = $request->validate([
            'name' => 'required|string|max:255|min:2|unique:cars,name,' . $car->id,
        ], $messages);
    
        // Update the car's name
        $car->name = $request->input('name');
        $car->save();
    
        return response()->json(['status' => 'success', 'message' => __('common.updated_successfully')], 200);
    }
    

    /**
     * Remove the specified resource from storage.
     */
    

    public function destroy($id)
    {
        $car = Car::findOrFail($id);
        
        // Check if any related record exists
        $boughtItemDetailsExists = BoughtItemDetails::where('car_id', $id)->exists();
        $warehouseItemExists = WarehouseItem::where('car_id', $id)->exists();
        $ordersItemExists = Order::where('car_id', $id)->exists();

    
        // If any record exists, prevent deletion
        if ($boughtItemDetailsExists || $warehouseItemExists || $ordersItemExists) 
        {
            return response()->json(['status' => 'failed', 'message' => __('validate.has_records_in_tables')]);
        }
    
        // If no related records exist, delete the currency
        $car->delete();
        return response()->json(['status' => 'success', 'message' => __('common.deleted_successfully')]);
    }
}

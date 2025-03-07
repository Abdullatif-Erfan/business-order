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
    public function index(Request $request)
    {

        //  $sessionData = Session::all();
        // $sessionData = Session::get('isAdmin');
 
         // Debugging: Display login status, user, and session data
        //  dd([
        //      'sessionData' => $sessionData,
        //     //  'isAdmin' => $sessionData['isAdmin'] === 1 ? "yes" : "no"
        //  ]);

        if ($request->ajax()) {

            // if($sessionData['isAdmin']){
                $warehouses = Warehouse::with('branch')->orderBy('id', 'DESC');
            // } 
            // else
            //  {
            //     $warehouses = Warehouse::with('branch')
            //     ->whereHas('branch', function ($query) {
            //         $query->where('id', $sessionData['branchId']); // Replace `1` with the desired branch ID
            //     })
            //     ->orderBy('id', 'DESC');
            //  }

              // Get the first record ID
              $firstRecordId = Warehouse::orderBy('id', 'ASC')->first()?->id; 

            return DataTables::eloquent($warehouses)
                ->addIndexColumn()
                ->addColumn('edit', function ($warehouse) {
                    return '<i class="fas fa-pen-square editWarehouse" data-id="' . $warehouse->id . '" style="font-size:20px; cursor: pointer;"></i>';
                })
                ->addColumn('delete', function ($warehouse) use ($firstRecordId) {
                    if($warehouse->id == $firstRecordId) {
                        return  '<br>';
                    }
                    return '<i class="fas fa-trash-alt deleteWarehouse" data-id="' . $warehouse->id . '" style="font-size:20px; color:red; cursor: pointer;"></i>';
                })
                ->rawColumns(['edit', 'delete'])
                ->make(true);
        }

        // return view('settings.warehouses.index'); // Ensure you have this view
    }


    public function create()
    {
        $branchs = Branch::all();
        return view('settings.warehouse.addForm',compact('branchs'));
    }

    public function store(Request $request)
    {
        $messages = [
            'name.required' => 'نام ضروری میباشد',
            'responsible.required' => 'شخص مسول ضروری میباشد',
            'address.required' => 'آدرس ضروری میباشد',
            'branch_id.required' => 'انتخاب شعبه  ضروری میباشد',
            'name.max' => 'حداکثر الی ۱۰۰ حرف مجاز میباشد',
            'name.min' => 'بالاتر از پنج حرف بنویسید',

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
        $branchs = Branch::all();
        // return response()->json([$warehouse, $branchs]);
        return view('settings.warehouse.editForm',compact('branchs','warehouse'));

        return response()->json($warehouse);
    }




    public function update(Request $request)
    {
        $messages = [
            'name.required' => 'نام ضروری میباشد',
            'responsible.required' => 'شخص مسول ضروری میباشد',
            'address.required' => 'آدرس ضروری میباشد',
            'branch_id.required' => 'انتخاب شعبه  ضروری میباشد',
            'name.max' => 'حداکثر الی ۱۰۰ حرف مجاز میباشد',
            'name.min' => 'بالاتر از پنج حرف بنویسید',

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
            return response()->json(['status' => 'failed', 'message' => 'حذف نگردید و در ژورنال یا سایر بخش‌ها ریکارد وجود دارد']);
        }
    
        // If no related records exist, delete the currency
        $warehouse->delete();
        return response()->json(['status' => 'success', 'message' => 'موفقانه حذف گردید']);
    }
    
}
<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\Setting\Warehouse;
use App\Models\Setting\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class WarehouseController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $warehouses = Warehouse::with('branch')->orderBy('id', 'DESC');
            return DataTables::eloquent($warehouses)
                ->addIndexColumn()
                ->addColumn('edit', function ($warehouse) {
                    return '<i class="fas fa-pen-square editWarehouse" data-id="' . $warehouse->id . '" style="font-size:20px; cursor: pointer;"></i>';
                })
                ->addColumn('delete', function ($warehouse) {
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

        if (!$warehouse) {
            return response()->json(['status' => 'failed'], 404);
        }

        $warehouse->delete();
        return response()->json(['status' => 'success']);
    }
}

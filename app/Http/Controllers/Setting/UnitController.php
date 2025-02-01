<?php

namespace App\Http\Controllers\Setting;

// use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting\Unit;
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
            'name.required' => 'نام ضروری میباشد',
            'name.string' => 'نام به حروف باشد',
            'name.max' => 'حداکثر الی ۱۰۰ حرف مجاز میباشد',
            'name.min' => 'بالاتر از دو حرف بنویسید',
            'name.unique' => 'این نام قبلاً ثبت شده است',
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
        return response()->json(['status' => 'success','message' => 'موفقانه ثبت گردید']);
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
        return response()->json(['message' => 'یافت نگردید'],404);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
         // Define custom validation messages
         $messages = [
            'name.required' => 'نام ضروری میباشد',
            'name.string' => 'نام به حروف باشد',
            'name.max' => 'حداکثر الی ۱۰۰ حرف مجاز میباشد',
            'name.min' => 'بالاتر از دو حرف بنویسید',
            'name.unique' => 'این نام قبلاً ثبت شده است',
        ];

        // Validate the request
         $validated = $request->validate([
            'name' => 'required|string|max:255|min:2|unique:units,name',
         ], $messages);

         $unit = Unit::find($request->id);

         if(!$unit) {
            return response()->json(['message' => 'ریکارد مورد نظر یافت نشد'], 404);
         }
     
        // Update the unit's name
        $unit->name = $request->input('name');
        $unit->save();

        return response()->json(['status' => 'success','message' => 'ریکارد با موفقیت بروزرسانی شد'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $unit = Unit::findOrFail($id);
        if($unit) 
        {
            $unit->delete();
            return response()->json(['status' => 'success', 'message' => 'موفقانه حذف گردید']);
        }
        return response()->json(['status' => 'failed', 'message' => ' حذف نگردید']);
    }
}

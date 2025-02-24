<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting\ExpenseType;
use Yajra\DataTables\Facades\DataTables;

class ExpenseTypeController extends Controller
{
    public function index(Request $request)
    {
        // $unites = ExpenseType::latest()->paginate(10); // Adjust pagination size as needed
        // return response()->json($unites);
        if($request->ajax())
        {
            $expenseType = ExpenseType::query()->orderBy('id', 'DESC');
            return  DataTables::eloquent($expenseType)

            // ->addColumn('edit', function($expenseType) {
            //     return '<a href="'.route('exp$expenseType.edit', $expenseType->id).'" data-id="'.$expenseType->id.'">
            //        <i class="fas fa-pen-square editExpenseType" style="font-size:20px;"></i>
            //     </a>';
            // })

            // Add Index Column
            ->addIndexColumn()

            ->addColumn('edit', function($expenseType) {
                return '<i class="fas fa-pen-square editExpenseType" data-id="'.$expenseType->id.'" style="font-size:20px;"></i>';
            })
            ->addColumn('delete', function($expenseType) {
                return '<i class="fas fa-trash-alt deleteExpenseType" data-id="'.$expenseType->id.'" style="font-size:20px; color:red;"></i>';
            })
            ->rawColumns(['edit','delete'])
            ->make(true);
            // dd($expenseType); 
        }


    }

    public function create()
    {
        $expenseType = ExpenseType::all();
        return view('settings.expense_type.addForm',compact('expenseType'));
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
            'name' => 'required|string|max:255|min:2|unique:expense_types,name',
         ], $messages);

        // Create new unit
        ExpenseType::create([
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
        $expenseType = ExpenseType::where('id',$id)->first(); 
        if($expenseType) {
             return view('settings.expense_type.editForm',compact('expenseType'));
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
            'name' => 'required|string|max:255|min:2|unique:expense_types,name,' . $request->id,
         ], $messages);

         $expenseType = ExpenseType::find($request->id);

         if(!$expenseType) {
            return response()->json(['message' => 'ریکارد مورد نظر یافت نشد'], 404);
         }
     
        // Update the exp$expenseType's name
        $expenseType->name = $request->input('name');
        $expenseType->save();

        return response()->json(['status' => 'success','message' => 'ریکارد با موفقیت بروزرسانی شد'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $expenseType = ExpenseType::findOrFail($id);
        if($expenseType) 
        {
            $expenseType->delete();
            return response()->json(['status' => 'success', 'message' => 'موفقانه حذف گردید']);
        }
        return response()->json(['status' => 'failed', 'message' => ' حذف نگردید']);
    }
}

<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting\ExpenseType;
use App\Models\Transaction\Journal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
            'user_id' => auth()->user()->id ?? 0
        ]);

        // Return success response
        return response()->json(['status' => 'success','message' => 'موفقانه ثبت گردید']);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {   
        $user = auth()->user();
        $isAdmin = $user->isAdmin == 1; 
        $user_id = $user->id;
        if($isAdmin)
        {
            $expenseType = ExpenseType::where('id',$id)->first(); 
        } 
        else 
        {
            $expenseType = ExpenseType::where('id',$id)->where('user_id',$user_id)->first(); 
        }

        if($expenseType) {
             return view('settings.expense_type.editForm',compact('expenseType'));
        }
        return response()->json(['message' => 'صلاحیت ویرایش معلومات دیگران را ندارید'],404);
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
        DB::beginTransaction();
        try 
        {
            $expenseType = ExpenseType::findOrFail($id);
    
            // Check if account exists before accessing properties
            if (!$expenseType) {
                return response()->json([
                    'status' => 'failed', 
                    'message' => ' یافت نگردید'
                ]);
            }
    
            // Check if the account has related records
            $journalRecordsExists = Journal::where('dynamic_type', $id)->exists();
    
            // If any record exists, prevent deletion
            if ($journalRecordsExists ) {
                return response()->json([
                    'status' => 'failed', 
                    'message' => 'حذف نگردید و در ژورنال یا سایر بخش‌ها ریکارد وجود دارد'
                ]);
            }
    
            // Delete the account
            $expenseType->delete();
    
            DB::commit();
            return response()->json([
                'status' => 'success', 
                'message' => 'حساب موفقانه حذف گردید'
            ]);
    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'خطایی رخ داده است. لطفاً دوباره تلاش کنید.',
                'error' => $e->getMessage(),
            ], 500);
        }
        
    }


}

<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting\IncomeType;
use App\Models\Transaction\Journal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class IncomeTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $unites = IncomeType::latest()->paginate(10); // Adjust pagination size as needed
        // return response()->json($unites);
        if($request->ajax())
        {
            $incomeType = IncomeType::query()->orderBy('id', 'DESC');
            return  DataTables::eloquent($incomeType)

            // ->addColumn('edit', function($incomeType) {
            //     return '<a href="'.route('exp$incomeType.edit', $incomeType->id).'" data-id="'.$incomeType->id.'">
            //        <i class="fas fa-pen-square editIncomeType" style="font-size:20px;"></i>
            //     </a>';
            // })

            // Add Index Column
            ->addIndexColumn()

            ->addColumn('edit', function($incomeType) {
                return '<i class="fas fa-pen-square editIncomeType" data-id="'.$incomeType->id.'" style="font-size:20px;"></i>';
            })
            ->addColumn('delete', function($incomeType) {
                return '<i class="fas fa-trash-alt deleteIncomeType" data-id="'.$incomeType->id.'" style="font-size:20px; color:red;"></i>';
            })
            ->rawColumns(['edit','delete'])
            ->make(true);
            // dd($incomeType); 
        }


    }

    public function create()
    {
        $incomeType = IncomeType::all();
        return view('settings.income_type.addForm',compact('incomeType'));
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
            'name' => 'required|string|max:255|min:2|unique:income_types,name',
         ], $messages);

        // Create new unit
        IncomeType::create([
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
            $incomeType = IncomeType::where('id',$id)->first(); 
        } 
        else 
        {
            $incomeType = IncomeType::where('id',$id)->where('user_id',$user_id)->first(); 
        }

        if($incomeType) {
            return view('settings.income_type.editForm',compact('incomeType'));
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
            'name' => 'required|string|max:255|min:2|unique:income_types,name,' . $request->id
         ], $messages);

         $incomeType = IncomeType::find($request->id);

         if(!$incomeType) {
            return response()->json(['message' => 'ریکارد مورد نظر یافت نشد'], 404);
         }
     
        // Update the exp$incomeType's name
        $incomeType->name = $request->input('name');
        $incomeType->save();

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
            $incomeType = IncomeType::findOrFail($id);

            // Check if account exists before accessing properties
            if (!$incomeType) {
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
            $incomeType->delete();

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
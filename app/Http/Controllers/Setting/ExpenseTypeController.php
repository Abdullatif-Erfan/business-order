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
            'name.required'   => __('validate.currency_name_required'),
            'name.string'     => __('validate.currency_name_string'),
            'name.max'        => __('validate.currency_name_max'),
            'name.min'        => __('validate.currency_name_min'),
            'name.unique'     => __('validate.currency_name_unique'),
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
        return response()->json(['status' => 'success','message' =>  __('common.added_successfully')]);
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
        return response()->json(['message' => __('common.not_allowed')],404);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
         // Define custom validation messages
         $messages = [
            'name.required'   => __('validate.currency_name_required'),
            'name.string'     => __('validate.currency_name_string'),
            'name.max'        => __('validate.currency_name_max'),
            'name.min'        => __('validate.currency_name_min'),
            'name.unique'     => __('validate.currency_name_unique'),
        ];

        // Validate the request
         $validated = $request->validate([
            'name' => 'required|string|max:255|min:2|unique:expense_types,name,' . $request->id,
         ], $messages);

         $expenseType = ExpenseType::find($request->id);

         if(!$expenseType) {
            return response()->json(['message' => __('common.not_found')], 404);
         }
     
        // Update the exp$expenseType's name
        $expenseType->name = $request->input('name');
        $expenseType->save();

        return response()->json(['status' => 'success','message' => __('common.updated_successfully')], 200);
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
                    'message' => __('common.not_found')
                ]);
            }
    
            // Check if the account has related records
            $journalRecordsExists = Journal::where('dynamic_type', $id)->exists();
    
            // If any record exists, prevent deletion
            if ($journalRecordsExists ) {
                return response()->json([
                    'status' => 'failed', 
                    'message' => __('common.has_records_in_tables')
                ]);
            }
    
            // Delete the account
            $expenseType->delete();
    
            DB::commit();
            return response()->json([
                'status' => 'success', 
                'message' => __('common.deleted_successfully'),
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

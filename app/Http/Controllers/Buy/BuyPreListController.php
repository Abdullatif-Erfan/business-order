<?php

namespace App\Http\Controllers\Buy;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Setting\Branch;
use App\Models\Buy\BuyPreList;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class BuyPreListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $branchs = Branch::all();
        // $buyPreLists = BuyPreList::with('branchRelation')->get();

        // return response()->json(['data' => $buyPreLists]);

        $branchs = Branch::all();
        return view('buy.prelist.list', compact('branchs'));
    }


       /**
     * Show the journal data
     */
    public function getData(Request $request)
    {
        $buyPreLists = BuyPreList::with('branchRelation')
        ->select('id', 'name', 'branch_id')
        ->orderBy('id', 'DESC')
        ->get(); // Adding get() here to ensure the query returns the correct results
    
        return DataTables::of($buyPreLists)
            
            ->addIndexColumn()

            ->addColumn('branch', function($buyPreList) {
                return $buyPreList->branchRelation->name;
            })
            ->addColumn('edit', function($buyPreList) {
                return '<i class="fas fa-pen-square editIcon" data-id="'.$buyPreList->id.'" style="font-size:20px;"></i>';
            })
            ->addColumn('delete', function($buyPreList) {
                return '<i class="fas fa-trash-alt deleteIcon" data-id="'.$buyPreList->id.'" style="font-size:20px; color:red;"></i>';
            })
            ->rawColumns(['edit','delete'])
            ->make(true);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Define custom validation messages
        $messages = [
            'name.required' => 'نام ضروری میباشد',
            'name.string' => 'نام باید حروف باشد',
            'name.max' => 'حداکثر ۲۵۵ حرف مجاز میباشد',
            'name.min' => 'حداقل باید ۳ حرف باشد',
            'name.unique' => 'این نام قبلاً ثبت شده است',
            'branch_id.exists' => 'انتخاب شده نامعتبر است',
        ];

        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255|min:3|unique:bought_item_pre_lists,name',
            'branch_id' => 'required|exists:branches,id',
        ], $messages);

        // Create new record in the correct model
        BuyPreList::create([  // Change this if you're using a different model
            'name' => $validated['name'],
            'branch_id' => $validated['branch_id'],
        ]);

        // Return success response
        return response()->json(['status' => 'success','message' => 'موفقانه ثبت گردید']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $branchs = Branch::all();
        $buyPreLists = BuyPreList::with('branchRelation')->where('id',$id)->get();
        return view('buy.prelist.edit', compact('branchs','buyPreLists'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        // Define custom validation messages
        $messages = [
            'name.required' => 'نام ضروری میباشد',
            'name.string' => 'نام باید حروف باشد',
            'name.max' => 'حداکثر ۲۵۵ حرف مجاز میباشد',
            'name.min' => 'حداقل باید ۳ حرف باشد',
            'name.unique' => 'این نام قبلاً ثبت شده است',
            'branch_id.exists' => 'انتخاب شده نامعتبر است',
        ];
    
        // Validate the request
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'min:3',
                Rule::unique('bought_item_pre_lists')->ignore($request->id), // Exclude the current record
            ],
            'branch_id' => 'required|exists:branches,id',
        ], $messages);
    
        // Find the record to update
        $prevData = BuyPreList::find($request->id);
    
        // Check if record exists
        if (!$prevData) {
            return response()->json(['status' => 'error', 'message' => 'سطر مورد نظر پیدا نشد']);
        }
    
        // Update the data
        $prevData->name = $request->name;
        $prevData->branch_id = $request->branch_id;
        $prevData->save();
    
        // Return success response
        return response()->json(['status' => 'success', 'message' => 'موفقانه ویرایش گردید']);
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $bpList = BuyPreList::findOrFail($id);
        if($bpList) 
        {
            $bpList->delete();
            return response()->json(['status' => 'success', 'message' => 'موفقانه حذف گردید']);
        }
        return response()->json(['status' => 'failed', 'message' => ' حذف نگردید']);
    }
}

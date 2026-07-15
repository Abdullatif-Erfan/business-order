<?php

namespace App\Http\Controllers\Buy;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Intervention\Image\Facades\Image;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use Milon\Barcode\DNS1D;

use App\Models\Setting\Category;
use App\Models\Setting\Account;

use App\Models\Buy\BuyPreList;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class BuyPreListController extends Controller
{
    protected $isAdmin, $packageId;
    public function __construct()
    {
        if (auth()->check()) {
            $this->isAdmin = session('isAdmin', auth()->user()->isAdmin == 1);
        } else {
            $this->isAdmin = false;
        }
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       $suppliers = Account::select('id','name')->where('account_type_id',4)->get();
       $categories = Category::select('id','name')->get();
        return view('buy.prelist.list', compact('categories','suppliers'));    
    }

    /**
     * Show the journal data
     */
    public function getData(Request $request)
    {
        $buyPreLists = BuyPreList::with(['categoryRelation'])
        ->select('id', 'name','category_id','supplier_id')
        ->orderBy('id', 'DESC');
    
        return DataTables::of($buyPreLists)
            
            ->addIndexColumn()


            ->addColumn('category', function($buyPreList) {
                return $buyPreList->categoryRelation->name ?? '';
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $messages = [
            'name.required' => __('validate.pre_list_name_required'),
            'name.string' => __('validate.pre_list_name_string'),
            'name.max' => __('validate.pre_list_name_max'),
            'name.min' => __('validate.pre_list_name_min'),
            'name.unique' => __('validate.pre_list_name_unique'),
        ];

        $times = time();


        $validated = $request->validate([
            'name' => 'required|string|max:255|min:2|unique:bought_item_pre_lists,name',
        ], $messages);

        BuyPreList::create([
            'name' => $validated['name'],
            'category_id' => $request->category_id,
            'supplier_id' => $request->supplier_id,
        ]);

        return response()->json(['status' => 'success', 'message' => __('common.added_successfully')]);
    }

    public function create() {
        $categories = Category::select('id','name')->get();
        $buyPreLists = BuyPreList::get();

        return view('settings.preListItems.addForm', compact('buyPreLists','categories'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $categories = Category::select('id','name')->get();
        $buyPreLists = BuyPreList::where('id',$id)->get();

        return view('settings.preListItems.editForm', compact('buyPreLists','categories'));
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
        // Log::info('Request Data', $request->all());
        $messages = [
            'name.required' => __('validate.pre_list_name_required'),
            'name.string' => __('validate.pre_list_name_string'),
            'name.max' => __('validate.pre_list_name_max'),
            'name.min' => __('validate.pre_list_name_min'),
            'name.unique' => __('validate.pre_list_name_unique'),
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
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], $messages);
    
        // Find the record to update
        $prevData = BuyPreList::find($request->id);
    
        // Check if record exists
        if (!$prevData) {
            return response()->json(['status' => 'error', 'message' => 'سطر مورد نظر پیدا نشد']);
        }
    
        if ($request->hasFile('image')) {
            // Store the uploaded image
            $image_path = $request->file('image')->store('item_images', 'public');
            $prevData->image_path = $image_path; // Save the image path
        }
        // Update the data
        $prevData->name = $request->name;
        $prevData->category_id = $request->category_id ?? null;
        $prevData->supplier_id = $request->supplier_id ?? null;
        $prevData->save();
    
        // Return success response
        return response()->json(['status' => 'success', 'message' => 
        __('common.updated_successfully')]);
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $bpList = BuyPreList::findOrFail($id);
    
        // Delete image if exists
        if ($bpList->image_path && Storage::disk('public')->exists($bpList->image_path)) {
            Storage::disk('public')->delete($bpList->image_path);
        }
    
        // Delete barcode if exists
        if ($bpList->barcode_path && Storage::disk('public')->exists($bpList->barcode_path)) {
            Storage::disk('public')->delete($bpList->barcode_path);
        }
    
        // Delete the database record
        $bpList->delete();
    
        return response()->json([
            'status'  => 'success',
            'message' => __('common.deleted_successfully'),
        ]);
    }
    

}

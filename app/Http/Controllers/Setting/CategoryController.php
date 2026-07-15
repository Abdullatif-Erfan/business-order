<?php
namespace App\Http\Controllers\Setting;

// use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting\Category;
use App\Models\Setting\Account;
use App\Models\Buy\BoughtItemDetails;
use App\Models\Warehouse\WarehouseItem;

use Yajra\DataTables\Facades\DataTables;



class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   
     public function index(Request $request)
    {
        if ($request->ajax()) {
            $category = Category::with('supplier')
                ->select('id', 'name', 'supplier_id', 'created_at') // Optional: select specific columns
                ->orderBy('id', 'DESC');

            return DataTables::of($category) 
                ->addIndexColumn()
                ->addColumn('supplier_name', function($category) {
                    return $category->supplier ? $category->supplier->name : '';
                })
                ->addColumn('edit', function($category) {
                return '<i class="fas fa-pen-square editCategory" data-id="'.$category->id.'" style="font-size:20px;"></i>';
            })
            ->addColumn('delete', function($category) {
                return '<i class="fas fa-trash-alt deleteCategory" data-id="'.$category->id.'" style="font-size:20px; color:red;"></i>';
            })
            ->rawColumns(['edit', 'delete'])
            ->make(true);
        }

        // For non-AJAX requests, return the view
        return view('setting.categories.index');
    }


    public function create()
    {
        $catgories = Category::all();
        $suppliers = Account::select('id','name')->where('account_type_id',4)->get();
        return view('settings.category.addForm',compact('catgories','suppliers'));
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
            'supplier_id.required' => __('validate.required'),
        ];

        // Validate the request
         $validated = $request->validate([
            'name' => 'required|string|max:255|min:2|unique:categories,name',
            'supplier_id' => 'required|exists:accounts,id',
         ], $messages);

        // Create new category
        Category::create([
            'name' => $validated['name'],
            'supplier_id' => $request->supplier_id,
        ]);

        // Return success response
        return response()->json(['status' => 'success','message' => __('common.added_successfully')]);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {   
        $suppliers = Account::select('id','name')->where('account_type_id',4)->get();
        $category  = Category::where('id',$id)->first(); 
        if($category) {
             return view('settings.category.editForm',compact('category','suppliers'));
         }
        return response()->json(['message' => __('common.not_found')],404);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        // Find the category first
        $category = Category::find($request->id);
    
        if (!$category) {
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
    
        // Validate the request and ignore the current category's ID in unique rule
        $validated = $request->validate([
            'name' => 'required|string|max:255|min:2|unique:categories,name,' . $category->id,
        ], $messages);
    
        // Update the category's name
        $category->name = $request->input('name');
        $category->supplier_id = $request->input('supplier_id');
        $category->save();
    
        return response()->json(['status' => 'success', 'message' => __('common.updated_successfully')], 200);
    }
    

    /**
     * Remove the specified resource from storage.
     */
    

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        
        // Check if any related record exists
        $boughtItemDetailsExists = BoughtItemDetails::where('category_id', $id)->exists();
        $warehouseItemExists = WarehouseItem::where('category_id', $id)->exists();
    
        // If any record exists, prevent deletion
        if ($boughtItemDetailsExists  || $warehouseItemExists ) 
        {
            return response()->json(['status' => 'failed', 'message' => __('validate.has_records_in_tables')]);
        }
    
        // If no related records exist, delete the currency
        $category->delete();
        return response()->json(['status' => 'success', 'message' => __('common.deleted_successfully')]);
    }
}

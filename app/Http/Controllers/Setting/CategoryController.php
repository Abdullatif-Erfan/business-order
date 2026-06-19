<?php
namespace App\Http\Controllers\Setting;

// use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting\Category;
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
        // $categoryes = Category::latest()->paginate(10); // Adjust pagination size as needed
        // return response()->json($categoryes);
        if($request->ajax())
        {
            $category = Category::query()->orderBy('id', 'DESC');
            return  DataTables::eloquent($category)

            // ->addColumn('edit', function($category) {
            //     return '<a href="'.route('category.edit', $category->id).'" data-id="'.$category->id.'">
            //        <i class="fas fa-pen-square editCategory" style="font-size:20px;"></i>
            //     </a>';
            // })

            // Add Index Column
            ->addIndexColumn()

            ->addColumn('edit', function($category) {
                return '<i class="fas fa-pen-square editCategory" data-id="'.$category->id.'" style="font-size:20px;"></i>';
            })
            ->addColumn('delete', function($category) {
                return '<i class="fas fa-trash-alt deleteCategory" data-id="'.$category->id.'" style="font-size:20px; color:red;"></i>';
            })
            ->rawColumns(['edit','delete'])
            ->make(true);
            // dd($category); 
        }


    }

    public function create()
    {
        $catgories = Category::all();
        return view('settings.category.addForm',compact('catgories'));
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
        ];

        // Validate the request
         $validated = $request->validate([
            'name' => 'required|string|max:255|min:2|unique:categories,name',
         ], $messages);

        // Create new category
        Category::create([
            'name' => $validated['name'],
        ]);

        // Return success response
        return response()->json(['status' => 'success','message' => __('common.added_successfully')]);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {   
        $category = Category::where('id',$id)->first(); 
        if($category) {
             return view('settings.category.editForm',compact('category'));
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
        $category->save();
    
        return response()->json(['status' => 'success', 'message' => __('common.updated_successfully')], 200);
    }
    

    /**
     * Remove the specified resource from storage.
     */
    

    public function destroy($id)
    {
        // $category = Category::findOrFail($id);
        
        // // Check if any related record exists
        // $boughtItemDetailsExists = BoughtItemDetails::where('category_id', $id)->exists();
        // $warehouseItemExists = WarehouseItem::where('category_id', $id)->exists();
    
        // // If any record exists, prevent deletion
        // if ($boughtItemDetailsExists  || $warehouseItemExists ) 
        // {
        //     return response()->json(['status' => 'failed', 'message' => __('validate.has_records_in_tables')]);
        // }
    
        // // If no related records exist, delete the currency
        // $category->delete();
        // return response()->json(['status' => 'success', 'message' => __('common.deleted_successfully')]);
    }
}

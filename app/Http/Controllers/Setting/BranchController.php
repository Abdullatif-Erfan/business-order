<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Session; // Import Session facade
use Illuminate\Support\Facades\Auth; // Import Auth facade
// use App\Helpers\ManagementHelper;
// use App\Helpers\FunctionHelper;
use Illuminate\Support\Facades\DB;
use App\Models\Setting\Branch;


class BranchController extends BaseController
{
    protected $module;
    public function __construct()
    {
        $this->isLoggedIn();
        $this->module = 'settings';	
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $branches = Branch::latest()->paginate(10); // Adjust pagination size as needed
        return response()->json($branches);
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
            'name.required' => 'نام شعبه ضروری میباشد',
            'name.string' => 'نام شعبه حروف باشد',
            'name.max' => 'حداکثر الی ۱۰۰ حرف مجاز میباشد',
            'name.min' => 'بالاتر از پنج حرف بنویسید',
        ];

        // Validate the request
         $validated = $request->validate([
            'name' => 'required|string|max:255|min:5',
         ], $messages);

        // Create new branch
        Branch::create([
            'name' => $validated['name'],
        ]);

        // Return success response
        return response()->json(['message' => 'موفقانه ثبت گردید']);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $branch = Branch::where('id',$id)->first(); 
        if($branch) {
             return response()->json($branch);
         }
        return response()->json(['message' => 'یافت نگردید'],404);
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
    public function update(Request $request, string $id)
    {
         // Define custom validation messages
         $messages = [
            'name.required' => 'نام شعبه ضروری میباشد',
            'name.string' => 'نام شعبه حروف باشد',
            'name.max' => 'حداکثر الی ۱۰۰ حرف مجاز میباشد',
            'name.min' => 'بالاتر از پنج حرف بنویسید',
        ];

        // Validate the request
         $validated = $request->validate([
            'name' => 'required|string|max:255|min:5',
         ], $messages);

         $branch = Branch::find($id);

         if(!$branch) {
            return response()->json(['message' => 'شعبه مورد نظر یافت نشد'], 404);
         }
     
        // Update the branch's name
        $branch->name = $request->input('name');
        $branch->save();

        return response()->json(['message' => 'شعبه با موفقیت بروزرسانی شد'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $branch = Branch::findOrFail($id);
        $branch->delete();

        return response()->json(['message' => 'موفقانه حذف گردید']);
    }
}

<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Setting\Branch;
use Yajra\DataTables\Facades\DataTables;


class BranchController extends Controller
{
    // protected $module;
    // public function __construct()
    // {
    //     $this->isLoggedIn();
    //     $this->module = 'settings';	
    // }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $branches = Branch::latest()->paginate(10); // Adjust pagination size as needed
        // return response()->json($branches);
        if($request->ajax())
        {
            if(Session::get('isAdmin'))
            {
                $branchs = Branch::query()->orderBy('id', 'DESC');
            } 
            else 
            {
                $branchs = Branch::query()->where('id',Session::get('branchId'))->orderBy('id', 'DESC');
            }


            return  DataTables::eloquent($branchs)

            // ->addColumn('edit', function($branch) {
            //     return '<a href="'.route('branch.edit', $branch->id).'" data-id="'.$branch->id.'">
            //        <i class="fas fa-pen-square editBranch" style="font-size:20px;"></i>
            //     </a>';
            // })

            // Add Index Column
            ->addIndexColumn()

            ->addColumn('edit', function($branch) {
                return '<i class="fas fa-pen-square editBranch" data-id="'.$branch->id.'" style="font-size:20px;"></i>';
            })
            ->addColumn('delete', function($branch) {
                return '<i class="fas fa-trash-alt deleteBranch" data-id="'.$branch->id.'" style="font-size:20px; color:red;"></i>';
            })
            ->rawColumns(['edit','delete'])
            ->make(true);
            // dd($branch); 
        }


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
        if($branch) 
        {
            $branch->delete();
            return response()->json(['status' => 'success', 'message' => 'موفقانه حذف گردید']);
        }
        return response()->json(['status' => 'failed', 'message' => ' حذف نگردید']);
    }
}

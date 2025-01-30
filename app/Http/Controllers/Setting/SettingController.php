<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session; // Import Session facade
use Illuminate\Support\Facades\Auth; // Import Auth facade
// use App\Helpers\ManagementHelper;
// use App\Helpers\FunctionHelper;
// use Illuminate\Support\Facades\DB;
use App\Models\Setting\Branch;
use Yajra\DataTables\Facades\DataTables;

class SettingController extends BaseController
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
    public function index(Request $request)
    {
        // dd($request->ajax());
        if($request->ajax())
        {
            $branchs = Branch::query()->orderBy('id', 'DESC');
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
         
        $global_data = ['global' => $this->global];
        // $branchs = Branch::all();

        // ManagementHelper::pre($global_data);
        return view('settings.setting', compact('global_data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
         
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

    }

 
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
       
    }
}

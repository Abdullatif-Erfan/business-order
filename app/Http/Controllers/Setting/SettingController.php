<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import Auth facade
// use App\Helpers\ManagementHelper;
// use App\Helpers\FunctionHelper;
// use Illuminate\Support\Facades\DB;


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
    public function index()
    {
       
         
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

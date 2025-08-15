<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Production\ModelDetails;

use App\Models\Setting\Currency;
use App\Models\Warehouse\WarehouseItem;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class ModelDetailsController extends Controller
{
    protected $branch_id, $isAdmin, $packageId;
    public function __construct()
    {
        if (auth()->check()) {
            $this->branch_id = session('branch_id', auth()->user()->branch_id ?? 0);
            $this->isAdmin = session('isAdmin', auth()->user()->isAdmin == 1);
            $this->packageId = session('package_type') ? session('package_type') : 1;
        } else {
            $this->branch_id = 0;
            $this->isAdmin = false;
            $this->packageId = 1;
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(String $modelId)
    {
        // 1. Check if the model exists
        $models = ModelDetails::where('branch_id', $this->branch_id)
        ->where('model_id', $modelId)
        ->first();

        $oldRecords = array();
        if($models)
        {
            $oldRecords = DB::table('model_details')
            ->join('bought_item_pre_lists', 'bought_item_pre_lists.id', '=', 'model_details.pre_list_id')
            ->join('units', 'units.id', '=', 'model_details.unit_id')
            ->select('model_details.id','model_details.currency_id','model_details.amount','model_details.unit_id','model_details.price','model_details.total_price',
            'pre_list_id', 'bought_item_pre_lists.name as item_name','units.name as unit_name')
            ->where('model_details.branch_id', $this->branch_id)
            ->where('model_details.model_id', $modelId)
            ->get();
        }

        $warehouseItems = DB::table('warehouse_items')
        ->join('bought_item_pre_lists', 'bought_item_pre_lists.id', '=', 'warehouse_items.buy_pre_id')
        ->join('warehouses', 'warehouses.id', '=', 'warehouse_items.warehouse_id')
        ->join('units', 'units.id', '=', 'warehouse_items.unit_id')
        ->where('warehouse_items.available_amount', '>', 0)
        ->select('warehouse_items.id','warehouse_items.currency_id','bought_item_pre_lists.code','warehouse_items.unit_id','avg_up','sell_up', 'warehouse_items.available_amount', 'units.name as unit_name','warehouses.id as warehouse_id', 'warehouses.name as warehouse_name', 'bought_item_pre_lists.name as item_name','bought_item_pre_lists.branch_id','bought_item_pre_lists.id as pre_list_id')
        ->where('warehouse_items.branch_id', $this->branch_id)
        ->get();

        // return response()->json($oldRecords);
        return view('production/aqlam/list',compact('modelId','warehouseItems', 'oldRecords'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $validated = $request->validate([
             'branch_id' => 'required|exists:branches,id',
             'model_id' => 'required|exists:models,id',
             'amount' => 'required|min:1',
         ]);
 
         foreach($request->warehouseItemId as $index => $itemId)
         {
             ModelDetails::create([
                 'model_id' => $request->model_id, 
                 'branch_id' => $this->branch_id ?? $request->branch_id[$index], 
                 'pre_list_id' => $request->pre_list_id[$index], 
                 'amount' => $request->amount[$index], 
                 'unit_id' => $request->unit_id[$index], 
                 'price' => $request->avg_up[$index], 
                 'total_price' => $request->total[$index],  
                 'currency_id' => $request->currency_id[$index],
             ]);
         }

         Session::put('notification', ['message' => __('common.added_successfully'), 'type' => 'success']);
        return redirect()->route('model.index');
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
    public function update(Request $request)
    {
        $validated = $request->validate([
            'model_id' => 'required|exists:models,id',
            'oamount.*' => 'required|numeric|min:0.01',
            'oavg_up.*' => 'required|numeric|min:0',
        ]);

        $modelId = $request->model_id;
        $branchId = $this->branch_id;

        // IDs of rows that were submitted
        $submittedIds = $request->oldRecordId ?? [];

        // 1️⃣ Delete records that were removed from the form
        if (!empty($submittedIds)) {
            ModelDetails::where('model_id', $modelId)
                ->where('branch_id', $branchId)
                ->whereNotIn('id', $submittedIds)
                ->delete();
        } else {
            // If no row remains, delete all
            ModelDetails::where('model_id', $modelId)
                ->where('branch_id', $branchId)
                ->delete();
        }

        // 2️⃣ Update remaining records
        foreach ($submittedIds as $index => $id) {
            ModelDetails::where('id', $id)->update([
                'amount'      => $request->oamount[$index],
                'price'       => $request->oavg_up[$index],
                'total_price' => $request->ototal[$index],
            ]);
        }

        Session::put('notification', [
            'message' => __('common.updated_successfully'),
            'type' => 'success'
        ]);

        return redirect()->route('model.index');
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

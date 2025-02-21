<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Setting\Currency;
use App\Models\Setting\Branch;
use Morilog\Jalali\Jalalian;
use App\Models\Setting\OrgBio;
// use App\Models\Setting\Unit;
// use App\Models\Buy\BuyPreList;
// use App\Models\Buy\BoughtItem;
// use App\Models\Buy\BoughtItemDetails; 
// use App\Models\Journal\Journal;
// use App\Models\Setting\Warehouse;
use App\Models\Warehouse\WarehouseSales;
use App\Models\Warehouse\WarehouseItem;
use App\Models\Setting\Account;

use Yajra\DataTables\Facades\DataTables;


class SalesController extends Controller
{
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
    public function create()
    {
        $todaysDate = Jalalian::now()->format('Y-m-d');
        // $warehouseItems = WarehouseItem::with(['preListRelation'])->where('available_amount','>',0)->get();
        $warehouseItems = DB::table('warehouse_items')
                        ->join('bought_item_pre_lists', 'bought_item_pre_lists.id', '=', 'warehouse_items.buy_pre_id')
                        ->join('warehouses', 'warehouses.id', '=', 'warehouse_items.warehouse_id')
                        ->join('units', 'units.id', '=', 'warehouse_items.unit_id')
                        ->where('warehouse_items.available_amount', '>', 0)
                        ->select('warehouse_items.id','warehouse_items.unit_id','avg_up','sell_up', 'warehouse_items.available_amount', 'units.name as unit_name', 'warehouses.name as warehouse_name', 'bought_item_pre_lists.name as item_name')
                        ->get();

        $customers = Account::select('id','name')->where('account_type_id',3)->orWhere('account_type_id',4)->get();
        $ownBanks = Account::select('id','name')->where('account_type_id',1)->orderBy('is_pre_select','DESC')->get();
        $currencies = Currency::all();
        $billno =  WarehouseSales::max('billno') + 1;

        // return response()->json(['data' => $warehouseItems]);
        return view('sales.create.form',compact('todaysDate','warehouseItems','customers','ownBanks','billno','currencies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return response()->json(['data' => $request->all()]);
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
    public function destroy(string $id)
    {
        //
    }
}

<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Setting\Branch;
use Morilog\Jalali\Jalalian;
use App\Models\Setting\OrgBio;
use App\Models\Setting\Unit;
use App\Models\Buy\BuyPreList;
use App\Models\Buy\BoughtItem;
use App\Models\Setting\Currency;

use App\Models\Transaction\Journal;
use App\Models\Buy\BoughtItemDetails; 
use App\Models\Setting\Warehouse;

use App\Models\Warehouse\WarehouseSales;
use App\Models\Warehouse\WarehouseItem;
use App\Models\Warehouse\SalesDetails;


use App\Models\Setting\Account;
use Yajra\DataTables\Facades\DataTables;

class SalesByItemController extends Controller
{
    protected $isAdmin;
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
        $currencies = Currency::all();
        $orgbios = OrgBio::all();
        $todaysDate = Jalalian::now()->format('Y-m-d');

        return view('sales.item_list',compact('currencies','todaysDate','orgbios'));
    }

    public function getData2(Request $request)
    {
        // Step 1: Start Query (no get(), no map yet)
        $query = SalesDetails::with([
            'preListRelation',
            'unitRelation',
            'warehouseSale.customer'
        ]);
    
        // 🔍 Filter: customer name
        if ($request->customer_name) {
            $query->whereHas('warehouseSale.customer', function ($q) use ($request) {
                $q->where('name', 'LIKE', "%{$request->customer_name}%");
            });
        }
    
        // 🔍 Filter: bill number
        if ($request->bill_number) {
            $query->where('billno', $request->bill_number);
        }
    
        // 🔍 Filter: date range (from sales_details.todays_date)
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('todays_date', [$request->start_date, $request->end_date]);
        } elseif ($request->start_date) {
            $query->whereDate('todays_date', '=', $request->start_date);
        } elseif ($request->end_date) {
            $query->whereDate('todays_date', '<=', $request->end_date);
        }
    
        // Step 2: Get Data
        $salesDetails = $query->get();
    
        // Step 3: Transform
        $mapped = $salesDetails->map(function ($item) {
            return [
                'id'            => $item->id,
                'billno'        => $item->billno,
                'product_name'  => $item->preListRelation->name ?? null,
                'unit'          => $item->unitRelation->name ?? null,
                'amount'        => $item->amount,
                'sell_up'       => $item->sell_up,
                'profit'        => $item->profit,
                'total'         => $item->total,
                'customer_name' => $item->warehouseSale->customer->name ?? null,
                'date'          => $item->todays_date,
            ];
        });
    
        // Step 4: Datatable response
        return DataTables::of($mapped)
            ->addIndexColumn()
            ->editColumn('total', fn($row) => number_format($row['total'], 0))
            ->make(true);
    }


public function getData(Request $request)
{
    // Base Query with joins (faster than whereHas)
    $query = SalesDetails::select(
        'sales_details.id',
        'sales_details.billno',
        'sales_details.amount',
        'sales_details.sell_up',
        'sales_details.total',
        'sales_details.profit',
        'sales_details.todays_date as date',
        'bought_item_pre_lists.name as product_name',
        'units.name as unit_name',
        'accounts.name as customer_name'
    )
    ->leftJoin('bought_item_pre_lists', 'bought_item_pre_lists.id', '=', 'sales_details.pre_list_id')
    ->leftJoin('units', 'units.id', '=', 'sales_details.unit_id')
    ->leftJoin('warehouse_sales', 'warehouse_sales.id', '=', 'sales_details.warehouse_sales_id')
    ->leftJoin('accounts', 'accounts.id', '=', 'warehouse_sales.customer_account_id');

    // 🔍 Filter: customer name
    if ($request->customer_name) {
        $query->where('accounts.name', 'LIKE', "%{$request->customer_name}%");
    }

    // Filter: currency id
    if ($request->currency_id) {
        $query->where('warehouse_sales.currency_id', '=', $request->currency_id);
    }

     // Filter: Item Name 
     if ($request->item_name) {
        $query->where('bought_item_pre_lists.name', 'LIKE', "%{$request->item_name}%");
    }

    // 🔍 Filter: bill number
    if ($request->bill_number) {
        $query->where('sales_details.billno', $request->bill_number);
    }

    // 🔍 Filter: date range
    if ($request->start_date && $request->end_date) {
        $query->whereBetween('sales_details.todays_date', [$request->start_date, $request->end_date]);
    } elseif ($request->start_date) {
        $query->whereDate('sales_details.todays_date', '=', $request->start_date);
    } elseif ($request->end_date) {
        $query->whereDate('sales_details.todays_date', '<=', $request->end_date);
    }

    // Return DataTable (auto-pagination, no heavy mapping)
    return DataTables::of($query)
        ->addIndexColumn()
        ->editColumn('unit', fn($row) => $row->unit_name)
        ->editColumn('product_name', fn($row) => $row->product_name)
        ->editColumn('total', fn($row) => number_format($row->total, 0))
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
        //
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

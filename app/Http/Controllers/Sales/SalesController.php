<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Setting\Currency;
use Carbon\Carbon;
use App\Models\Setting\OrgBio;
use App\Models\Setting\Car;
use App\Models\Setting\Unit;
use App\Models\Transaction\Journal;
use App\Models\Warehouse\WarehouseSales;
use App\Models\Warehouse\WarehouseItem;
use App\Models\Warehouse\SalesDetails;
use App\Models\Order\DraftOrder;

use App\Models\SalesInvoice\SalesInvoice;
use App\Models\SalesInvoice\SalesInvoiceItem;
use App\Models\SalesInvoice\SalesInvoicePayment;

use App\Models\Setting\Account;

use Yajra\DataTables\Facades\DataTables;


class SalesController extends Controller
{
    protected  $isAdmin;
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

        // $soldItems = DB::table('warehouse_sales')
        //     ->join('accounts', 'accounts.id', '=', 'warehouse_sales.customer_account_id')
        //     ->join('currencies', 'currencies.id', '=', 'warehouse_sales.currency_id')
        //     ->select('warehouse_sales.id','billno','factor','accounts.name as customer_name','total_price','total_discount','payable','cur_pay','is_cleared','remained','currencies.name as currency_name','short_date','iby')
        //     ->orderBy('warehouse_sales.id','DESC')->get();
        // return $soldItems;
            

        $currencies = Currency::all();
        $todaysDate = Carbon::now()->format('Y-m-d');
        $orgbios = OrgBio::all();
        return view('sales.list',compact('currencies','todaysDate','orgbios'));
    }

    public function getData(Request $request)
    {
            $soldItems = DB::table('warehouse_sales')
            ->join('accounts', 'accounts.id', '=', 'warehouse_sales.customer_account_id')
            ->join('currencies', 'currencies.id', '=', 'warehouse_sales.currency_id')
            ->select('warehouse_sales.id','billno','factor','accounts.name as customer_name','total','cur_pay','is_cleared','remained','currencies.name as currency_name','idate','user_name','warehouse_sales.invoice_id','warehouse_sales.has_invoice')
            ->orderBy('warehouse_sales.id','DESC');
            

            // Apply filters if provided
              if ($request->customer_name) {
                 $soldItems->where('accounts.name', 'LIKE', "%{$request->customer_name}%");
            }
            
            if ($request->currency_id) {
                $soldItems->where('currency_id', $request->currency_id);
            }
            
            if ($request->start_date && $request->end_date) {
                $soldItems->whereBetween('idate', [$request->start_date, $request->end_date]);
            } elseif ($request->start_date) {
                $soldItems->whereDate('idate', '=', $request->start_date);
            } elseif ($request->end_date) {
                $soldItems->whereDate('idate', '>=', $request->end_date); // Until today
            }
            
            if ($request->bill_number) {
                $soldItems->where('billno', $request->bill_number);
            }
            
            return DataTables::of($soldItems)
            ->addIndexColumn()
            ->addColumn('billno', function($soldItem) {
                $checkIcon = $soldItem->is_cleared == 1 
                    ? '<i class="fas fa-check-circle success"></i>' 
                    : '';
                return $soldItem->billno 
                    ? $checkIcon . ' SALES_' . $soldItem->billno 
                    : 0;
            })
            ->addColumn('total', fn($s) => number_format($s->total, 2))
            // ->addColumn('total_discount', fn($s) => number_format($s->total_discount, 2))
            // ->addColumn('payable', fn($s) => number_format($s->payable, 2))
            ->addColumn('cur_pay', fn($s) => number_format($s->cur_pay, 2))
            ->addColumn('remained', fn($s) => number_format($s->remained, 2))
            ->addColumn('view', function ($soldItem) {
                return '<a href="/sales/details/'.$soldItem->billno.'">
                    <i class="fas fa-eye viewItems" style="font-size:20px;"></i>
                </a>';
            })
            ->rawColumns(['billno','view'])
            ->make(true);
        

    }


    /**
     * Show the form for creating a new resource.
     */
    public function create_v1()
    {
        $todaysDate = Carbon::now()->format('Y-m-d');
        // $warehouseItems = WarehouseItem::with(['preListRelation'])->where('available_amount','>',0)->get();
       $warehouseItems = DB::table('warehouse_items')
        ->join('bought_item_pre_lists', 'bought_item_pre_lists.id', '=', 'warehouse_items.buy_pre_id')
        ->join('units', 'units.id', '=', 'warehouse_items.unit_id')
        ->where('warehouse_items.available_amount', '>', 0)
        ->select(
            'warehouse_items.id',
            'warehouse_items.unit_id',
            DB::raw("CASE 
                WHEN warehouse_items.buy_tax_per IS NOT NULL AND warehouse_items.buy_tax_per > 0 
                THEN warehouse_items.buy_up_vat 
                ELSE warehouse_items.buy_up 
            END as buy_up"),
            DB::raw("CASE 
                WHEN warehouse_items.buy_tax_per IS NOT NULL AND warehouse_items.buy_tax_per > 0 
                THEN warehouse_items.sell_up_vat 
                ELSE warehouse_items.sell_up 
            END as sell_up"),
            'warehouse_items.buy_tax_per',
            'warehouse_items.sell_up as sell_up_no_tax',
            'warehouse_items.sell_tax_per',
            'warehouse_items.sell_tax_price',
            'warehouse_items.available_amount',
            'units.name as unit_name',
            'warehouse_items.warehouse_id',
            'bought_item_pre_lists.name as item_name',
            'bought_item_pre_lists.id as pre_list_id',
            'bought_item_pre_lists.category_id as category_id'
        )
        ->get();
    
        $customers = Account::select('id','name')->where('account_type_id',3)->get();
        $ownBanks = Account::select('id','name')->whereIn('account_type_id',[1,6])->orderBy('is_pre_select','DESC')->get();

        $currencies = Currency::all();
        $billno =  WarehouseSales::max('billno') + 1;
        $journal_code = Journal::max('code') + 1;
        $times = time();
        

        // return response()->json(['data' => $warehouseItems]);
        return view('sales.create.form',compact('todaysDate','warehouseItems','customers','ownBanks','billno','currencies','journal_code','times'));
    }
    public function create_v2_backup()
    {
        $customers = Account::select('id', 'name')->where('account_type_id', 3)->get();
        $currencies = Currency::select('id', 'name')->get();
        $ownBanks = Account::select('id', 'name')->whereIn('account_type_id', [1, 6])->get();
        $tax = OrgBio::select('tax_activation')->first();
        $units = Unit::select('id', 'name')->get();
        $cars = Car::select('id', 'name')->get(); // later should be filtered based on driver
        
        // Get warehouse items with available stock > 0
        $warehouseItems = DB::table('warehouse_items')
            ->join('bought_item_pre_lists', 'bought_item_pre_lists.id', '=', 'warehouse_items.buy_pre_id')
            ->join('units', 'units.id', '=', 'warehouse_items.unit_id')
            ->where('warehouse_items.available_amount', '>', 0)
            ->select(
                'warehouse_items.id',
                'warehouse_items.unit_id',
                DB::raw("CASE WHEN warehouse_items.buy_tax_per IS NOT NULL AND warehouse_items.buy_tax_per > 0 THEN warehouse_items.sell_up_vat ELSE warehouse_items.sell_up END as sell_up"),
                'warehouse_items.available_amount',
                'units.name as unit_name',
                'warehouse_items.warehouse_id',
                'bought_item_pre_lists.name as item_name',
                'bought_item_pre_lists.id as pre_list_id',
                'bought_item_pre_lists.category_id as category_id'
            )
            ->get();

        // Get draft orders with state = 2 (in progress) and only those with available stock
        $draftOrders = DraftOrder::select(
            'id',
            'dord_num',
            'customer_id',
            'category_id',
            'pre_list_id',
            'unit_id',
            'amount',
            'idate',
            'iby',
            'user_name',
            'state',
            'times'
        )
        ->with([
            'customerRelation:id,name',
            'preListRelation:id,name,category_id',
            'unitRelation:id,name',
        ])
        ->where('draft_orders.state', 2)
        ->orderBy('id', 'DESC')
        ->get();

        // Filter draft orders: only keep items that exist in warehouse with available_amount > 0
        $availablePreListIds = $warehouseItems->pluck('pre_list_id')->toArray();
        $filteredDraftOrders = $draftOrders->filter(function ($order) use ($availablePreListIds) {
            return in_array($order->pre_list_id, $availablePreListIds);
        });

        $billno = WarehouseSales::max('billno') + 1;
        $times = time();
        $journal_code = 1;

        return view('sales.v2.create.create', compact(
            'customers',
            'units',
            'currencies',
            'ownBanks',
            'tax',
            'warehouseItems',
            'draftOrders',
            'filteredDraftOrders',
            'billno',
            'times',
            'journal_code',
            'cars'
        ));
    }

    public function create()
    {
        $customers = Account::select('id', 'name')->where('account_type_id', 3)->get();
        $currencies = Currency::select('id', 'name')->get();
        $ownBanks = Account::select('id', 'name')->whereIn('account_type_id', [1, 6])->get();
        $tax = OrgBio::select('tax_activation')->first();
        $units = Unit::select('id', 'name')->get();
        $cars = Car::select('id', 'name')->get();
        
        // Get warehouse items with available stock > 0
        $warehouseItems = DB::table('warehouse_items')
            ->join('bought_item_pre_lists', 'bought_item_pre_lists.id', '=', 'warehouse_items.buy_pre_id')
            ->join('units', 'units.id', '=', 'warehouse_items.unit_id')
            ->where('warehouse_items.available_amount', '>', 0)
            ->select(
                'warehouse_items.id as warehouse_item_id',
                'warehouse_items.unit_id as warehouse_unit_id',
                'units.name as warehouse_unit_name',
                DB::raw("CASE WHEN warehouse_items.buy_tax_per IS NOT NULL AND warehouse_items.buy_tax_per > 0 THEN warehouse_items.sell_up_vat ELSE warehouse_items.sell_up END as sell_up"),
                'warehouse_items.available_amount',
                'warehouse_items.warehouse_id',
                'bought_item_pre_lists.name as item_name',
                'bought_item_pre_lists.id as pre_list_id',
                'bought_item_pre_lists.category_id as category_id'
            )
            ->get();

        // Get draft orders with state = 2
        $draftOrders = DraftOrder::select(
            'id',
            'dord_num',
            'customer_id',
            'category_id',
            'pre_list_id',
            'unit_id',
            'amount',
            'idate',
            'iby',
            'user_name',
            'state',
            'times'
        )
        ->with([
            'customerRelation:id,name',
            'preListRelation:id,name,category_id',
            'unitRelation:id,name',
        ])
        ->where('draft_orders.state', 2)
        ->orderBy('id', 'DESC')
        ->get();

        // Get customer IDs that have orders (state=2)
        $customerIdsWithOrders = $draftOrders->pluck('customer_id')->unique()->toArray();

        // Combine: Match by pre_list_id AND unit_id
        $combinedItems = collect();
        
        foreach ($draftOrders as $order) {
            $warehouseItem = $warehouseItems->first(function ($item) use ($order) {
                return $item->pre_list_id == $order->pre_list_id 
                    && $item->warehouse_unit_id == $order->unit_id;
            });
            
            if ($warehouseItem) {
                $combinedItems->push((object) [
                    'dord_num' => $order->dord_num,
                    'customer_id' => $order->customer_id,
                    'customer_name' => $order->customerRelation->name ?? 'Unknown',
                    'category_id' => $order->category_id,
                    'pre_list_id' => $order->pre_list_id,
                    'pre_list_name' => $order->preListRelation->name ?? 'Unknown',
                    'unit_id' => $order->unit_id,
                    'unit_name' => $order->unitRelation->name ?? 'Unknown',
                    'amount' => $order->amount,
                    'idate' => $order->idate,
                    'state' => $order->state,
                    'times' => $order->times,
                    'warehouse_item_id' => $warehouseItem->warehouse_item_id,
                    'sell_up' => $warehouseItem->sell_up,
                    'available_amount' => $warehouseItem->available_amount,
                    'warehouse_unit_id' => $warehouseItem->warehouse_unit_id,
                    'warehouse_unit_name' => $warehouseItem->warehouse_unit_name,
                    'item_name' => $warehouseItem->item_name,
                    'has_order' => true,
                ]);
            }
        }

        // Prepare customers with order status
        $customersWithStatus = $customers->map(function ($customer) use ($customerIdsWithOrders, $combinedItems) {
            $hasOrder = in_array($customer->id, $customerIdsWithOrders);
            $hasAvailableItems = $combinedItems->where('customer_id', $customer->id)->isNotEmpty();
            
            return (object) [
                'id' => $customer->id,
                'name' => $customer->name,
                'has_order' => $hasOrder,
                'has_available_items' => $hasAvailableItems,
                'items' => $hasAvailableItems ? $combinedItems->where('customer_id', $customer->id)->values() : collect()
            ];
        });

        $billno = WarehouseSales::max('billno') + 1;
        $times = time();
        $journal_code = 1;

        // return ['data' => $customersWithStatus, 'items' => $combinedItems];
        return ['warehouseItems' => $warehouseItems];
        return view('sales.v2.create.create', compact(
            'customers',
            'units',
            'currencies',
            'ownBanks',
            'tax',
            'warehouseItems',
            'combinedItems',
            'customersWithStatus',
            'billno',
            'times',
            'journal_code',
            'cars'
        ));
    }
            
            
    public function store(Request $request)
    {
        // return response()->json($request->all());

        $validated = $request->validate([
            'customer_account_id' => 'required|exists:accounts,id',
            'account_id' => 'required|exists:accounts,id',
            'car_id' => 'required|exists:cars,id',
            'todays_date' => 'required',
            'billno' => 'required|numeric|unique:warehouse_sales,billno',
            'factor' => 'nullable|string|max:255',
            'total_price' => 'required|numeric|min:0',
            'cur_pay' => 'required|numeric|min:0',
            'remained' => 'required|numeric|min:0',
            'currency_id' => 'required|exists:currencies,id',
            'note' => 'nullable|string|max:1000',
            'times' => 'required|integer',
            'items' => 'required|array|min:1',
            'items.*.pre_list_id' => 'required|exists:bought_item_pre_lists,id',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.amount' => 'required|numeric|min:0.01',
            'items.*.sell_up' => 'required|numeric|min:0',
            'items.*.total' => 'required|numeric|min:0',
            'items.*.warehouse_item_id' => 'required|exists:warehouse_items,id',
            'items.*.category_id' => 'nullable|exists:categories,id',
        ]);

        DB::beginTransaction();
        try {
            $date = Carbon::parse($validated['todays_date']);
            
            // Create WarehouseSales
            $warehouseSale = WarehouseSales::create([
                'billno' => $validated['billno'],
                'factor' => $validated['factor'] ?? null,
                'account_id' => $validated['account_id'],
                'customer_account_id' => $validated['customer_account_id'],
                'car_id' => $validated['car_id'],
                'total' => $validated['total_price'],
                'cur_pay' => $validated['cur_pay'],
                'remained' => $validated['remained'],
                'currency_id' => $validated['currency_id'],
                'tax_activation' => 0,
                'note' => $validated['note'] ?? null,
                'idate' => $date->format('Y-m-d'),
                'year' => $date->year,
                'month' => $date->month,
                'day' => $date->day,
                'times' => $validated['times'],
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name ?? 'System',
                'has_invoice' => 0,
                'invoice_id' => null,
                'is_cleared' => 0,
            ]);

            // Create Sales Details and update warehouse
            foreach ($validated['items'] as $index => $item) {
                // Get warehouse item details for additional fields
                $warehouseItem = WarehouseItem::with(['preListRelation', 'unitRelation'])
                    ->where('id', $item['warehouse_item_id'])
                    ->first();

                if (!$warehouseItem) {
                    throw new \Exception("Warehouse item not found: {$item['warehouse_item_id']}");
                }

                // Calculate profit (sell_up - buy_up)
                $buyUp = $warehouseItem->buy_up ?? 0;
                $sellUp = $item['sell_up'] ?? 0;
                $profit = $sellUp - $buyUp;

                // Determine tax values
                $sellTaxPer = $warehouseItem->sell_tax_per ?? 0;
                $sellTaxPrice = $warehouseItem->sell_tax_price ?? 0;
                $sellUpNoTax = $warehouseItem->sell_up_no_tax ?? $sellUp;

                // Create sales detail with all fields
                SalesDetails::create([
                    'billno' => $validated['billno'],
                    'warehouse_id' => $warehouseItem->warehouse_id ?? null,
                    'warehouse_sales_id' => $warehouseSale->id,
                    'pre_list_id' => $item['pre_list_id'],
                    'category_id' => $item['category_id'] ?? $warehouseItem->category_id ?? null,
                    'unit_id' => $item['unit_id'],
                    'amount' => $item['amount'],
                    'buy_up' => $buyUp,
                    'sell_up' => $sellUp,
                    'sell_up_no_tax' => $sellUpNoTax,
                    'sell_tax_per' => $sellTaxPer,
                    'sell_tax_price' => $sellTaxPrice,
                    'profit' => $profit,
                    'total' => $item['total'],
                    'is_returned' => 0,
                    'todays_date' => $date->format('Y-m-d'),
                ]);

                // Decrease warehouse items with proper tracking
                $this->decreaseWarehouseItemAfterStore($warehouseItem, $item['amount']);
            }

            // Update draft order state to completed (3) for this customer
            if (!empty($validated['customer_account_id'])) {
                DraftOrder::where('customer_id', $validated['customer_account_id'])
                    ->where('state', 2)
                    ->update(['state' => 3]);
            }

            // Handle journal entry
            $this->handleJournalEntry($request);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => __('common.added_successfully'),
                'data' => [
                    'sale_id' => $warehouseSale->id,
                    'billno' => $warehouseSale->billno,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sales Creation Error: ' . $e->getMessage());
            Log::error('Request Data: ', $request->all());
            
            return response()->json([
                'status' => 'error',
                'message' => __('common.error_occurred') . ': ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Store a newly created resource in storage.
    */
    public function store_v1(Request $request)
    {
        $validator = Validator::make($request->all(), $this->validationRules(), $this->validationMessages());

        if ($validator->fails()) {
            return redirect()->route('sales.create')
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            // Create warehouse_sales
            $warehouseSalesId = $this->createWarehouseSales($request);
            if (!$warehouseSalesId) {
                throw new \Exception('Failed to create warehouse sales');
            }

            // Create sales_details
            $this->createSalesDetails($request, $warehouseSalesId);

            // Decrease from warehouse_items
            $this->decreaseWarehouseItemFromSoldAmount($request);

            // Handle journal entry - NO TRANSACTION INSIDE
            $this->handleJournalEntry($request);

            DB::commit();
            
            Session::put('notification', [
                'message' => __('common.added_successfully'),
                'type' => 'success',
            ]);
            
            return redirect()->route('sales.create');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error storing SalesController', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            Session::put('notification', [
                'message' => $e->getMessage() ?: __('common.add_failed'),
                'type' => 'danger',
            ]);
            
            return redirect()->route('sales.create')->withInput();
        }
    }
   

    
    /**
     * Validation rules
     */
    private function validationRules()
    {
        return [
            'customer_account_id' => 'required|integer|exists:accounts,id',
            'times'        => 'required',
            'todays_date' => 'required',
            'billno' => 'required|integer',
            'factor' => 'nullable|string',
            'warehouse_id'  => 'required',
            'warehouseItemId' => 'required|array',
            'warehouseItemId.*' => 'required|integer|exists:warehouse_items,id',
            'amount' => 'required|array',
            'amount.*' => 'required|numeric|min:0',
            'unit_id' => 'required|array',
            'unit_id.*' => 'required|integer|exists:units,id',
            'unit_name' => 'required|array',
            'unit_name.*' => 'required|string|max:255',
            'buy_up' => 'required|array',
            'buy_up.*' => 'nullable|numeric|min:0',
            'sell_up' => 'required|array',
            'sell_up.*' => 'nullable|numeric|min:0',
            'profit' => 'required|array',
            'profit.*' => 'nullable|numeric',
            'total' => 'required|array',
            'total.*' => 'nullable|numeric|min:0',
            'total_price' => 'required|numeric|min:0',
            'cur_pay' => 'required|numeric|min:0',
            'remained' => 'required|numeric|min:0',
            'from_account_id' => 'required|integer|exists:accounts,id',
            'currency_id' => 'required|integer|exists:currencies,id',
            'note' => 'nullable|string|max:500',
        ];
    }

    /**
     * Custom validation messages
     */
    private function validationMessages()
    {
        return [
            'customer_account_id.required' => __('validate.customer_account_id_required'),
            'customer_account_id.integer' => __('validate.customer_account_id_integer'),
            'customer_account_id.exists' => __('validate.customer_account_id_exists'),
        
            'todays_date.required' => __('validate.todays_date_required'),
            'todays_date.date_format' => __('validate.todays_date_date_format'),
        
            'billno.required' => __('validate.billno_required'),
            'billno.integer' => __('validate.billno_integer'),
        
            'factor.string' => __('validate.factor_string'),
        
            'warehouseItemId.required' => __('validate.warehouseItemId_required'),
            'warehouseItemId.array' => __('validate.warehouseItemId_array'),
            'warehouseItemId.*.integer' => __('validate.warehouseItemId_*_integer'),
            'warehouseItemId.*.exists' => __('validate.warehouseItemId_*_exists'),
        
            'amount.required' => __('validate.amount_required'),
            'amount.array' => __('validate.amount_array'),
            'amount.*.numeric' => __('validate.amount_*_numeric'),
            'amount.*.min' => __('validate.amount_*_min'),
        
            'unit_id.required' => __('validate.unit_id_required'),
            'unit_id.array' => __('validate.unit_id_array'),
            'unit_id.*.integer' => __('validate.unit_id_*_integer'),
            'unit_id.*.exists' => __('validate.unit_id_*_exists'),
        
            'unit_name.required' => __('validate.unit_name_required'),
            'unit_name.array' => __('validate.unit_name_array'),
            'unit_name.*.string' => __('validate.unit_name_*_string'),
            'unit_name.*.max' => __('validate.unit_name_*_max'),
        
            'buy_up.array' => __('validate.buy_up_array'),
            'buy_up.*.numeric' => __('validate.buy_up_*_numeric'),
            'buy_up.*.min' => __('validate.buy_up_*_min'),
        
            'sell_up.array' => __('validate.sell_up_array'),
            'sell_up.*.numeric' => __('validate.sell_up_*_numeric'),
            'sell_up.*.min' => __('validate.sell_up_*_min'),
    
            'profit.array' => __('validate.profit_array'),
            'profit.*.numeric' => __('validate.profit_*_numeric'),
        
            'total.required' => __('validate.total_required'),
            'total.array' => __('validate.total_array'),
            'total.*.numeric' => __('validate.total_*_numeric'),
            'total.*.min' => __('validate.total_*_min'),
        
            'total_price.required' => __('validate.total_price_required'),
            'total_price.numeric' => __('validate.total_price_numeric'),
            'total_price.min' => __('validate.total_price_min'),
        
            'general_discount.numeric' => __('validate.general_discount_numeric'),
            'general_discount.min' => __('validate.general_discount_min'),
        
        
            'cur_pay.required' => __('validate.cur_pay_required'),
            'cur_pay.numeric' => __('validate.cur_pay_numeric'),
            'cur_pay.min' => __('validate.cur_pay_min'),
        
            'remained.required' => __('validate.remained_required'),
            'remained.numeric' => __('validate.remained_numeric'),
            'remained.min' => __('validate.remained_min'),
        
            'from_account_id.required' => __('validate.from_account_id_required'),
            'from_account_id.integer' => __('validate.from_account_id_integer'),
            'from_account_id.exists' => __('validate.from_account_id_exists'),
        
            'currency_id.required' => __('validate.currency_id_required'),
            'currency_id.integer' => __('validate.currency_id_integer'),
            'currency_id.exists' => __('validate.currency_id_exists'),
        
            'note.string' => __('validate.note_string'),
            'note.max' => __('validate.note_max'),
        ];
    }


    /**
    * Create Warehouse Sales
    */
    private function createWarehouseSales($request)
    {
        $tax = OrgBio::select('tax_activation')->first();
        try {
            $user_name = auth()->user()->full_name ?? '';
            $user_id = auth()->user()->id ?? '';
            
            // Fix: Properly parse the date
            $idate = $request->todays_date 
                ? Carbon::parse($request->todays_date) 
                : Carbon::now();
            
            $year = $idate->year;
            $month = $idate->month;
            $day = $idate->day;

            $warehouseSales = WarehouseSales::create([
                'billno' => $request->billno,
                'factor' => $request->factor,
                'account_id' => $request->from_account_id,
                'customer_account_id' => $request->customer_account_id,
                'total' => $request->total_price,
                'cur_pay' => $request->cur_pay,
                'remained' => $request->remained,
                'currency_id' => $request->currency_id,
                'tax_activation' => $tax->tax_activation ?? 0,
                'note' => $request->note,
                'idate' => $idate->format('Y-m-d'),
                'user_id' => $user_id,
                'user_name' => $user_name,
                'year' => $year,
                'month' => $month,
                'day' => $day,
                'has_invoice' => 0,
                'invoice_id' => null,
                'times' => $request->times,
                'is_cleared' => 0,
            ]);

            return $warehouseSales->id;

        } catch (\Exception $e) {
            \Log::error('Failed to insert warehouse sales', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);
            throw $e; // Rethrow to be caught in store()
        }
    }
    
    /**
     * Create Sales Details
     */
    private function createSalesDetails($request, $warehouseSalesId)
    {
        $todays_date = $request->todays_date ?? Carbon::now()->format('Y-m-d');
        $data = [];

        foreach ($request->warehouseItemId as $index => $itemId) {
            $data[] = [
                'billno' => $request->billno,
                'warehouse_id' => $request->warehouse_id[$index],
                'warehouse_sales_id' => $warehouseSalesId,
                'pre_list_id' => $request->pre_list_id[$index],
                'unit_id' => $request->unit_id[$index],
                'category_id' => $request->category_id[$index],
                'amount' => $request->amount[$index],
                'buy_up' => $request->buy_up[$index],
                'sell_up' => $request->sell_up[$index],
                'sell_up_no_tax' =>  $request->sell_up_no_tax[$index],
                'sell_tax_per' => $request->sell_tax_per[$index] ?? 0,
                'sell_tax_price' => $request->sell_tax_price[$index] ?? 0,
                'profit' => $request->profit[$index],
                'total' => $request->total[$index],
                'is_returned' => 0,
                'todays_date' => $todays_date,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (empty($data)) {
            throw new \Exception('No sales details to create');
        }

        SalesDetails::insert($data);
    }

    /**
     * Decrease warehouse item with proper tracking
     */
    private function decreaseWarehouseItemAfterStore($warehouseItem, $soldAmount)
    {
        if ($soldAmount <= 0) {
            return;
        }

        // Lock for update to prevent race conditions
        $item = WarehouseItem::where('id', $warehouseItem->id)
            ->lockForUpdate()
            ->first();

        if (!$item) {
            throw new \Exception("Warehouse item not found: {$warehouseItem->id}");
        }

        if ($item->available_amount < $soldAmount) {
            throw new \Exception(
                "Insufficient stock for item {$item->id}. Available: {$item->available_amount}, Requested: {$soldAmount}"
            );
        }

        // Update warehouse item
        $item->out_amount += $soldAmount;
        $item->available_amount -= $soldAmount;

        // Determine which price to use for valuation
        if (intval($item->buy_tax_per) > 0) {
            $valuationPrice = $item->buy_up_vat ?? $item->buy_up;
        } else {
            $valuationPrice = $item->buy_up;
        }

        $item->available_total = round($item->available_amount * $valuationPrice, 2);
        $item->save();
    }
    /**
     * Decrease the amount of items in stock by sold amount
     */
    private function decreaseWarehouseItemFromSoldAmount($request)
    {
        if (!isset($request->warehouseItemId) || empty($request->warehouseItemId)) {
            throw new \Exception('No warehouse items provided');
        }

        // Using lockForUpdate() but NO transaction here - parent handles it
        foreach ($request->warehouseItemId as $index => $itemId) {
            $soldAmount = $request->amount[$index] ?? 0;

            if ($soldAmount <= 0) {
                continue;
            }

            // Lock for update to prevent race conditions
            $warehouseItem = WarehouseItem::where('id', $itemId)
                ->lockForUpdate()
                ->first();

            if (!$warehouseItem) {
                throw new \Exception("Warehouse item not found: {$itemId}");
            }

            if ($warehouseItem->available_amount < $soldAmount) {
                throw new \Exception(
                    "Insufficient stock for item {$itemId}. Available: {$warehouseItem->available_amount}, Requested: {$soldAmount}"
                );
            }

            $warehouseItem->out_amount += $soldAmount;
            $warehouseItem->available_amount -= $soldAmount;
            $buy_up = $warehouseItem->buy_up;

            // Determine which price to use for valuation
            if(intval($warehouseItem->buy_tax_per) > 0) {
                $valuationPrice = $warehouseItem->buy_up_vat ?? $warehouseItem->buy_up;
            } else {
                $valuationPrice = $warehouseItem->buy_up;
            }
            $warehouseItem->available_total = round($warehouseItem->available_amount * $valuationPrice, 2);
            $warehouseItem->save();
        }
    }


    /**
     * Create Journal Entry
     */
    private function handleJournalEntry($request)
    {       
        $short_date = $request->todays_date ?? Carbon::now()->format('Y-m-d');
        $date = Carbon::parse($short_date);
        $day = $date->day;
        $year = $date->year;
        $month = $date->month;
        $time = $request->times ?? '00:00:00';
        $full_date = $date->format('Y-m-d') . ' ' . $time;

        /**
         * ================================== insert in to journal ========================
         * status: 1: old journal, 2: journal, 3:income, 4:expense, 5:salary, 6:participants, 7:buy, 8:sales, 9:other
         * transaction_type: 1:recieved   2:paid
         * payment_type:     1: cache,    2: loan
         */
        
        try {
            /**
             * اگر هیچ پرداخت نکند وتمام شان قرض ثبت گردد
             * خزانه باید طلب ثبت گردد =  paid Loan 
             * مشتری باید قرضدار ثبت گردد = Recieved Loan 
             */
            if(intval($request->cur_pay) === 0 && intval($request->remained) === intval($request->total_price))
            { 
                // ثبت طلب خزانه = paid(ttype=2), loan(ptype=2) 
                $details =   __('validate.sales_talab_bill').' SALES_'.$request->billno;
                $optionLabel = __('validate.sales_talab'); $dynamic_type = 2; $dt_comment = 'clearable';
                $this->createJournalEntry($request,  $optionLabel, $request->from_account_id,  $request->total_price, $ttype = "2", $ptype="2", $date, $full_date, $details, $dynamic_type, $dt_comment);
                
                // ثبت قرضه مشتری = recieved(ttype=1) loan(ptype=2)
                $details = __('validate.sales_loan_bill').' SALES_'.$request->billno;
                $optionLabel = __('validate.sales_loan'); $dynamic_type = 2; $dt_comment = 'clearable';
                $this->createJournalEntry($request, $optionLabel, $request->customer_account_id,  $request->total_price,
                $ttype = "1", $ptype="2", $date, $full_date, $details, $dynamic_type, $dt_comment);
            }

            // کمی شانرا پرداخت کرده و متباقی شانرا قرض انتخاب کرده است
            else if(intval($request->remained) > 0 && intval($request->cur_pay) > 0) 
            {
                // ثبت دریافت نقدی خزانه = Cache Recieved = t1p1
                $details =  __('validate.sales_recieve_bill').' SALES_'.$request->billno;
                $optionLabel = __('validate.sales_cache_recieved'); $dynamic_type = 0; $dt_comment = 'not clearable';
                $this->createJournalEntry($request, $optionLabel, $request->from_account_id, $request->cur_pay, $ttype = "1", $ptype="1", $date, $full_date, $details, $dynamic_type, $dt_comment);

                // ثبت قرضه مشتری = Loan Recieved = p2t1
                $details =  __('validate.sales_loan_bill').' SALES_'.$request->billno;
                $optionLabel = __('validate.sales_loan'); $dynamic_type = 2; $dt_comment = 'clearable';
                $this->createJournalEntry($request, $optionLabel, $request->customer_account_id, $request->remained,  
                $ttype = "1", $ptype="2", $date, $full_date, $details, $dynamic_type, $dt_comment);
            
                // ثبت طلب خزانه = Paid Loan = t2p2
                $details =  __('validate.sales_talab_bill').' SALES_'.$request->billno;
                $optionLabel = __('validate.sales_talab'); $dynamic_type = 2; $dt_comment = 'clearable';
                $this->createJournalEntry($request, $optionLabel,  $request->from_account_id, $request->remained,
                $ttype = "2", $ptype="2", $date, $full_date, $details, $dynamic_type, $dt_comment);
            }

            // قرضدار نمانده است و مکمل پرداخت کرده است
            // تنها در حساب خزانه اضافه شود
            else if(intval($request->remained) === 0 && intval($request->cur_pay) === intval($request->total_price)) 
            {
                // ثبت دریافت نقدی خزانه = Cache Recieved = t1p1
                $details =  __('validate.sales_recieve_bill').' SALES_'.$request->billno;
                $optionLabel = __('validate.sales_cache_recieved'); $dynamic_type = 0; $dt_comment = 'not clearable';
                $this->createJournalEntry($request, $optionLabel, $request->from_account_id, $request->cur_pay,
                $ttype = "1", $ptype="1", $date, $full_date, $details, $dynamic_type, $dt_comment);
            }
        
            return true; 

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Error storing journal entry in SalesController', ['error' => $e->getMessage()]);
            throw $e; // Rethrow to be caught in store()
            return false;
        }
    }

    private function createJournalEntry($request, $optionLabel, $account_id, $amount, $ttype, $ptype, $date, $full_date, $details, $dynamic_type, $dt_comment)
    {
        try 
        {
            $account_type_id = Account::where('id', $account_id)->value('account_type_id');
            Journal::create([
                'bill_no' => $request->billno,
                'code' => $request->code,
                'account_type_id' => $account_type_id,
                'account_id' => $account_id,
                'amount' => $amount,
                'currency_id' => $request->currency_id,
                'transaction_type' => $ttype,
                'payment_type' => $ptype,
                'dynamic_type' => $dynamic_type,
                'dt_comment' => $dt_comment,
                'option_label' => $optionLabel,
                'user_id' => auth()->user()->id ?? '',
                'user_name' => auth()->user()->full_name ?? '',
                'year' => $date->year,
                'month' => $date->month,
                'day' => $date->day,
                'idate' => $request->todays_date,
                'details' => $details,
                'status' => 8,
                'times' => $request->times,
                'is_single_record' => 1,
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error('Create Journal Entry Error: ' . $e->getMessage(), [
                'account_id' => $account_id,
                'amount' => $amount,
                'details' => $details
            ]);
            return false;
        }
    }



    /**
     * Display the specified resource.
    */
    public function details(string $billno)
    {
        $orgbios = OrgBio::all();
        $todaysDate = Carbon::now()->format('Y-m-d');
        $warehouseSales = WarehouseSales::with(['currencyRelation','accountRelation'])->where('billno',$billno)->get();
        $salesDetails = SalesDetails::with(['preListRelation','unitRelation'])->where('billno',$billno)->get();

        $saved_with_tax = $salesDetails->contains(function($item) {
            return $item->sell_tax_per > 0;
        }) ? true : false;

        $customer_account_id = $warehouseSales->first()->customer_account_id ?? 0;
        $currency_id = $warehouseSales->first()->currency_id ?? 1;
        $times = $warehouseSales->first()->times ?? 1;

        // get previous balances
        $customer_balance = $this->getCustomerBalance($customer_account_id, $currency_id,  $times);
        
        // return response()->json(['warehouseSales' => $warehouseSales,'salesDetails'=> $salesDetails]);
        // return ['warehouseSales' => $warehouseSales];
        return view('sales.details',compact('warehouseSales','salesDetails','orgbios','todaysDate','customer_balance',
        'saved_with_tax'));

    }

    /**
     * Get Customer balance by customer_account_id
     */
    private function getCustomerBalance($customer_account_id, $currency_id, $times)
    {
        $journal = DB::table('journals')
            ->select([
                DB::raw("SUM(CASE 
                            WHEN journals.transaction_type = 1 
                            AND journals.payment_type = 1 
                            AND journals.is_cleared = 0 
                            THEN journals.amount ELSE 0 END) as cache_recieved"),
                DB::raw("SUM(CASE 
                            WHEN journals.transaction_type = 2 
                            AND journals.payment_type = 1 
                            AND journals.is_cleared = 0 
                            THEN journals.amount ELSE 0 END) as cache_paid"),
                DB::raw("SUM(CASE 
                            WHEN journals.transaction_type = 1 
                            AND journals.payment_type = 2 
                            AND journals.is_cleared = 0 
                            THEN journals.amount ELSE 0 END) as loan_recieved"),
                DB::raw("SUM(CASE 
                            WHEN journals.transaction_type = 2 
                            AND journals.payment_type = 2 
                            AND journals.is_cleared = 0 
                            THEN journals.amount ELSE 0 END) as loan_paid"),
            ])
            ->where('currency_id', $currency_id)
            ->where('account_id', $customer_account_id)
            
            // ->where('journals.times', '<=', $times)
            ->first();

            // balance = (CachePaid + LoanPaid) - (CacheRecieved + LoanRecieved); 
    
        // Calculate the balance
        $talabat = ($journal->cache_paid + $journal->loan_paid);
        $loans = ($journal->cache_recieved + $journal->loan_recieved);

        // return $balance;
        return ['talabat' => $talabat , 'loans' => $loans];
    }
    


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $billno)
    {

        $orgbios = OrgBio::all();
        $todaysDate = Carbon::now()->format('Y-m-d');
        $warehouseSales = WarehouseSales::with(['currencyRelation','accountRelation'])->where('billno',$billno)->get();
        $salesDetails = SalesDetails::with(['preListRelation','unitRelation'])->where('billno',$billno)->get();
        $billno = $billno;
         $saved_with_tax = $salesDetails->contains(function($item) {
            return $item->sell_tax_per > 0;
        }) ? true : false;

        $customers = Account::select('id','name')->whereIn('account_type_id',[3,4])->get();
        $ownBanks = Account::select('id','name')->whereIn('account_type_id',[1,6])->orderBy('is_pre_select','DESC')->get();

        $currencies = Currency::select('id','name')->get();
        // return response()->json(['warehouseSales' => $warehouseSales,'salesDetails'=> $salesDetails]);
        return view('sales.edit',compact('warehouseSales','salesDetails','orgbios','todaysDate','customers','ownBanks','currencies','billno','saved_with_tax'));
    }

    /**
     * 
     */
    public function getSingleRecordForEdit(string $id)
    {
        $units = Unit::select('id','name')->get();
        $salesDetails = SalesDetails::with(['preListRelation','unitRelation'])->where('id', $id)->first();

        if (!$salesDetails) {
            return response()->json(['error' => 'Sales Details not found'], 404);
        }

        //  return response()->json(['boughtItemDetails' => $boughtItemDetails]);
        // return response()->json(['boughtItemDetails' => $boughtItemDetails, 'warehouseItems' => $warehouseItems]);
        return view('sales.editModalContent', compact('salesDetails', 'units'));
    }

    public function updateSalesAndWarehouseItems(Request $request)
    {
        $validated = $request->validate([
            'id'                => 'required|exists:sales_details,id',
            'pre_list_id'       => 'required|exists:bought_item_pre_lists,id',
            'warehouse_id'      => 'required|exists:warehouses,id',
            'amount'            => 'required|numeric|min:0',
            'old_amount'        => 'required|numeric|min:0',
            'billno'            => 'required|numeric|min:0',
            'unit_id'           => 'required|exists:units,id',
            'sell_up'           => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();
        try {
            $salesDetails = SalesDetails::findOrFail($validated['id']);

            $new_total = $validated['amount'] * $validated['sell_up'];
            $profit = $new_total - ($validated['amount'] * $salesDetails->buy_up);

            $salesDetails->update([
                'amount'   => $validated['amount'],
                'sell_up'  => $validated['sell_up'],
                'profit'   => $profit,
                'unit_id'  => $validated['unit_id'],
                'total'    => $new_total,
            ]);

            // Find Warehouse Item
            $warehouseItem = WarehouseItem::where('warehouse_id', $validated['warehouse_id'])
                                            ->where('buy_pre_id', $validated['pre_list_id'])
                                            ->where('unit_id', $validated['unit_id'])
                                            ->where('available_amount', '>', 0)
                                            ->first();

            if (!$warehouseItem) {
                throw new \Exception('Warehouse item not found.');
            }

            // Calculate the difference
            $oldAmount = (float) $validated['old_amount'];
            $newAmount = (float) $validated['amount'];
            $diff = abs($newAmount - $oldAmount);
            
            if (!$warehouseItem) {
                throw new \Exception('Warehouse item not found.');
            }

            // Update warehouse quantities based on difference
            // اگر مقدار کمتر شود باید به همان مقدار از 
            // out_amount کم شود و available_amount نیز کم شود
            // مثلا قبلا ۴ دانه فروخته بودیم و حالا ۲ ساختیم
            if ($oldAmount > $newAmount) 
            {
                // Amount decreased - return items to warehouse
                $warehouseItem->available_amount += $diff;
                $warehouseItem->out_amount -= $diff;
                // اگر مقدار زیادتر شود باید از مقدار موجود کم شود و 
                // مثلا: دو دانه فروخته بودیم حالا چهار دانه ویرایش میکنم این دو دانه 
            } 
            elseif ($newAmount > $oldAmount) 
            {
                // Amount increased - take more items from warehouse
                if ($warehouseItem->available_amount < $diff) {
                    throw new \Exception('Not enough stock available in warehouse.');
                }
                $warehouseItem->available_amount -= $diff;
                $warehouseItem->out_amount += $diff;
            }

            // Determine the valuation price (with or without tax)
            $valuationPrice = $warehouseItem->buy_up ?? 0;
            if (intval($warehouseItem->buy_tax_per ?? 0) > 0) {
                $valuationPrice = $warehouseItem->buy_up_vat ?? $warehouseItem->buy_up ?? 0;
            }

            // Calculate available_total = available_amount × valuation_price
            $warehouseItem->available_total = round($warehouseItem->available_amount * $valuationPrice, 2);

            $warehouseItem->save();

            DB::commit();

            return redirect()->route('sales.edit', ['billno' => $validated['billno']])
                            ->with('notification', [
                                'message' =>  __('common.updated_successfully'),
                                'type'    => 'success',
                            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Error in updateSalesAndWarehouseItems', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'request' => $validated,
            ]);

            return redirect()->back()->withInput()
                            ->with('notification', [
                                'message' => __('common.update_failed') . ' ' . $e->getMessage(),
                                'type'    => 'danger',
                            ]);
        }
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'billno'              => 'required|integer|min:1',
            'customer_account_id' => 'required|exists:accounts,id',
            'currency_id'         => 'required|exists:currencies,id',
            'total_price'         => 'required|numeric|min:0',
            'from_account_id'     => 'required|exists:accounts,id',
            'cur_pay'             => 'required|numeric|min:0',
            'remained'            => 'required|numeric|min:0',
            'note'                => 'nullable|string|max:500',
            'factor'              => 'nullable',
        ]);
    
        DB::beginTransaction();
    
        try {
            // Find the warehouse sale record
            $warehouseSales = WarehouseSales::where('billno', $validated['billno'])->firstOrFail();
    
           
            // Update warehouse sale details
            $warehouseSales->update([
                'total'          => $validated['total_price'],
                'cur_pay'        => $validated['cur_pay'],
                'remained'       => $validated['remained'],
                'note'           => $validated['note'],
                'factor'         => $validated['factor'],
            ]);
    
            // Retrieve old journal records
            $oldJournals = Journal::where('times', $request->times)->where('status', 8)->get();
    
            if ($oldJournals->isNotEmpty()) {
                // Clone request to avoid modifying original data
                $clonedRequest = clone $request;
                $clonedRequest->merge([
                    'code' => $oldJournals->first()->code, // Get 'code' from the first record
                ]);
    
                // Delete all journal records in a single query
                Journal::where('times', $request->times)->where('status', 8)->delete();
    
                // Handle new journal entry
                $checkJournal = $this->handleJournalEntry($clonedRequest);
    
                if (!$checkJournal) {
                    DB::rollBack();
                    return redirect()->route('sales.details', ['billno' => $request->billno])
                        ->with('notification', [
                            'message' => __('common.update_failed'),
                            'type'    => 'danger',
                        ]);
                }
            }
    
            // Commit transaction
            DB::commit();
            return redirect()->route('sales.details', ['billno' => $request->billno])
                ->with('notification', [
                    'message' => __('common.updated_successfully'),
                    'type'    => 'success',
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating WarehouseSales: ' . $e->getMessage());
    
            return redirect()->route('sales.details', ['billno' => $request->billno])
                ->with('notification', [
                    'message' => __('common.update_failed'),
                    'type'    => 'danger',
                ]);
        }
    }
    


    /**
     * Remove the specified resource from storage.
     * Param: sales_deatils_id
     */
    public function deleteSingleItem(string $id)
    {
        DB::beginTransaction();
        try {
            // Retrieve SalesDetails correctly
            $SalesDetails = SalesDetails::findOrFail($id);

            // Find Warehouse Item
            $warehouseItem = WarehouseItem::where('warehouse_id', $SalesDetails->warehouse_id)
                                        ->where('buy_pre_id', $SalesDetails->pre_list_id)
                                        ->where('unit_id', $SalesDetails->unit_id)
                                        ->first();

            if (!$warehouseItem) {
                throw new \Exception('Warehouse item not found.');
            }

            // Update warehouse item
            // در صورت حذف باید از رفت همان تعداد کم شود و در موجود اضافه شود
            $warehouseItem->available_amount += $SalesDetails->amount;
            $warehouseItem->out_amount -= $SalesDetails->amount; 
            // $warehouseItem->available_total = (($warehouseItem->available_amount + $SalesDetails->amount) * $warehouseItem->buy_up);
            $warehouseItem->available_total = $warehouseItem->available_amount * $SalesDetails->buy_up;
            $warehouseItem->save();

            // Delete SalesDetails **after** updating warehouse item
            $SalesDetails->delete();

            DB::commit();

            Session::put('notification', [
                'message' => __('common.deleted_successfully'),
                'type' => 'success',
            ]);

            return true; 
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Error deleting records: ' . $e->getMessage());

            Session::put('notification', [
                'message' => __('common.delete_failed'),
                'type' => 'danger',
            ]);

            return false;
        }
    }

    public function destroy(string $times)
    {
        DB::beginTransaction();
        try {
            $warehouse_sales = WarehouseSales::where('times', $times)
                
                ->first();

            if ($warehouse_sales) {
                SalesDetails::where('warehouse_sales_id', $warehouse_sales->id)
                    
                    ->delete();

                $warehouse_sales->delete();
            }

            Journal::where('times', $times)
                
                ->delete();

            DB::commit();

            Session::put('notification', [
                'message' => __('common.deleted_successfully'),
                'type' => 'success',
            ]);

            return redirect()->route('sales.index');
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Error deleting records: ' . $e->getMessage());

            Session::put('notification', [
                'message' => __('common.update_failed'),
                'type' => 'danger',
            ]);

            return back();
        }
    }


    // ========================================== INVOICES ==========================================

    /**
     * Display invoice list
     */
    public function invoiceList()
    {
        $tax = OrgBio::select('tax_activation')->first();
        return view('sales.invoice.invoice_list',compact('tax'));
    }

    /**
     * Get invoice data for DataTable
     */
    public function getInvoiceData(Request $request)
    {
        $tax_activation = $request->input('tax_activation');
        $invoices = SalesInvoice::with(['customer', 'currency'])
            ->orderBy('id', 'DESC');

        return DataTables::of($invoices)
            ->addIndexColumn()
            ->addColumn('customer_name', function($invoice) {
                return $invoice->customer ? $invoice->customer->name : '-';
            })
            ->addColumn('total', function($invoice) use ($tax_activation) {
                return  number_format($invoice->total ?? 0, 2);
            })
            ->addColumn('paid_amount', function($invoice) {
                return number_format($invoice->paid_amount, 2);
            })
            ->addColumn('remaining', function($invoice) use ($tax_activation) {
                 return number_format($invoice->remaining ?? 0, 2);
            })
            ->addColumn('status', function($invoice) {
                $statusClasses = [
                    0 => 'badge-secondary',
                    1 => 'badge-warning',
                    2 => 'badge-info',
                    3 => 'badge-success',
                    4 => 'badge-danger'
                ];
                $statusLabels = [
                    0 => __('order.draft'),
                    1 => __('order.pending'),
                    2 => __('order.partial'),
                    3 => __('order.paid'),
                    4 => __('order.cancelled')
                ];
                return '<span class="badge ' . ($statusClasses[$invoice->status] ?? 'badge-secondary') . '">' 
                    . ($statusLabels[$invoice->status] ?? __('order.unknown')) . '</span>';
            })
            ->addColumn('invoice_date', function($invoice) {
                return $invoice->invoice_date->format('Y-m-d');
            })
            ->addColumn('action', function($invoice) {
                return '<a href="' . route('sales.showInvoice', $invoice->id) . '" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i>
                        </a>';
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    /**
     * Generate invoice from selected bought items
     */
    public function generateInvoice(Request $request)
    {
        try 
        {
            // id of warehouse_sales table
            $warehouseSalesIds = $request->sold_item_ids;

            // return ['warehouseSalesIds' => $warehouseSalesIds];
            
            if (empty($warehouseSalesIds)) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('buy.select_at_least_one')
                ]);
            }

            // Get selected sold items
            $warehouseSales = WarehouseSales::whereIn('id', $warehouseSalesIds)->get();
            
            if ($warehouseSales->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('buy.no_items_found')
                ]);
            }

            // Check if all items belong to same customer
            $customerId = $warehouseSales->first()->customer_account_id;
            $differentCustomer = $warehouseSales->where('customer_account_id', '!=', $customerId)->count() > 0;
            
            if ($differentCustomer) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('buy.different_customers')
                ]);
            }

            DB::beginTransaction();

            // Generate invoice number
            $invoiceNumber = 'SINV-' . date('Ymd') . '-' . (SalesInvoice::count() + 1);

            // Calculate totals
            $totalAmount = $warehouseSales->sum('total');
            $paidAmount = $warehouseSales->sum('cur_pay');
            $remainingAmount = $warehouseSales->sum('remained');

            // Create invoice
            $invoice = SalesInvoice::create([
                'invoice_number' => $invoiceNumber,
                'customer_id' => $customerId,
                'total' => $totalAmount,
                'paid_amount' => $paidAmount,
                'remaining' => $remainingAmount,
                'currency_id' => $warehouseSales->first()->currency_id,
                'status' =>   1, // 0: draft, 1: in progress, 2: partial, 3: paid
                'tax_activation' => $warehouseSales->first()->tax_activation ?? 0,  
                'invoice_date' => now(),
                'due_date' => now()->addDays(30),
                'notes' => __('buy.invoice_generated_from_bought_items'),
                'created_by' => auth()->id(),
                'times' => time()
            ]);

            // Create invoice items
            foreach ($warehouseSales as $salestItem) {
                // Get details for this Sales item from sales details
                $details = SalesDetails::where('warehouse_sales_id', $salestItem->id)->get();
                
                foreach ($details as $detail) {
                    SalesInvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'sales_details_id' => $detail->id,
                        'warehouse_sales_id' => $salestItem->id,
                        'pre_list_id' => $detail->pre_list_id,
                        'amount' => $detail->amount,
                        'unit_id' => $detail->unit_id,
                        'unit_price' => $detail->sell_up_no_tax,
                        'unit_price_vat' => $detail->sell_up_vat ?? 0, 
                        'tax_percentage' => $detail->sell_tax_per ?? 0,
                        'tax_amount' => $detail->sell_tax_price ?? 0,
                        'sell_up_vat' => $detail->sell_up ?? 0, // in sales_details it stores with or without tax
                        'total' => $detail->amount * $detail->sell_up_no_tax,
                        'total_vat' => $detail->amount * $detail->sell_up,  
                        'times' => time()
                    ]);
                }
            }

            // Update warehouse_sales to mark as invoiced (you need to add a column)
            WarehouseSales::whereIn('id', $warehouseSalesIds)->update(['has_invoice' => 1,'invoice_id' => $invoice->id]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => __('buy.invoice_generated_successfully'),
                'invoice_id' => $invoice->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Generate Invoice Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => __('common.error_occurred') . ': ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show invoice details
     */
    public function showInvoice($id)
    {
        $orgbios = OrgBio::all();
        $times = time();
        $invoice = SalesInvoice::with(['customer', 'items.unit', 'items.preList', 'payments', 'currency'])
            ->findOrFail($id);
        $customers = Account::select('id','name')->whereIn('account_type_id',[3])->get();
        $ownBanks = Account::select('id','name')->whereIn('account_type_id',[1,6])->orderBy('is_pre_select','DESC')->get();
        $newJournalCode =  Journal::max('code') + 1;
        $currencies = Currency::select('id','name')->get();
        // return ['data' => $invoice];

        return view('sales.invoice.invoice_details', compact('invoice','orgbios','customers','ownBanks','newJournalCode','times','currencies'));
    }

    public function addPayment(Request $request)
    {
        try 
        {
            $validated = $request->validate([
                'invoice_id' => 'required|exists:sales_invoices,id',
                'amount' => 'required|numeric|min:0.01',
                'payment_method' => 'required|in:1,2,3',
                'account_id' => 'required|exists:accounts,id',
                'customer_account_id' => 'required|exists:accounts,id',
                'payment_date' => 'required|date',
                'reference_number' => 'nullable|string|max:100',
                'notes' => 'nullable|string|max:255',
                'code' => 'required',
                'times' => 'required',
                'currency_id' => 'required',
                'tax_activation' => 'nullable|in:0,1'
            ]);
 

            // Log::info($request->all());

            DB::beginTransaction();

            $invoice = SalesInvoice::findOrFail($validated['invoice_id']);
            $taxActivation = (int) ($request->tax_activation ?? 0);
            $amount = (float) $validated['amount'];
            $invoice_id = substr($invoice->invoice_number, strrpos($invoice->invoice_number, '-') + 1);
            
            // ========================= Update Invoice =================================
            $newPaidAmount = $invoice->paid_amount + $amount;
            $newRemaining = $invoice->total - $newPaidAmount;
            // Determine status
            if ($newPaidAmount >= $invoice->total) {
                $status = 3; // Fully paid
            } elseif ($newPaidAmount > 0) {
                $status = 2; // Partial
            } else {
                $status = 1; // Pending
            }
            
            $invoice->update([
                'paid_amount' => $newPaidAmount,
                'remaining' => max(0, $newRemaining),
                'status' => $status
            ]);

            // ========================= Create Payment Record =================================
            $payment = SalesInvoicePayment::create([
                'invoice_id' => $invoice->id,
                'payment_date' => $validated['payment_date'],
                'amount' => $amount,
                'payment_method' => $validated['payment_method'],
                'account_id' => $validated['account_id'],
                'customer_account_id' => $validated['customer_account_id'],
                'reference_number' => $validated['reference_number'],
                'notes' => $validated['notes'],
                'created_by' => auth()->id(),
                'times' => time()
            ]);

            // ========================= Update warehouse Sales =================================
            $warehouseSales = WarehouseSales::where('invoice_id', $invoice->id)
                ->orderBy('id', 'ASC')
                ->get();

            if ($warehouseSales->isEmpty()) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => __('common.record_not_found')
                ], 404);
            }

            //  Check if single or multiple records
            $itemsCount = $warehouseSales->count();
            $remainingPayment = (float) $amount;

            if ($itemsCount === 1) 
            {
                // =============================================
                // SINGLE RECORD - Apply payment directly
                // =============================================
                $warehouseItem = $warehouseSales->first();
                
                $itemTotalPrice = (float) $warehouseItem->total;
                $itemCurrentPaid = (float) $warehouseItem->cur_pay;
                
                // Calculate new values
                $newCurPay = $itemCurrentPaid + $amount;
                $newRemainingPrice = max(0, $itemTotalPrice - $newCurPay);
                
                // Update single item
                $warehouseItem->update([
                    'cur_pay' => $newCurPay,
                    'remained' => $newRemainingPrice,
                    'status' => $newRemainingPrice <= 0 ? 3 : 2
                ]);
            }  
            else 
            {
                /**
                * 
                * Initial State:
                * Item 1: total=480, cur_pay=0, remained=480
                * Item 2: total=200, cur_pay=0, remained=200

                * Payment 1: 50
                * → Item 1: allocated=50, cur_pay=50, remained=430, remaining_payment=0
                * Result: Item 1 = 50/480, Item 2 = 0/200

                * Payment 2: 350
                * → Item 1: itemRemainingPrice = 480 - 50 = 430
                * → allocated=350 (partial), cur_pay=400, remained=80, remaining_payment=0
                * Result: Item 1 = 400/480, Item 2 = 0/200

                * Payment 3: 100
                * → Item 1: itemRemainingPrice = 480 - 400 = 80
                * → allocated=80, cur_pay=480, remained=0, remaining_payment=20
                * → Item 2: itemRemainingPrice = 200 - 0 = 200
                * → allocated=20, cur_pay=20, remained=180, remaining_payment=0
                * Result: Item 1 = 480/480 (PAID), Item 2 = 20/200 (PARTIAL)
                *
                */
                // =============================================
                // MULTIPLE RECORDS - Distribute payment sequentially
                // =============================================
                
                // Calculate total remaining for validation
                $totalRemaining = 0;
                foreach ($warehouseSales as $item) {
                    $totalRemaining += max(0, (float) $item->total - (float) $item->cur_pay);
                }

                if ($remainingPayment > $totalRemaining) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => __('buy.payment_exceeds_remaining', [
                            'remaining' => number_format($totalRemaining, 2)
                        ])
                    ], 422);
                }

                // Distribute payment sequentially across items
                foreach ($warehouseSales as $index => $warehouseSalesItem) {
                    // Stop if no more payment to distribute
                    if ($remainingPayment <= 0.01) {
                        break;
                    }
                    
                    $itemTotalPrice = (float) ($warehouseSalesItem->total ?? 0);
                    $itemCurrentPaid = (float) ($warehouseSalesItem->cur_pay ?? 0);
                    $itemRemainingPrice = max(0, $itemTotalPrice - $itemCurrentPaid);
                    
                    // Skip if item is already fully paid
                    if ($itemRemainingPrice <= 0.01) {
                        continue;
                    }
                    
                    // Determine how much to allocate to this item
                    $allocatedAmount = 0;
                    if ($remainingPayment >= $itemRemainingPrice) {
                        // Pay the FULL remaining amount of this item
                        $allocatedAmount = $itemRemainingPrice;
                        $remainingPayment -= $itemRemainingPrice;
                    } else {
                        // Pay PARTIAL amount to this item (remainingPayment will become 0)
                        $allocatedAmount = $remainingPayment;
                        $remainingPayment = 0;
                    }
                    
                    // Calculate new values
                    $newCurPay = round($itemCurrentPaid + $allocatedAmount, 2);
                    $newRemainingPrice = round($itemTotalPrice - $newCurPay, 2);
                   
                    
                    // Determine status
                    if ($newCurPay <= 0) {
                        $status = 1;       // unpaid
                    } elseif ($newRemainingPrice <= 0.01) {
                        $status = 3;       // paid
                    } else {
                        $status = 2;       // partial
                    }
                    
                    // Log before update
                    // \Log::info('Before Update - Item ' . $warehouseSalesItem->id, [
                    //     'total_price' => $itemTotalPrice,
                    //     'current_paid' => $itemCurrentPaid,
                    //     'remaining' => $itemRemainingPrice,
                    //     'allocated' => $allocatedAmount,
                    //     'new_paid' => $newCurPay,
                    //     'new_remaining' => $newRemainingPrice
                    // ]);
                    
                    
                    $warehouseSalesItem->update([
                        'cur_pay' => $newCurPay,
                        'remained' => max(0, $newRemainingPrice),
                        'status' => $status,
                    ]);

                    // Log after update
                    // \Log::info('After Update - Item ' . $warehouseSalesItem->id, [
                    //     'cur_pay' => $warehouseSalesItem->fresh()->cur_pay,
                    //     'remained' => $warehouseSalesItem->fresh()->remained
                    // ]);
                }
            }

            // ========================= Journal Entries =================================
            $date = $request->payment_date 
                ? Carbon::parse($request->payment_date) 
                : Carbon::now();

            $full_date = $date->format('Y-m-d H:i:s');

            // Add these to the request for the journal entry
            $request->merge([
                'bill_no' => $invoice_id,
                'payment_date' => $date->format('Y-m-d'),
                'todays_date' => $date->format('Y-m-d'), // Add this for compatibility
                'idate' => $date,
            ]);

            // Payment from account (Paid)
            $details = __('validate.cache_payment_invoice') . 'SINV_' . $invoice_id;
            // 1: old journal, 2: journal, 3:income, 4:expense, 5:salary, 6:participants, 7:buy, 8:sales, 9:buy invoice,  10:sales invoice, 11:other
            $status = 10; 
            $optionLabel = __('validate.inv_pay');
            $dynamic_type = $invoice_id;
            $dt_comment = 'Invoice';
            
            $check1 = $this->createJournalEntry($request, $optionLabel, $request->account_id,  $amount, 
                "2", "1", $date, $full_date, $details, $dynamic_type, $dt_comment, $status
            );

            // Received by supplier
            $details2 = __('validate.cache_recieved_invoice') . 'SINV_' . $invoice_id;
            $optionLabel = __('validate.inv_rec');
            
            $check2 = $this->createJournalEntry(
                $request,  $optionLabel, $request->customer_account_id, $amount, 
                "1", "1", $date, $full_date, $details2, $dynamic_type, $dt_comment, $status
            );

            if (!$check1 || !$check2) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => __('common.add_failed')
                ], 500);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => __('common.added_successfully'),
                'data' => [
                    'payment' => $payment,
                    'invoice' => $invoice->fresh(),
                    'warehouseSales' => $warehouseSales->fresh(),
                    'items_count' => $itemsCount
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Add Payment Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => __('common.error_occurred') . ': ' . $e->getMessage()
            ], 500);
        }
    }

    public function addPayment2(Request $request)
    {
        try 
        {
            \Log::info('=== START addPayment ===');
            \Log::info('Request data:', $request->all());

            $validated = $request->validate([
                'invoice_id' => 'required|exists:sales_invoices,id',
                'amount' => 'required|numeric|min:0.01',
                'payment_method' => 'required|in:1,2,3',
                'account_id' => 'required|exists:accounts,id',
                'customer_account_id' => 'required|exists:accounts,id',
                'payment_date' => 'required|date',
                'reference_number' => 'nullable|string|max:100',
                'notes' => 'nullable|string|max:255',
                'code' => 'required',
                'times' => 'required',
                'currency_id' => 'required',
                'tax_activation' => 'nullable|in:0,1'
            ]);

            \Log::info('Validation passed', ['validated' => $validated]);

            DB::beginTransaction();
            \Log::info('Transaction started');

            $invoice = SalesInvoice::findOrFail($validated['invoice_id']);
            \Log::info('Invoice found', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'total' => $invoice->total,
                'paid_amount' => $invoice->paid_amount,
                'remaining' => $invoice->remaining,
                'status' => $invoice->status
            ]);

            $taxActivation = (int) ($request->tax_activation ?? 0);
            $amount = (float) $validated['amount'];
            $invoice_id = substr($invoice->invoice_number, strrpos($invoice->invoice_number, '-') + 1);
            
            \Log::info('Payment details', [
                'amount' => $amount,
                'tax_activation' => $taxActivation,
                'invoice_id' => $invoice_id
            ]);

            // ========================= Update Invoice =================================
            $newPaidAmount = $invoice->paid_amount + $amount;
            $newRemaining = $invoice->total - $newPaidAmount;
            
            \Log::info('Invoice update calculation', [
                'old_paid_amount' => $invoice->paid_amount,
                'new_paid_amount' => $newPaidAmount,
                'old_remaining' => $invoice->remaining,
                'new_remaining' => $newRemaining
            ]);

            // Determine status
            if ($newPaidAmount >= $invoice->total) {
                $status = 3; // Fully paid
            } elseif ($newPaidAmount > 0) {
                $status = 2; // Partial
            } else {
                $status = 1; // Pending
            }
            
            \Log::info('Invoice status determined', ['status' => $status]);
            
            $invoice->update([
                'paid_amount' => $newPaidAmount,
                'remaining' => max(0, $newRemaining),
                'status' => $status
            ]);

            \Log::info('Invoice updated successfully');

            // ========================= Create Payment Record =================================
            $payment = SalesInvoicePayment::create([
                'invoice_id' => $invoice->id,
                'payment_date' => $validated['payment_date'],
                'amount' => $amount,
                'payment_method' => $validated['payment_method'],
                'account_id' => $validated['account_id'],
                'customer_account_id' => $validated['customer_account_id'],
                'reference_number' => $validated['reference_number'],
                'notes' => $validated['notes'],
                'created_by' => auth()->id(),
                'times' => time()
            ]);

            \Log::info('Payment record created', [
                'payment_id' => $payment->id,
                'payment_amount' => $payment->amount
            ]);

            // ========================= Update Warehouse Sales =================================
            $warehouseSales = WarehouseSales::where('invoice_id', $invoice->id)
                ->orderBy('id', 'ASC')
                ->get();

            \Log::info('Warehouse sales retrieved', [
                'count' => $warehouseSales->count(),
                'items' => $warehouseSales->map(function($item) {
                    return [
                        'id' => $item->id,
                        'total' => $item->total,
                        'cur_pay' => $item->cur_pay,
                        'remained' => $item->remained,
                        'status' => $item->status
                    ];
                })->toArray()
            ]);

            if ($warehouseSales->isEmpty()) {
                \Log::error('No warehouse sales found for invoice', ['invoice_id' => $invoice->id]);
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => __('common.record_not_found')
                ], 404);
            }

            $itemsCount = $warehouseSales->count();
            $remainingPayment = (float) $amount;

            \Log::info('Payment distribution started', [
                'items_count' => $itemsCount,
                'remaining_payment' => $remainingPayment
            ]);

            if ($itemsCount === 1) 
            {
                // =============================================
                // SINGLE RECORD - Apply payment directly
                // =============================================
                \Log::info('=== SINGLE RECORD MODE ===');
                
                $warehouseItem = $warehouseSales->first();
                
                $itemTotalPrice = (float) $warehouseItem->total;
                $itemCurrentPaid = (float) $warehouseItem->cur_pay;
                
                \Log::info('Single item before update', [
                    'id' => $warehouseItem->id,
                    'total' => $itemTotalPrice,
                    'cur_pay' => $itemCurrentPaid,
                    'remained' => $warehouseItem->remained
                ]);
                
                // Calculate new values
                $newCurPay = $itemCurrentPaid + $amount;
                $newRemainingPrice = max(0, $itemTotalPrice - $newCurPay);
                
                \Log::info('Single item calculation', [
                    'new_cur_pay' => $newCurPay,
                    'new_remaining' => $newRemainingPrice,
                    'status' => $newRemainingPrice <= 0 ? 3 : 2
                ]);
                
                // Update single item
                $warehouseItem->update([
                    'cur_pay' => $newCurPay,
                    'remained' => $newRemainingPrice,
                    'status' => $newRemainingPrice <= 0 ? 3 : 2
                ]);

                \Log::info('Single item updated', [
                    'id' => $warehouseItem->id,
                    'new_cur_pay' => $warehouseItem->fresh()->cur_pay,
                    'new_remained' => $warehouseItem->fresh()->remained
                ]);
            }  
            else 
            {
                // =============================================
                // MULTIPLE RECORDS - Distribute payment sequentially
                // =============================================
                \Log::info('=== MULTIPLE RECORDS MODE ===');
                
                // Calculate total remaining for validation
                $totalRemaining = 0;
                foreach ($warehouseSales as $item) {
                    $itemRemaining = max(0, (float) $item->total - (float) $item->cur_pay);
                    $totalRemaining += $itemRemaining;
                    \Log::info('Item remaining calculation', [
                        'id' => $item->id,
                        'total' => $item->total,
                        'cur_pay' => $item->cur_pay,
                        'remaining' => $itemRemaining
                    ]);
                }

                \Log::info('Total remaining calculation', [
                    'total_remaining' => $totalRemaining,
                    'payment_amount' => $remainingPayment
                ]);

                if ($remainingPayment > $totalRemaining) {
                    \Log::error('Payment exceeds total remaining', [
                        'payment' => $remainingPayment,
                        'total_remaining' => $totalRemaining
                    ]);
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => __('buy.payment_exceeds_remaining', [
                            'remaining' => number_format($totalRemaining, 2)
                        ])
                    ], 422);
                }

                // Distribute payment sequentially across items
                $iteration = 0;
                foreach ($warehouseSales as $warehouseSalesItem) {
                    $iteration++;
                    \Log::info("=== Iteration {$iteration} ===");
                    
                    // Stop if no more payment to distribute
                    if ($remainingPayment <= 0.01) {
                        \Log::info('No more payment to distribute, breaking loop');
                        break;
                    }
                    
                    $itemTotalPrice = (float) ($warehouseSalesItem->total ?? 0);
                    $itemCurrentPaid = (float) ($warehouseSalesItem->cur_pay ?? 0);
                    $itemRemainingPrice = max(0, $itemTotalPrice - $itemCurrentPaid);
                    
                    \Log::info('Item details', [
                        'id' => $warehouseSalesItem->id,
                        'total' => $itemTotalPrice,
                        'current_paid' => $itemCurrentPaid,
                        'remaining_price' => $itemRemainingPrice
                    ]);
                    
                    // Skip if item is already fully paid
                    if ($itemRemainingPrice <= 0.01) {
                        \Log::info('Item already fully paid, skipping', ['id' => $warehouseSalesItem->id]);
                        continue;
                    }
                    
                    // Determine how much to allocate to this item
                    $allocatedAmount = 0;
                    if ($remainingPayment >= $itemRemainingPrice) {
                        // Pay the FULL remaining amount of this item
                        $allocatedAmount = $itemRemainingPrice;
                        $remainingPayment -= $itemRemainingPrice;
                        \Log::info('Paying full remaining amount', [
                            'allocated' => $allocatedAmount,
                            'remaining_payment' => $remainingPayment
                        ]);
                    } else {
                        // Pay PARTIAL amount to this item
                        $allocatedAmount = $remainingPayment;
                        $remainingPayment = 0;
                        \Log::info('Paying partial amount', [
                            'allocated' => $allocatedAmount,
                            'remaining_payment' => $remainingPayment
                        ]);
                    }
                    
                    // Calculate new values
                    $newCurPay = round($itemCurrentPaid + $allocatedAmount, 2);
                    $newRemainingPrice = round($itemTotalPrice - $newCurPay, 2);
                
                    // Determine status
                    if ($newCurPay <= 0) {
                        $status = 1;       // unpaid
                    } elseif ($newRemainingPrice <= 0.01) {
                        $status = 3;       // paid
                    } else {
                        $status = 2;       // partial
                    }
                    
                    \Log::info('Before update - Item ' . $warehouseSalesItem->id, [
                        'total_price' => $itemTotalPrice,
                        'current_paid' => $itemCurrentPaid,
                        'remaining' => $itemRemainingPrice,
                        'allocated' => $allocatedAmount,
                        'new_paid' => $newCurPay,
                        'new_remaining' => $newRemainingPrice,
                        'status' => $status
                    ]);
                    
                    // Update the item
                    $warehouseSalesItem->update([
                        'cur_pay' => $newCurPay,
                        'remained' => max(0, $newRemainingPrice),
                        'status' => $status,
                    ]);

                    // Log after update
                    $freshItem = $warehouseSalesItem->fresh();
                    \Log::info('After update - Item ' . $warehouseSalesItem->id, [
                        'cur_pay' => $freshItem->cur_pay,
                        'remained' => $freshItem->remained,
                        'status' => $freshItem->status
                    ]);
                }

                \Log::info('Distribution complete', [
                    'remaining_payment' => $remainingPayment,
                    'items_processed' => $iteration
                ]);
            }

            // ========================= Journal Entries =================================
            \Log::info('=== Journal Entries ===');
            
            $date = $request->payment_date 
                ? Carbon::parse($request->payment_date) 
                : Carbon::now();

            $time = $request->times ?? '00:00:00';
            $full_date = $date->format('Y-m-d') . ' ' . $time;

            $request->merge([
                'bill_no' => 0,
                'idate' => $date,
            ]);

            \Log::info('Journal entry details', [
                'payment_date' => $date,
                'full_date' => $full_date,
                'amount' => $amount
            ]);

            // Payment from account (Paid)
            $details = __('validate.cache_payment_invoice') . 'SINV_' . $invoice_id;
            $status = 10; 
            $optionLabel = __('validate.inv_pay');
            $dynamic_type = 2;
            $dt_comment = 'Invoice';
            
            \Log::info('Creating journal entry 1 (Payment from account)', [
                'account_id' => $request->account_id,
                'amount' => $amount,
                'details' => $details
            ]);
            
            $check1 = $this->createJournalEntry($request, $optionLabel, $request->account_id, $amount, 
                "2", "1", $date, $full_date, $details, $dynamic_type, $dt_comment, $status
            );

            // Received by customer
            $details2 = __('validate.cache_recieved_invoice') . 'SINV_' . $invoice_id;
            $optionLabel = __('validate.inv_rec');
            
            \Log::info('Creating journal entry 2 (Received by customer)', [
                'account_id' => $request->customer_account_id,
                'amount' => $amount,
                'details' => $details2
            ]);
            
            $check2 = $this->createJournalEntry(
                $request, $optionLabel, $request->customer_account_id, $amount, 
                "1", "1", $date, $full_date, $details2, $dynamic_type, $dt_comment, $status
            );

            \Log::info('Journal entries result', [
                'check1' => $check1,
                'check2' => $check2
            ]);

            if (!$check1 || !$check2) {
                \Log::error('Journal entry creation failed', [
                    'check1' => $check1,
                    'check2' => $check2
                ]);
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => __('common.add_failed')
                ], 500);
            }

            DB::commit();
            \Log::info('=== Transaction committed successfully ===');

            return response()->json([
                'status' => 'success',
                'message' => __('buy.payment_added_successfully'),
                'data' => [
                    'payment' => $payment,
                    'invoice' => $invoice->fresh(),
                    'warehouseSales' => $warehouseSales->fresh(),
                    'items_count' => $itemsCount
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('=== ADD PAYMENT ERROR ===');
            \Log::error('Error message: ' . $e->getMessage());
            \Log::error('Error trace: ' . $e->getTraceAsString());
            \Log::error('Request data: ', $request->all());
            
            return response()->json([
                'status' => 'error',
                'message' => __('common.error_occurred') . ': ' . $e->getMessage()
            ], 500);
        }
    }

    private function logWarehouseSalesState($warehouseSales, $label = 'State')
    {
        \Log::info("=== {$label} ===");
        foreach ($warehouseSales as $item) {
            \Log::info("Item {$item->id}:", [
                'total' => $item->total,
                'cur_pay' => $item->cur_pay,
                'remained' => $item->remained,
                'status' => $item->status
            ]);
        }
    }
}

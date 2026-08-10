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
use App\Models\Buy\BoughtItem;
use App\Models\Transaction\Journal;
use App\Models\Warehouse\WarehouseSales;
use App\Models\Warehouse\WarehouseItem;
use App\Models\Warehouse\SalesBillPayment;
use App\Models\Warehouse\SalesDetails;
use App\Models\Order\DraftOrder;

use App\Models\SalesInvoice\SalesInvoice;
use App\Models\SalesInvoice\SalesInvoiceItem;
use App\Models\SalesInvoice\SalesInvoicePayment;

use App\Models\Setting\Account;

use Yajra\DataTables\Facades\DataTables;


class SalesController extends Controller
{
    protected $isAdmin, $userId, $userName, $customerIds, $carIds;
    public function __construct()
    {
        if (auth()->check()) {
            $this->isAdmin = session('isAdmin', auth()->user()->isAdmin == 1);
            $this->userId = session('userId', auth()->user()->id);
            $this->userName = auth()->user()->full_name;
            $this->carIds = session('carIds', []);
            $this->customerIds = session('customerIds', []);
        } else {
            $this->isAdmin = false;
            $this->userId = 0;
            $this->userName='System';
            $this->customerIds = [];
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

            if(!$this->isAdmin){
                $soldItems->whereIn('warehouse_sales.car_id', $this->carIds);
            }

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
                return '<span style="cursor:pointer" class="itemList" data-id="'.$soldItem->billno.'" >'. ' SALES_' . $soldItem->billno. '</span>';
            })
            ->addColumn('total', fn($s) => number_format($s->total, 2))
            ->addColumn('cur_pay', fn($s) => number_format($s->cur_pay, 2))
            ->addColumn('remained', fn($s) => number_format($s->remained, 2))
            // ->addColumn('view', function ($soldItem) {
            //     return '<a href="/sales/details/'.$soldItem->billno.'">
            //         <i class="fas fa-eye viewItems" style="font-size:20px;"></i>
            //     </a>';
            // })
           ->addColumn('action', function ($soldItem) {
            $editLink = $soldItem->has_invoice 
                ? '<span class="dropdown-item disabled text-muted">' . __('common.edit') . '</span>'
                : '<a class="dropdown-item" target="_blank" href="' . route('sales.edit', $soldItem->billno) . '">' . __('common.edit') . '</a>';
            
            return '
                <div class="dropdown detailsDropdown dropend">
                    <button class="btn btn-primary btn-sm dropdown-toggle"
                        type="button"  data-toggle="dropdown">
                    </button>

                    <div class="dropdown-menu">
                        <a class="dropdown-item" target="_blank" href="' . route('sales.bill', $soldItem->billno) . '">' . __('sales.sales_bill') . '</a>
                        <a class="dropdown-item billPayment" href="#" data-id="'.$soldItem->billno.'"
                        data-id2="'.$soldItem->has_invoice.'" data-id3="'.$soldItem->remained.'" >' . 'دریافتی بل' . '</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item itemList" href="#" data-id="'.$soldItem->billno.'">' . __('sales.item_lists') . '</a>
                        ' . $editLink . '
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" target="_blank" href="' . route('sales.details', $soldItem->billno) . '">' . __('common.details') . '</a>
                    </div>
                </div>';
            })
            

            ->rawColumns(['billno','action'])
            ->make(true);
        

    }

    public function bill(string $billno)
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

        // get Bill Payments

        $salesBillPayments = SalesBillPayment::with('account:id,name')->where('billno',$billno)->get();

        // get previous balances
        $customer_balance = $this->getCustomerBalance($customer_account_id, $currency_id,  $times);
        // return ['customer_balance' => $customer_balance];
        
        return view('sales.bill.list',compact('warehouseSales','salesDetails','orgbios','todaysDate','customer_balance',
        'saved_with_tax','salesBillPayments'));
    }

    public function billPayment(string $billno)
    {
        $soldItems = DB::table('warehouse_sales')
            ->join('accounts', 'accounts.id', '=', 'warehouse_sales.customer_account_id')
            ->join('currencies', 'currencies.id', '=', 'warehouse_sales.currency_id')
            ->select(
                'warehouse_sales.id',
                'billno',
                'factor',
                'accounts.id as customer_account_id',
                'accounts.name as customer_name',
                'total',
                'cur_pay',
                'remained',
                'currencies.id as currency_id',
                'currencies.name as currency_name',
                'idate',
                'user_name',
                'warehouse_sales.invoice_id',
                'warehouse_sales.has_invoice'
            )
            ->where('warehouse_sales.billno', $billno)
            ->first();

        if (!$soldItems) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sale not found'
            ], 404);
        }

        // Get own banks (company accounts)
         if($this->isAdmin) {
            $ownBanks = Account::select('id', 'name')->whereIn('account_type_id', [1,7])->get();
        } else {
           $ownBanks = Account::select('id', 'name', 'emp_car_id')
            ->where('account_type_id', 1)
            ->orWhere(function($query) {
                $query->whereIn('emp_car_id', $this->carIds)
                       ->where('account_type_id', 7);
            })
            ->get();
        }

        return view('sales.bill.payment', compact('soldItems', 'ownBanks'));
    }


    // store bill payments
    public function storePayment(Request $request)
    {
        // return response()->json($request->all());

        $validated = $request->validate([
            'billno' => 'required|exists:warehouse_sales,billno',
            'warehouse_sales_id' => 'required|exists:warehouse_sales,id',
            'payment_amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'account_id' => 'required|exists:accounts,id',
            'currency_id' => 'required|exists:currencies,id',
            'note' => 'nullable|string|max:500',
            'current_remained' => 'required|numeric|min:0',
            'customer_account_id' => 'required|exists:accounts,id',
        ]);

        // Check if payment amount exceeds remained
        if ($validated['payment_amount'] > $validated['current_remained']) {
            return response()->json([
                'status' => 'error',
                'message' => __('sales.payment_cannot_exceed_remained')
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Find the sale
            $sale = WarehouseSales::find($validated['warehouse_sales_id']);
            
            if (!$sale) {
                throw new \Exception('Sale not found');
            }

            // Calculate new values
            $newCurPay = $sale->cur_pay + $validated['payment_amount'];
            $newRemained = $sale->remained - $validated['payment_amount'];

            $company_account_type_id = Account::where('id', $request->account_id)->value('account_type_id');
            $customer_account_type_id = Account::where('id', $request->customer_account_id)->value('account_type_id');

            // Generate journal code
            $journal_code =  Journal::lockForUpdate()->max('code') ?? 0;
            $journalCode = $journal_code + 1;
            $date = Carbon::parse($validated['payment_date']);
            $year = $date->year;
            $month = $date->month;
            $day = $date->day;
            $times = time();

             /**
             * خزانه نقد دریافت میکند
             * مشتری نقد پرداخت میکند
             */
            
            // خزانه نقد دریافت میکند
            // Create journal entry for payment
            $journal = new Journal();
            $journal->code = $journalCode;
            $journal->bill_no = $validated['billno'];
            $journal->account_type_id = $company_account_type_id;
            $journal->account_id = $validated['account_id'];
            $journal->amount = $validated['payment_amount'];
            $journal->currency_id = $validated['currency_id'];
            $journal->details =  __('sales.payment_for_bill').' SALES_'.$validated['billno'];  
            $journal->transaction_type = 1; // Recieved
            $journal->payment_type = 1; // Cash
            $journal->status = 8; // Sales
            $journal->idate = $validated['payment_date'];
            $journal->year = $year;
            $journal->month = $month;
            $journal->user_id = $this->userId;
            $journal->user_name = $this->userName ?? '';
            $journal->times = $times;
            $journal->save();

            // Create customer journal entry
            // پرداخت نقد مشتری
            $customerJournal = new Journal();
            $customerJournal->code = $journalCode;
            $customerJournal->bill_no = $validated['billno'];
            $customerJournal->account_type_id = $customer_account_type_id;
            $customerJournal->account_id = $validated['customer_account_id'];
            $customerJournal->amount = $validated['payment_amount'];
            $customerJournal->currency_id = $validated['currency_id'];
            $customerJournal->details = __('sales.recieved_of_bill').' SALES_'.$validated['billno'];
            $customerJournal->transaction_type = 2; // Paid
            $customerJournal->payment_type = 1; // Cash
            $customerJournal->status = 8; // Sales
            $customerJournal->idate = $validated['payment_date'];
            $customerJournal->year = $year;
            $customerJournal->month = $month;
            $customerJournal->user_id = $this->userId;
            $customerJournal->user_name = $this->userName ?? ' ';
            $customerJournal->times = $times;
            $customerJournal->save();

            // =========================================
            // STORE PAYMENT IN SALES_BILL_PAYMENTS TABLE
            // =========================================
            $salePayment = SalesBillPayment::create([
                'warehouse_sales_id' => $sale->id,
                'billno' => $validated['billno'],
                'customer_account_id' => $validated['customer_account_id'],
                'account_id' => $validated['account_id'],
                'currency_id' => $validated['currency_id'],
                'cur_pay' => $validated['payment_amount'],
                'remained' => $newRemained,
                'payment_date' => $validated['payment_date'],
                'note' => $validated['note'] ?? null,
                'journal_code' => $journalCode,
                'user_id' => $this->userId,
                'user_name' => $this->userName ?? 'System',
                'times' => $times,
            ]);

            // Update sale
            $sale->cur_pay = $newCurPay;
            $sale->remained = $newRemained;
            $sale->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => __('common.added_successfully'),
                'data' => [
                    'sale_id' => $sale->id,
                    // 'payment_id' => $salePayment->id,
                    // 'new_remained' => $newRemained,
                    // 'new_cur_pay' => $newCurPay,
                    // 'journal_code' => $journalCode,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Payment Error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => __('common.error_occurred') . ': ' . $e->getMessage()
            ], 500);
        }
    }

    public function return(string $billno)
    {
      
    }
    public function getListOfItemsToShowInModal(string $billno)
    {
        $salesDetails = SalesDetails::with(['preListRelation','unitRelation'])->where('billno',$billno)->get();
        $saved_with_tax = $salesDetails->contains(function($item) {
            return $item->sell_tax_per > 0;
        }) ? true : false;
        return view('sales.item_list_in_modal',compact('salesDetails','saved_with_tax'));
    }

    // SHOW CREATE FORM
    public function create()
    {
        if($this->isAdmin) 
        {
            $ownBanks = Account::select('id', 'name')->whereIn('account_type_id', [1,7])->get();
            $customers = Account::select('id', 'name')->where('account_type_id', 3)->get();
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
                    'warehouse_items.sell_up as sell_up', 
                    // DB::raw("CASE WHEN warehouse_items.buy_tax_per IS NOT NULL AND warehouse_items.buy_tax_per > 0 THEN warehouse_items.sell_up_vat ELSE warehouse_items.sell_up END as sell_up"),
                    DB::raw("CASE WHEN warehouse_items.buy_tax_per IS NOT NULL AND warehouse_items.buy_tax_per > 0 THEN warehouse_items.buy_up_vat ELSE warehouse_items.buy_up END as buy_up"),
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

        } 
        else 
        {
            $customers = Account::select('id', 'name')->where('account_type_id', 3)->whereIn('id', $this->customerIds)->get();
            $cars = Car::select('id', 'name')->whereIn('id', $this->carIds)->get();
             // Get warehouse items with available stock > 0
            $warehouseItems = DB::table('warehouse_items')
                ->join('bought_item_pre_lists', 'bought_item_pre_lists.id', '=', 'warehouse_items.buy_pre_id')
                ->join('units', 'units.id', '=', 'warehouse_items.unit_id')
                ->where('warehouse_items.available_amount', '>', 0)
                ->whereIn('warehouse_items.car_id', $this->carIds)
                ->select(
                    'warehouse_items.id as warehouse_item_id',
                    'warehouse_items.unit_id as warehouse_unit_id',
                    'units.name as warehouse_unit_name',
                    'warehouse_items.sell_up as sell_up', 
                    // DB::raw("CASE WHEN warehouse_items.buy_tax_per IS NOT NULL AND warehouse_items.buy_tax_per > 0 THEN warehouse_items.sell_up_vat ELSE warehouse_items.sell_up END as sell_up"),
                    DB::raw("CASE WHEN warehouse_items.buy_tax_per IS NOT NULL AND warehouse_items.buy_tax_per > 0 THEN warehouse_items.buy_up_vat ELSE warehouse_items.buy_up END as buy_up"),
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
            ->whereIn('draft_orders.customer_id', $this->customerIds)
            ->orderBy('id', 'DESC')
            ->get();

        }
        $currencies = Currency::select('id', 'name')->get();
      
        $ownBanks = Account::select('id', 'name', 'emp_car_id')->where('account_type_id', 1)
            ->orWhere(function($query) {
                $query->whereIn('emp_car_id', $this->carIds)
                       ->where('account_type_id', 7);
            })
            ->get();
        
        $tax = OrgBio::select('tax_activation','tax_per')->first();
        $units = Unit::select('id', 'name')->get();
       
        // Get customer IDs that have orders (state=2)
        $customerIdsWithOrders = $draftOrders->pluck('customer_id')->unique()->toArray();

        // Combine: Match by pre_list_id AND unit_id
        $combinedItems = collect();
        
        foreach ($draftOrders as $order) {
            if($this->isAdmin) {
                $warehouseItem = $warehouseItems->first(function ($item) use ($order) {
                    return $item->pre_list_id == $order->pre_list_id 
                        && $item->warehouse_unit_id == $order->unit_id;
                });
            } else {
                 $warehouseItem = $warehouseItems->first(function ($item) use ($order) {
                    return $item->pre_list_id == $order->pre_list_id 
                        && $item->warehouse_unit_id == $order->unit_id
                        && $item->user_id == $this->userId;
                });
            }
            
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
                    'buy_up' => $warehouseItem->buy_up,
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

        // Sort customers: 
        // 1. First by has_order (true first)
        // 2. Then by has_available_items (true first)
        // 3. Then by item_count (higher first)
        // 4. Then by name alphabetically
        $customersWithStatus = $customersWithStatus->sortByDesc(function ($customer) {
            return [
                $customer->has_order ? 1 : 0,
                $customer->has_available_items ? 1 : 0,
            ];
        })->values();

       $billno = WarehouseSales::max('billno') + 1;
       
      
       
        // return ['data' => $customersWithStatus, 'items' => $combinedItems];
        // return ['warehouseItems' => $warehouseItems];
        if((int)$tax->tax_activation === 1) 
        {
           return view('sales.sales_create.create_with_tax', compact('customers','units','currencies','ownBanks',
            'tax','warehouseItems','combinedItems','customersWithStatus','billno','cars'
           ));
        } else {
           return view('sales.sales_create.create', compact('customers','units','currencies','ownBanks',
            'tax','warehouseItems','combinedItems','customersWithStatus','billno','cars'
           ));
        }
    }
            
    // STORE NEW SALES      
    public function store(Request $request)
    {
        // return response()->json($request->all());

        // billno: "1"
        // car_id: "20"
        // cur_pay: "0"
        // currency_id: "1"
        // customer_account_id: "85"
        // factor: null
        // account_id: "33"
        // items:
        // {
        //     pre_list_id: "90", warehouse_item_id: "7", order_id: "2", amount: "5", unit_id: "18", buy_up: "13",
        //     order_id: "2", profit_amount: "5", sell_tax_per: "6", sell_tax_price: "5.40", sell_up: "18.00",sell_up_vat: "23.40",
        //     total: "90.00", total_vat: "117.00", unit_id: "18", warehouse_item_id: "7"
        // } 
  
        // note: null
        // remained: "10166.00"
        // tax_activation: "1"
        // tax_per: "6"
        // todays_date: "2026-08-01"
        // total_price: "7820.00"
        // total_vat_summary: "10166.00"

       
        $validated = $request->validate([
            'customer_account_id' => 'required|exists:accounts,id',
            'account_id' => 'required|exists:accounts,id',
            'car_id' => 'required|exists:cars,id',
            'todays_date' => 'required',
            // 'billno' => 'required|numeric|unique:warehouse_sales,billno',
            'billno' => 'required|numeric',
            'tax_activation' => 'required|numeric|min:0',
            'tax_per' => 'required|numeric|min:0',
            'factor' => 'nullable|string|max:255',
            'total_price' => 'required|numeric|min:1',
            'cur_pay' => 'required|numeric|min:0',
            'remained' => 'required|numeric|min:0',
            'currency_id' => 'required|exists:currencies,id',
            'note' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.pre_list_id' => 'required|exists:bought_item_pre_lists,id',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.amount' => 'required|numeric|min:0.01',
            'items.*.sell_up' => 'required|numeric|min:0',

            // 'items.*.order_id' => 'required|exists:orders,id',
            'items.*.buy_up' => 'required|numeric|min:0',
            'items.*.profit_amount' => 'required|numeric|min:1',
            'items.*.sell_tax_per' => 'nullable|numeric',
            'items.*.sell_tax_price' => 'nullable|numeric',
            'items.*.sell_up_vat' => 'nullable|numeric',
            'items.*.total' => 'required|numeric|min:0',
            'items.*.total_vat' => 'nullable|numeric',

            'items.*.warehouse_item_id' => 'required|exists:warehouse_items,id',
            'items.*.category_id' => 'nullable|exists:categories,id',
        ]);

        DB::beginTransaction();
        try {
            $date = Carbon::parse($validated['todays_date']);

            /**
             * برای اینکه چند کاربر همزمان ثبت نکند و  بل نمبر یکسان نباشد باید 
             * اینجا چک شود اگر ثبت نبود همان بل نمبر اوکی است و اگر ثبت شده بود بل نمبر جدید بیگیرد
             */
            
             /**
             * FIXED: Use lockForUpdate() to prevent race conditions
             * Multiple users can't get the same billno anymore
             */
            $times = time();
            $billno = null;
            
            // Lock the table to prevent concurrent access
            // Check if billno exists with lock
            $existingBill = WarehouseSales::where('billno', $validated['billno'])
                ->lockForUpdate()
                ->first();

            $journalCode =  Journal::lockForUpdate()->max('code') ?? 0;
            $journal_code = $journalCode + 1;
              

            if ($existingBill) {
                // Get max billno with lock to prevent duplicates
                $maxBill = WarehouseSales::lockForUpdate()->max('billno') ?? 0;
                $billno = $maxBill + 1;
                $times = time();
            } else {
                $billno = $validated['billno'];
            }

            // ADD TO REQUEST: Merge the new values into the request
            $request->merge([
                'billno' => $billno,
                'code' => $journal_code,
                'times' => $times,
            ]);


            // Create WarehouseSales
            $warehouseSale = WarehouseSales::create([
                'billno' => $billno,
                'journal_code' => $journal_code,
                'factor' => $validated['factor'] ?? null,
                'account_id' => $validated['account_id'],
                'customer_account_id' => $validated['customer_account_id'],
                'car_id' => $validated['car_id'],
                'total' => $validated['total_price'],
                'cur_pay' => $validated['cur_pay'],
                'remained' => $validated['remained'],
                'currency_id' => $validated['currency_id'],
                'tax_activation' => $validated['tax_activation'],
                'note' => $validated['note'] ?? null,
                'idate' => $date->format('Y-m-d'),
                'year' => $date->year,
                'month' => $date->month,
                'day' => $date->day,
                'times' => $times,
                'user_id' => $this->userId,
                'user_name' => $this->userName,
                'has_invoice' => 0,
                'invoice_id' => null,
                'is_cleared' => 0,
            ]);

            // update bought_items by last car_id set editable to false, if
            BoughtItem::where('car_id', $validated['car_id'])
                ->where('isEditable', 0)
                ->where('user_id', $this->userId)
                ->orderBy('id', 'DESC')
                ->limit(1)
                ->update(['isEditable' => 1]);

            // Create Sales Details and update warehouse
            foreach ($validated['items'] as $index => $item) {
                // Get warehouse item details for additional fields
                $warehouseItem = WarehouseItem::with(['preListRelation', 'unitRelation'])
                    ->where('id', $item['warehouse_item_id'])
                    ->first();

                if (!$warehouseItem) {
                    throw new \Exception("Warehouse item not found: {$item['warehouse_item_id']}");
                }

                $flag = (bool)$validated['tax_activation'];

                // Calculate profit (sell_up - buy_up)
                $buyUp  = $item['buy_up'] ?? 0;
                $sellUp = $flag ? $item['sell_up_vat'] : $item['sell_up'];
                $profit = $item['amount'] * $item['profit_amount'];

                // Determine tax values
                $sellTaxPer = $item['sell_tax_per'] ?? 0;
                $sellTaxPrice = $item['sell_tax_price'] ?? 0;
                $sellUpNoTax = $item['sell_up'] ?? 0;
                $total = $flag ? $item['total_vat'] : $item['total'];

                // Create sales detail with all fields
                SalesDetails::create([
                    'billno' => $billno,
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
                    'expected_profit' => $item['profit_amount'],
                    'total' => $total,
                    'is_returned' => 0,
                    'todays_date' => $date->format('Y-m-d'),
                ]);

                // Decrease warehouse items with proper tracking
                $this->decreaseWarehouseItemAfterStore($warehouseItem, $item['amount']);
            }

            // Update draft order state to completed (3) for this customer
            if (!empty($validated['customer_account_id'])) {
                if($this->isAdmin) {
                     DraftOrder::where('customer_id', $validated['customer_account_id'])
                    ->where('state', 2)
                    ->update(['state' => 3]);
                } else {
                     DraftOrder::where('customer_id', $validated['customer_account_id'])
                    ->where('state', 2)
                    ->where('iby', $this->userId)
                    ->update(['state' => 3]);
                }
            }

            // Handle journal entry
            $this->handleJournalEntry($request);

    
            // ===========================================
            // STORE PAYMENT IN SALES_BILL_PAYMENTS TABLE
            // ===========================================
            if($validated['cur_pay'] > 0 && $validated['cur_pay'] <= $validated['total_price']) 
            {
                $salePayment = SalesBillPayment::create([
                    'warehouse_sales_id' => $warehouseSale->id,
                    'billno' => $billno,
                    'customer_account_id' =>  $validated['customer_account_id'],
                    'account_id' => $validated['account_id'],
                    'currency_id' => $validated['currency_id'],
                    'cur_pay' => $validated['cur_pay'],
                    'remained' =>  $validated['remained'],
                    'payment_date' => $date->format('Y-m-d'),
                    'note' => 'پرداخت نقد فروش',
                    'journal_code' => $journal_code,
                    'user_id' => $this->userId,
                    'user_name' => $this->userName,
                    'times' => $times,
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => __('common.added_successfully'),
                'data' => [
                    'sale_id' => $warehouseSale->id,
                    'billno'  => $warehouseSale->billno,
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
            'account_id' => 'required|integer|exists:accounts,id',
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
        
            'account_id.required' => __('validate.account_id_required'),
            'account_id.integer' => __('validate.account_id_integer'),
            'account_id.exists' => __('validate.account_id_exists'),
        
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
            $user_name = $this->userName ?? '';
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
                'account_id' => $request->account_id,
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
     * Decrease warehouse item with proper tracking and taxes
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
        // \Log::info('Request Data:', $request->all());

        $short_date = $request->todays_date ?? Carbon::now()->format('Y-m-d');
        $date = Carbon::parse($short_date);
        $day = $date->day;
        $year = $date->year;
        $month = $date->month;
        $time = $request->times ?? '00:00:00';

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

            $companyAccount = Account::select('id','account_type_id')
                    ->where('account_type_id',1)
                    ->where('is_pre_select',1)
                    ->first();

            $khazana_account_id = $companyAccount->id ?? $request->account_id;
            $counted = 0;
            // if((int)$khazana_account_id === (int)$request->from_account_id) 
            // {
            //     // $payer_account_id = $request->from_account_id;
            //     // $counted = 0;
            // } 
            // else 
            // {
            //     // $payer_account_id = $khazana_account_id;
            //     // $counted = 1; // disable count of this transation in chart_of_account for khazana
            // }

            if(floatval($request->cur_pay) == 0 && floatval($request->remained) == floatval($request->total_price))
            { 
                // ثبت طلب خزانه = paid(ttype=2), loan(ptype=2) 
                $details =   __('validate.sales_talab_bill').' SALES_'.$request->billno;
                $this->createJournalEntry($request, $khazana_account_id,  $request->total_price, $ttype = "2", $ptype="2", $date,  $details);
                
                // ثبت قرضه مشتری = recieved(ttype=1) loan(ptype=2)
                $details = __('validate.sales_loan_bill').' SALES_'.$request->billno;
                $this->createJournalEntry($request, $request->customer_account_id,  $request->total_price, 
                $ttype = "1", $ptype="2", $date,  $details);
            }

            // کمی شانرا پرداخت کرده و متباقی شانرا قرض انتخاب کرده است
            else if(floatval($request->remained) > 0 && floatval($request->cur_pay) > 0) 
            {
                // ثبت دریافت نقدی توسط خزانه / موتر = Cache Recieved = t1p1
                $details =  __('validate.sales_recieve_bill').' SALES_'.$request->billno;
                $this->createJournalEntry($request,  $request->account_id, $request->cur_pay, $ttype = "1", $ptype="1", $date,  $details);

                // ثبت قرضه مشتری = Loan Recieved = p2t1
                $details =  __('validate.sales_loan_bill').' SALES_'.$request->billno;
                $this->createJournalEntry($request,  $request->customer_account_id, $request->remained,  
                $ttype = "1", $ptype="2", $date,  $details);
            
                // ثبت طلب خزانه = Paid Loan = t2p2
                $details =  __('validate.sales_talab_bill').' SALES_'.$request->billno;
                $this->createJournalEntry($request,   $khazana_account_id, $request->remained,
                $ttype = "2", $ptype="2", $date,  $details);
            }

            // قرضدار نمانده است و مکمل پرداخت کرده است
            // تنها در حساب خزانه اضافه شود
            else if(floatval($request->remained) == 0 && floatval($request->cur_pay) == floatval($request->total_price)) 
            {
                // ثبت دریافت نقدی خزانه = Cache Recieved = t1p1
                $details =  __('validate.sales_recieve_bill').' SALES_'.$request->billno;
                $this->createJournalEntry($request,  $request->account_id, $request->cur_pay,
                $ttype = "1", $ptype="1", $date,  $details);
            }
        
            return true; 

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Error storing journal entry in SalesController', ['error' => $e->getMessage()]);
            throw $e; // Rethrow to be caught in store()
            return false;
        }
    }

    private function createJournalEntry($request, $account_id, $amount, $ttype, $ptype, $date, $details)
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
                'car_id' => $request->car_id ?? 0,
                'transaction_type' => $ttype,
                'payment_type' => $ptype,
                'user_id' => $this->userId ?? '',
                'user_name' => $this->userName ?? '',
                'year' => $date->year,
                'month' => $date->month,
                'idate' => $request->todays_date,
                'details' => $details,
                'status' => 8,
                'times' => $request->times,
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
                            THEN journals.amount ELSE 0 END) as cache_recieved"),
                DB::raw("SUM(CASE 
                            WHEN journals.transaction_type = 2 
                            AND journals.payment_type = 1 
                            THEN journals.amount ELSE 0 END) as cache_paid"),
                DB::raw("SUM(CASE 
                            WHEN journals.transaction_type = 1 
                            AND journals.payment_type = 2 
                            THEN journals.amount ELSE 0 END) as loan_recieved"),
                DB::raw("SUM(CASE 
                            WHEN journals.transaction_type = 2 
                            AND journals.payment_type = 2 
                            THEN journals.amount ELSE 0 END) as loan_paid"),
            ])
            ->where('currency_id', $currency_id)
            ->where('account_id', $customer_account_id)
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

        if($this->isAdmin) {
            $customers = Account::select('id','name')->whereIn('account_type_id',[3,4])->get();
        } else {
            $customers = Account::select('id','name')->whereIn('id',$this->customerIds)->get();
        }
        if($this->isAdmin) {
            $ownBanks = Account::select('id', 'name')->whereIn('account_type_id', [1,7])->get();
        } else {
           $ownBanks = Account::select('id', 'name', 'emp_car_id')
            ->where('account_type_id', 1)
            ->orWhere(function($query) {
                $query->whereIn('emp_car_id', $this->carIds)
                       ->where('account_type_id', 7);
            })
            ->get();
        }

        $currencies = Currency::select('id','name')->get();
        // return response()->json(['warehouseSales' => $warehouseSales,'salesDetails'=> $salesDetails]);
        return view('sales.edit',compact('warehouseSales','salesDetails','orgbios','todaysDate','customers','ownBanks','currencies','billno','saved_with_tax'));
    }

    /**
     * 
     */
    public function getSingleRecordForEdit(string $id)
    {
        // $units = Unit::select('id','name')->get();
        $salesDetails = SalesDetails::with(['preListRelation','unitRelation'])->where('id', $id)->first();

        if (!$salesDetails) {
            return response()->json(['error' => 'Sales Details not found'], 404);
        }

        $saved_with_tax = $salesDetails->sell_tax_per > 0 ? true : false;
        
        $warehouse_id = $salesDetails->warehouse_id ?? 0;
        $pre_list_id = $salesDetails->pre_list_id ?? 0;
        $unit_id = $salesDetails->unit_id ?? 0;


         if($this->isAdmin) {
                $warehouseAmount = WarehouseItem::select('available_amount','buy_tax_per')->where('warehouse_id', $warehouse_id)
                    ->where('buy_pre_id', $pre_list_id)
                    ->where('unit_id', $unit_id)
                    // ->where('available_amount', '>', 0)
                    ->first();
            } else {
                $warehouseAmount = WarehouseItem::select('available_amount','buy_tax_per')->where('warehouse_id', $warehouse_id)
                    ->where('buy_pre_id', $pre_list_id)
                    ->where('unit_id', $unit_id)
                    ->whereIn('car_id', $this->carIds)
                    // ->where('available_amount', '>', 0)
                    ->first();
            }
        
        //  return response()->json(['boughtItemDetails' => $boughtItemDetails]);
        // return response()->json(['boughtItemDetails' => $boughtItemDetails, 'warehouseItems' => $warehouseItems]);
        if($saved_with_tax) {
            return view('sales.editModalContentWithTax', compact('salesDetails','warehouseAmount','saved_with_tax'));
        } else {
            return view('sales.editModalContent', compact('salesDetails','warehouseAmount','saved_with_tax'));
        }
    }

    // update items one by one in edit page of sales
    public function updateSalesAndWarehouseItems(Request $request)
    {
        // return response()->json($request->all());

        $flag=false;
        if((int)$request->saved_with_tax === 1) {
         $flag = true;

            $validated = $request->validate([
                'id'                => 'required|exists:sales_details,id',
                'pre_list_id'       => 'required|exists:bought_item_pre_lists,id',
                'warehouse_id'      => 'required|exists:warehouses,id',
                'amount'            => 'required|numeric|min:1',
                'old_amount'        => 'required|numeric|min:1',
                'billno'            => 'required|numeric|min:1',
                'unit_id'           => 'required|exists:units,id',
                'sell_up'           => 'required|numeric|min:1',
                "max_available_amount"  => 'required|numeric|min:1',
                "sell_tax_per"     =>  'required|numeric|min:0',
                "saved_with_tax"  => 'required|numeric|min:1',
                "sell_tax_per"    => 'required|numeric|min:1',
                "sell_tax_price"  => 'required|numeric|min:1',
                "sell_up_no_tax"  => 'required|numeric|min:1',
                "sell_up"         => 'required|numeric|min:1',
                "total"           => 'required|numeric|min:1',
            ]);

        }
        else 
        {
            $flag = false;
             $validated = $request->validate([
                'id'                => 'required|exists:sales_details,id',
                'pre_list_id'       => 'required|exists:bought_item_pre_lists,id',
                'warehouse_id'      => 'required|exists:warehouses,id',
                'amount'            => 'required|numeric|min:1',
                'old_amount'        => 'required|numeric|min:1',
                'billno'            => 'required|numeric|min:1',
                'unit_id'           => 'required|exists:units,id',
                'sell_up'           => 'required|numeric|min:1',
                "max_available_amount"  => 'required|numeric|min:1',
                "total"           => 'required|numeric|min:1',
            ]);
        }

        DB::beginTransaction();
        try 
        {
            $salesDetails = SalesDetails::where('id', $validated['id'])
                ->lockForUpdate()
                ->firstOrFail();

             if(!$salesDetails) {
                throw new \Exception('جنس یافت نشد');
            }

            $new_total = (float)$validated['total'];
            $profit = $new_total - ($validated['amount'] * $salesDetails->buy_up);

            if($flag)
            {
                $salesDetails->update([
                    'amount'   => $validated['amount'],
                    'sell_up'  => $validated['sell_up'],
                    'sell_up_no_tax'  => $validated['sell_up_no_tax'],
                    'sell_tax_per'    => $validated['sell_tax_per'],
                    'sell_tax_price'  => $validated['sell_tax_price'],
                    'profit'   => $profit,
                    // 'unit_id'  => $validated['unit_id'],
                    'total'    => $new_total,
                ]);
            } 
            else 
            {
                $salesDetails->update([
                    'amount'   => $validated['amount'],
                    'sell_up'  => $validated['sell_up'],
                    'profit'   => $profit,
                    // 'unit_id'  => $validated['unit_id'],
                    'total'    => $new_total,
                ]);
            }

            $salesDetails->refresh();

            // Find Warehouse Item
            if($this->isAdmin) 
            {
                $warehouseItem = WarehouseItem::where('warehouse_id', $validated['warehouse_id'])
                        ->where('buy_pre_id', $validated['pre_list_id'])
                        ->where('unit_id', $validated['unit_id'])
                        // ->where('available_amount', '>', 0) 
                        // ->orderBy('id', 'DESC') 
                        ->lockForUpdate()
                        ->first();
            } 
            else 
            {
                $warehouseItem = WarehouseItem::where('warehouse_id', $validated['warehouse_id'])
                        ->where('buy_pre_id', $validated['pre_list_id'])
                        ->where('unit_id', $validated['unit_id'])
                        ->where('user_id', $this->userId)
                        // ->where('available_amount', '>', 0)
                        // ->orderBy('id', 'DESC') 
                        ->lockForUpdate()
                        ->first();
            }
            

                // Calculate the difference
                $oldAmount = (float) $validated['old_amount'];
                $newAmount = (float) $validated['amount'];
                $diff = abs($newAmount - $oldAmount);
                
                if (!$warehouseItem) {
                    throw new \Exception('این جنس در گدام یافت نشد');
                }
                

                // Update warehouse quantities based on difference
                // اگر مقدار کمتر شود باید به همان مقدار از 
                // out_amount کم شود و available_amount نیز کم شود
                // مثلا قبلا ۴ دانه فروخته بودیم و حالا ۲ ساختیم

                if ($oldAmount > $newAmount) 
                {
                    // Amount decreased - return items to warehouse
                    $warehouseItem->available_amount += $diff;
                    // $warehouseItem->out_amount -= $diff;
                    
                    //  Check: out_amount should not go negative
                    if ($warehouseItem->out_amount >= $diff) {
                        $warehouseItem->out_amount -= $diff;
                    } else {
                        // If out_amount is less than diff, set it to 0
                        $warehouseItem->out_amount = 0;
                    }

                // اگر مقدار زیادتر شود باید از مقدار موجود کم شود و 
                // مثلا: دو دانه فروخته بودیم حالا چهار دانه ویرایش میکنم این دو دانه 
            } 
            elseif ($newAmount > $oldAmount) 
            {  
                // Amount increased - take more items from warehouse
                // if ($warehouseItem->available_amount < $diff) {
                //     throw new \Exception('جنس به این تعداد موجود نیست');
                //     return;
                // }

                $warehouseItem->available_amount -= $diff;  // مقدار جدید از گدام کم شود
                $warehouseItem->out_amount += $diff;   // مقدار جدید بیشتر فروش شده
            }

            // Determine the valuation price (with or without tax)
            $valuationPrice = $warehouseItem->buy_up ?? 0;
            if (intval($warehouseItem->buy_tax_per ?? 0) > 0) {
                $valuationPrice = $warehouseItem->buy_up_vat ?? $warehouseItem->buy_up ?? 0;
            }

            // Calculate available_total = available_amount × valuation_price
            $warehouseItem->available_total = round($warehouseItem->available_amount * $valuationPrice, 2);


            //  Ensure out_amount is never negative (safety check)
            if ($warehouseItem->out_amount < 0) {
                $warehouseItem->out_amount = 0;
            }

            //  Ensure available_amount is never negative (safety check)
            if ($warehouseItem->available_amount < 0) {
                $warehouseItem->available_amount = 0;
                $warehouseItem->available_total = 0;
            }
                

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
        // return response()->json($request->all());
        // Validate request
        $validated = $request->validate([
            'billno'              => 'required|integer|min:1',
            'customer_account_id' => 'required|exists:accounts,id',
            'account_id'          => 'required|exists:accounts,id',
            'currency_id'         => 'required|exists:currencies,id',
            'total_price'         => 'required|numeric|min:0',
            'cur_pay'             => 'required|numeric|min:0',
            'remained'            => 'required|numeric|min:0',
            'note'                => 'nullable|string|max:500',
            'factor'              => 'nullable',
        ]);
    
        DB::beginTransaction();
    
        try {
            // Find the warehouse sale record
            if($this->isAdmin) {
                $warehouseSales = WarehouseSales::where('billno', $validated['billno'])->firstOrFail();
            } else {
                $warehouseSales = WarehouseSales::where('billno', $validated['billno'])->where('user_id', $this->userId)->firstOrFail();
            }

            if(!$warehouseSales) {
                throw new \Exception('اجناس یافت نشد');
            }

            $journal_code = $warehouseSales->journal_code ?? 0;
        //    if(!$this->isAdmin) {
        //         if (!$warehouseSales) {
        //                 throw new \Exception('شما نمی توانید معلومات دیگران را ویرایش نمایید');
        //         }
        //    }

            

            // Retrieve old journal records
            $oldJournals = Journal::where('times', $request->times)
            ->where('user_id', $this->userId)
            ->where('status', 8)->get();
    
            if ($oldJournals->isNotEmpty()) 
            {

               
               if($oldJournals->first()->code > 1) {  //  همیشه ریکارد هایکه کد نگیرد همین کد را ثبت میکند
                   $journal_code = $oldJournals->first()->code; 
                } 
               else 
                {
                   $journal_code = Journal::max('code') + 1;
                }

                // Clone request to avoid modifying original data
                $clonedRequest = clone $request;
                $clonedRequest->merge([
                        'code' => $journal_code, 
                    ]);

                 // Update warehouse sale details
                $warehouseSales->update([
                    'total'          => $validated['total_price'],
                    'cur_pay'        => $validated['cur_pay'],
                    'remained'       => $validated['remained'],
                    'note'           => $validated['note'],
                    'factor'         => $validated['factor'],
                    'journal_code'   => $journal_code,
                ]);
    
                // Delete all journal records in a single query
                Journal::where('times', $request->times)->where('user_id', $this->userId)->where('status', 8)->delete();
    
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
            else // بنابر دلایل ریکارد ژورنال قبلا ایجاد نشده بوده حالا باید ایجاد شود
            {
                // $clonedRequest = clone $request;
                // $new_journal_code = Journal::max('code') + 1;
                // $clonedRequest->merge([
                //     'code' => $new_journal_code, 
                // ]);
                // $checkJournal = $this->handleJournalEntry($clonedRequest);

                // $warehouseSales->update([
                //     'journal_code'   => $new_journal_code,
                // ]);
            }
            
            $salePayment = SalesBillPayment::where('billno', $validated['billno'])
                ->where('times', $warehouseSales->times)
                ->update([
                    'cur_pay' => $validated['cur_pay'],
                    'remained' => $validated['remained'],
                ]);

    
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
           // Find the sales detail or fail with a clear message
            $salesDetail = SalesDetails::findOrFail($id);

            // Get the bill number
            $salesBillNo = $salesDetail->billno ?? 0;

            // Validate bill number exists
            if (empty($salesBillNo)) {
                throw new \Exception('بل نمبر در این فروش یافت نشد');
            }

            // Check if any payments exist for this bill
            $hasPayments = SalesBillPayment::where('billno', $salesBillNo)->exists();

            if ($hasPayments) {
                throw new \Exception('این فاکتور دارای پرداخت می باشد و قابل حذف نیست');
            }

            // Find Warehouse Item
            $warehouseItem = WarehouseItem::where('warehouse_id', $salesDetail->warehouse_id)
                                        ->where('buy_pre_id', $salesDetail->pre_list_id)
                                        ->where('unit_id', $salesDetail->unit_id)
                                        ->where('user_id', $this->userId)
                                        ->first();

            if (!$warehouseItem) {
                throw new \Exception('Warehouse item not found.');
            }

            // Update warehouse item
            // در صورت حذف باید از رفت همان تعداد کم شود و در موجود اضافه شود
            $warehouseItem->available_amount += $salesDetail->amount;
            $warehouseItem->out_amount -= $salesDetail->amount; 
            // $warehouseItem->available_total = (($warehouseItem->available_amount + $salesDetail->amount) * $warehouseItem->buy_up);
            $warehouseItem->available_total = round($warehouseItem->available_amount * $salesDetail->buy_up, 2);
            $warehouseItem->save();

            // Delete salesDetail **after** updating warehouse item
            $salesDetail->delete();

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
            $warehouse_sales = WarehouseSales::where('times', $times)->where('user_id', $this->userId)
                
                ->first();

            if ($warehouse_sales) {
                SalesDetails::where('warehouse_sales_id', $warehouse_sales->id)
                    
                    ->delete();

                $warehouse_sales->delete();
            }

            Journal::where('times', $times)
                ->where('user_id', $this->userId)
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
     * ایجاد انوایس که تمام آیتم هارا نیز ذخیره میکند
     */
    public function generateInvoiceV1(Request $request)
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

            // Extract bill numbers as an array
            $billNumbers = $warehouseSales->pluck('billno')->toArray();
            
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
                'sales_bill_numbers' => json_encode($billNumbers), // Store as JSON
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
                'created_by' => $this->userId,
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
                        'billno' => $detail->billno,
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
     * Generate invoice from selected bought items
     * ایجاد انوایس که تمام بل هارا نیز ذخیره میکند
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

            // Extract bill numbers as an array
            $billNumbers = $warehouseSales->pluck('billno')->toArray();
            
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
            $times = time();
            $invoice_date = now()->format('Y-m-d');

            // Create invoice
            $invoice = SalesInvoice::create([
                'invoice_number' => $invoiceNumber,
                'sales_bill_numbers' => json_encode($billNumbers), // Store as JSON
                'customer_id' => $customerId,
                'total' => $totalAmount,
                'paid_amount' => $paidAmount,
                'remaining' => $remainingAmount,
                'currency_id' => $warehouseSales->first()->currency_id,
                'status' =>   1, // 0: draft, 1: in progress, 2: partial, 3: paid
                'tax_activation' => $warehouseSales->first()->tax_activation ?? 0,  
                'invoice_date' => $invoice_date,
                'due_date' => now()->addDays(30),
                'notes' => __('buy.invoice_generated_from_bought_items'),
                'created_by' => $this->userId,
                'times' => $times,
            ]);

            // Create invoice based on bill
            foreach ($billNumbers as $billno) 
            {
                // Get details for this Sales item from sales details
                $details = WarehouseSales::where('billno', $billno)->first();
                SalesInvoiceItem::create([
                    'invoice_id'    => $invoice->id,
                    'billno'        => $billno,
                    'total'         => $details->total,
                    'cur_pay'       => $details->cur_pay,
                    'remained'      => $details->remained,
                    'times'         => $times,
                    'invoice_date'  => $invoice_date,
                    'user_name'     => $this->userName,
                ]);
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
        $invoice = SalesInvoice::with(['customer:id,name','payments.account:id,name'])
            ->findOrFail($id);
        $invoiceItems = SalesInvoiceItem::select('id','billno','total','cur_pay','remained','invoice_date','user_name','created_at')
            ->where('invoice_id', $id)->get();
        $customers = Account::select('id','name')->whereIn('account_type_id',[3])->get();
         if($this->isAdmin) {
            $ownBanks = Account::select('id', 'name')->whereIn('account_type_id', [1,7])->get();
        } else {
           $ownBanks = Account::select('id', 'name', 'emp_car_id')
            ->where('account_type_id', 1)
            ->orWhere(function($query) {
                $query->whereIn('emp_car_id', $this->carIds)
                       ->where('account_type_id', 7);
            })
            ->get();
        }
        $newJournalCode =  Journal::max('code') + 1;
        $currencies = Currency::select('id','name')->get();
        // return ['invoice' => $invoice];

        return view('sales.invoice.invoice_details', compact('invoice','orgbios','customers','ownBanks','newJournalCode','times','currencies'));
    }

    // Add Invoice Payments
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

            // check journal code, if exists, create new journal code
            $journalCode = $validated['code'];
            $checkJournalCode = Journal::where('code', $validated['code'])->lockForUpdate()->first();
            $times = $validated['times'];
            if($checkJournalCode) 
            {
                $times = time();
                $journalCode = Journal::lockForUpdate()->max('code') + 1;
                $request->merge([
                    'code' => $journalCode,
                    'times' => $times,
                ]);
            } 

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
                'journal_code' => $journalCode,
                'created_by' => $this->userId,
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
            $dt_comment = 'Invoice Id';
            

            // خزانه یا موتر دریافت کننده میباشد 
            $check1 = $this->createJournalEntry($request,  $request->account_id,  $amount, "1", "1", $date, $details);


            $details2 = __('validate.cache_recieved_invoice') . 'SINV_' . $invoice_id;    
            // ثبت پرداخت نقد توسط مشتری
            $check2 = $this->createJournalEntry($request,   $request->customer_account_id, $amount,"2", "1", $date, $details2);

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

    public function addPaymentBkp(Request $request)
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
                'created_by' => $this->userId,
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
            
            $check1 = $this->createJournalEntry($request,  $request->account_id, $amount, 
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
                $request,  $request->customer_account_id, $amount, 
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

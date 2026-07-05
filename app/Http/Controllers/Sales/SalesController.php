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
use App\Models\Setting\Unit;
use App\Models\Transaction\Journal;
use App\Models\Warehouse\WarehouseSales;
use App\Models\Warehouse\WarehouseItem;
use App\Models\Warehouse\SalesDetails;

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
            ->select('warehouse_sales.id','billno','factor','accounts.name as customer_name','total','cur_pay','is_cleared','remained','currencies.name as currency_name','idate','user_name')
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
    public function create()
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

    /**
     * Store a newly created resource in storage.
    */
    public function store(Request $request)
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
        }
    }

    private function createJournalEntry($request, $optionLabel, $account_id, $amount, $ttype, $ptype, $date, $full_date, $details, $dynamic_type, $dt_comment)
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
}

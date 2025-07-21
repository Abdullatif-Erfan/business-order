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
use App\Models\Setting\Branch;
use Morilog\Jalali\Jalalian;
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
    protected $branch_id, $isAdmin;
    public function __construct()
    {
        if (auth()->check()) {
            $this->branch_id = session('branch_id', auth()->user()->branch_id ?? 0);
            $this->isAdmin = session('isAdmin', auth()->user()->isAdmin == 1);
        } else {
            $this->branch_id = 0;
            $this->isAdmin = false;
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $currencies = Currency::all();
        $todaysDate = Jalalian::now()->format('Y-m-d');
        $orgbios = OrgBio::all();
        $branchs = Branch::where('id', $this->branch_id)->get();
        return view('sales.list',compact('currencies','todaysDate','orgbios','branchs'));
    }

    public function getData(Request $request)
    {
            $soldItems = DB::table('warehouse_sales')
            ->join('accounts', 'accounts.id', '=', 'warehouse_sales.customer_account_id')
            ->join('currencies', 'currencies.id', '=', 'warehouse_sales.currency_id')
            ->select('warehouse_sales.id','billno','factor','warehouse_sales.branch_id','accounts.name as customer_name','total_price','total_discount','payable','cur_pay','is_cleared','remained','currencies.name as currency_name','short_date','iby')
            ->where('warehouse_sales.branch_id', $this->branch_id)
            ->orderBy('warehouse_sales.id','DESC');
            

            // Apply filters if provided
              if ($request->customer_name) {
                 $soldItems->where('accounts.name', 'LIKE', "%{$request->customer_name}%");
            }
            
            if ($request->currency_id) {
                $soldItems->where('currency_id', $request->currency_id);
            }
            
            if ($request->start_date && $request->end_date) {
                $soldItems->whereBetween('short_date', [$request->start_date, $request->end_date]);
            } elseif ($request->start_date) {
                $soldItems->whereDate('short_date', '=', $request->start_date);
            } elseif ($request->end_date) {
                $soldItems->whereDate('short_date', '>=', $request->end_date); // Until today
            }
            
            if ($request->bill_number) {
                $soldItems->where('billno', $request->bill_number);
            }
            
            return DataTables::of($soldItems->get())
            
            ->addIndexColumn()
           
            ->addColumn('billno', function($soldItem) {
                $checkIcon = $soldItem->is_cleared == 1 ? '<i class="fas fa-check-circle success"></i>' : '';
                return $soldItem->billno ? $checkIcon.' '.'SALES_'.$soldItem->billno: 0;
            })

            ->addColumn('total_price', function ($soldItem) {
                $total_price = $soldItem->total_price;
                return (fmod($total_price, 1) == 0) ? number_format($total_price, 0) : number_format($total_price, 2);
            })

            ->addColumn('total_discount', function ($soldItem) {
                $total_discount = $soldItem->total_discount;
                return (fmod($total_discount, 1) == 0) ? number_format($total_discount, 0) : number_format($total_discount, 2);
            })

            ->addColumn('payable', function ($soldItem) {
                $payable = $soldItem->payable;
                return (fmod($payable, 1) == 0) ? number_format($payable, 0) : number_format($payable, 2);
            })

            ->addColumn('cur_pay', function ($soldItem) {
                $cur_pay = $soldItem->cur_pay;
                return (fmod($cur_pay, 1) == 0) ? number_format($cur_pay, 0) : number_format($cur_pay, 2);
            })

            ->addColumn('remained', function ($soldItem) {
                $remained = $soldItem->remained;
                return (fmod($remained, 1) == 0) ? number_format($remained, 0) : number_format($remained, 2);
            })
            
        
            ->addColumn('view', function ($soldItem) {
                return '<a href="/sales/details/'.$soldItem->billno.'" class="hidden-print"><i class="fas fa-eye viewItems" 
                data-id="' . $soldItem->id . '" style="font-size:20px;"></i></a>';
            })

            ->rawColumns(['billno','view'])
            ->make(true);

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
                        ->select('warehouse_items.id','bought_item_pre_lists.code','warehouse_items.unit_id','avg_up','sell_up', 'warehouse_items.available_amount', 'units.name as unit_name','warehouses.id as warehouse_id', 'warehouses.name as warehouse_name', 'bought_item_pre_lists.name as item_name','bought_item_pre_lists.branch_id','bought_item_pre_lists.id as pre_list_id')
                        ->where('warehouse_items.branch_id', $this->branch_id)
                        ->get();

        $customers = Account::select('id','name')->whereIn('account_type_id',[3,4])->where('branch_id', $this->branch_id)->get();
        $ownBanks = Account::select('id','name')->whereIn('account_type_id',[1,6])->where('branch_id', $this->branch_id)->orderBy('is_pre_select','DESC')->get();

        $currencies = Currency::all();
        $billno =  WarehouseSales::where('branch_id', $this->branch_id)->max('billno') + 1;
        $journal_code = Journal::where('branch_id', $this->branch_id)->max('code') + 1;
        $times = time();
        

        // return response()->json(['data' => $warehouseItems]);
        return view('sales.create.form',compact('todaysDate','warehouseItems','customers','ownBanks','billno','currencies','journal_code','times'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // return response()->json(['data' => $request->all()]);
        
        // Validate the request and return errors if validation fails
        $validator = Validator::make($request->all(), $this->validationRules(), $this->validationMessages());

        if ($validator->fails()) 
        {
            return redirect()->route('sales.create')
                ->withErrors($validator)
                ->withInput(); // Preserve old input
        }

        // Start the transaction
        DB::beginTransaction();

        try 
        {
            
            // create warehouse_sales
            $warehouseSalesId = $this->createWarehouseSales($request);
            
            // create sales_details
            $salesDetails = $this->createSalesDetails($request, $warehouseSalesId);

            // decrease from warehouse_items
            $checkWarehouseItems = $this->decreaseWarehouseItemFromSoldAmount($request);

            $checkJournal =  $this->handleJournalEntry($request);
            if(!$checkJournal || !$salesDetails || !$warehouseSalesId || !$checkWarehouseItems)
            {
                DB::rollBack();
                Session::put('notification', [
                    'message' =>  __('common.add_failed'),
                    'type' => 'danger',
                ]);
                return redirect()->route('sales.create');
            }

            // Flash error message
            DB::commit();
            Session::put('notification', [
                'message' =>  __('common.added_successfully'),
                'type' => 'success',
            ]);
             return redirect()->route('sales.create');
 
 
         } catch (\Exception $e) {
             // Rollback the transaction if an error occurs
             DB::rollBack();
             // Optionally, log the error for debugging
             \Log::error('Error storing SalesController', ['error' => $e]);

            // Flash error message
            Session::put('notification', [
                'message' =>  __('common.add_failed'),
                'type' => 'danger',
            ]);
             return redirect()->route('sales.create');
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
            'todays_date' => 'required|date_format:Y-m-d',
            'billno' => 'required|integer',
            'factor' => 'nullable|string',
            'warehouse_id'  => 'required',
            'warehouseItemId' => 'required|array',
            'warehouseItemId.*' => 'required|integer|exists:warehouse_items,id',
            'amount' => 'required|array',
            'amount.*' => 'required|numeric|min:1',
            'unit_id' => 'required|array',
            'unit_id.*' => 'required|integer|exists:units,id',
            'unit_name' => 'required|array',
            'unit_name.*' => 'required|string|max:255',
            'avg_up' => 'required|array',
            'avg_up.*' => 'nullable|numeric|min:0',
            'sell_up' => 'required|array',
            'sell_up.*' => 'nullable|numeric|min:0',
            'discount' => 'required|array',
            'discount.*' => 'nullable|numeric|min:0',
            'profit' => 'required|array',
            'profit.*' => 'nullable|numeric',
            'total' => 'required|array',
            'total.*' => 'nullable|numeric|min:0',
            'total_price' => 'required|numeric|min:0',
            'general_discount' => 'nullable|numeric|min:0',
            'payable' => 'required|numeric|min:0',
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
        
            'avg_up.array' => __('validate.avg_up_array'),
            'avg_up.*.numeric' => __('validate.avg_up_*_numeric'),
            'avg_up.*.min' => __('validate.avg_up_*_min'),
        
            'sell_up.array' => __('validate.sell_up_array'),
            'sell_up.*.numeric' => __('validate.sell_up_*_numeric'),
            'sell_up.*.min' => __('validate.sell_up_*_min'),
        
            'discount.array' => __('validate.discount_array'),
            'discount.*.numeric' => __('validate.discount_*_numeric'),
            'discount.*.min' => __('validate.discount_*_min'),
        
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
        
            'payable.required' => __('validate.payable_required'),
            'payable.numeric' => __('validate.payable_numeric'),
            'payable.min' => __('validate.payable_min'),
        
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
    *  Create Warehouse Sales
    */
    private function createWarehouseSales($request)
    {
        
        try {
            // Prepare date and other fields
            $full_date = $request->todays_date . ' ' . now()->format('H:i:s A');
            $insertedBy = auth()->user()->full_name ?? '';
            $short_date = $request->todays_date ?? Jalalian::now()->format('Y-m-d');
            [$year, $month, $day] = explode('-', $short_date);
    
            // // Ensure branch_id is an array before accessing the first element
            $branch_id = is_array($request->branch_id) ? $request->branch_id[0] : $request->branch_id;
            // \Log::info('Start inserting into warehouse sales', ['request' => $request->all()]);
    
           
            // Insert the new warehouse sale record
            $warehouseSales = WarehouseSales::create([
                'billno' => $request->billno, 
                'factor' => $request->factor, 
                'account_id' => $request->from_account_id, 
                'branch_id' => $this->branch_id ?? $branch_id, 
                'customer_account_id' => $request->customer_account_id, 
                'total_price' => $request->total_price, 
                'total_discount' => $request->total_discount, 
                'payable' => $request->payable, 
                'cur_pay' => $request->cur_pay,
                'remained' => $request->remained, 
                'currency_id' => $request->currency_id,  
                'note' => $request->note, 
                'short_date' => $request->todays_date,
                'ifull_date' => $full_date,
                'iby' => $insertedBy, 
                'uby' => '',
                'year' => $year, 
                'month' => $month, 
                'day' => $day,
                'times' => $request->times,
                'is_cleared' => 0,
            ]);
    
            // Check if the insertion was successful
            // if ($warehouseSales) {
            //     \Log::info('Successfully inserted warehouse sales', ['warehouseSales' => $warehouseSales]);
            // } else {
            //     \Log::error('Failed insertion - warehouse sales is null');
            // }
    
            // Return the ID of the inserted record
            return $warehouseSales->id ?? null;
    
        } catch (\Exception $e) {
            // Log the error in case of an exception
            \Log::error('Failed to insert warehouse sales', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);
    
            // Optionally rethrow or return null
            return null;
        }
    }
    
    /**
     * Create Sales Details
     */
    private function createSalesDetails($request, $warehouseSalesId)
    {
        $todays_date = $request->todays_date ?? Jalalian::now()->format('Y-m-d');

        foreach($request->warehouseItemId as $index => $itemId)
        {
             SalesDetails::create([
                'billno' => $request->billno, 
                'branch_id' => $this->branch_id ?? $request->branch_id[$index], 
                'warehouse_id' => $request->warehouse_id[$index],
                'warehouse_sales_id' => $warehouseSalesId, 
                'pre_list_id' => $request->pre_list_id[$index], 
                'unit_id' => $request->unit_id[$index], 
                'amount' => $request->amount[$index], 
                'avg_up' => $request->avg_up[$index], 
                'sell_up' => $request->sell_up[$index], 
                'discount' => $request->discount[$index],
                'profit' => $request->profit[$index], 
                'total' => $request->total[$index],  
                'is_returned' => 0, 
                'todays_date' => $todays_date,
            ]);
        }

        return true;
    }

    /**
     * Decrease the amount of items in stock by sold amount
     */
    private function decreaseWarehouseItemFromSoldAmount($request)
    {
        foreach ($request->warehouseItemId as $index => $itemId) {
            $warehouseItem = WarehouseItem::where('id', $itemId)->first(); 

            if ($warehouseItem) {
                $warehouseItem->out_amount += $request->amount[$index];
                $warehouseItem->available_amount -= $request->amount[$index];
                $warehouseItem->save();
            } else {
                return false;
            }
        }
        return true;
    }


    /**
     * Create Journal
     */
    private function handleJournalEntry($request)
    {
            $date = explode('-', $request->todays_date);
            $year = $date[0];
            $month = $date[1];
            $day  = $date[2];
            $full_date =  $year.'-'.$month.'-'.$day.' '.Date('H:i:s A');
             /**
             * ================================== insert in to journal ========================
             * status: 1: old journal, 2: journal, 3:income, 4:expense, 5:salary, 6:participants, 7:buy, 8:sales, 9:other
             * transaction_type: 1:recieved   2:paid
             * payment_type:     1: cache,    2: loan
             */
            
            DB::beginTransaction();
            try {
            /**
             * اگر هیچ پرداخت نکند وتمام شان قرض ثبت گردد
             * خزانه باید طلب ثبت گردد =  paid Loan 
             * مشتری باید قرضدار ثبت گردد = Recieved Loan 
             */
            if(intval($request->cur_pay) === 0 && intval($request->remained) === intval($request->payable))
            { 
                // ثبت طلب خزانه = paid(ttype=2), loan(ptype=2) 
                $details =   __('validate.sales_talab_bill').' SALES_'.$request->billno;
                $optionLabel = __('validate.sales_talab'); $dynamic_type = 2; $dt_comment = 'clearable';
                $this->createJournalEntry($request,  $optionLabel, $request->from_account_id,  $request->payable, $ttype = "2", $ptype="2", $date, $full_date, $details, $dynamic_type, $dt_comment);
                
                // ثبت قرضه مشتری = recieved(ttype=1) loan(ptype=2)
                $details = __('validate.sales_loan_bill').' SALES_'.$request->billno;
                $optionLabel = __('validate.sales_loan'); $dynamic_type = 2; $dt_comment = 'clearable';
                $this->createJournalEntry($request, $optionLabel, $request->customer_account_id,  $request->payable,
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
            else if(intval($request->remained) === 0 && intval($request->cur_pay) === intval($request->payable)) 
            {
                // ثبت دریافت نقدی خزانه = Cache Recieved = t1p1
                $details =  __('validate.sales_recieve_bill').' SALES_'.$request->billno;
                $optionLabel = __('validate.sales_cache_recieved'); $dynamic_type = 0; $dt_comment = 'not clearable';
                $this->createJournalEntry($request, $optionLabel, $request->from_account_id, $request->cur_pay,
                $ttype = "1", $ptype="1", $date, $full_date, $details, $dynamic_type, $dt_comment);
            }
        
            DB::commit();
            return true; 

        } catch (\Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollBack();
            // Optionally, log the error for debugging
            \Log::error('Error storing journal entry in SalesController', ['error' => $e->getMessage()]);
    
            // Use MessageService to return error message
            Session::put('notification', [
                'message' =>  __('common.add_failed'),
                'type' => 'danger',
            ]);
             return false;
        }
    }

    private function createJournalEntry($request, $optionLabel, $account_id, $amount, $ttype, $ptype, $date, $full_date, $details, $dynamic_type, $dt_comment)
    {
        $branch_id = is_array($request->branch_id) ? $request->branch_id[0] : $request->branch_id;
        $account_type_id = Account::where('id', $account_id)->value('account_type_id');

        Journal::create([
            'bill_no' => $request->billno,
            'code' =>  $request->code,
            'account_type_id' => $account_type_id,
            'account_id' => $account_id,
            'branch_id' => $this->branch_id ?? $branch_id,
            'amount' => $amount,
            'currency_id' => $request->currency_id,
            'transaction_type' => $ttype,
            'payment_type' => $ptype,
            'dynamic_type' => $dynamic_type, 
            'dt_comment' => $dt_comment,
            'option_label' => $optionLabel,
            'user' => auth()->user()->full_name ?? '',
            'year' =>  $date[0],
            'month' =>  $date[1],
            'day' =>  $date[2],
            'inserted_short_date' => $request->todays_date,
            'inserted_full_date' => $full_date,
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
        $todaysDate = Jalalian::now()->format('Y-m-d');
        $warehouseSales = WarehouseSales::with(['currencyRelation','accountRelation'])->where('billno',$billno)->where('branch_id', $this->branch_id)->get();
        $salesDetails = SalesDetails::with(['preListRelation','unitRelation'])->where('billno',$billno)->where('branch_id', $this->branch_id)->get();

        $customer_account_id = $warehouseSales->first()->customer_account_id ?? 0;
        $currency_id = $warehouseSales->first()->currency_id ?? 1;

        // get previous balances
        $customer_balance = $this->getCustomerBalance($customer_account_id, $currency_id);
        
        // return response()->json(['warehouseSales' => $warehouseSales,'salesDetails'=> $salesDetails]);
        // return ['customer_balance' => $customer_balance];
        return view('sales.details',compact('warehouseSales','salesDetails','orgbios','todaysDate','customer_balance'));

    }

    /**
     * Get Customer balance by customer_account_id
     */
    private function getCustomerBalance($customer_account_id, $currency_id)
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
            ->where('branch_id', $this->branch_id)
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
        $todaysDate = Jalalian::now()->format('Y-m-d');
        $warehouseSales = WarehouseSales::with(['currencyRelation','accountRelation'])->where('billno',$billno)->where('branch_id', $this->branch_id)->get();
        $salesDetails = SalesDetails::with(['preListRelation','unitRelation'])->where('billno',$billno)->where('branch_id', $this->branch_id)->get();
        $billno = $billno;

        $customers = Account::select('id','name')->whereIn('account_type_id',[3,4])->where('branch_id', $this->branch_id)->get();
        $ownBanks = Account::select('id','name')->whereIn('account_type_id',[1,6])->where('branch_id', $this->branch_id)->orderBy('is_pre_select','DESC')->get();

        $currencies = Currency::select('id','name')->get();
        // return response()->json(['warehouseSales' => $warehouseSales,'salesDetails'=> $salesDetails]);
        return view('sales.edit',compact('warehouseSales','salesDetails','orgbios','todaysDate','customers','ownBanks','currencies','billno'));
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
            'discount'          => 'nullable|numeric|min:0', // Added validation for discount
        ]);

        DB::beginTransaction();
        try {
            $salesDetails = SalesDetails::findOrFail($validated['id']);

            $new_total = $validated['amount'] * $validated['sell_up'];
            $profit = $new_total - ($validated['amount'] * $salesDetails->avg_up);

            $salesDetails->update([
                'amount'   => $validated['amount'],
                'sell_up'  => $validated['sell_up'],
                'discount' => $validated['discount'] ?? 0, // Handle nullable case
                'profit'   => $profit,
                'unit_id'  => $validated['unit_id'],
                'total'    => $new_total,
            ]);

            // Find Warehouse Item
            $warehouseItem = WarehouseItem::where('warehouse_id', $validated['warehouse_id'])
                                            ->where('buy_pre_id', $validated['pre_list_id'])
                                            ->where('unit_id', $validated['unit_id'])
                                            ->first();

            if (!$warehouseItem) {
                throw new \Exception('Warehouse item not found.');
            }

            if ($validated['old_amount'] > $validated['amount']) {
                $diff = $validated['old_amount'] - $validated['amount'];
                $warehouseItem->available_amount += $diff;
                $warehouseItem->in_amount += $diff;
            } elseif ($validated['amount'] > $validated['old_amount']) {
                $diff = $validated['amount'] - $validated['old_amount'];
                if ($warehouseItem->available_amount < $diff) {
                    throw new \Exception('Not enough stock available in warehouse.');
                }
                $warehouseItem->available_amount -= $diff;
                $warehouseItem->out_amount += $diff; // Adjusted to increase, not decrease
            }

            $warehouseItem->available_total = $warehouseItem->available_amount * $warehouseItem->avg_up;
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
                                'message' => __('common.update_failed') . $e->getMessage(),
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
            'total_discount'      => 'required|numeric|min:0',
            'from_account_id'     => 'required|exists:accounts,id',
            'payable'             => 'required|numeric|min:0',
            'cur_pay'             => 'required|numeric|min:0',
            'remained'            => 'required|numeric|min:0',
            'note'                => 'nullable|string|max:500',
        ]);
    
        DB::beginTransaction();
    
        try {
            // Find the warehouse sale record
            $warehouseSales = WarehouseSales::where('billno', $validated['billno'])->where('branch_id', $this->branch_id)->firstOrFail();
    
           
            // Update warehouse sale details
            $warehouseSales->update([
                'total_price'    => $validated['total_price'],
                'total_discount' => $validated['total_discount'],
                'payable'        => $validated['payable'],
                'cur_pay'        => $validated['cur_pay'],
                'remained'       => $validated['remained'],
                'note'           => $validated['note'],
            ]);
    
            // Retrieve old journal records
            $oldJournals = Journal::where('times', $request->times)->where('branch_id', $this->branch_id)->where('status', 8)->get();
    
            if ($oldJournals->isNotEmpty()) {
                // Clone request to avoid modifying original data
                $clonedRequest = clone $request;
                $clonedRequest->merge([
                    'code' => $oldJournals->first()->code, // Get 'code' from the first record
                ]);
    
                // Delete all journal records in a single query
                Journal::where('times', $request->times)->where('branch_id', $this->branch_id)->where('status', 8)->delete();
    
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

            // Debug: Log the retrieved SalesDetails info
            \Log::info("Deleting SalesDetails ID: $id", [
                'warehouse_id' => $SalesDetails->warehouse_id,
                'buy_pre_id' => $SalesDetails->pre_list_id,
                'unit_id' => $SalesDetails->unit_id
            ]);

            // Find Warehouse Item
            $warehouseItem = WarehouseItem::where('warehouse_id', $SalesDetails->warehouse_id)
                                        ->where('buy_pre_id', $SalesDetails->pre_list_id)
                                        ->where('unit_id', $SalesDetails->unit_id)
                                        ->where('branch_id', $this->branch_id)
                                        ->first();

            if (!$warehouseItem) {
                throw new \Exception('Warehouse item not found.');
            }

            // Update warehouse item
            $warehouseItem->available_amount += $SalesDetails->amount;
            $warehouseItem->in_amount += $SalesDetails->amount; 
            $warehouseItem->available_total = (($warehouseItem->available_amount + $SalesDetails->amount) * $warehouseItem->avg_up);
            $warehouseItem->save();

            // Delete SalesDetails **after** updating warehouse item
            $SalesDetails->delete();

            DB::commit();

            Session::put('notification', [
                'message' => __('common.updated_successfully'),
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
                ->where('branch_id', $this->branch_id)
                ->first();

            if ($warehouse_sales) {
                SalesDetails::where('warehouse_sales_id', $warehouse_sales->id)
                    ->where('branch_id', $this->branch_id)
                    ->delete();

                $warehouse_sales->delete();
            }

            Journal::where('times', $times)
                ->where('branch_id', $this->branch_id)
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

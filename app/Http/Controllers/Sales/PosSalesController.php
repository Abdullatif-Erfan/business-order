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


class PosSalesController extends Controller
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

    public function item_list(Request $request)
    {
        $query = DB::table('warehouse_items')
        ->join('bought_item_pre_lists', 'bought_item_pre_lists.id', '=', 'warehouse_items.buy_pre_id')
        ->join('warehouses', 'warehouses.id', '=', 'warehouse_items.warehouse_id')
        ->join('units', 'units.id', '=', 'warehouse_items.unit_id')
        ->where('warehouse_items.available_amount', '>', 0)
        ->where('warehouse_items.branch_id', $this->branch_id)
        ->select(
            'warehouse_items.id',
            'warehouse_items.unit_id',
            'avg_up',
            'sell_up', 
            'warehouse_items.available_amount', 
            'units.name as unit_name',
            'warehouses.id as warehouse_id', 
            'warehouses.name as warehouse_name', 
            'bought_item_pre_lists.name as item_name',
            'bought_item_pre_lists.branch_id',
            'bought_item_pre_lists.id as pre_list_id',
            'bought_item_pre_lists.code',
            'image_path'
        )
        ->orderBy('warehouse_items.id','DESC')
        ->limit(12);

      
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            
            $query->where(function($q) use ($searchTerm) {
                // For non-numeric input - partial match on name
                $q->where('bought_item_pre_lists.name', 'LIKE', "%{$searchTerm}%");
                // Optional: also allow exact code match if user types the full code
                $q->orWhere('bought_item_pre_lists.code', 'LIKE', "%{$searchTerm}%");
            });
        }

        $warehouseItems = $query->get();

        return view('sales.pos.item_card',compact('warehouseItems'));
    }

    /**
     * POS Create form
     */
    public function pos_create()
    {
        $customers = Account::select('id','name')->whereIn('account_type_id',[3,4])->where('branch_id', $this->branch_id)->get();
        $ownBanks = Account::select('id','name')->whereIn('account_type_id',[1,6])->where('branch_id', $this->branch_id)->orderBy('is_pre_select','DESC')->get();

        $currencies = Currency::all();
        $orgbios = OrgBio::all();
    
        return view('sales.pos.pos_create_form',compact('customers','ownBanks','currencies','orgbios'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function pos_store(Request $request)
    {
        // return response()->json(['data' => $request->all()]);

        // $items = $request->input('items'); // This will be an array of items
        // return response()->json(['items' => $items]);
        
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
            
            $times = time();
            $journal_code = Journal::where('branch_id', $this->branch_id)->max('code') + 1;
            $billno =  WarehouseSales::where('branch_id', $this->branch_id)->max('billno') + 1;

            // create warehouse_sales
            $warehouseSalesId = $this->createWarehouseSales($request, $times, $billno);
            
            // create sales_details
            $salesDetails = $this->createSalesDetails($request, $warehouseSalesId, $billno);

            // decrease from warehouse_items
            $checkWarehouseItems = $this->decreaseWarehouseItemFromSoldAmount($request);

            $checkJournal =  $this->handleJournalEntry($request, $times, $journal_code ,$billno);
            if(!$checkJournal || !$salesDetails || !$warehouseSalesId || !$checkWarehouseItems)
            {
                DB::rollBack();
                Session::put('notification', [
                    'message' => __('common.add_failed'),
                    'type' => 'danger',
                ]);
                // return redirect()->route('sales.pos_create');
                return response()->json(['status' => 'failed', 'message' =>  __('common.add_failed')]);
            }

            // Flash error message
            DB::commit();
            Session::put('notification', [
                'message' => __('common.added_successfully'),
                'type' => 'success',
            ]);
            //  return redirect()->route('sales.pos_create');
            return response()->json(['status' => 'success', 'message' => __('common.added_successfully')]);
 
 
         } catch (\Exception $e) {
             // Rollback the transaction if an error occurs
             DB::rollBack();
             // Optionally, log the error for debugging
             \Log::error('Error storing PosSalesController', ['error' => $e]);

            // Flash error message
            Session::put('notification', [
                'message' =>  __('common.add_failed'),
                'type' => 'danger',
            ]);
            //  return redirect()->route('sales.pos_create');
            return response()->json(['status' => 'failed', 'message' =>  __('common.add_failed')]);
         }   
    }

     /**
     * print pos format
     */
    public function pos_print(string $billno)
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
        return view('sales.pos.print',compact('warehouseSales','salesDetails','orgbios','todaysDate','customer_balance'));
    }

    /**
     * Validation rules
     */

    private function validationRules()
    {
        return [
            'customer_account_id' => 'required|integer|exists:accounts,id',
            'items.*.warehouse_id'  => 'required|integer',
            'items.*.id' => 'required|integer|min:1',
            'items.*.amount' => 'required|numeric|min:1',
            'items.*.unit_id' => 'required|integer|min:1',
            'total' => 'required|numeric|min:1',
            'profit' => 'nullable|numeric|min:0',
            'payable' => 'required|numeric|min:1',
            'cur_pay' => 'required|numeric|min:0',
        ];
    }


    /**
     * Custom validation messages
     */
    private function validationMessages()
    {
        return [
            'customer_account_id.required' => 'انتخاب حساب مشتری الزامی است.',
            'items.*.warehouse_id.required' => 'انتخاب گدام الزامی است.',
            'items.*.id.required' => 'آیدی فروشات ضروری می‌باشد.',
            'items.*.amount.required' => 'مقدار جنس الزامی است.',
            'items.*.unit_id.required' => 'انتخاب واحد جنس الزامی است.',
            'total.required' => 'مجموع فاکتور الزامی است.',
            'payable.required' => 'مبلغ قابل پرداخت الزامی است.',
            'cur_pay.required' => 'مبلغ پرداخت شده الزامی است.',
        ];
    }


    /**
    *  Create Warehouse Sales
    */
    private function createWarehouseSales($request,$times, $billno)
    {
        
        try {
            // Prepare date and other fields
            $full_date = Jalalian::now()->format('Y-m-d H:i:s A');
            $insertedBy = auth()->user()->full_name ?? '';
            $short_date = Jalalian::now()->format('Y-m-d');
            [$year, $month, $day] = explode('-', $short_date);
    
        
            // \Log::info('Start inserting into warehouse sales', ['request' => $request->all()]);
    
           
            // Insert the new warehouse sale record
            $warehouseSales = WarehouseSales::create([
                'billno' => $billno, 
                'factor' => '', 
                'account_id' => $request->from_account_id, 
                'branch_id' => $this->branch_id, 
                'customer_account_id' => $request->customer_account_id, 
                'total_price' => $request->total, 
                'total_discount' => $request->discount, 
                'payable' => $request->payable, 
                'cur_pay' => $request->cur_pay,
                'remained' => $request->payable - $request->cur_pay, 
                'currency_id' => $request->currency_id,  
                'note' => 'POS Sales', 
                'short_date' => $short_date,
                'ifull_date' => $full_date,
                'iby' => $insertedBy, 
                'uby' => '',
                'year' => $year, 
                'month' => $month, 
                'day' => $day,
                'times' => $times,
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
    private function createSalesDetails($request, $warehouseSalesId, $billno)
    {
        $todays_date = Jalalian::now()->format('Y-m-d');
        $items = $request->input('items');

        // foreach($items->id as $index => $itemId)
        foreach ($items as $item)
        {
            SalesDetails::create([
                'billno' => $billno,
                'branch_id' => $this->branch_id,
                'warehouse_id' => $item['warehouse_id'],
                'warehouse_sales_id' => $warehouseSalesId,
                'pre_list_id' => $item['pre_list_id'],
                'unit_id' => $item['unit_id'],
                'amount' => $item['amount'],
                'avg_up' => $item['avg_up'],
                'sell_up' => $item['sell_up'],
                'discount' => 0,
                'profit' => $item['profit'],
                'total' => $item['total'],
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
        $items = $request->input('items');
        foreach ($items as $item) {
            $warehouseItem = WarehouseItem::where('id', $item['id'])->first(); 

            if ($warehouseItem) {
                $warehouseItem->out_amount += $item['amount'];
                $warehouseItem->available_amount -= $item['amount'];
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
    private function handleJournalEntry($request,$times, $journal_code, $billno)
    {
            $todays_date = Jalalian::now()->format('Y-m-d');
            $date = explode('-', $todays_date);
            $year = $date[0];
            $month = $date[1];
            $day  = $date[2];
            $full_date = Jalalian::now()->format('Y-m-d H:i:s A');
            $remained = floatval($request->payable) - floatval($request->cur_pay);
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
            if(intval($request->cur_pay) === 0 && intval($remained) === intval($request->payable))
            { 
                // ثبت طلب خزانه = paid(ttype=2), loan(ptype=2) 
                $details =  ' طلب فروشات - بل '.' SALES_'.$billno;
                $optionLabel = 'طلب فروشات'; $dynamic_type = 2; $dt_comment = 'clearable';
                $this->createJournalEntry($request,  $optionLabel, $request->from_account_id,  $request->payable, $ttype = "2", $ptype="2", $date, $full_date, $details, $dynamic_type, $dt_comment,$times,$journal_code, $billno);
                
                // ثبت قرضه مشتری = recieved(ttype=1) loan(ptype=2)
                $details =  ' قرضه فروشات - بل '.' SALES_'.$billno;
                $optionLabel = 'قرضه فروشات'; $dynamic_type = 2; $dt_comment = 'clearable';
                $this->createJournalEntry($request, $optionLabel, $request->customer_account_id,  $request->payable,
                 $ttype = "1", $ptype="2", $date, $full_date, $details, $dynamic_type, $dt_comment,$times,$journal_code, $billno);
            }

            // کمی شانرا پرداخت کرده و متباقی شانرا قرض انتخاب کرده است
            else if(intval($remained) > 0 && intval($request->cur_pay) > 0) 
            {
                // ثبت دریافت نقدی خزانه = Cache Recieved = t1p1
                $details =  'دریافت فروشات - بل  '.' SALES_'.$billno;
                $optionLabel = 'دریافت نقد'; $dynamic_type = 0; $dt_comment = 'not clearable';
                $this->createJournalEntry($request, $optionLabel, $request->from_account_id, $request->cur_pay, $ttype = "1", $ptype="1", $date, $full_date, $details, $dynamic_type, $dt_comment,$times,$journal_code, $billno);

                // ثبت قرضه مشتری = Loan Recieved = p2t1
                $details =  ' قرضه فروشات - بل '.' SALES_'.$billno;
                $optionLabel = 'قرضه فروشات'; $dynamic_type = 2; $dt_comment = 'clearable';
                $this->createJournalEntry($request, $optionLabel, $request->customer_account_id, $remained,  
                $ttype = "1", $ptype="2", $date, $full_date, $details, $dynamic_type, $dt_comment,$times,$journal_code, $billno);
               
                // ثبت طلب خزانه = Paid Loan = t2p2
                $details =  ' طلب فروشات - بل '.' SALES_'.$billno;
                $optionLabel = 'طلب فروشات'; $dynamic_type = 2; $dt_comment = 'clearable';
                $this->createJournalEntry($request, $optionLabel,  $request->from_account_id, $remained,
                $ttype = "2", $ptype="2", $date, $full_date, $details, $dynamic_type, $dt_comment,$times,$journal_code, $billno);
            }

             // قرضدار نمانده است و مکمل پرداخت کرده است
             // تنها در حساب خزانه اضافه شود
            else if(intval($remained) === 0 && intval($request->cur_pay) === intval($request->payable)) 
            {
                // ثبت دریافت نقدی خزانه = Cache Recieved = t1p1
                $details =  'دریافت فروشات - بل  '.' SALES_'.$billno;
                $optionLabel = 'دریافت نقد'; $dynamic_type = 0; $dt_comment = 'not clearable';
                $this->createJournalEntry($request, $optionLabel, $request->from_account_id, $request->cur_pay,
                $ttype = "1", $ptype="1", $date, $full_date, $details, $dynamic_type, $dt_comment,$times,$journal_code, $billno);
            }
        
            DB::commit();
            return true; 

        } catch (\Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollBack();
            // Optionally, log the error for debugging
            \Log::error('Error storing journal entry in PosSalesController', ['error' => $e->getMessage()]);
    
            // Use MessageService to return error message
            Session::put('notification', [
                'message' =>  __('common.add_failed'),
                'type' => 'danger',
            ]);
             return false;
        }
    }

    private function createJournalEntry($request, $optionLabel, $account_id, $amount, $ttype, $ptype, $date, $full_date, $details, $dynamic_type, $dt_comment,$times,$journal_code, $billno)
    {
        $branch_id = is_array($request->branch_id) ? $request->branch_id[0] : $request->branch_id;
        $account_type_id = Account::where('id', $account_id)->value('account_type_id');

        Journal::create([
            'bill_no' => $billno,
            'code' =>  $journal_code,
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
            'times' => $times,
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
                                'message' => __('common.updated_successfully'),
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
                'message' => __('common.delete_failed'),
                'type' => 'danger',
            ]);

            return back();
        }
    }

}

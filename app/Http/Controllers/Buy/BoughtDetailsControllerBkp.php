<?php

namespace App\Http\Controllers\Buy;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Setting\OrgBio;
use App\Models\Setting\Unit;
use App\Models\Buy\BuyPreList;
use App\Models\Buy\BuyInvoice;
use App\Models\Buy\BuyInvoiceItem;
use App\Models\Buy\BuyInvoicePayment;
use App\Models\Buy\BoughtItem;
use App\Models\Setting\Currency;
use App\Models\Buy\BoughtItemDetails; 
use App\Models\Transaction\Journal;
use App\Models\Setting\Warehouse;
use App\Models\Warehouse\WarehouseItem;
use Carbon\Carbon;
use App\Models\Setting\Account;
use Yajra\DataTables\Facades\DataTables;


class BoughtDetailsController extends Controller
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
        // $boughtList = BoughtItemDetails::with(['boughtItemRelation','preListRelation'])->get();
        // return response()->json($boughtItems);

        // $insertedData = BoughtItemDetails::with(['preListRelation','unitRelation'])->get();
        // return response()->json(['insertedData' => $insertedData]); 
        // return view('buy.bought.curlist',compact('insertedData'));
        
        // return response()->json(auth()->user());
        // return response()->json(auth()->user());

        // $boughtItems = BoughtItem::with(['currencyRelation','customerRelation'])->orderBy('id', 'DESC')->get();
        // return response()->json($boughtItems);


        $currencies = Currency::all();
        $orgbios = OrgBio::all();
        $todaysDate = Carbon::now()->format('Y-m-d');

        return view('buy.bought.list',compact('currencies','todaysDate','orgbios'));
    }


    public function getData(Request $request)
    {
            $tax_activation = $request->input('tax_activation');
            $boughtItems = BoughtItem::with(['currencyRelation', 'customerRelation'])->orderBy('id', 'DESC');
            
              // Apply filters if provided
              if ($request->customer_name) {
                $boughtItems->whereHas('customerRelation', function ($query) use ($request) {
                    $query->where('name', 'LIKE', DB::raw('?'), ["%{$request->customer_name}%"]);
                });
            }
            
            if ($request->currency_id) {
                $boughtItems->where('currency_id', $request->currency_id);
            }
            
            if ($request->start_date && $request->end_date) {
                $boughtItems->whereBetween('idate', [$request->start_date, $request->end_date]);
            } elseif ($request->start_date) {
                $boughtItems->whereDate('idate', '=', $request->start_date);
            } elseif ($request->end_date) {
                $boughtItems->whereDate('idate', '<=', $request->end_date); // Until today
            }
            
            if ($request->bill_number) {
                $boughtItems->where('billno', $request->bill_number);
            }
            
            return DataTables::of($boughtItems->get())
            

            ->addIndexColumn()
            // ->addColumn('branch', function($buyPreList) {
            //     return $buyPreList->branchRelation->name;
            // })

            ->addColumn('billno', function($boughtItem) {
                $checkIcon = $boughtItem->is_cleared == 1 ? '<i class="fas fa-check-circle success"></i>' : '';
                return $boughtItem->billno ? $checkIcon.' '.'BUY_'.$boughtItem->billno: 0;
            })

             ->addColumn('total', function ($boughtItem) use ($tax_activation) {
                 return (int)$tax_activation === 1 
                ? number_format($boughtItem->total_vat ?? 0, 2) 
                : number_format($boughtItem->total_price ?? 0, 2);
            })

            ->addColumn('cur_pay', function ($boughtItem) {
                $cur_pay = $boughtItem->cur_pay;
                return (fmod($cur_pay, 1) == 0) ? number_format($cur_pay, 0) : number_format($cur_pay, 2);
            })

            ->addColumn('remained', function ($boughtItem) use ($tax_activation) {
                return (int)$tax_activation === 1 
                ? number_format($boughtItem->remained_vat ?? 0, 2) 
                : number_format($boughtItem->remained ?? 0, 2);
            })

            ->addColumn('currencyRelation', function ($boughtItem) {
                return $boughtItem->currencyRelation->name ? $boughtItem->currencyRelation->name : '';
            })
        
            ->addColumn('view', function ($boughtItem) {
                return '<a href="boughtList/details/'.$boughtItem->times.'" class="hidden-print"><i class="fas fa-eye viewItems" 
                data-id="' . $boughtItem->details_id . '" style="font-size:20px;"></i></a>';
            })

            ->rawColumns(['billno','view'])
            ->make(true);

    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // $boughtList = BoughtItemDetails::with(['boughtItemRelation','preListRelation'])->get();
        $currencies = Currency::select('id','name')->get();
        $warehouses = Warehouse::select('id','name')->get();
        $suppliers = Account::select('id','name')->whereIn('account_type_id',[4])->get();
        $ownBanks = Account::select('id','name')->whereIn('account_type_id',[1,6])->orderBy('is_pre_select','DESC')->get();
        $billno =  BoughtItem::max('billno') + 1;
    
        $preLists = BuyPreList::select('id','name','code')->get();
        $todaysDate = Carbon::now()->format('Y-m-d');
        $units = Unit::select('id','name')->get();
        $newJournalCode =  Journal::max('code') + 1;
        $tax = OrgBio::select('tax_activation')->first();

        $times = time();

        // return response()->json($preLists);
        return view('buy.bought.create',compact('currencies','suppliers','todaysDate','ownBanks','preLists','units','warehouses','times','newJournalCode','billno','tax'));
    }

    /**
     * Store a newly created resource in storage.
    */
    public function store(Request $request)
    {
        // return response()->json(['data' => $request->all()]); 

        // $insertedData = BoughtItemDetails::where('times','1739588540')->get();
        // return view('buy.bought.curlist',compact('insertedData'));

        $this->validateRequest($request);

        $short_date = $request->todays_date ?? Carbon::now()->format('Y-m-d');
        $date = Carbon::parse($short_date);
        $time = $request->times ?? '00:00:00';
        $full_date = $date->format('Y-m-d') . ' ' . $time;
        $times = $request->times; 
        

        // Start the transaction
        DB::beginTransaction();
   
        try {

        // 1: insert in to bought_items table
        $BoughtItemId = $this->createOrUpdateBoughtItem($request, $short_date, $times);

        // 2: insert in to bought_item_details table
        $this->storeBoughtItemDetails($request, $BoughtItemId,  $times);

        // 3: insert or update warehouse_items
        $this->createOrUpdateWarehouseItems($request);

        DB::commit();

        // 4: fetch inserted data from bought_item_details
        $insertedData = BoughtItemDetails::with(['preListRelation','unitRelation'])->where('times',$times)->get();
             
        //  return response()->json(['insertedData' => $insertedData]); 
           return view('buy.bought.curlist',compact('insertedData'));

        } catch (\Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollBack();
            // Optionally, log the error for debugging
            \Log::error('Error storing BoughtDetailsController', ['error' => $e]);

            return response()->json(['status' => 'failed'], 404);
        }        
    }
    
   
    private function createOrUpdateBoughtItem($request, $short_date, $times)
    {
        $date = Carbon::parse($request->todays_date);
        $year = $date->year;   
        $month = $date->month; 
        $day = $date->day;     
        
        // Check if a record with the same billno exists
        $BoughtItem = BoughtItem::where('billno', $request->billno)->first();

        if ($BoughtItem) {
            // \Log::info('updating BoughtItem');
            // ✅ Update existing record
            $BoughtItem->update([
                'total_price'         => $request->total_price ?? 0,
                'total_vat'           => $request->total_vat ?? 0,
                'cur_pay'             => $request->cur_pay ?? 0,
                'remained'            => $request->remained ?? 0,
                'currency_id'         => $request->currency_id ?? 0,
                'account_id'          => $request->from_account_id,
                'supplier_account_id' => $request->supplier_account_id,
                'tax_activation'      => $request->tax_activation ?? 0,
                'note'                => $request->note ?? '',
            ]);
        } else {
            // \Log::info('Inserting BoughtItem');
            $BoughtItem = BoughtItem::create([
                'factor'              => $request->factor ?? 0,
                'billno'              => $request->billno,
                'journal_code'        => $request->journal_code ?? 0,
                'total_price'         => $request->total_price ?? 0,
                'total_vat'           => $request->total_vat ?? 0,
                'cur_pay'             => $request->cur_pay ?? 0,
                'remained'            => $request->remained ?? 0,
                'account_id'          => $request->from_account_id,
                'supplier_account_id' => $request->supplier_account_id,
                'currency_id'         => $request->currency_id,
                'tax_activation'      => $request->tax_activation ?? 0,
                'note'                => $request->note ?? '',
                'idate'               => $short_date,
                'year'                => $year,
                'month'               => $month,
                'day'                 => $day,
                'times'               => $times,
                'user_id'             => auth()->user()->id ?? '',
                'user_name'           => auth()->user()->full_name ?? '',
                'has_invoice'         => 0,

            ]);
        }

        return $BoughtItem->id;
    }


    private function storeBoughtItemDetails($request, $boughtItemId, $times)
    {
        // If not exists, create new record
        $flag = $request->tax_activation == 1 ? true : false;
        return BoughtItemDetails::create([
            'billno' => $request->billno,
            'bought_item_id' => $boughtItemId,
            'supplier_account_id' => $request->supplier_account_id,
            'pre_list_id' => $request->pre_list_id,
            'amount' => $request->amount,
            'unit_id' => $request->unit_id,
            'buy_up' => $request->buy_up,
            'buy_tax_per' => $flag ? $request->buy_tax_per : NULL, 
            'buy_tax_price' => $flag ? $request->buy_tax_price : NULL, 
            'buy_up_vat' =>  $flag ? $request->buy_up_vat : NULL, 
            'total' => $request->total_price,   
            // 'total_vat' => $flag ? $request->total_vat : NULL, 
            'total_vat' => $flag ? ($request->buy_up_vat ?? 0) * $request->amount : NULL, 
            'sell_up' => $request->sell_up,
            'sell_tax_per' =>  $flag ? $request->sell_tax_per : NULL, 
            'sell_tax_price' =>  $flag ? $request->sell_tax_price : NULL, 
            'sell_up_vat' =>  $flag ? $request->sell_up_vat : NULL,
            'is_moved' => 1,
            'times' => $times,
            'user_id'             => auth()->user()->id ?? '',
            'user_name'           => auth()->user()->full_name ?? '',
        ]);
        return true;
    }    

    private function createOrUpdateWarehouseItems($request)
    {
        $flag = $request->tax_activation == 1 ? true : false;
        $date = Carbon::parse($request->todays_date);
        $year = $date->year;   
        $month = $date->month; 
        $day = $date->day;   

        // Prepare bulk insert data
        $warehouseItemsToInsert = [];

        $default_warehouse_id = 1;
        $new_total = $request->buy_up * $request->amount; // Cost of new stock   
        // Insert new warehouse item
        $warehouseItemsToInsert[] = [
            'warehouse_id' => $default_warehouse_id,
            'buy_pre_id' => $request->pre_list_id,
            'name' => $request->item_name ?? '',
            'in_amount' => $request->amount,
            'out_amount' => 0.00,
            'available_amount' => $request->amount,
            'unit_id' => $request->unit_id,
            'buy_up' => $request->buy_up,
            'buy_tax_per' => $flag ? $request->buy_tax_per : NULL, 
            'buy_tax_price' => $flag ? $request->buy_tax_price : NULL, 
            'buy_up_vat' =>  $flag ? $request->buy_up_vat : NULL, 
            'total' => $request->total_price,
            // 'total_vat' => $flag ? $request->total_vat : NULL, 
            'total_vat' => $flag ? ($request->buy_up_vat ?? 0) * $request->amount : NULL, 
            'sell_up' => $request->sell_up,
            'sell_tax_per' =>  $flag ? $request->sell_tax_per : NULL, 
            'sell_tax_price' =>  $flag ? $request->sell_tax_price : NULL, 
            'sell_up_vat' =>  $flag ? $request->sell_up_vat : NULL,
            'currency_id' => $request->currency_id,
            'category_id' => $request->category_id,
            'idate' => $request->todays_date ?? Carbon::now()->format('Y-m-d'),
            'user_id' => auth()->user()->id ?? 0,
            'year' => $year,
            'month' => $month,
            'day' => $day,
            'times' => $request->times,
            'is_cleared' => 0,
        ];
    
        // Bulk insert new records
        if (!empty($warehouseItemsToInsert)) {
            WarehouseItem::insert($warehouseItemsToInsert);
        }
    
        return true;
    }
    

    /**
    * final submit in creation form
    * in this function update bought_items based on billno and create journal reacord
    */
     public function submit(Request $request)
    {
        // return ['data' => $request->all()];

        $validated = $request->validate([
            'billno'    => 'required|numeric',
            'total_vat' => 'required', // VAT = Value Added Tax
            'cur_pay'   => 'required',
            'remained'  => 'required',
        ]);

        DB::beginTransaction(); // transaction start

        try {
            // Get sum of total and total_vat
            $boughtItemDetails = BoughtItemDetails::where('billno', $validated['billno'])
                ->selectRaw('SUM(total) as total_sum, SUM(total_vat) as total_vat_sum')
                ->first();

            if (!$boughtItemDetails) {
                DB::rollBack();
                Session::put('notification', [
                    'message' => __('common.add_failed'),
                    'type' => 'danger',
                ]);
                return redirect()->route('boughtList.index');
            }

            $total_price = $boughtItemDetails->total_sum ?? 0;
            $total_vat = $boughtItemDetails->total_vat_sum ?? 0;
            
            $cur_pay = (float) $request->cur_pay;
            $remained = $total_price - $cur_pay;

            // Calculate remained_vat 
            if ((int) $request->tax_activation === 1) {
                $remained_vat = $total_vat - $cur_pay; 
                // If you have separate vat payment, use: $remained_vat = $total_vat - $request->cur_pay_vat;
            } else {
                $remained_vat = null;
            }

            $BoughtItem = BoughtItem::where('billno', $request->billno)->first();

            if (!$BoughtItem) {
                DB::rollBack();
                Session::put('notification', [
                    'message' => __('common.record_not_found'),
                    'type' => 'danger',
                ]);
                return redirect()->route('boughtList.index');
            }

            // Update BoughtItem record
            $BoughtItem->update([
                'journal_code' => $request->journal_code,
                'total_price' => $total_price,
                'total_vat' => $total_vat,
                'cur_pay' => $cur_pay,
                'remained' => $remained,
                'remained_vat' => $remained_vat,
                'note' => $request->note ?? '',
                'times' => $request->times,
            ]);

            // Log::info('Start inserting into Journal');
            // Insert into journal
            $check = $this->handleJournalEntry($request);
            if (!$check) {
                DB::rollBack();
                Session::put('notification', [
                    'message' => __('common.add_failed'),
                    'type' => 'danger',
                ]);
                return redirect()->route('boughtList.index');
            }

            DB::commit(); // Commit transaction

            // Flash success message
            Session::put('notification', [
                'message' => __('common.added_successfully'),
                'type' => 'success',
            ]);

            return redirect()->route('boughtList.index');

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback on error

            // Log the error
            \Log::error('Error occurred during the transaction', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Flash error message
            Session::put('notification', [
                'message' => __('common.add_failed') . ': ' . $e->getMessage(),
                'type' => 'danger',
            ]);

            return redirect()->route('boughtList.index');
        }
    }
    
    private function deleteBoughtRecords($request)
    {
         // Optionally delete or revert other records here as per your requirements
         BoughtItem::where('times', $request->times)->where('billno', $request->billno)->delete();
         BoughtItemDetails::where('times', $request->times)->where('billno', $request->billno)->delete();
         Journal::where('times', $request->times)->where('bill_no', $request->billno)->delete();
 
         // Rollback warehouse updates
         $WarehouseItems = WarehouseItem::where('times', $request->times)
             ->where('buy_pre_id', $request->pre_list_id)
             ->get();
 
            if ($WarehouseItems->isEmpty()) {
                DB::rollBack();
                Session::put('notification', [
                    'message' => __('common.no_data_found'),
                    'type' => 'danager',
                ]);

                return redirect()->route('boughtList.index');
            }

            // Loop through each WarehouseItem
            foreach ($WarehouseItems as $WarehouseItem) {
                if ($request->amount == $WarehouseItem->in_amount) {
                    // Delete the record if the amounts match
                    $WarehouseItem->delete();
                } else {
                    // Decrease in_amount by the requested amount
                    $WarehouseItem->in_amount -= $request->amount;
                    $WarehouseItem->available_amount -= $request->amount;
                    $WarehouseItem->save();
                }
            }
    }

    private function validateRequest($request)
    {
        $validated = $request->validate([
            'todays_date' => 'required|date_format:Y-m-d', // Ensures it's exactly 2026-06-25
            'times' =>       'nullable', // e.g., 14:30 (optional)
            'pre_list_id' => 'required|integer',
            'amount' => 'required|numeric|min:0.01',
            'unit_id' => 'required|integer',
            'buy_up' => 'required|numeric|min:0.01',
            'billno' => 'required|integer|min:0',
            'supplier_account_id' => 'required|integer',
            'from_account_id' => 'required|integer',
            'currency_id' => 'required|integer',
        ], [
            'pre_list_id.required' => __('validate.pre_list_id_required'),
        
            'amount.required' => __('validate.amount_required'),
            'amount.numeric' => __('validate.amount_numeric'),
            
            'unit_id.required' => __('validate.unit_id_required'),
            'unit_id.integer' => __('validate.unit_id_integer'),
            
            'buy_up.required' => __('validate.bought_up_required'),
            'buy_up.numeric' => __('validate.bought_up_numeric'),
            'buy_up.min' => __('validate.bought_up_min'),
            
            'billno.required' => __('validate.billno_required'),
            'billno.integer' => __('validate.billno_integer'),
            'billno.min' => __('validate.billno_min'),
            
            'supplier_account_id.required' => __('validate.customer_account_id_required'),
            'supplier_account_id.integer' => __('validate.customer_account_id_integer'),
            
            'from_account_id.required' => __('validate.from_account_id_required'),
            
            'currency_id.required' => __('validate.currency_id_required'),
            'currency_id.integer' => __('validate.currency_id_integer'),
        ]);
        
    }


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
            
            DB::beginTransaction();
            try {
            /**
             * اگر هیچ پرداخت نکند وتمام شان قرض ثبت گردد
             * خزانه باید قرضدار ثبت گردد = Recieved Loan 
             * مشتری باید طلب ثبت گردد = paid Loan 
             */

            if(intval($request->cur_pay) === 0 && intval($request->remained) === intval($request->total_price))
            { 
                // ثبت قرضه خزانه = recieved(ttype=1) loan(ptype=2)
                $details =  __('validate.qkbill').' BUY_'.$request->billno;
                $optionLabel = __('validate.qkharid'); $dynamic_type = 2; $dt_comment = 'clearable';
                $this->createJournalEntry($request,  $optionLabel, $request->from_account_id,  $request->total_price, $ttype = "1", $ptype="2", $date, $full_date, $details, $dynamic_type, $dt_comment);
                
                // ثبت طلب مشتری = paid(ttype=2), loan(ptype=2) 
                $details = __('validate.tkbill').' BUY_'.$request->billno;
                $optionLabel = __('validate.tkharid'); $dynamic_type = 2; $dt_comment = 'clearable';
                $this->createJournalEntry($request, $optionLabel, $request->supplier_account_id,  $request->total_price,
                 $ttype = "2", $ptype="2", $date, $full_date, $details, $dynamic_type, $dt_comment);
            }

            // کمی شانرا پرداخت کرده و متباقی شانرا قرض انتخاب کرده است
            else if(intval($request->remained) > 0 && intval($request->cur_pay) > 0) 
            {
                // ثبت پرداخت نقدی خزانه = Cache paid
                $details = __('validate.pkbill').' BUY_'.$request->billno;
                $optionLabel = __('validate.cpayment'); $dynamic_type = 0; $dt_comment = 'not clearable';
                $this->createJournalEntry($request, $optionLabel, $request->from_account_id, $request->cur_pay, $ttype = "2", $ptype="1", $date, $full_date, $details, $dynamic_type, $dt_comment);

                // ثبت قرضه خزانه = Loan Recieved 
                $details =  __('validate.qkbill').' BUY_'.$request->billno;
                $optionLabel = __('validate.qkharid'); $dynamic_type = 2; $dt_comment = 'clearable';
                $this->createJournalEntry($request, $optionLabel, $request->from_account_id, $request->remained,  
                $ttype = "1", $ptype="2", $date, $full_date, $details, $dynamic_type, $dt_comment);
               
                // ثبت طلب مشتری = Paid Loan
                $details =  __('validate.tkbill').' BUY_'.$request->billno;
                $optionLabel = __('validate.tkharid'); $dynamic_type = 2; $dt_comment = 'clearable';
                $this->createJournalEntry($request, $optionLabel,  $request->supplier_account_id, $request->remained,
                $ttype = "2", $ptype="2", $date, $full_date, $details, $dynamic_type, $dt_comment);
            }

             // قرضدار نمانده است و مکمل پرداخت کرده است
             // تنها از حساب خزانه کم شود
            else if(intval($request->remained) === 0 && intval($request->cur_pay) === intval($request->total_price)) 
            {
                // ثبت پرداخت نقدی خزانه = Cache paid
                $details =  __('validate.pkbill').' BUY_'.$request->billno;
                $optionLabel = __('validate.cpayment'); $dynamic_type = 0; $dt_comment = 'not clearable';
                $this->createJournalEntry($request, $optionLabel, $request->from_account_id, $request->cur_pay, $ttype = "2", $ptype="1", $date, $full_date, $details, $dynamic_type, $dt_comment);
            }

            DB::commit();
            return true; 

        } catch (\Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollBack();
            // Optionally, log the error for debugging
            \Log::error('Error storing journal entry in BoughtDetailsController', ['error' => $e]);
    
            // Use MessageService to return error message
            Session::put('notification', [
                'message' =>  __('common.add_failed'),
                'type' => 'danger',
            ]);
             return false;
        }
    }

   private function createJournalEntry($request, $optionLabel, $account_id, $amount, $ttype, $ptype, $date, $full_date, $details, $dynamic_type, $dt_comment, $status = 7)
    {
        try {
            $account_type_id = Account::where('id', $account_id)->value('account_type_id');
            $day = $date->day;
            $year = $date->year;
            $month = $date->month;
            
            Journal::create([
                'bill_no' => $request->billno,
                'code' =>  $request->journal_code,
                'account_type_id' => $account_type_id,
                'account_id' => $account_id,
                'amount' => $amount,
                'currency_id' => $request->currency_id,
                'transaction_type' => $ttype,
                'payment_type' => $ptype,
                'option_label' => $optionLabel,
                'dynamic_type' => $dynamic_type,
                'dt_comment' => $dt_comment,
                'user_id' => auth()->user()->id ?? '',
                'user_name' => auth()->user()->full_name ?? '',
                'year' =>  $year,
                'month' => $month,
                'day' =>  $year,
                'idate' => $request->todays_date,
                'details' => $details,
                'status' => $status,  
                'times' => $request->times,
                'is_single_record' => 1, 
            ]);
            
            return true; 
            
        } catch (\Exception $e) {
            \Log::error('Journal entry creation failed: ' . $e->getMessage());
            return false; 
        }
    }


    /**
     * Display the specified resource.
     */
    public function details(string $times)
    {
        $orgbios = OrgBio::all();
        $short_date = Carbon::now()->format('Y-m-d');

            $boughtItemDetails = BoughtItemDetails::with(['accountRelation','preListRelation','unitRelation'])
            ->where('times',$times)->get();
   
            $boughtItems = BoughtItem::with(['account' => function($query) {
                $query->select('id', 'name');
            }, 'currencyRelation' => function ($query){
                $query->select('id','name','symbols');
            }])->where('times', $times)->get();

            $jexists = Journal::where('times', $times)->exists();

        // return response()->json(['boughtItemDetails' => $boughtItemDetails]);
        // return response()->json(['jexists' => $jexists]);

        return view('buy.bought.details',compact('boughtItemDetails','boughtItems','short_date','orgbios','jexists'));

    }

    public function checkBillNoDuplication(Request $request)
    {
        $exists = BoughtItem::where('billno', $request->billno)->exists();
        return response()->json(['exists' => $exists]);
    }

    /**
     * Show Edit Form
     * http://127.0.0.1:8000/boughtList/edit/1740019057 
     */
    public function edit(string $times)
    {
        $billno =  BoughtItem::max('billno') + 1;   
        $currencies = Currency::select('id','name')->get();
        $ownBanks = Account::select('id','name')->whereIn('account_type_id',[1,6])->orderBy('is_pre_select','DESC')->get();
        $journal_code = Journal::select('code')->where('times',$times)->first();

        if(!$journal_code)
        {
            echo "Journal Code and Branch Id not found";
            die();
        }

        $orgbios = OrgBio::all();
        $boughtItemDetails = BoughtItemDetails::with(['accountRelation','preListRelation','unitRelation'])
        ->where('times',$times)->get();
        $boughtItems = BoughtItem::select('id','billno','times','idate','account_id','currency_id','cur_pay','note')->where('times', $times)->get();

        // return response()->json(['boughtItemDetails' => $boughtItemDetails]);
        // return response()->json(['boughtItems' => $boughtItems]);
        // echo $journal_code;
        // die();

        return view('buy.bought.edit',compact('boughtItemDetails','boughtItems','orgbios','currencies','ownBanks','journal_code'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        // return response()->json(['data' => $request->all()]);
       DB::beginTransaction();
        try 
        {
             $validated = $request->validate([
                'journal_code' => 'required',
                'billno' => 'required|min:1',
                'from_account_id' => 'required',
                'total_price' => 'required',
                'currency_id' => 'required',
            ], [
                'journal_code.required' => __('validate.journal_code_required'),
                'billno.required' => __('validate.billno_required'),
                'from_account_id.required' => __('validate.from_account_id_required'),
                'total_price.required' => __('validate.total_price_required'),
                'currency_id.required' => __('validate.currency_id_required'),
            ]);

            $boughtItem = BoughtItem::where('billno', $request->billno)->first();

            if (!$boughtItem) {
                DB::rollBack();
                Session::put('notification', [
                    'message' => __('common.not_found'),
                    'type' => 'danger',
                ]);
                return redirect()->route('boughtList.details', ['times' => $request->times]);
            }

            // Update bought item
            if ((int)$request->tax_activation === 1) {
                $boughtItem->total_vat = $request->total_price ?? 0;
                $boughtItem->cur_pay = $request->cur_pay ?? 0;
                $boughtItem->remained_vat = $request->remained ?? 0;
            } else {
                $boughtItem->total_price = $request->total_price ?? 0;
                $boughtItem->cur_pay = $request->cur_pay ?? 0;
                $boughtItem->remained = $request->remained ?? 0;
            }
            $boughtItem->account_id = $request->from_account_id;
            $boughtItem->note = $request->note ?? '';
            $boughtItem->save();

            // delete journal records
            Journal::where('times', $request->times)->delete();

            // insert new records instead of updating
            $check = $this->handleJournalEntry($request);

            if(!$check)
            {
                DB::rollBack();
                Session::put('notification', [
                    'message' => __('common.update_failed'),
                    'type' => 'danger',
                ]);
        
                return redirect()->route('boughtList.details', ['times' => $request->times]);
            }

            // Flash success message
            Session::put('notification', [
                'message' => __('common.updated_successfully'),
                'type' => 'success',
            ]);

            DB::commit();
            return redirect()->route('boughtList.details', ['times' => $request->times]);

        } catch (\Exception $e) {
            DB::rollBack();
            // Log the error
            \Log::error('Error occurred during the journal update', ['error' => $e]);
    
            // Flash error message
            Session::put('notification', [
                'message' => __('common.update_failed'),
                'type' => 'danger',
            ]);
    
            return redirect()->route('boughtList.details', ['times' => $request->times]);
        }
    }
    
    
    /**
     * get single record from bought_item_details and amounts from warehouses for edit
     * testing url: http://127.0.0.1:8000/boughtList/getSingleRecordForEdit/
     * id is bought_item_details_id
     */
    public function getSingleRecordForEdit(string $id)
    {
        $units = Unit::select('id','name')->get();
        $tax_activation = OrgBio::select('tax_activation')->first();
        $boughtItemDetails = BoughtItemDetails::with(['accountRelation','preListRelation','unitRelation'])->where('id', $id)->first();

        if (!$boughtItemDetails) {
            return response()->json(['error' => 'Bought Item Details not found'], 404);
        }

        return view('buy.bought.editModalContent', compact('boughtItemDetails', 'units','tax_activation'));
    }



    /**
    * update warehouse_items and bought_item_details
    */
    public function updateItemAndWarehouseItems(Request $request)
    {
        // return response()->json(['formData' => $request->all()]);

        // Validate input data
        $validated = $request->validate([
            'id'                    => 'required|exists:bought_item_details,id',
            'amount'                => 'required|numeric|min:0',
            'buy_up'                => 'required|numeric|min:0',
            'unit_id'               => 'required|exists:units,id',
            'pre_list_id'           => 'required',
            'times'                 => 'required|string',
            "total"                 => 'required|numeric|',
        ]);
        

        DB::beginTransaction();
        try {
            // Update BoughtItemDetails
            $boughtItemDetails = BoughtItemDetails::findOrFail($validated['id']);
            // $boughtItemDetails->update([
            //     'amount' => $validated['amount'],
            //     'buy_up' => $validated['buy_up'],
            //     'pre_list_id' => $validated['pre_list_id'],
            //     'unit_id' => $validated['unit_id'],
            //     'total' => $validated['total'],
            //     "buy_tax_per"  =>  $request->buy_tax_per ?? NULL,
            //     "buy_tax_price" =>  $request->buy_tax_price ?? NULL,
            //     "buy_up_vat" =>  $request->buy_up_vat ?? NULL,
            //     "total_vat" =>  $request->total_vat ?? NULL,
            //     "note"  =>  $request->note ?? '',
            //     "sell_up" =>  $request->sell_up ?? NULL,
            //     "sell_tax_per" =>  $request->sell_tax_per ?? NULL,
            //     "sell_tax_price" =>  $request->sell_tax_price ?? NULL, 
            //     "sell_up_vat" =>  $request->sell_up_vat ?? NULL,
            // ]);

            $boughtItemData['amount'] = $validated['amount'];
            $boughtItemData['buy_up'] = $validated['buy_up'];
            $boughtItemData['pre_list_id'] = $validated['pre_list_id'];
            $boughtItemData['unit_id'] = $validated['unit_id'];
            $boughtItemData['total'] = $validated['total'];
            // Add fields only if they exist in the request
            if ($request->has('buy_tax_per')) { $boughtItemData['buy_tax_per'] = $request->buy_tax_per; }
            if ($request->has('buy_tax_price')) { $boughtItemData['buy_tax_price'] = $request->buy_tax_price; }
            if ($request->has('buy_up_vat')) {  $boughtItemData['buy_up_vat'] = $request->buy_up_vat; }
            if ($request->has('total_vat')) {  $boughtItemData['total_vat'] = $request->total_vat; }
            if ($request->has('note')) {  $boughtItemData['note'] = $request->note; }
            if ($request->has('sell_up')) { $boughtItemData['sell_up'] = $request->sell_up; }
            if ($request->has('sell_tax_per')) { $boughtItemData['sell_tax_per'] = $request->sell_tax_per; }
            if ($request->has('sell_tax_price')) {  $boughtItemData['sell_tax_price'] = $request->sell_tax_price; }
            if ($request->has('sell_up_vat')) { $boughtItemData['sell_up_vat'] = $request->sell_up_vat; }

            $boughtItemDetails->update($boughtItemData);

            // Refresh the model to get the updated value
            $boughtItemDetails->refresh();
            
                /**
                 * If multiple users are updating warehouseItems simultaneously, one update might override another.
                 * Solution: Use optimistic locking with a version column or locking queries:
                 */
                $WarehouseItem = WarehouseItem::where('times', $validated['times'])
                    ->where('buy_pre_id', $validated['pre_list_id'])
                    ->where('unit_id', $validated['unit_id'])
                    ->lockForUpdate() // Prevents race conditions
                    ->first();

                if (!$WarehouseItem) {
                    throw new \Exception('Warehouse item not found');
                }

                $old_amount = (float) $request->old_amount;
                $amount = (float) $request->amount;
                $diff = $amount - $old_amount;
    
                // out_amount: amount that has been sold/used
                // in_amount: total purchased amount
                // available_amount: current stock
                $in_amount = $WarehouseItem->in_amount + $diff;
                $available_amount = $WarehouseItem->available_amount + $diff;
                $out_amount = $WarehouseItem->out_amount; // Keep out_amount as is unless sold
                
                 // Prevent negative values
                if (!$WarehouseItem || $available_amount < 0) {
                   
            
                    Session::put('notification', [
                        'message' => __('common.update_failed'),
                        'type' => 'danger',
                    ]);
                     return redirect()->route('boughtList.edit', ['times' => $validated['times']]);
                }

                // $WarehouseItem->update([
                //     'in_amount' => $in_amount , 
                //     "out_amount" => $out_amount,
                //     "available_amount" => $available_amount,
                //     'buy_up' => $validated['buy_up'],
                //     'unit_id' => $validated['unit_id'],
                //     'total' => $validated['total'],
                //     "buy_tax_per"  =>  $request->buy_tax_per ?? NULL,
                //     "buy_tax_price" =>  $request->buy_tax_price ?? NULL,
                //     "buy_up_vat" =>  $request->buy_up_vat ?? NULL,
                //     "total_vat" =>  $request->total_vat ?? NULL,
                //     "note"  =>  $request->note,
                //     "sell_up" =>  $request->sell_up ?? NULL,
                //     "sell_tax_per" =>  $request->sell_tax_per ?? NULL,
                //     "sell_tax_price" =>  $request->sell_tax_price ?? NULL,
                //     "sell_up_vat" =>  $request->sell_up_vat ?? NULL,
                // ]);

            $WarehouseData['in_amount'] = $in_amount;
            $WarehouseData['out_amount'] = $out_amount;
            $WarehouseData['available_amount'] = $available_amount;
            $WarehouseData['buy_up'] = $validated['buy_up'];
            $WarehouseData['unit_id'] = $validated['unit_id'];
            $WarehouseData['total'] = $validated['total'];
            // Add fields only if they exist in the request
            if ($request->has('buy_tax_per')) { $WarehouseData['buy_tax_per'] = $request->buy_tax_per; }
            if ($request->has('buy_tax_price')) { $WarehouseData['buy_tax_price'] = $request->buy_tax_price; }
            if ($request->has('buy_up_vat')) {  $WarehouseData['buy_up_vat'] = $request->buy_up_vat; }
            if ($request->has('total_vat')) {  $WarehouseData['total_vat'] = $request->total_vat; }
            if ($request->has('note')) {  $WarehouseData['note'] = $request->note; }
            if ($request->has('sell_up')) { $WarehouseData['sell_up'] = $request->sell_up; }
            if ($request->has('sell_tax_per')) { $WarehouseData['sell_tax_per'] = $request->sell_tax_per; }
            if ($request->has('sell_tax_price')) {  $WarehouseData['sell_tax_price'] = $request->sell_tax_price; }
            if ($request->has('sell_up_vat')) { $WarehouseData['sell_up_vat'] = $request->sell_up_vat; }
            $WarehouseItem->update($WarehouseData);

            DB::commit();
            Session::put('notification', [
                'message' => __('common.updated_successfully'),
                'type' => 'success',
            ]);

            return redirect()->route('boughtList.edit', ['times' => $validated['times']]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error in updateItemAndWarehouseItems: ' . $e->getMessage());

            Session::put('notification', [
                'message' => __('common.update_failed'),
                'type' => 'danger',
            ]);

            return redirect()->route('boughtList.edit', ['times' => $validated['times']]);
        }
    }

    


    /**
     * delete a single item during buying form
     */
    public function deleteSingleItem(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            // Get BoughtItemDetails
            $boughtItemDetails = BoughtItemDetails::findOrFail($id);
            $boughtItemDetailsUnitId = $boughtItemDetails->unit_id ?? 0;
            $boughtItemDetailsPreListId = $boughtItemDetails->pre_list_id ?? 0;
            $boughtItemDetailsTimes = $boughtItemDetails->times ?? 0;
            
            // Delete the bought item detail
            $boughtItemDetails->delete();

            // Find and delete the warehouse item
            $WarehouseItem = WarehouseItem::where('times', $boughtItemDetailsTimes)
                ->where('buy_pre_id', $boughtItemDetailsPreListId)
                ->where('unit_id', $boughtItemDetailsUnitId)
                ->first(); // Add ->first() to get the model instance

            // Check if warehouse item exists before deleting
            if ($WarehouseItem) {
                $WarehouseItem->delete();
            }

            DB::commit();

            Session::put('notification', [
                'message' => __('common.deleted_successfully'),
                'type' => 'success',
            ]);

            return redirect()->route('boughtList.edit', ['times' => $boughtItemDetailsTimes]);

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Error deleting records: ' . $e->getMessage());

            Session::put('notification', [
                'message' => __('common.delete_failed'),
                'type' => 'danger',
            ]);

            return redirect()->route('boughtList.edit', ['times' => $boughtItemDetailsTimes ?? 0]);
        }
    }
    

    /**
    * Remove the specified resource from storage.
    */
    public function destroy(string $times)
    {
        DB::beginTransaction();
        try {
            // Delete all related records directly
            WarehouseItem::where('times', $times)->delete();
            BoughtItemDetails::where('times', $times)->delete();
            BoughtItem::where('times', $times)->delete();
            Journal::where('times', $times)->delete();

            DB::commit();
    
            Session::put('notification', [
                'message' => __('common.deleted_successfully'),
                'type' => 'success',
            ]);
    
            return redirect()->route('boughtList.index'); 
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


    // ========================================== INOICES ==========================================

    /**
     * Display invoice list
     */
    public function invoiceList()
    {
        $tax = OrgBio::select('tax_activation')->first();
        return view('buy.invoice.invoice_list',compact('tax'));
    }

    /**
     * Get invoice data for DataTable
     */
    public function getInvoiceData(Request $request)
    {
        $tax_activation = $request->input('tax_activation');
        $invoices = BuyInvoice::with(['supplier', 'currency'])
            ->orderBy('id', 'DESC');

        return DataTables::of($invoices)
            ->addIndexColumn()
            ->addColumn('supplier_name', function($invoice) {
                return $invoice->supplier ? $invoice->supplier->name : '-';
            })
            ->addColumn('total', function($invoice) use ($tax_activation) {
                return (int)$tax_activation === 1 
                    ? number_format($invoice->total_vat ?? 0, 2) 
                    : number_format($invoice->total ?? 0, 2);
            })
            ->addColumn('paid_amount', function($invoice) {
                return number_format($invoice->paid_amount, 2);
            })
            ->addColumn('remaining', function($invoice) use ($tax_activation) {
                 return (int)$tax_activation === 1 
                    ? number_format($invoice->remaining_vat ?? 0, 2) 
                    : number_format($invoice->remaining ?? 0, 2);
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
                return '<a href="' . route('boughtList.showInvoice', $invoice->id) . '" class="btn btn-sm btn-info">
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
        try {
            // id of bought_items table
            $boughtItemIds = $request->bought_item_ids;

            // return ['boughtItemIds' => $boughtItemIds];
            
            if (empty($boughtItemIds)) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('buy.select_at_least_one')
                ]);
            }

            // Get selected bought items
            $boughtItems = BoughtItem::whereIn('id', $boughtItemIds)->get();
            
            if ($boughtItems->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('buy.no_items_found')
                ]);
            }

            // Check if all items belong to same supplier
            $supplierId = $boughtItems->first()->supplier_account_id;
            $differentSupplier = $boughtItems->where('supplier_account_id', '!=', $supplierId)->count() > 0;
            
            if ($differentSupplier) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('buy.different_suppliers')
                ]);
            }

            DB::beginTransaction();

            // Generate invoice number
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . (BuyInvoice::count() + 1);

            // Calculate totals
            $totalAmount = $boughtItems->sum('total_price');
            $totalAmountVat = $boughtItems->sum('total_vat');
            $paidAmount = $boughtItems->sum('cur_pay');
            $remainingAmount = $boughtItems->sum('remained');
            $remainingAmountVat = $boughtItems->sum('remained_vat');

            // Create invoice
            $invoice = BuyInvoice::create([
                'invoice_number' => $invoiceNumber,
                'supplier_id' => $supplierId,
                'total' => $totalAmount,
                'total_vat' => $totalAmountVat,
                'paid_amount' => $paidAmount,
                'remaining' => $remainingAmount,
                'remaining_vat' => $remainingAmountVat,
                'currency_id' => $boughtItems->first()->currency_id,
                'status' => ($remainingAmount || ($remainingAmountVat > 0)) ? 1 : 3, // 0: draft, 1: pending, 2: partial, 3: paid
                'invoice_date' => now(),
                'due_date' => now()->addDays(30),
                'notes' => __('buy.invoice_generated_from_bought_items'),
                'created_by' => auth()->id(),
                'times' => time()
            ]);

            // Create invoice items
            foreach ($boughtItems as $boughtItem) {
                // Get details for this bought item
                $details = BoughtItemDetails::where('bought_item_id', $boughtItem->id)->get();
                
                foreach ($details as $detail) {
                    BuyInvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'bought_item_detail_id' => $detail->id,
                        'bought_item_id' => $boughtItem->id,
                        'pre_list_id' => $detail->pre_list_id,
                        'amount' => $detail->amount,
                        'unit_id' => $detail->unit_id,
                        'unit_price' => $detail->buy_up,
                        'unit_price_vat' => $detail->buy_up_vat ?? 0,
                        'tax_percentage' => $detail->buy_tax_per ?? 0,
                        'tax_amount' => $detail->buy_tax_price ?? 0,
                        'buy_up_vat' => $detail->buy_up_vat ?? 0,
                        'total' => $detail->total,
                        'total_vat' => $detail->total_vat,  
                        'times' => time()
                    ]);
                }
            }

            // Update bought_items to mark as invoiced (you need to add a column)
            BoughtItem::whereIn('id', $boughtItemIds)->update(['has_invoice' => 1,'invoice_id' => $invoice->id]);

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
        $invoice = BuyInvoice::with(['supplier', 'items.unit', 'items.preList', 'payments', 'currency'])
            ->findOrFail($id);
        $suppliers = Account::select('id','name')->whereIn('account_type_id',[4])->get();
        $ownBanks = Account::select('id','name')->whereIn('account_type_id',[1,6])->orderBy('is_pre_select','DESC')->get();
        $newJournalCode =  Journal::max('code') + 1;
        $currencies = Currency::select('id','name')->get();
        // return ['data' => $invoice];

        return view('buy.invoice.invoice_details', compact('invoice','orgbios','suppliers','ownBanks','newJournalCode','times','currencies'));
    }

        /**
         * Add payment to invoice
         */
        // public function addPayment(Request $request)
        // {
        //     // return ['data' => $request->all()];
        //     //     {
        //     //     "data": {
        //     //         "_token": "0RzZ329CcNdYkiVo6DuJ0274As9B4o5JlmMi9xUA",
        //     //         "invoice_id": "3",
        //                 // "tax_activation": "1",
        //     //         "times": "1782532169",
        //     //         "newJournalCode": "4",
        //     //         "supplier_account_id": "91",
        //     //         "account_id": "33",
        //     //         "payment_date": "2026-06-27",
        //     //         "amount": "1000",
        //     //         "payment_method": "1",
        //     //         "currency_id": "1",
        //     //         "reference_number": "123",
        //     //         "notes": "wwwwwwwwww"
        //     //     }
        //     // }
     

        //     try {
        //           $validated = $request->validate([
        //             'invoice_id' => 'required|exists:buy_invoices,id',
        //             'amount' => 'required|numeric|min:0.01',
        //             'payment_method' => 'required|in:1,2,3',
        //             'account_id' => 'required|exists:accounts,id',
        //             'supplier_account_id' => 'required|exists:accounts,id',
        //             'payment_date' => 'required|date',
        //             'reference_number' => 'nullable|string|max:100',
        //             'notes' => 'nullable|string|max:255',
        //             'tax_activation' => 'nullable|in:0,1',
        //             'journal_code' => 'nullable|string',
        //             'times' => 'nullable|string'
        //         ]);

        //         DB::beginTransaction();

        //         $invoice = BuyInvoice::findOrFail($validated['invoice_id']);
                
        //         // Create payment record
        //         $payment = BuyInvoicePayment::create([
        //             'invoice_id' => $invoice->id,
        //             'payment_date' => $validated['payment_date'],
        //             'amount' => $validated['amount'],
        //             'payment_method' => $validated['payment_method'], // 1: cash, 2: bank, 3: loan
        //             'account_id' => $validated['account_id'],
        //             'supplier_account_id' => $validated['supplier_account_id'],
        //             'reference_number' => $validated['reference_number'],
        //             'notes' => $validated['notes'],
        //             'created_by' => auth()->id(),
        //             'times' => time()
        //         ]);

        //         $total_price = $boughtItemDetails->total_sum ?? 0;
        //         $total_vat = $boughtItemDetails->total_vat_sum ?? 0;
                
        //         $cur_pay = (float) $request->cur_pay;
        //         $remained = $total_price - $cur_pay;

        //         // Calculate remained_vat 
        //         if ((int) $request->tax_activation === 1) {
        //             $remained_vat = $total_vat - $cur_pay; 
        //             // If you have separate vat payment, use: $remained_vat = $total_vat - $request->cur_pay_vat;
        //         } else {
        //             $remained_vat = null;
        //         }

        //         // ========================= Update invoice paid amount =================================
        //         $newPaidAmount = $invoice->paid_amount + $validated['amount'];
        //         $newRemainingAmount = $invoice->total - $newPaidAmount;
        //         $status = 0;
        //         if ((int) $request->tax_activation === 1) {
        //             $newRemainingAmountVat = $invoice->total_vat - $newPaidAmount;
        //             $status = $newRemainingAmountVat;
        //         } else {
        //             $newRemainingAmountVat = $invoice->remaining_vat;
        //             $status = $newRemainingAmount;
        //         }
                
        //         $invoice->update([
        //             'paid_amount' => $newPaidAmount,
        //             'remaining' => $newRemainingAmount,
        //             'remaining_vat' => $newRemainingAmountVat,
        //             'status' => $status <= 0 ? 3 : 2 // 3: paid, 2: partial
        //         ]);

        //         // ============================ Update bought_items ======================================

        //          // Get all bought items associated with this invoice
        //         $boughtItems = BoughtItem::where('invoice_id', $invoice->id)->get();
                
        //         if ($boughtItems->isEmpty()) {
        //             DB::rollBack();
        //             return response()->json([
        //                 'status' => 'error',
        //                 'message' => __('common.record_not_found')
        //             ], 404);
        //         }


        //          // Create invoice items
        //         foreach ($BoughtItem as $boughtItem) {
        //             //  check if total_paid_amount yet is less than cur_pay amount
        //             // if($newPaidAmount <= $boughtItem->cur_pay) {
        //             //     $cur_pay = $boughtItem->cur_pay - $newPaidAmount;
        //             // }
        //             //  $cur_pay = $boughtItem->cur_pay
        //              $BoughtItem->update([
        //                 'journal_code' => $request->journal_code,
        //                 'total_price' => $total_price,
        //                 'total_vat' => $total_vat,
        //                 'cur_pay' => $cur_pay,
        //                 'remained' => $remained,
        //                 'remained_vat' => $remained_vat,
        //                 'note' => $request->note ?? '',
        //                 'times' => $request->times,
        //            ]);
        //         }
               

        //         // Also update the bought_items cur_pay and remained
        //         // You can update the related bought_items here

        //          $check = $this->handleJournalEntry($request);
        //         if (!$check) {
        //             DB::rollBack();
        //             Session::put('notification', [
        //                 'message' => __('common.add_failed'),
        //                 'type' => 'danger',
        //             ]);
        //             return redirect()->route('boughtList.index');
        //         }


        //         DB::commit();

        //         return response()->json([
        //             'status' => 'success',
        //             'message' => __('buy.payment_added_successfully'),
        //             'data' => $payment
        //         ]);

        //     } catch (\Exception $e) {
        //         DB::rollBack();
        //         \Log::error('Add Payment Error: ' . $e->getMessage());
        //         return response()->json([
        //             'status' => 'error',
        //             'message' => __('common.error_occurred') . ': ' . $e->getMessage()
        //         ], 500);
        //     }
        // }

        public function addPayment(Request $request)
        {
            try 
            {
                $validated = $request->validate([
                    'invoice_id' => 'required|exists:buy_invoices,id',
                    'amount' => 'required|numeric|min:0.01',
                    'payment_method' => 'required|in:1,2,3',
                    'account_id' => 'required|exists:accounts,id',
                    'supplier_account_id' => 'required|exists:accounts,id',
                    'payment_date' => 'required|date',
                    'reference_number' => 'nullable|string|max:100',
                    'notes' => 'nullable|string|max:255',
                    'journal_code' => 'required',
                    'times' => 'required',
                    'currency_id' => 'required',
                    'tax_activation' => 'nullable|in:0,1'
                ]);

                DB::beginTransaction();

                $invoice = BuyInvoice::findOrFail($validated['invoice_id']);
                $taxActivation = (int) ($request->tax_activation ?? 0);
                $amount = (float) $validated['amount'];
                $invoice_id = substr($invoice->invoice_number, strrpos($invoice->invoice_number, '-') + 1);
                
                // ========================= Update Invoice =================================
                $newPaidAmount = $invoice->paid_amount + $amount;
                $status = 2;

                if ($taxActivation === 1) 
                {
                    $newRemainingVat = $invoice->total_vat - $newPaidAmount;
                    $newRemaining = $invoice->total - $newPaidAmount;

                    if ($invoice->total_vat > $newPaidAmount) 
                    {
                      $status = 2; // partial
                    } else if($invoice->total_vat === $newPaidAmount) {
                         $status = 3; // complete pay
                    }

                } else {
                    $newRemainingVat = null;
                    $newRemaining = $invoice->total - $newPaidAmount;
                    if ($invoice->total > $newPaidAmount) 
                    {
                      $status = 2; // partial
                    } else if($invoice->total === $newPaidAmount) {
                         $status = 3; // complete pay
                    }
                }
                
                $invoice->update([
                    'paid_amount' => $newPaidAmount,
                    'remaining' => $newRemaining,
                    'remaining_vat' => $newRemainingVat,
                    'status' => $status
                ]);

                // ========================= Create Payment Record =================================
                $payment = BuyInvoicePayment::create([
                    'invoice_id' => $invoice->id,
                    'payment_date' => $validated['payment_date'],
                    'amount' => $amount,
                    'payment_method' => $validated['payment_method'],
                    'account_id' => $validated['account_id'],
                    'supplier_account_id' => $validated['supplier_account_id'],
                    'reference_number' => $validated['reference_number'],
                    'notes' => $validated['notes'],
                    'created_by' => auth()->id(),
                    'times' => time()
                ]);

                 // ========================= Update Bought Items =================================
                /**
                 * Example
                 * id       total_price       total_vat       cur_pay           remained          remained_vat
                 * 1        25                  27.5            0                25                 27.5
                 * 2        350                 423.5           0                350               423.5
                 * 
                 * first payment: if new_pay = 20
                 * id       total_price       total_vat       cur_pay           remained          remained_vat
                 * 1        25                  27.5            20                5                 7.5
                 * 2        350                 423.5           0                 350               423.5
                 * 
                 * second payment: if new_pay = 25
                 * id       total_price       total_vat       cur_pay           remained          remained_vat
                 * 1        25                  27.5            25                0                 0             5 decrease, 20 move
                 * 2        350                 423.5           20                330               403.5
                 * 
                 * Formula:  total_price - cur_pay = remained;    total_vat - cur_pay = remained_vat
                 * 
                 * 
                 * 
                 * third payment: if new_pay = 330
                 * id       total_price       total_vat       cur_pay           remained          remained_vat
                 * 1        25                  27.5            25                0                 0             
                 * 2        350                 423.5           350               0                73.5
                 * 
                 * 
                 */
                     $boughtItems = BoughtItem::where('invoice_id', $invoice->id)
                        ->orderBy('id', 'ASC')
                        ->get();

                    if ($boughtItems->isEmpty()) {
                        DB::rollBack();
                        return response()->json([
                            'status' => 'error',
                            'message' => __('common.record_not_found')
                        ], 404);
                    }

                    $remainingToDistribute = (float) $amount;
                    $totalDistributed = 0;

                    // Calculate total remaining for all items first
                    $totalRemaining = 0;
                    foreach ($boughtItems as $item) {
                        $itemRemaining = (float) ($item->total_price ?? 0) - (float) ($item->cur_pay ?? 0);
                        $totalRemaining += $itemRemaining;
                    }

                    // If payment amount exceeds total remaining, cap it
                    if ($remainingToDistribute > $totalRemaining) {
                        $remainingToDistribute = $totalRemaining;
                        // Optionally: create a notification that overpayment is not allowed
                    }

                    // Distribute payment across bought items
                    foreach ($boughtItems as $boughtItem) {
                        if ($remainingToDistribute <= 0) {
                            break;
                        }

                        // Get current values
                        $itemTotalPrice = (float) ($boughtItem->total_price ?? 0);
                        $itemTotalVat = (float) ($boughtItem->total_vat ?? 0);
                        $itemCurrentPaid = (float) ($boughtItem->cur_pay ?? 0);
                        
                        // Calculate remaining amounts for this item
                        $itemRemainingPrice = $itemTotalPrice - $itemCurrentPaid;
                        $itemRemainingVat = $itemTotalVat - $itemCurrentPaid;

                        // Skip if item is fully paid
                        if ($itemRemainingPrice <= 0) {
                            continue;
                        }

                        // Calculate amount to allocate to this item
                        $allocatedAmount = 0;
                        if ($remainingToDistribute >= $itemRemainingPrice) {
                            // Full payment for this item
                            $allocatedAmount = $itemRemainingPrice;
                            $remainingToDistribute -= $itemRemainingPrice;
                        } else {
                            // Partial payment for this item
                            $allocatedAmount = $remainingToDistribute;
                            $remainingToDistribute = 0;
                        }

                        // Calculate new values
                        $newCurPay = $itemCurrentPaid + $allocatedAmount;
                        $newRemainingPrice = max(0, $itemTotalPrice - $newCurPay);
                        $newRemainingVat = max(0, $itemTotalVat - $newCurPay);

                        // Update ONLY these fields: cur_pay, remained, remained_vat
                        $boughtItem->cur_pay = $newCurPay;
                        $boughtItem->remained = $newRemainingPrice;
                        $boughtItem->remained_vat = $taxActivation === 1 ? $newRemainingVat : null;
                        $boughtItem->save();

                        $totalDistributed += $allocatedAmount;

                        // Log for debugging
                        // \Log::info('Payment Distribution:', [
                        //     'item_id' => $boughtItem->id,
                        //     'total_price' => $itemTotalPrice,
                        //     'total_vat' => $itemTotalVat,
                        //     'old_cur_pay' => $itemCurrentPaid,
                        //     'allocated' => $allocatedAmount,
                        //     'new_cur_pay' => $newCurPay,
                        //     'new_remained' => $newRemainingPrice,
                        //     'new_remained_vat' => $newRemainingVat,
                        //     'remaining_to_distribute' => $remainingToDistribute
                        // ]);
                    }

                    // After distribution, verify if any amount remains undistributed
                    if ($remainingToDistribute > 0) {
                        \Log::warning('Payment amount not fully distributed', [
                            'remaining' => $remainingToDistribute,
                            'total_paid' => $amount,
                            'distributed' => $totalDistributed
                        ]);
                    }
                

                $date = $request->payment_date 
                    ? Carbon::parse($request->payment_date) 
                    : Carbon::now();

                $time = $request->times ?? '00:00:00';
                $full_date = $date->format('Y-m-d') . ' ' . $time;

                $request->merge([
                    'bill_no' => 0,
                    'idate' => $date,
                ]);

                // ثبت پرداخت توسط پرداخت کننده = paid(ttype=2), cache(ptype=1) 
                $details =  __('validate.cache_payment_invoice').' INV_'.$invoice_id;
                $status = 9; // buy invoice
                $optionLabel = __('validate.inv_pay'); $dynamic_type = 2; $dt_comment = 'Invoice';
                $check1 = $this->createJournalEntry($request,  $optionLabel, $request->account_id,  $amount, $ttype = "2", $ptype="1", $date, $full_date, $details, $dynamic_type, $dt_comment, $status);

                // ثبت دریافت توسط دریافت کننده = recieved(ttype=1) cache(ptype=1)
                $details2 =  __('validate.cache_recieved_invoice').' INV_'.$invoice_id;
                $optionLabel = __('validate.inv_rec'); $dynamic_type2 = 2; $dt_comment = 'Invoice';
                $check12 = $this->createJournalEntry($request,  $optionLabel, $request->supplier_account_id,  $amount, $ttype = "1", $ptype="1", $date, $full_date, $details2, $dynamic_type2, $dt_comment, $status);

                if (!$check1 || !$check12) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => __('common.add_failed')
                    ], 500);
                }

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'message' => __('buy.payment_added_successfully'),
                    'data' => $payment
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Add Payment Error: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json([
                    'status' => 'error',
                    'message' => __('common.error_occurred') . ': ' . $e->getMessage()
                ], 500);
            }
        }



        public function addPaymentNew(Request $request)
    {
        try 
        {
            $validated = $request->validate([
                'invoice_id' => 'required|exists:buy_invoices,id',
                'amount' => 'required|numeric|min:0.01',
                'payment_method' => 'required|in:1,2,3',
                'account_id' => 'required|exists:accounts,id',
                'supplier_account_id' => 'required|exists:accounts,id',
                'payment_date' => 'required|date',
                'reference_number' => 'nullable|string|max:100',
                'notes' => 'nullable|string|max:255',
                'journal_code' => 'required',
                'times' => 'required',
                'currency_id' => 'required',
                'tax_activation' => 'nullable|in:0,1'
            ]);

            DB::beginTransaction();

            $invoice = BuyInvoice::findOrFail($validated['invoice_id']);
            $taxActivation = (int) ($request->tax_activation ?? 0);
            $amount = (float) $validated['amount'];
            $invoice_id = substr($invoice->invoice_number, strrpos($invoice->invoice_number, '-') + 1);
            
            // ========================= Update Invoice =================================
            $newPaidAmount = $invoice->paid_amount + $amount;
            
            if ($taxActivation === 1) {
                $newRemainingVat = $invoice->total_vat - $newPaidAmount;
                $newRemaining = $invoice->total - $newPaidAmount;
            } else {
                $newRemainingVat = null;
                $newRemaining = $invoice->total - $newPaidAmount;
            }
            
            // Determine status
            if ($newPaidAmount >= $invoice->total && ($taxActivation !== 1 || $newPaidAmount >= $invoice->total_vat)) {
                $status = 3; // Fully paid
            } elseif ($newPaidAmount > 0) {
                $status = 2; // Partial
            } else {
                $status = 1; // Pending
            }
            
            $invoice->update([
                'paid_amount' => $newPaidAmount,
                'remaining' => max(0, $newRemaining),
                'remaining_vat' => $taxActivation === 1 ? max(0, $newRemainingVat) : null,
                'status' => $status
            ]);

            // ========================= Create Payment Record =================================
            $payment = BuyInvoicePayment::create([
                'invoice_id' => $invoice->id,
                'payment_date' => $validated['payment_date'],
                'amount' => $amount,
                'payment_method' => $validated['payment_method'],
                'account_id' => $validated['account_id'],
                'supplier_account_id' => $validated['supplier_account_id'],
                'reference_number' => $validated['reference_number'],
                'notes' => $validated['notes'],
                'created_by' => auth()->id(),
                'times' => time()
            ]);

            // ========================= Update Bought Items =================================
            $boughtItems = BoughtItem::where('invoice_id', $invoice->id)
                ->orderBy('id', 'ASC')
                ->get();

            if ($boughtItems->isEmpty()) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => __('common.record_not_found')
                ], 404);
            }

            //  Check if single or multiple records
            $itemsCount = $boughtItems->count();
            $remainingPayment = (float) $amount;

            if ($itemsCount === 1) {
                // =============================================
                // SINGLE RECORD - Apply payment directly
                // =============================================
                $boughtItem = $boughtItems->first();
                
                $itemTotalPrice = (float) $boughtItem->total_price;
                $itemTotalVat = (float) $boughtItem->total_vat;
                $itemCurrentPaid = (float) $boughtItem->cur_pay;
                
                // Calculate new values
                $newCurPay = $itemCurrentPaid + $amount;
                $newRemainingPrice = max(0, $itemTotalPrice - $newCurPay);
                $newRemainingVat = $taxActivation === 1 
                    ? max(0, $itemTotalVat - $newCurPay) 
                    : null;
                
                // Update single item
                $boughtItem->update([
                    'cur_pay' => $newCurPay,
                    'remained' => $newRemainingPrice,
                    'remained_vat' => $newRemainingVat !== null ? $newRemainingVat : null,
                    'status' => $newRemainingPrice <= 0.01 ? 3 : 2
                ]);
            } 
            else 
            {
                // =============================================
                // MULTIPLE RECORDS - Distribute payment sequentially
                // =============================================
                    $totalDistributed = 0;
                    $distributionLog = [];

                    // Calculate total remaining for validation
                    $totalRemaining = $boughtItems->sum(function ($item) {
                        return max(0, (float) $item->total_price - (float) $item->cur_pay);
                    });

                    if ($remainingPayment > $totalRemaining) {
                        DB::rollBack();
                        return response()->json([
                            'status' => 'error',
                            'message' => __('buy.payment_exceeds_remaining', [
                                'remaining' => number_format($totalRemaining, 2)
                            ])
                        ], 422);
                    }

                    foreach ($boughtItems as $index => $boughtItem) 
                    {
                        if ($remainingPayment <= 0) {
                            break;
                        }
                        
                        $itemTotalPrice = (float) ($boughtItem->total_price ?? 0);
                        $itemTotalVat = (float) ($boughtItem->total_vat ?? 0);
                        $itemCurrentPaid = (float) ($boughtItem->cur_pay ?? 0);
                        $itemRemainingPrice = max(0, $itemTotalPrice - $itemCurrentPaid);
                        
                        if ($itemRemainingPrice <= 0.01) {
                            continue;
                        }
                        
                        // Calculate allocation
                        $allocatedAmount = min($remainingPayment, $itemRemainingPrice);
                        $remainingPayment -= $allocatedAmount;
                        $totalDistributed += $allocatedAmount;
                        
                        // Calculate new values
                        $newCurPay = round($itemCurrentPaid + $allocatedAmount, 2);
                        $newRemainingPrice = round($itemTotalPrice - $newCurPay, 2);
                        $newRemainingVat = $taxActivation === 1 
                            ? round($itemTotalVat - $newCurPay, 2) 
                            : null;
                    

                        if ($newCurPay <= 0) {
                            $status = 1;       // unpaid
                        } elseif ($newRemainingPrice <= 0) {
                            $status = 3;       // paid
                        } else {
                            $status = 2;       // partial
                        }

                        $boughtItem->update([
                            'cur_pay'       => $newCurPay,
                            'remained'      => max(0, $newRemainingPrice),
                            'remained_vat'  => $taxActivation ? max(0, $newRemainingVat) : null,
                            'status'        => $status,
                        ]);

                        
                        // Log distribution
                        $distributionLog[] = [
                            'item_id' => $boughtItem->id,
                            'allocated' => $allocatedAmount,
                            'new_cur_pay' => $newCurPay,
                            'new_remained' => $newRemainingPrice
                        ];
                    }

                    // Verify all payment was distributed
                    // if ($remainingPayment > 0.01) {
                    //     \Log::warning('Payment not fully distributed', [
                    //         'amount' => $amount,
                    //         'distributed' => $totalDistributed,
                    //         'remaining' => $remainingPayment
                    //     ]);
                    // }
            }

            // ========================= Journal Entries =================================
            $date = $request->payment_date 
                ? Carbon::parse($request->payment_date) 
                : Carbon::now();

            $time = $request->times ?? '00:00:00';
            $full_date = $date->format('Y-m-d') . ' ' . $time;

            $request->merge([
                'bill_no' => 0,
                'idate' => $date,
            ]);

            // Payment from account (Paid)
            $details = __('validate.cache_payment_invoice') . ' INV_' . $invoice_id;
            $status = 9; // buy invoice
            $optionLabel = __('validate.inv_pay');
            $dynamic_type = 2;
            $dt_comment = 'Invoice';
            
            $check1 = $this->createJournalEntry( $request, $optionLabel, $request->account_id,  $amount, 
                "2", "1", $date, $full_date, $details, $dynamic_type, $dt_comment, $status
            );

            // Received by supplier
            $details2 = __('validate.cache_recieved_invoice') . ' INV_' . $invoice_id;
            $optionLabel = __('validate.inv_rec');
            
            $check2 = $this->createJournalEntry(
                $request,  $optionLabel, $request->supplier_account_id, $amount, 
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
                'message' => __('buy.payment_added_successfully'),
                'data' => [
                    'payment' => $payment,
                    'invoice' => $invoice->fresh(),
                    'bought_items' => $boughtItems->fresh(),
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



    }

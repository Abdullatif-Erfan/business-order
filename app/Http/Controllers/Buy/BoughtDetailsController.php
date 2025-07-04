<?php

namespace App\Http\Controllers\Buy;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
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
use App\Models\Buy\BoughtItemDetails; 
use App\Models\Transaction\Journal;
use App\Models\Setting\Warehouse;
use App\Models\Warehouse\WarehouseItem;

use App\Models\Setting\Account;
use Yajra\DataTables\Facades\DataTables;


class BoughtDetailsController extends Controller
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
        $branches = Branch::where('id',$this->branch_id)->get();
        $orgbios = OrgBio::all();
        $todaysDate = Jalalian::now()->format('Y-m-d');

        return view('buy.bought.list',compact('currencies','branches','todaysDate','orgbios'));
    }


    public function getData(Request $request)
    {
            $boughtItems = BoughtItem::with(['currencyRelation', 'customerRelation'])->where('branch_id', $this->branch_id)->orderBy('id', 'DESC');
            
              // Apply filters if provided
              if ($request->customer_name) {
                $boughtItems->whereHas('customerRelation', function ($query) use ($request) {
                    $query->where('name', 'LIKE', "%{$request->customer_name}%");
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

            ->addColumn('total_price', function ($boughtItem) {
                $total_price = $boughtItem->total_price;
                // return (fmod($total_price, 1) == 0) ? number_format($total_price, 0) : number_format($total_price, 2);
                return  number_format($total_price, 2);

            })

            ->addColumn('trans_spend', function ($boughtItem) {
                $trans_spend = $boughtItem->trans_spend;
                return (fmod($trans_spend, 1) == 0) ? number_format($trans_spend, 0) : number_format($trans_spend, 2);
            })

            ->addColumn('discount', function ($boughtItem) {
                $discount = $boughtItem->discount;
                return (fmod($discount, 1) == 0) ? number_format($discount, 0) : number_format($discount, 2);
            })

            ->addColumn('payable', function ($boughtItem) {
                $payable = $boughtItem->payable;
                return (fmod($payable, 1) == 0) ? number_format($payable, 0) : number_format($payable, 2);
            })

            ->addColumn('cur_pay', function ($boughtItem) {
                $cur_pay = $boughtItem->cur_pay;
                return (fmod($cur_pay, 1) == 0) ? number_format($cur_pay, 0) : number_format($cur_pay, 2);
            })

            ->addColumn('remained', function ($boughtItem) {
                $remained = $boughtItem->remained;
                return (fmod($remained, 1) == 0) ? number_format($remained, 0) : number_format($remained, 2);
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
        // account_id 3 and 4 is belongs to customers and sellers
        // TODO : filter by branch_id
        $warehouses = Warehouse::select('id','name')->where('branch_id', $this->branch_id)->get();
        $customers = Account::select('id','name')->whereIn('account_type_id',[3,4])->where('branch_id', $this->branch_id)->get();
        $ownBanks = Account::select('id','name')->whereIn('account_type_id',[1,6])->where('branch_id', $this->branch_id)->orderBy('is_pre_select','DESC')->get();
        $billno =  BoughtItem::where('branch_id', $this->branch_id)->max('billno') + 1;
    
        $preLists = BuyPreList::select('id','name','code','branch_id')->where('branch_id', $this->branch_id)->get();
        // TODO : filter by branch_id
        $todaysDate = Jalalian::now()->format('Y-m-d');
        $units = Unit::select('id','name')->get();
        $newJournalCode =  Journal::where('branch_id', $this->branch_id)->max('code') + 1;

        $times = time();

        // return response()->json($preLists);
        return view('buy.bought.create',compact('currencies','customers','todaysDate','ownBanks','preLists','units','warehouses','times','newJournalCode','billno'));
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

        $short_date = $request->todays_date ?? Jalalian::now()->format('Y-m-d');
        $date = explode('-', $short_date);
        $year = $date[0];
        $month = $date[1];
        $day = $date[2];
        $full_date =  $year.'-'.$month.'-'.$day.' '.Date('H:i:s A');
        $times = $request->times;
        
        $preLists = BuyPreList::select('branch_id','name')->where('id',$request->pre_list_id)->first();
        $branch_id = $this->branch_id ?? $preLists->branch_id;
        $item_name = $preLists->name ?? '';

        // Start the transaction
        DB::beginTransaction();
   
        try {
        
        // 1: insert in to bought_items table
        $BoughtItemId = $this->createOrUpdateBoughtItem($request, $short_date, $branch_id, $times);

        // 2: insert in to bought_item_details table
        $this->storeBoughtItemDetails($request, $BoughtItemId,  $branch_id, $times);

        // 3: insert or update warehouse_items
        $this->createOrUpdateWarehouseItems($request, $branch_id);

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
    
   
    private function createOrUpdateBoughtItem($request, $short_date, $branch_id, $times)
    {
        $date = explode('-', $short_date);
        $year = $date[0];
        $month = $date[1];
        $day = $date[2];
        
        // $note = "Total Payable: " . ($request->payable ?? 0) . ", Paid: " . ($request->cur_pay ?? 0) . ", Remained: " . ($request->remained ?? 0);

        // Check if a record with the same billno exists
        $BoughtItem = BoughtItem::where('billno', $request->billno)->first();

        if ($BoughtItem) {
            \Log::info('updating BoughtItem');
            // ✅ Update existing record
            $BoughtItem->update([
                'branch_id'           => $branch_id,
                'total_price'         => $request->total_price ?? 0,
                'discount'            => $request->discount ?? 0,
                'payable'             => $request->payable ?? 0,
                'cur_pay'             => $request->cur_pay ?? 0,
                'remained'            => $request->remained ?? 0,
                'currency_id'         => $request->currency_id ?? 0,
                'trans_spend'         => $request->trans_spend ?? 0,
                'account_id'          => $request->from_account_id,
                'customer_account_id' => $request->customer_account_id,
                'note'                => $request->note ?? '',
                'is_cleared'         => 0,
            ]);
        } else {
            // ✅ Create new record
            \Log::info('Inserting BoughtItem');
            $BoughtItem = BoughtItem::create([
                'branch_id'           => $branch_id,
                'factor'              => $request->factor ?? 0,
                'billno'              => $request->billno,
                'journal_code'        => $request->journal_code ?? 0,
                'total_price'         => $request->total_price ?? 0,
                'discount'            => $request->discount ?? 0,
                'payable'             => $request->payable ?? 0,
                'cur_pay'             => $request->cur_pay ?? 0,
                'remained'            => $request->remained ?? 0,
                'account_id'          => $request->from_account_id,
                'customer_account_id' => $request->customer_account_id,
                'currency_id'         => $request->currency_id,
                'trans_spend'         => $request->trans_spend ?? 0,
                'note'                => $request->note ?? '',
                'idate'               => $short_date,
                'year'                => $year,
                'month'               => $month,
                'day'                 => $day,
                'iby'                 => auth()->user()->full_name ?? '',
                'times'               => $times,
                'is_cleared'         => 0,
            ]);
        }

        return $BoughtItem->id;
    }


    private function storeBoughtItemDetails($request, $boughtItemId,  $branch_id, $times)
    {
        // If not exists, create new record
        return BoughtItemDetails::create([
            'billno' => $request->billno,
            'bought_item_id' => $boughtItemId,
            'pre_list_id' => $request->pre_list_id,
            'customer_account_id' => $request->customer_account_id,
            'amount' => $request->amount,
            'unit_id' => $request->unit_id,
            'bought_up' => $request->bought_up,
            'discount' => $request->discount ?? 0,
            'transport' => $request->transport ?? 0,
            'total' => $request->amount * $request->bought_up,
            'expire_date' => $request->expire_date,
            'is_moved' => 1,
            'times' => $times
        ]);
        return true;
    }
    

    private function createOrUpdateWarehouseItems($request, $branch_id)
    {
        /**
         * 1: Check based on (buy_pre_id and warehouse_id) 
         * 2: If record exists, update it
         * 3: If it doesn't exist, insert a new item
         */
    
        $short_date = $request->todays_date ?? Jalalian::now()->format('Y-m-d');
        [$year, $month, $day] = explode('-', $short_date);
        $insertedBy = auth()->user()->full_name ?? '';
    
        // Prepare bulk insert/update data
        $warehouseItemsToInsert = [];
        $warehouseItemsToUpdate = [];
    
        /***
         * first:  amount = 30; bought_up = 100; total = 30 * 100 = 3000;
         * second: amount = 10;  bought_up = 150; total = 10  *  150 = 1500; 
         * find out the new bought unit price ?
         * first_total + second_total  divided by amounts, new_unit_price = ((3000 + 1500) / 40) = 112.5
         */

        foreach ($request->warehouse_id as $index => $warehouseId) 
        {
            $WarehouseItem = WarehouseItem::where('warehouse_id', $warehouseId)
                ->where('buy_pre_id', $request->pre_list_id)
                ->where('unit_id', $request->unit_id)
                ->where('branch_id', $branch_id)
                ->first();
    
            $warehouseAmount = $request->warehouse_amount[$index];
            $new_total = $request->bought_up * $warehouseAmount; // Cost of new stock
            
    
            if ($WarehouseItem) 
            {

                /**
                 * if available_amount is greater than zero udate with new average unit price
                 * else update with new unit_price without calculating avg price
                 */

                /**
                 * TODO : this line $new_available_total = $available_amounts * $new_avg_up; do not provide the exact total
                 */

                if($WarehouseItem->available_amount > 0)
                {
                    $available_amounts = $WarehouseItem->available_amount + $warehouseAmount;
                    $new_avg_up = ($available_amounts > 0) ? (($WarehouseItem->available_total + $new_total) / $available_amounts) : 0;
                    $new_available_total = $available_amounts * $new_avg_up;
                }
                else
                {
                    $available_amounts =  (float) $warehouseAmount;
                    $new_avg_up =  (float) $request->bought_up;
                    $new_available_total =  $available_amounts * $new_avg_up;
                }
             

                // Update existing warehouse item
                $warehouseItemsToUpdate[] = [
                    'id' => $WarehouseItem->id,
                    'in_amount' => $WarehouseItem->in_amount + $warehouseAmount,
                    'available_amount' => $available_amounts,
                    'wastage_amount' => $WarehouseItem->wastage_amount,
                    'wastage_total' => $WarehouseItem->wastage_total,
                    'bought_up' => $request->bought_up,
                    'avg_up' => $new_avg_up,
                    'total' => $WarehouseItem->total + $new_total,
                    'available_total' => $new_available_total,
                    'sell_up' => $request->warehouse_sell_up[$index],
                    'notification_amount' => $request->notification_amount ?? 0,
                    'inserted_by' => $insertedBy,
                    'expire_date' => $request->expire_date ?? null,
                    'times' => $request->times,
                    'is_cleared' => 0,
                ];   
            } 
            else 
            {
                // Insert new warehouse item
                $warehouseItemsToInsert[] = [
                    'warehouse_id' => $warehouseId,
                    'buy_pre_id' => $request->pre_list_id,
                    'name' => $request->item_name ?? '',
                    'in_amount' => $warehouseAmount,
                    'out_amount' => 0.00,
                    'available_amount' => $warehouseAmount,
                    'wastage_amount' => $request->wastage_amount ?? 0,
                    'wastage_total' => $request->wastage_total ?? 0,
                    'unit_id' => $request->unit_id,
                    'bought_up' => $request->bought_up,
                    'avg_up' => $request->bought_up,
                    'sell_up' => $request->warehouse_sell_up[$index],
                    'total' => $new_total,
                    'available_total' => $new_total,
                    'currency_id' => $request->currency_id,
                    'notification_amount' => $request->notification_amount ?? 0,
                    'inserted_by' => $insertedBy,
                    'branch_id' => $branch_id ?? 0,
                    'expire_date' => $request->expire_date ?? null,
                    'inserted_short_date' => $short_date,
                    'year' => $year,
                    'month' => $month,
                    'day' => $day,
                    'times' => $request->times,
                    'is_cleared' => 0,
                ];
            }
        }
    
        // Bulk update existing records
        if (!empty($warehouseItemsToUpdate)) {
            foreach ($warehouseItemsToUpdate as $updateData) {
                WarehouseItem::where('id', $updateData['id'])->update($updateData);
            }
        }
    
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
            'billno' => 'required|numeric',
            'total_price' => 'required',
            'discount' => 'required',
            'trans_spend' => 'required',
            'payable' => 'required',
            'cur_pay' => 'required',
            'remained' => 'required',
        ]);
    
        // Use proper null coalescing to avoid precedence issues
        // $note = "Total Payable: ".($request->payable ?? 0).", Paid: ".($request->cur_pay ?? 0).", Remained: ".($request->remained ?? 0);
        
        // Ensure you're updating a specific record by using the where clause first
        $BoughtItem = BoughtItem::where('billno', $request->billno)->where('branch_id', $this->branch_id)->first();
    
    
        try {
            // Update BoughtItem record
            $BoughtItem->update([
                'journal_code' => $request->journal_code,
                'total_price' => $request->total_price,
                'discount' => $request->discount,
                'payable' => $request->payable,
                'cur_pay' => $request->cur_pay,
                'remained' => $request->remained,
                'trans_spend' => $request->trans_spend,
                'note' => $request->note ?? '',
                'times' => $request->times,
            ]);
    
            // Insert into journal
            $check = $this->handleJournalEntry($request);
            if(!$check)
            {
                DB::rollBack();
                Session::put('notification', [
                    'message' => __('common.add_failed'),
                    'type' => 'danger',
                ]);
    
                return redirect()->route('boughtList.index');

            }
     
               // Flash success message
                Session::put('notification', [
                     'message' => __('common.added_successfully'),
                    'type' => 'success',
                ]);
    
                return redirect()->route('boughtList.index');
           
    
        } catch (\Exception $e) {

            // Log the error
            \Log::error('Error occurred during the transaction', ['error' => $e]);
    
            // $this->deleteBoughtRecords($request);
    
            // Flash error message
            Session::put('notification', [
                'message' => __('common.add_failed'),
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
            'pre_list_id' => 'required|integer',
            'amount' => 'required|numeric|min:0.01',
            'unit_id' => 'required|integer',
            'bought_up' => 'required|numeric|min:0.01',
            'billno' => 'required|integer|min:0',
            'customer_account_id' => 'required|integer',
            'from_account_id' => 'required|integer',
            'currency_id' => 'required|integer',
            'warehouse_id' => 'required|array',
            'warehouse_id.*' => 'exists:warehouses,id',
            'warehouse_amount' => 'required|array',
            'warehouse_amount.*' => 'numeric',
            'warehouse_sell_up' => 'required|array',
            'warehouse_sell_up.*' => 'numeric',
        ], [
            'pre_list_id.required' => ' نام جنس از  فهرست الزامی است.',
        
            'amount.required' => 'تعداد جنس الزامی است.',
            'amount.numeric' => 'تعداد جنس باید عدد باشد.',
        
            'unit_id.required' => 'انتخاب واحد جنس الزامی است.',
            'unit_id.integer' => 'شناسه واحد باید عدد صحیح باشد.',
        
            'bought_up.required' => 'قیمت خرید الزامی است.',
            'bought_up.numeric' => 'قیمت خرید باید عدد باشد.',
            'bought_up.min' => 'قیمت خرید باید حداقل 0.01 باشد.',
        
            'billno.required' => 'بل نمبر  الزامی است ',
            'billno.integer' => 'بل نمبر باید عدد صحیح باشد.',
            'billno.min' => 'بل نمبر نمی‌تواند منفی باشد.',
        
            'customer_account_id.required' => 'انتخاب حساب مشتری الزامی است.',
            'customer_account_id.integer' => 'شناسه حساب مشتری باید عدد صحیح باشد.',
        
            'from_account_id.required' => 'انتخاب حساب شرکت الزامی است.',
        
            'currency_id.required' => 'انتخاب واحد پول الزامی است.',
            'currency_id.integer' => 'شناسه واحد پول باید عدد صحیح باشد.',
        
            'warehouse_id.required' => 'حداقل یک گدام را انتخاب کنید.',
            'warehouse_id.array' => 'فرمت گدام‌ها نادرست است.',
            'warehouse_id.*.exists' => 'انتخاب گدام الزامی است.',
        
            'warehouse_amount.required' => 'تعداد انتقال الزامی است.',
            'warehouse_amount.array' => 'فرمت تعداد انتقال نادرست است.',
            'warehouse_amount.*.numeric' => 'تعداد انتقال باید عدد باشد.',
        
            'warehouse_sell_up.required' => 'قیمت فروش الزامی است.',
            'warehouse_sell_up.array' => 'فرمت قیمت‌های فروش نادرست است.',
            'warehouse_sell_up.*.numeric' => 'قیمت فروش باید عدد باشد.',
        ]);
        
    }


    private function handleJournalEntry($request)
    {
            $date = explode('-', $request->todays_date);
            $year = $date[0];
            $month = $date[1];
            $day = $date[2];
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
             * خزانه باید قرضدار ثبت گردد = Recieved Loan 
             * مشتری باید طلب ثبت گردد = paid Loan 
             */
            if(intval($request->cur_pay) === 0 && intval($request->remained) === intval($request->payable))
            { 
                // ثبت قرضه خزانه = recieved(ttype=1) loan(ptype=2)
                $details =  ' قرضه خرید - بل '.' BUY_'.$request->billno;
                $optionLabel = 'قرضه خرید'; $dynamic_type = 2; $dt_comment = 'clearable';
                $this->createJournalEntry($request,  $optionLabel, $request->from_account_id,  $request->payable, $ttype = "1", $ptype="2", $date, $full_date, $details, $dynamic_type, $dt_comment);
                
                // ثبت طلب مشتری = paid(ttype=2), loan(ptype=2) 
                $details =  ' طلب خرید - بل '.' BUY_'.$request->billno;
                $optionLabel = 'طلب خرید'; $dynamic_type = 2; $dt_comment = 'clearable';
                $this->createJournalEntry($request, $optionLabel, $request->customer_account_id,  $request->payable,
                 $ttype = "2", $ptype="2", $date, $full_date, $details, $dynamic_type, $dt_comment);
            }

            // کمی شانرا پرداخت کرده و متباقی شانرا قرض انتخاب کرده است
            else if(intval($request->remained) > 0 && intval($request->cur_pay) > 0) 
            {
                // ثبت پرداخت نقدی خزانه = Cache paid
                $details =  'پرداخت خرید - بل  '.' BUY_'.$request->billno;
                $optionLabel = 'پرداخت نقد'; $dynamic_type = 0; $dt_comment = 'not clearable';
                $this->createJournalEntry($request, $optionLabel, $request->from_account_id, $request->cur_pay, $ttype = "2", $ptype="1", $date, $full_date, $details, $dynamic_type, $dt_comment);

                // ثبت قرضه خزانه = Loan Recieved 
                $details =  ' قرضه خرید - بل '.' BUY_'.$request->billno;
                $optionLabel = 'قرضه خرید'; $dynamic_type = 2; $dt_comment = 'clearable';
                $this->createJournalEntry($request, $optionLabel, $request->from_account_id, $request->remained,  
                $ttype = "1", $ptype="2", $date, $full_date, $details, $dynamic_type, $dt_comment);
               
                // ثبت طلب مشتری = Paid Loan
                $details =  ' طلب خرید - بل '.' BUY_'.$request->billno;
                $optionLabel = 'طلب خرید'; $dynamic_type = 2; $dt_comment = 'clearable';
                $this->createJournalEntry($request, $optionLabel,  $request->customer_account_id, $request->remained,
                $ttype = "2", $ptype="2", $date, $full_date, $details, $dynamic_type, $dt_comment);
            }

             // قرضدار نمانده است و مکمل پرداخت کرده است
             // تنها از حساب خزانه کم شود
            else if(intval($request->remained) === 0 && intval($request->cur_pay) === intval($request->payable)) 
            {
                // ثبت پرداخت نقدی خزانه = Cache paid
                $details =  'پرداخت خرید - بل  '.' BUY_'.$request->billno;
                $optionLabel = 'پرداخت نقد'; $dynamic_type = 0; $dt_comment = 'not clearable';
                $this->createJournalEntry($request, $optionLabel, $request->from_account_id, $request->cur_pay, $ttype = "2", $ptype="1", $date, $full_date, $details, $dynamic_type, $dt_comment);
            }

            // ثبت مصارف ترانسپورت به روش فعلی هروقت که بزرگتر از صفر بود باید از حساب خزانه کم شود
            if(intval($request->trans_spend) > 0 && intval($request->from_account_id) > 0) 
            {
                // رفت پول نقد از بابت ترانسپورت = Cache paid
                $details =  'پرداخت مصارف ترانسپورت - بل  '.' BUY_'.$request->billno;
                $optionLabel = 'مصارف ترانسپورت'; $dynamic_type = 0; $dt_comment = 'not clearable';
                $this->createJournalEntry($request, $optionLabel, $request->from_account_id, $request->trans_spend, $ttype = "2", $ptype="1", $date, $full_date, $details, $dynamic_type, $dt_comment);
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

    private function createJournalEntry($request, $optionLabel, $account_id, $amount, $ttype, $ptype, $date, $full_date, $details, $dynamic_type, $dt_comment)
    {
        $account_type_id = Account::where('id', $account_id)->value('account_type_id');

        Journal::create([
            'bill_no' => $request->billno,
            'code' =>  $request->journal_code,
            'account_type_id' => $account_type_id,
            'account_id' => $account_id,
            'branch_id' => $this->branch_id ?? $request->branch_id,
            'amount' => $amount,
            'currency_id' => $request->currency_id,
            'transaction_type' => $ttype,
            'payment_type' => $ptype,
            'option_label' => $optionLabel,
            'dynamic_type' => $dynamic_type,
            'dt_comment' => $dt_comment,
            'user' => auth()->user()->full_name ?? '',
            'year' =>  $date[0],
            'month' =>  $date[1],
            'day' =>  $date[2],
            'inserted_short_date' => $request->todays_date,
            'inserted_full_date' => $full_date,
            'details' => $details,
            'status' => 7,  
            'times' => $request->times,
            'is_single_record' => 1, 
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function details(string $times)
    {
        $orgbios = OrgBio::all();
        $short_date = Jalalian::now()->format('Y-m-d');

            $boughtItemDetails = BoughtItemDetails::with(['accountRelation','preListRelation','unitRelation'])
            ->where('times',$times)->get();
   
            $boughtItems = BoughtItem::with(['account' => function($query) {
                $query->select('id', 'name');
            }, 'currencyRelation' => function ($query){
                $query->select('id','name','symbols');
            }])->where('times', $times)->get();

        // return response()->json(['boughtItemDetails' => $boughtItemDetails]);
        // return response()->json(['boughtItems' => $boughtItems]);

        return view('buy.bought.details',compact('boughtItemDetails','boughtItems','short_date','orgbios'));

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

        $warehouses = Warehouse::select('id','name')->where('branch_id', $this->branch_id)->get();
        $billno =  BoughtItem::where('branch_id', $this->branch_id)->max('billno') + 1;
        
        $currencies = Currency::select('id','name')->get();
        $warehouses = Warehouse::select('id','name')->where('branch_id', $this->branch_id)->get();
        
        $customers = Account::select('id','name')->whereIn('account_type_id',[3,4])->where('branch_id', $this->branch_id)->get();
        $ownBanks = Account::select('id','name')->whereIn('account_type_id',[1,6])->where('branch_id', $this->branch_id)->orderBy('is_pre_select','DESC')->get();
        
        $preLists = BuyPreList::select('id','name','branch_id')->where('branch_id', $this->branch_id)->get();
  
        $units = Unit::select('id','name')->get();
        $journal_code = Journal::select('code','branch_id')->where('branch_id', $this->branch_id)->where('times',$times)->first();

        if(!$journal_code)
        {
            echo "Journal Code and Branch Id not found";
            die();
        }

        $orgbios = OrgBio::all();
        $short_date = Jalalian::now()->format('Y-m-d');

            $boughtItemDetails = BoughtItemDetails::with(['accountRelation','preListRelation','unitRelation'])
            ->where('times',$times)->get();

            $boughtItems = BoughtItem::with(['account' => function($query) {
                $query->select('id', 'name');
            }, 'currencyRelation' => function ($query){
                $query->select('id','name');
            }])->where('times', $times)->get();

        // return response()->json(['boughtItemDetails' => $boughtItemDetails]);
        // return response()->json(['boughtItems' => $boughtItems]);
        // echo $journal_code;
        // die();

        return view('buy.bought.edit',compact('boughtItemDetails','boughtItems','short_date','orgbios','currencies','warehouses','customers','ownBanks','preLists','units','journal_code'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        // return response()->json(['data' => $request->all()]);
        try 
        {
           
            $validated = $request->validate([
                'journal_code' => 'required',
                'billno' => 'required|min:1',
                'from_account_id' => 'required',
                'total_price' => 'required|min:1',
                'payable' => 'required|min:1',
                'currency_id' => 'required',
            ], [
                'journal_code.required' => 'کد ژورنال یافت نشد',
                'billno.required' => 'بل نمبر ضروری میباشد',
                'from_account_id.required' => ' حساب شرکت ضروری میباشد',
                'total_price.required' => ' قیمت مجموعی ضروری میباشد',
                'payable.required' => ' قابل پرداخت ضروری میباشد',
                'currency_id.required' => ' کرنسی ضروری میباشد',

            ]);

            $boughtItem = BoughtItem::where('billno', $request->billno)->first();

            // $note = "Total Payable: " . ($request->payable ?? 0) . ", Paid: " . ($request->cur_pay ?? 0) . ", Remained: " . ($request->remained ?? 0);

            $boughtItem->total_price = $request->total_price;
            $boughtItem->discount = $request->total_discount;
            $boughtItem->payable = $request->payable;
            $boughtItem->cur_pay = $request->cur_pay;
            $boughtItem->remained = $request->remained;
            $boughtItem->currency_id = $request->currency_id;
            $boughtItem->trans_spend = $request->trans_spend;
            $boughtItem->account_id = $request->from_account_id;
            $boughtItem->note = $request->note ?? '';
            $boughtItem->save();

            // delete journal records
            Journal::where('times', $request->times)->where('branch_id', $this->branch_id)->delete();

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
        $boughtItemDetails = BoughtItemDetails::with(['accountRelation','preListRelation','unitRelation'])->where('id', $id)->first();

        if (!$boughtItemDetails) {
            return response()->json(['error' => 'Bought Item Details not found'], 404);
        }

        // \Log::info('Pre List ID:', ['pre_list_id' => $boughtItemDetails->pre_list_id]);

        $warehouseItems = WarehouseItem::with('warehouseRelation')
            ->where('buy_pre_id', (int) $boughtItemDetails->pre_list_id) // Ensure correct type
            ->where('branch_id', $this->branch_id)
            ->where('unit_id', (int) $boughtItemDetails->unit_id)
            ->get();

        //  return response()->json(['boughtItemDetails' => $boughtItemDetails]);
        // return response()->json(['boughtItemDetails' => $boughtItemDetails, 'warehouseItems' => $warehouseItems]);
        return view('buy.bought.editModalContent', compact('boughtItemDetails', 'warehouseItems', 'units'));
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
            'bought_up'             => 'required|numeric|min:0',
            'discount'              => 'nullable|numeric|min:0',
            'transport'             => 'nullable|numeric|min:0',
            'unit_id'               => 'required|exists:units,id',
            'warehouse_id'          => 'required|array',
            'warehouse_id.*'        => 'required|numeric',
            'pre_list_id'           => 'required|exists:warehouse_items,buy_pre_id',
            'increment'             => 'nullable|array',
            'increment.*'           => 'nullable|numeric|min:0',
            'decrement'             => 'nullable|array',
            'decrement.*'           => 'nullable|numeric|min:0',
            'notification_amount'   => 'nullable|numeric|min:0',
            'expire_date'           => 'nullable|date',
            'times'                 => 'required|string',
        ]);
        

        DB::beginTransaction();
        try {
            // Update BoughtItemDetails
            $boughtItemDetails = BoughtItemDetails::findOrFail($validated['id']);
            $boughtItemDetails->update([
                'amount' => $validated['amount'],
                'bought_up' => $validated['bought_up'],
                'discount' => $validated['discount'],
                'transport' => $validated['transport'],
                'unit_id' => $validated['unit_id'],
                'total' => $validated['amount'] * $validated['bought_up'],
            ]);

            // Refresh the model to get the updated value
            $boughtItemDetails->refresh();

            $updated_available_total = $boughtItemDetails->available_total;

            // Update warehouse_items
            $insertedBy = auth()->user()->full_name ?? '';

              /**
               * صرف اگر قیمت فی واحد و مقدار تغیر کرده بود باید قیمت ها در گدام تغیر کند
               */
              
            if ((int) $request->old_bought_up !== (int) $validated['bought_up'] 
               || (int)$request->old_amount !== (int)$validated['amount']) 
            {

                foreach ($validated['warehouse_id'] as $index => $warehouseId) 
                {
                    /**
                     * If multiple users are updating warehouseItems simultaneously, one update might override another.
                     * Solution: Use optimistic locking with a version column or locking queries:
                     */
                    $WarehouseItem = WarehouseItem::where('warehouse_id', $warehouseId)
                        ->where('buy_pre_id', $validated['pre_list_id'])
                        ->where('unit_id', $validated['unit_id'])
                        ->where('branch_id', $this->branch_id)
                        ->lockForUpdate() // Prevents race conditions
                        ->first();
    
                    if (!$WarehouseItem) {
                        continue; // Skip if not found
                    }
    
                    // Update existing record if increment has value
                    /***
                     * Logic
                     * first:  amount = 30; bought_up = 100; total = 30 * 100 = 3000;
                     * second: amount = 10;  bought_up = 150; total = 10 *  150 = 1500; 
                     * find out the new bought unit price ?
                     * first_total + second_total  divided by amounts, new_unit_price = ((3000 + 1500) / 40) = 112.5
                     * 
                     * Question
                     * suppose I have 100 items in stock and bought unit price was 150; total = 15000
                     * now i have bought 5 items and bought unit price is 200, total = 1000
                     * how to find unit price of 105 items ?
                     * Formula:  New Unit Price = (Old Total Value + New Total Value) / Total Quantity
                     * or new unit price = 15000 + 1000 / 105; =  152.38
                     * 
                    */
                    if (!empty($validated['increment'][$index]) && $validated['increment'][$index] > 0) 
                    {
                        $increment_qty = $validated['increment'][$index];
                        $increment_price = $validated['bought_up']; // New purchase price

                        // Calculate new total for the incremented stock
                        $increment_total = $increment_price * $increment_qty;

                        // Update total stock amount
                        $new_in_amount = $WarehouseItem->in_amount + $increment_qty;
                        $available_amount  = $WarehouseItem->available_amount + $increment_qty;

                        // Calculate the new weighted average price

                        $new_total = $WarehouseItem->total + $increment_total;
                        $new_avg_total = $WarehouseItem->available_total + $increment_total;
                        $new_avg_up = ($available_amount > 0) ? ($new_avg_total / $available_amount) : 0;


                        $WarehouseItem->update([
                            'in_amount' => $new_in_amount,
                            'available_amount' => $available_amount,
                            'bought_up' => $validated['bought_up'], // store last bought_up
                            'avg_up'    => $new_avg_up,
                            'total' => $new_total, 
                            'available_total' => $new_avg_up * $available_amount,
                            'notification_amount' => $validated['notification_amount'] ?? 0,
                            'unit_id' => $validated['unit_id'],
                            'inserted_by' => $insertedBy,
                            'expire_date' => $validated['expire_date'] ?? null,
                            'times' => $validated['times'],
                        ]);
                    } 
                    // decreate the amounts
                    else if (!empty($validated['decrement'][$index]) && $validated['decrement'][$index] > 0) 
                    {
                        $decrement_qty = $validated['decrement'][$index];
                        $decrement_price = $validated['bought_up']; // New purchase price

                        // Calculate how much value needs to be removed from the total
                        $decrement_total = $decrement_price * $decrement_qty;

                        // Update total stock amount
                        $new_in_amount = $WarehouseItem->in_amount - $decrement_qty;
                        $available_amount  = $WarehouseItem->available_amount - $decrement_qty;

                        // Calculate the new weighted average price
                        $new_total = $WarehouseItem->total - $decrement_total;
                        $new_avg_total = $WarehouseItem->available_total - $decrement_total;
                        $new_avg_up = ($available_amount > 0) ? ($new_avg_total / $available_amount) : 0;
                        
                        $WarehouseItem->update([
                            'in_amount' => $new_in_amount,
                            'available_amount' => $available_amount,
                            'bought_up' => $validated['bought_up'], // store last bought_up
                            'avg_up'    => $new_avg_up,
                            'total' => $new_total, 
                            'available_total' => $new_avg_up * $available_amount,
                            'notification_amount' => $validated['notification_amount'] ?? 0,
                            'unit_id' => $validated['unit_id'],
                            'inserted_by' => $insertedBy,
                            'expire_date' => $validated['expire_date'] ?? null,
                            'times' => $validated['times'],
                        ]);
                    }
                    else 
                    {

                         /**
                         * ==========  قیمت فی واحد تغیر کرده باشد ===========
                         * باید مقدار سابق با قیمت سابق شان از مجموع کم شود
                         * قیمت فعلی با مقدار شان در مجموع اضافه شود
                         * قیمت اوسط نیز دریافت گردد
                         */

                        /**
                         * suppose I have 100 items in stock and bought unit price was 150; total = 15000 saved in table
                         * now i have bought 5 items and bought unit price is 200, total = 1000  saved in table
                         * then i will update just the unit price or just last entry  and change from 200 to 180
                         * how to find average of unit price and then mulitply to findout the total ?
                         * 
                         * Formula
                         * New Average Unit Price = (Total Previous Quantity + New Quantity) / (Total Previous Cost + New Purchase Cost) 
                         * 
                         * 
                         * 
                         */

                        // ---------------- Update new unit price -----------------------------
                        if ((int) $request->old_bought_up !== (int) $validated['bought_up']) {
                            

                            // Check if the bought_up has increased or decreased
                            if ($validated['bought_up'] > $request->old_bought_up) 
                            {
                                // Case: Increase in bought_up

                                // Calculate the new total for the bought items
                                $just_new_increased_price_per_item = $validated['bought_up'] - $request->old_bought_up;
                                // increased_price
                                $just_new_increased_price_total = $just_new_increased_price_per_item * $validated['amount'];
                                $new_total = $WarehouseItem->total + $just_new_increased_price_total;

                                // ChatGPT Suggestion
                                // $new_avg_up = ($WarehouseItem->available_total + ($validated['amount'] * $validated['bought_up'])) / ($WarehouseItem->available_total + $validated['amount']);


                                $new_available_total = $WarehouseItem->available_total + $just_new_increased_price_total;
                                $available_amount = $WarehouseItem->available_amount;

                                // Update the bought_up and total values accordingly
                                $new_avg_up = ($available_amount > 0) ? ($new_available_total / $available_amount) : 0;

                        
                                $WarehouseItem->update([
                                    'bought_up' => $validated['bought_up'],
                                    'avg_up' => $new_avg_up,
                                    'total' => $new_total,
                                    'available_total' => $new_avg_up * $available_amount,
                                    'notification_amount' => $validated['notification_amount'] ?? 0,
                                    'unit_id' => $validated['unit_id'],
                                    'inserted_by' => $insertedBy,
                                    'expire_date' => $validated['expire_date'] ?? null,
                                    'times' => $validated['times'],
                                ]);

                            } 
                            elseif ($validated['bought_up'] < $request->old_bought_up) 
                            {
                                // Case: Decrease in bought_up
                        
                                // Calculate the new total for the bought items
                                $just_new_decrease_price_per_item = $request->old_bought_up - $validated['bought_up'];
                                // decrease_price
                                $just_new_decrease_price_total = $just_new_decrease_price_per_item * $validated['amount'];
                                   
                                $new_total = $WarehouseItem->total - $just_new_decrease_price_total;

                                $new_available_total = $WarehouseItem->available_total - $just_new_decrease_price_total;
                                $available_amount = $WarehouseItem->available_amount;

                                // Update the bought_up and total values accordingly
                                $new_avg_up = ($available_amount > 0) ? ($new_available_total / $available_amount) : 0;
                        
                                $WarehouseItem->update([
                                    'bought_up' => $validated['bought_up'],
                                    'avg_up' => $new_avg_up,
                                    'total' => $new_total,
                                    'available_total' => $new_avg_up * $available_amount,
                                    'notification_amount' => $validated['notification_amount'] ?? 0,
                                    'unit_id' => $validated['unit_id'],
                                    'inserted_by' => $insertedBy,
                                    'expire_date' => $validated['expire_date'] ?? null,
                                    'times' => $validated['times'],
                                ]);
                            }
                        }
    
                    }
                }

            }
        

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
     * get single record from  warehouses for delete or decrement
     * from this page : http://127.0.0.1:8000/boughtList/edit/1739721412
     * for testing: http://127.0.0.1:8000/boughtList/getWarehouseListForDelete/150
     * @PARAM: warehouse_items_id
     * TODO : نام گدام درست مکمل نشان داده نمیشود
     */
    public function getWarehouseListForDelete(string $id)
    {  

        $boughtItemDetails = BoughtItemDetails::with(['accountRelation','preListRelation','unitRelation'])->where('id', $id)->first();
        
        if (!$boughtItemDetails) {
            return response()->json(['error' => 'Bought Item not found'], 404);
        }
        
        $units = Unit::select('id','name')->where('id',(int) $boughtItemDetails->unit_id)->get();


        $boughtItemDetailsId = $boughtItemDetails->id ?? 0;
        $boughtItemDetailsAmount = $boughtItemDetails->amount ?? 0 ;
        $preListId = $boughtItemDetails->pre_list_id ?? 0 ;
        $preListName = $boughtItemDetails->preListRelation->name ?? 0 ;

        $times = $boughtItemDetails->times ?? 0 ;


        $warehouseItems = WarehouseItem::with('warehouseRelation')
            ->where('buy_pre_id', (int) $boughtItemDetails->pre_list_id) 
            ->where('unit_id', (int) $boughtItemDetails->unit_id) 
            ->where('branch_id', $this->branch_id)
            ->get();

        //  return response()->json(['units' => $units]);
        //  return response()->json(['boughtItemDetails' => $boughtItemDetails]);
        // return response()->json(['warehouseItems' => $warehouseItems]);
        //  return response()->json(['boughtItemDetailsAmount' => $boughtItemDetailsAmount,'boughtItemDetailsId' => $boughtItemDetailsId]);

        return view('buy.bought.deleteModalContent', compact('warehouseItems', 'boughtItemDetailsId', 'boughtItemDetailsAmount','preListId','times','units','preListName'));
    }


    /**
     * delete a single item during buying form
     */
    public function deleteSingleItem(Request $request)
    {
        DB::beginTransaction();
    
        try {
            // Get BoughtItemDetails and delete based on bought_item_details_id
            $boughtItemDetails = BoughtItemDetails::findOrFail($request->delete_id);
            $boughtItemDetailsUnitId = $boughtItemDetails->unit_id ?? 0;
            $boughtItemDetails->delete();
    
            // Ensure the current user ID or another identifier is set for inserted_by
            $insertedBy = auth()->user()->id;
    
            // Process WarehouseItems
            foreach ($request->warehouse_id as $index => $warehouseId) 
            {
                $WarehouseItem = WarehouseItem::where('warehouse_id', $warehouseId)
                ->where('buy_pre_id', $request->preListId)
                ->where('unit_id', $boughtItemDetailsUnitId)
                ->where('branch_id', $this->branch_id)
                ->first();
    
                if (!$WarehouseItem) {
                    continue; // Skip if not found
                }
    
                // Handle deletion or decrement
                if ($request->delete_amount > 0) 
                {
                    if ($request->decrement[$index] == $WarehouseItem->available_amount) {
                        $WarehouseItem->delete(); // Delete if available matches decrement
                    } 
                    elseif ($WarehouseItem->available_amount > $request->decrement[$index]) 
                    {
                        // Adjust stock values
                        $newTotal = $WarehouseItem->total - ($WarehouseItem->bought_up * $request->decrement[$index]);
                        $new_avg_total = $WarehouseItem->available_total - ($WarehouseItem->avg_up * $request->decrement[$index]);

                        $newInAmount = $WarehouseItem->in_amount - $request->decrement[$index];
                        $availableAmount = $WarehouseItem->available_amount - $request->decrement[$index];
                  
                        // Ensure stock never goes negative
                        if ($newInAmount < 0 || $new_avg_total < 0) {
                            throw new \Exception("Stock cannot be negative for warehouse ID: $warehouseId");
                        }
    
                        // Update WarehouseItem
                        $WarehouseItem->update([
                            'in_amount' => $newInAmount,
                            'available_amount' => $availableAmount,
                            'total' => $newTotal,
                            'available_total' => $new_avg_total,
                            'inserted_by' => $insertedBy // Ensure this is defined
                        ]);
                    }
                }
            }
    
            DB::commit();
    
            Session::put('notification', [
                'message' => __('common.deleted_successfully'),
                'type' => 'success',
            ]);
    
            return redirect()->route('boughtList.edit', ['times' => $request->times]);
        } catch (\Exception $e) {
            DB::rollBack();
    
            \Log::error('Error deleting records: ' . $e->getMessage());
    
            Session::put('notification', [
                'message' => __('common.delete_failed'),
                'type' => 'danger',
            ]);
    
            return redirect()->route('boughtList.edit', ['times' => $request->times] );
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
            WarehouseItem::where('times', $times)->where('branch_id', $this->branch_id)->delete();
            BoughtItemDetails::where('times', $times)->where('branch_id', $this->branch_id)->delete();
            BoughtItem::where('times', $times)->where('branch_id', $this->branch_id)->delete();
            Journal::where('times', $times)->where('branch_id', $this->branch_id)->delete();

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
    
}

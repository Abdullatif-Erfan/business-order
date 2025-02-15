<?php

namespace App\Http\Controllers\Buy;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Setting\Currency;
use App\Models\Setting\Branch;
use Morilog\Jalali\Jalalian;
use App\Models\Setting\OrgBio;
use App\Models\Setting\Unit;
use App\Models\Buy\BuyPreList;
use App\Models\Buy\BoughtItem;
use App\Models\Buy\BoughtItemDetails; 
use App\Models\Journal\Journal;
use App\Models\Setting\Warehouse;
use App\Models\Warehouse\WarehouseItem;

use App\Models\Setting\Account;
use Yajra\DataTables\Facades\DataTables;


class BoughtDetailsController extends Controller
{
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


        $currencies = Currency::all();
        $branches = Branch::all();
        $orgbios = OrgBio::all();
        $todaysDate = Jalalian::now()->format('Y-m-d');

        return view('buy.bought.list',compact('currencies','branches','todaysDate','orgbios'));
    }


    public function getData(Request $request)
    {
        $boughtItems = DB::table('bought_item_details')
            ->select(
                'bought_item_details.billno',
                'bought_item_details.id as details_id',
                'bought_item_details.amount',
                'bought_item_details.bought_up',
                'bought_item_details.sell_up',
                'bought_item_details.total',
                'bought_item_details.times',
                'bought_item_pre_lists.name as pre_list_name',
                'units.name as unit_name',
                'bought_items.payable',
                'bought_items.cur_pay',
                'bought_items.remained',
                'bought_items.times as btimes',
                'bought_items.idate',
                'bought_item_details.is_moved',
                'accounts.name as account_name'
            )
            ->leftJoin('bought_items', 'bought_items.id', '=', 'bought_item_details.bought_item_id')
            ->join('accounts', 'accounts.id', '=', 'bought_items.customer_account_id') // INNER JOIN
            ->leftJoin('bought_item_pre_lists', 'bought_item_pre_lists.id', '=', 'bought_item_details.pre_list_id')
            ->leftJoin('units', 'units.id', '=', 'bought_item_details.unit_id')
            ->orderBy('bought_item_details.is_moved', 'ASC')
            ->orderBy('bought_item_details.id', 'DESC');

            return DataTables::of($boughtItems)
            
            ->addIndexColumn()
            // ->addColumn('branch', function($buyPreList) {
            //     return $buyPreList->branchRelation->name;
            // })

            ->addColumn('bought_up', function ($buyList) {
                return $buyList->bought_up ? number_format($buyList->bought_up,2) : '';
            })

            ->addColumn('total', function ($buyList) {
                return $buyList->total ? number_format($buyList->total,2) : '';
            })

            ->addColumn('cur_pay', function ($buyList) {
                return $buyList->cur_pay ? number_format($buyList->cur_pay,2) : '';
            })

            ->addColumn('remained', function ($buyList) {
                return $buyList->remained ? number_format($buyList->remained,2) : '';
            })

            ->addColumn('is_moved', function ($buyList) {
                return $buyList->is_moved == 1 ? '<badge class="badge badge-info" style="padding: 3px 8px !important"><i class="fas fa-check" style="color:#fff"></i></badge>' : '<badge class="badge badge-default" style="padding: 3px 8px !important"><i class="fas fa-times" style="color:#fff"></i></badge>';
            })
            

            ->addColumn('billno', function($buyList) {
                return $buyList ? 'BUY_'.$buyList->billno: 0;
            })
            ->addColumn('view', function ($buyList) {
                return '<a href="boughtList/details/'.$buyList->times.'" class="hidden-print"><i class="fas fa-eye viewItems" 
                data-id="' . $buyList->details_id . '" style="font-size:20px;"></i></a>';
            })
            ->rawColumns(['billno','view','is_moved'])
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
        $warehouses = Warehouse::select('id','name')->get();
        $customers = Account::select('id','name')->where('account_type_id',3)->orWhere('account_type_id',4)->get();
        $ownBanks = Account::select('id','name')->where('account_type_id',1)->orderBy('is_pre_select','DESC')->get();
        $preLists = BuyPreList::select('id','name','branch_id')->get();
        // TODO : filter by branch_id
        $todaysDate = Jalalian::now()->format('Y-m-d');
        $units = Unit::select('id','name')->get();
        $newJournalCode =  Journal::max('code') + 1;

        $times = time();


        // return response()->json($preLists);
        return view('buy.bought.create',compact('currencies','customers','todaysDate','ownBanks','preLists','units','warehouses','times','newJournalCode'));
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
        $branch_id = $preLists->branch_id;
        $item_name = $preLists->name ?? '';

        // Start the transaction
        DB::beginTransaction();
   
        try {
        
        // 1: insert in to bought_items table
        $BoughtItemId = $this->createBoughtItem($request, $short_date, $branch_id, $times);

        // 2: insert in to bought_item_details table
        $boughtItemDetails = $this->storeBoughtItemDetails($request, $BoughtItemId, $times);

        // 3: insert or update warehouse_items
        $this->createOrUpdateWarehouseItems($request, $short_date, $item_name, $times);

        DB::commit();

        // 4: fetch inserted data from bought_item_details
        $insertedData = BoughtItemDetails::with(['preListRelation','unitRelation'])->where('times',$times)->get();
             

        //    return response()->json(['insertedData' => $insertedData]); 
           return view('buy.bought.curlist',compact('insertedData'));

        } catch (\Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollBack();
            // Optionally, log the error for debugging
            \Log::error('Error storing journal entry', ['error' => $e]);

            return response()->json(['status' => 'failed'], 404);
        }        
    }

   
    private function createBoughtItem($request, $short_date, $branch_id, $times)
    {
        $date = explode('-', $short_date);
        $year = $date[0];
        $month = $date[1];
        $day = $date[2];
        $note = "Total: ".($request->payable ?? 0).", Paid: ".($request->cur_pay ?? 0).", Remained: ".($request->remained ?? 0);
        $BoughtItem =  BoughtItem::create([
            'customer_account_id' => $request->customer_account_id, 
            'branch_id'           => $branch_id,
            'billno'              => $request->billno,
            'journal_code'        => $request->journal_code,
            'total_price'         => $request->total_price ?? 0,
            'discount'            => $request->discount ?? 0,
            'payable'             => $request->payable ?? 0,
            'cur_pay'             => $request->cur_pay ?? 0,
            'remained'            => $request->remained ?? 0,
            'account_id'          => $request->from_account_id,
            'currency_id'         => $request->currency_id,
            'trans_spend'         => $request->trans_spend  ?? 0, 
            'trans_account_id'    => $request->trans_account_id ?? 0,
            'note'                => $note,     
            'idate'               => $short_date,    
            'year'                => $year, 
            'month'               => $month, 
            'day'                 => $day, 
            'iby'                 => auth()->user()->full_name ?? '',
            'times'               => $times
        ]); 
        return $BoughtItem->id;
    }

    private function storeBoughtItemDetails($request, $boughtItemId, $times)
    {
        $BoughtItemDetails = BoughtItemDetails::create([
            'billno' => $request->billno,
            'bought_item_id' => $boughtItemId,
            'pre_list_id' => $request->pre_list_id,
            'amount' => $request->amount,
            'unit_id' => $request->unit_id,
            'bought_up' => $request->bought_up,
            'discount' => $request->discount,
            'transport' => $request->transport,
            'total' => $request->amount * $request->bought_up,
            'expire_date' => $request->expire_date,
            'is_moved' => 1,
            'times' => $times
        ]);
    }

    private function createOrUpdateWarehouseItems($request, $short_date, $item_name, $times)
    {
        $date = explode('-', $short_date);
        $year = $date[0];
        $month = $date[1];
        $day = $date[2];

        $insertedBy = auth()->user()->full_name ?? '';

        // Loop through each warehouse_id
        foreach ($request->warehouse_id as $index => $warehouseId) {
            $WarehouseItem = WarehouseItem::where('warehouse_id', $warehouseId)
                ->where('buy_pre_id', $request->pre_list_id)
                ->first();

            if ($WarehouseItem) {
                // Update existing record

                /***
                 * first:  amount = 30; bought_up = 100; total = 30 * 100 = 3000;
                 * second: amount = 10;  bought_up = 150; total = 10 *  150 = 1500; 
                 * find out the new bought unit price ?
                 * first_total + second_total  divided by amounts, new_unit_price = ((3000 + 1500) / 40) = 112.5
                 */

                $new_total = $request->bought_up * $request->warehouse_amount[$index]; // 1500
                $new_in_amount = $WarehouseItem->in_amount + $request->warehouse_amount[$index]; // 40
                $new_bought_up = ($new_in_amount > 0) ? (($WarehouseItem->total + $new_total) / $new_in_amount) : 0; // 112.5

                $WarehouseItem->update([
                    'in_amount' => $new_in_amount,
                    'bought_up' => $new_bought_up,
                    'total' => $new_bought_up * $new_in_amount,
                    'sell_up' => $request->warehouse_sell_up[$index],
                    'notification_amount' => $request->notification_amount,
                    'inserted_by' => $insertedBy,
                    'expire_date' => $request->expire_date ?? null,
                    'times' => $request->times,
                ]);
            } else {
                // Insert a new record
                WarehouseItem::create([
                    'warehouse_id' => $warehouseId,
                    'buy_pre_id' => $request->pre_list_id,
                    'name' => $item_name ?? '',
                    'in_amount' => $request->warehouse_amount[$index],
                    'out_amount' => 0.00,
                    'wastage_amount' => 0.00,
                    'unit_id' => $request->unit_id,
                    'bought_up' => $request->bought_up,
                    'sell_up' => $request->warehouse_sell_up[$index],
                    'total' => $request->bought_up * $request->warehouse_amount[$index],
                    'currency_id' => $request->currency_id,
                    'notification_amount' => $request->notification_amount,
                    'inserted_by' => $insertedBy,
                    'expire_date' => $request->expire_date ?? null,
                    'inserted_short_date' => $short_date ?? null,
                    'year' => $year,
                    'month' => $month,
                    'day' => $day,
                    'times' => $request->times
                ]);
            }
        }
    }


    /**
    * final submit in creation form
    * in this function update bought_items based on billno and create journal reacord
    */
    public function submit(Request $request)
    {
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
        $note = "Total: ".($request->payable ?? 0).", Paid: ".($request->cur_pay ?? 0).", Remained: ".($request->remained ?? 0);
        
        // Ensure you're updating a specific record by using the where clause first
        $BoughtItem = BoughtItem::where('billno', $request->billno)->first();
    
        // Start the transaction
        DB::beginTransaction();
    
        try {
            // Update BoughtItem record
            $BoughtItem->update([
                'total_price' => $request->total_price,
                'discount' => $request->discount,
                'payable' => $request->payable,
                'cur_pay' => $request->cur_pay,
                'remained' => $request->remained,
                'trans_spend' => $request->trans_spend,
                'note' => $note,
            ]);
    
            // Insert into journal
            $check = $this->handleJournalEntry($request);
    
            if ($check) {
                // Commit the transaction
                DB::commit();
    
                // Flash success message
                Session::flash('notification', [
                    'message' => 'موفقانه ثبت گردید',
                    'type' => 'success',
                ]);
    
                return redirect()->route('boughtList.index');
            } else {
                DB::rollBack();
                // Optionally, log the error for debugging
                \Log::error('Error storing journal entry');

                $this->deleteBoughtRecords($request);

                Session::flash('notification', [
                    'message' => ' ثبت نگردید',
                    'type' => 'danger',
                ]);
                return redirect()->route('boughtList.index');
            }
    
        } catch (\Exception $e) {
            // Rollback all changes if an exception occurs
            DB::rollBack();
    
            // Log the error
            \Log::error('Error occurred during the transaction', ['error' => $e]);
    
            $this->deleteBoughtRecords($request);
    
            // Flash error message
            Session::flash('notification', [
                'message' => 'ثبت نگردید',
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
                Session::flash('notification', [
                    'message' => 'ریکارد در گدام یافت نشد',
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
                    $WarehouseItem->save();
                }
            }
    }

    private function validateRequest($request)
    {
        $validated = $request->validate([
            'pre_list_id.*' => 'required|integer',
            'amount.*' => 'required|numeric|min:0.01',
            'unit_id.*' => 'required|integer',
            'bought_up.*' => 'required|numeric|min:0.01',
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
            'pre_list_id.*.required' => ' نام جنس از  فهرست الزامی است.',
        
            'amount.*.required' => 'مقدار الزامی است.',
            'amount.*.numeric' => 'مقدار باید عدد باشد.',
        
            'unit_id.*.required' => 'انتخاب واحد الزامی است.',
            'unit_id.*.integer' => 'شناسه واحد باید عدد صحیح باشد.',
        
            'bought_up.*.required' => 'قیمت خرید الزامی است.',
            'bought_up.*.numeric' => 'قیمت خرید باید عدد باشد.',
            'bought_up.*.min' => 'قیمت خرید باید حداقل 0.01 باشد.',
        
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
            'warehouse_id.*.exists' => 'گدام انتخاب شده معتبر نیست.',
        
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
             * status: 1: old journal, 2: journal, 3:buy, 4:sales, 5:clearance
             * transaction_type: 1:recieved/income/increase/talab   2:paid/outcome/decrease/baqi
             * payment_type: 1: cache, 2: loan
             */
            
            /**
             * اگر هیچ پرداخت نکند وتمام شان قرض ثبت گردد
             * خزانه باید قرضدار ثبت گردد = Loan Recieved
             * مشتری باید طلب ثبت گردد = paid Loan 
             */
         DB::beginTransaction();
         try {

            if(intval($request->cur_pay) === 0 && intval($request->remained) === intval($request->payable))
            { 
                // ثبت قرضه خزانه = Loan Recieved = 2 1
                $details =  ' قرضه خرید - بل '.' BUY_'.$request->billno;
                $this->createJournalEntry($request,  $request->from_account_id,  $request->payable, $ttype = "1", $ptype="2", $date,
                $full_date, $details);
                
                // ثبت طلب مشتری = Loan Paid = 2 2
                $details =  ' طلب خرید - بل '.' BUY_'.$request->billno;
                $this->createJournalEntry($request, $request->customer_account_id,  $request->payable, $ttype = "2", $ptype="2", $date,
                $full_date, $details);
            }

            // کمی شانرا پرداخت کرده و متباقی شانرا قرض انتخاب کرده است
            else if(intval($request->remained) > 0 && intval($request->cur_pay) > 0) 
            {
                // ثبت پرداخت نقدی خزانه = Cache paid
                $details =  'پرداخت خرید - بل  '.' BUY_'.$request->billno;
                $this->createJournalEntry($request,  $request->from_account_id, $request->cur_pay, $ttype = "2", $ptype="1", $date,
                $full_date, $details);

                // ثبت قرضه خزانه = Loan Recieved 
                $details =  ' قرضه خرید - بل '.' BUY_'.$request->billno;
                $this->createJournalEntry($request, $request->from_account_id, $request->remained,  $ttype = "1", $ptype="2", $date,
                $full_date, $details);
               
                // ثبت طلب مشتری = Paid Loan
                $details =  ' طلب خرید - بل '.' BUY_'.$request->billno;
                $this->createJournalEntry($request, $request->customer_account_id, $request->remained, $ttype = "2", $ptype="2", $date,
                $full_date, $details);
            }

             // قرضدار نمانده است و مکمل پرداخت کرده است
             // تنها از حساب خزانه کم شود
            else if(intval($request->remained) === 0 && intval($request->cur_pay) === intval($request->payable)) 
            {
                // ثبت پرداخت نقدی خزانه = Cache paid
                $details =  'پرداخت خرید - بل  '.' BUY_'.$request->billno;
                $this->createJournalEntry($request, $request->from_account_id, $request->cur_pay, $ttype = "2", $ptype="1", $date,
                $full_date, $details);
            }

            // // ثبت مصارف ترانسپورت به روش فعلی روی خزانه جارج میشود
            // if(intval($request->trans_spend) > 0 && intval($request->trans_account_id) > 0) 
            // {
            //     // رفت پول نقد از بابت ترانسپورت = Cache paid
            //     $details =  'پرداخت خرید - بل  '.' BUY_'.$request->billno;
            //     $this->createJournalEntry($request, $request->trans_account_id, $request->trans_spend, $ttype = "2", $ptype="1", $date,
            //     $full_date, $details);
            // }
            
            DB::commit();
            return true; 

        } catch (\Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollBack();
            // Optionally, log the error for debugging
            \Log::error('Error storing journal entry', ['error' => $e]);
    
            // Use MessageService to return error message
            Session::flash('notification', [
                'message' => ' ثبت نگردید',
                'type' => 'danger',
            ]);
             return false;
        }
    }

    private function createJournalEntry($request, $account_id, $amount, $ttype, $ptype, $date, $full_date, $details)
    {
        Journal::create([
            'bill_no' => $request->billno,
            'code' =>  $request->journal_code,
            'account_id' => $account_id,
            'branch_id' => $request->branch_id,
            'amount' => $amount,
            'currency_id' => $request->currency_id,
            'transaction_type' => $ttype,
            'payment_type' => $ptype,
            'user_id' => auth()->user()->id ?? '',
            'year' =>  $date[0],
            'month' =>  $date[1],
            'day' =>  $date[2],
            'inserted_short_date' => $request->todays_date,
            'inserted_full_date' => $full_date,
            'details' => $details,
            'status' => 3,  
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

            $boughtItemDetails = DB::table('bought_item_details')
            ->select(
                'bought_item_details.id',
                'bought_item_pre_lists.name as pre_list_name',
                'bought_item_details.amount',
                'units.name as unit_name',
                'bought_item_details.bought_up',
                'bought_item_details.total',
                'bought_item_details.times',
                'bought_item_details.expire_date'
            )
            ->leftJoin('bought_item_pre_lists', 'bought_item_pre_lists.id', '=', 'bought_item_details.pre_list_id')
            ->leftJoin('units', 'units.id', '=', 'bought_item_details.unit_id')
            ->where('bought_item_details.times', $times)
            ->get();

            // 'customer_account_id', 'billno', 'journal_code', 'total_price', 'discount', 'payable', 'cur_pay', 'remained', 'account_id', 'currency_id', 'trans_spend', 'trans_account_id', 'note', 'idate', 'year', 'month', 'day', 'iby', 'times'

            $boughtItems = DB::table('bought_items')
                ->select(
                    'bought_items.id',
                    'bought_items.billno',
                    'bought_items.idate',
                    'bought_items.customer_account_id',
                    'customer_accounts.name as customer_account_name', // Alias for first join
                    'bought_items.total_price',
                    'bought_items.discount',
                    'bought_items.payable',
                    'bought_items.cur_pay',
                    'bought_items.currency_id',
                    'currencies.name as currency_name',
                    'bought_items.remained',
                    'bought_items.account_id',
                    'account_accounts.name as account_name', // Alias for second join
                    'bought_items.times as btimes',
                    'bought_items.trans_spend',
                    'bought_items.trans_account_id',
                    'bought_items.note'
                )
                ->join('accounts as customer_accounts', 'customer_accounts.id', '=', 'bought_items.customer_account_id') 
                ->join('accounts as account_accounts', 'account_accounts.id', '=', 'bought_items.account_id') // Second join with alias
                ->join('currencies', 'currencies.id', '=', 'bought_items.currency_id')
                ->where('bought_items.times', $times)
                ->get();


        // return response()->json(['boughtItemDetails' => $boughtItemDetails]);
        // return response()->json(['boughtItems' => $boughtItems]);

        return view('buy.bought.details',compact('boughtItemDetails','boughtItems','short_date','orgbios'));


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
        try 
        {
            // Get BoughtItemDetails
            $boughtItemDetails = BoughtItemDetails::findOrFail($request->id);
    
            // Store previous values
            $prevAmount = $boughtItemDetails->amount;
            $prevBoughtUp = $boughtItemDetails->bought_up;
            $prevTimes = $boughtItemDetails->times;

            // Update BoughtItemDetails
            $boughtItemDetails->amount = $request->amount;
            $boughtItemDetails->bought_up = $request->bought_up;
            $boughtItemDetails->discount = $request->discount;
            $boughtItemDetails->transport = $request->transport;
            $boughtItemDetails->save();
    
            return response()->json(['status' => 'success']);
    
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating warehouse items', ['error' => $e]);
            return response()->json(['status' => 'failed'], 500);
        }
    }
    

    /**
     * delete a single item during buying form
     */

     public function deleteSingleItem(string $id)
     {
        DB::beginTransaction();
        try {

            // Get BoughtItemDetails
            $boughtItemDetails = BoughtItemDetails::findOrFail($id);
            
            // Store previous values
            $prevAmount = $boughtItemDetails->amount;
            $prevBoughtUp = $boughtItemDetails->bought_up;
            $prevTimes = $boughtItemDetails->times;

            $boughtItemDetails->delete();

            // Update BoughtItemDetails
            $boughtItemDetails->amount = $request->amount;
            $boughtItemDetails->bought_up = $request->bought_up;
            $boughtItemDetails->discount = $request->discount;
            $boughtItemDetails->transport = $request->transport;
            $boughtItemDetails->save();
    
            // Get the WarehouseItem
            $warehouseItem = WarehouseItem::where('buy_pre_id', $boughtItemDetails->pre_list_id)
                ->firstOrFail();


            // Delete all related records directly
            Journal::where('times', $times)->delete();
            BoughtItem::where('times', $times)->delete();
            BoughtItemDetails::where('times', $times)->delete();
    
            DB::commit();
    
            Session::flash('notification', [
                'message' => 'موفقانه حذف گردید',
                'type' => 'success',
            ]);
    
            return redirect()->route('boughtList.index'); 
        } catch (\Exception $e) {
            DB::rollBack();
    
            \Log::error('Error deleting records: ' . $e->getMessage());
    
            Session::flash('notification', [
                'message' => ' حذف نگردید',
                'type' => 'danger',
            ]);
    
            return back();
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
            Journal::where('times', $times)->delete();
            BoughtItem::where('times', $times)->delete();
            BoughtItemDetails::where('times', $times)->delete();
    
            DB::commit();
    
            Session::flash('notification', [
                'message' => 'موفقانه حذف گردید',
                'type' => 'success',
            ]);
    
            return redirect()->route('boughtList.index'); 
        } catch (\Exception $e) {
            DB::rollBack();
    
            \Log::error('Error deleting records: ' . $e->getMessage());
    
            Session::flash('notification', [
                'message' => ' حذف نگردید',
                'type' => 'danger',
            ]);
    
            return back();
        }
    }
    
}

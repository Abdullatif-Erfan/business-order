<?php

namespace App\Http\Controllers\Buy;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

        // $boughtItems = BoughtItem::with(['currency','customer'])->orderBy('id', 'DESC')->get();
        // return response()->json($boughtItems);


        $currencies = Currency::all();
        $branches = Branch::all();
        $orgbios = OrgBio::all();
        $todaysDate = Jalalian::now()->format('Y-m-d');

        return view('buy.bought.list',compact('currencies','branches','todaysDate','orgbios'));
    }


    public function getData(Request $request)
    {
            $boughtItems = BoughtItem::with(['currency', 'customer'])->orderBy('id', 'DESC');
            
              // Apply filters if provided
              if ($request->customer_name) {
                $boughtItems->whereHas('customer', function ($query) use ($request) {
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
                return $boughtItem->billno ? 'BUY_'.$boughtItem->billno: 0;
            })

            ->addColumn('total_price', function ($boughtItem) {
                return $boughtItem->total_price ? number_format($boughtItem->total_price,2) : '';
            })

            ->addColumn('trans_spend', function ($boughtItem) {
                return $boughtItem->trans_spend ? number_format($boughtItem->trans_spend,2) : '';
            })

            ->addColumn('discount', function ($boughtItem) {
                return $boughtItem->discount ? number_format($boughtItem->discount,2) : '';
            })

            ->addColumn('payable', function ($boughtItem) {
                return $boughtItem->payable ? number_format($boughtItem->payable,2) : '';
            })

            ->addColumn('cur_pay', function ($boughtItem) {
                return $boughtItem->cur_pay ? number_format($boughtItem->cur_pay,2) : '';
            })
            ->addColumn('remained', function ($boughtItem) {
                return $boughtItem->remained ? number_format($boughtItem->remained,2) : '';
            })
            ->addColumn('currency', function ($boughtItem) {
                return $boughtItem->currency->name ? $boughtItem->currency->name : '';
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
        $warehouses = Warehouse::select('id','name')->get();
        $customers = Account::select('id','name')->where('account_type_id',3)->orWhere('account_type_id',4)->get();
        $ownBanks = Account::select('id','name')->where('account_type_id',1)->orderBy('is_pre_select','DESC')->get();
        $preLists = BuyPreList::select('id','name','branch_id')->get();
        // TODO : filter by branch_id
        $todaysDate = Jalalian::now()->format('Y-m-d');
        $units = Unit::select('id','name')->get();
        $newJournalCode =  Journal::max('code') + 1;
        $billno =  BoughtItem::max('billno') + 1;

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
        $branch_id = $preLists->branch_id;
        $item_name = $preLists->name ?? '';

        // Start the transaction
        DB::beginTransaction();
   
        try {
        
        // 1: insert in to bought_items table
        $BoughtItemId = $this->createOrUpdateBoughtItem($request, $short_date, $branch_id, $times);

        // 2: insert in to bought_item_details table
        $boughtItemDetails = $this->storeBoughtItemDetails($request, $BoughtItemId, $times);

        // 3: insert or update warehouse_items
        $this->createOrUpdateWarehouseItems($request, $short_date, $item_name, $times);

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
        
        $note = "Total: " . ($request->payable ?? 0) . ", Paid: " . ($request->cur_pay ?? 0) . ", Remained: " . ($request->remained ?? 0);

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
                'note'                => $note,
            ]);
        } else {
            // ✅ Create new record
            \Log::info('Inserting BoughtItem');
            $BoughtItem = BoughtItem::create([
                'branch_id'           => $branch_id,
                'factor'              => $request->factor ?? 0,
                'billno'              => $request->billno,
                'journal_code'        => 0,
                'total_price'         => $request->total_price ?? 0,
                'discount'            => $request->discount ?? 0,
                'payable'             => $request->payable ?? 0,
                'cur_pay'             => $request->cur_pay ?? 0,
                'remained'            => $request->remained ?? 0,
                'account_id'          => $request->from_account_id,
                'customer_account_id' => $request->customer_account_id,
                'currency_id'         => $request->currency_id,
                'trans_spend'         => $request->trans_spend ?? 0,
                'note'                => $note,
                'idate'               => $short_date,
                'year'                => $year,
                'month'               => $month,
                'day'                 => $day,
                'iby'                 => auth()->user()->full_name ?? '',
                'times'               => $times
            ]);
        }

        return $BoughtItem->id;
    }


    private function storeBoughtItemDetails($request, $boughtItemId, $times)
    {
        $BoughtItemDetails = BoughtItemDetails::create([
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

            if ($WarehouseItem) 
            {
                // Update existing record

                /***
                 * first:  amount = 30; bought_up = 100; total = 30 * 100 = 3000;
                 * second: amount = 10;  bought_up = 150; total = 10 *  150 = 1500; 
                 * find out the new bought unit price ?
                 * first_total + second_total  divided by amounts, new_unit_price = ((3000 + 1500) / 40) = 112.5
                 */

                $new_total = $request->bought_up * $request->warehouse_amount[$index]; // 1500
                $new_in_amount = $WarehouseItem->in_amount + $request->warehouse_amount[$index]; // 40
                $available_amount  = $WarehouseItem->available_amount + $request->warehouse_amount[$index];
                $new_bought_up = ($new_in_amount > 0) ? (($WarehouseItem->total + $new_total) / $new_in_amount) : 0; // 112.5

                $WarehouseItem->update([
                    'in_amount' => $new_in_amount,
                    'available_amount' => $available_amount,
                    'bought_up' => $new_bought_up,
                    'total' => $new_bought_up * $new_in_amount,
                    'sell_up' => $request->warehouse_sell_up[$index],
                    'notification_amount' => $request->notification_amount ?? 0,
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
                    'available_amount' => $request->warehouse_amount[$index],
                    'wastage_amount' => 0.00,
                    'unit_id' => $request->unit_id,
                    'bought_up' => $request->bought_up,
                    'sell_up' => $request->warehouse_sell_up[$index],
                    'total' => $request->bought_up * $request->warehouse_amount[$index],
                    'currency_id' => $request->currency_id,
                    'notification_amount' => $request->notification_amount ?? 0,
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
            // $check = $this->handleJournalEntry($request);
     
               // Flash success message
                Session::flash('notification', [
                    'message' => 'موفقانه ثبت گردید',
                    'type' => 'success',
                ]);
    
                return redirect()->route('boughtList.index');
           
    
        } catch (\Exception $e) {

            // Log the error
            \Log::error('Error occurred during the transaction', ['error' => $e]);
    
            // $this->deleteBoughtRecords($request);
    
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

            $boughtItemDetails = BoughtItemDetails::with(['accountRelation','preListRelation','unitRelation'])
            ->where('times',$times)->get();
   
            $boughtItems = BoughtItem::with(['account' => function($query) {
                $query->select('id', 'name');
            }, 'currency' => function ($query){
                $query->select('id','name');
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
     * http://127.0.0.1:8000/boughtList/edit/1739721412 
     */
    public function edit(string $times)
    {

        $currencies = Currency::select('id','name')->get();
        $warehouses = Warehouse::select('id','name')->get();
        $customers = Account::select('id','name')->where('account_type_id',3)->orWhere('account_type_id',4)->get();
        $ownBanks = Account::select('id','name')->where('account_type_id',1)->orderBy('is_pre_select','DESC')->get();
        $preLists = BuyPreList::select('id','name','branch_id')->get();
        $units = Unit::select('id','name')->get();


        $orgbios = OrgBio::all();
        $short_date = Jalalian::now()->format('Y-m-d');

            $boughtItemDetails = BoughtItemDetails::with(['accountRelation','preListRelation','unitRelation'])
            ->where('times',$times)->get();

            $boughtItems = BoughtItem::with(['account' => function($query) {
                $query->select('id', 'name');
            }, 'currency' => function ($query){
                $query->select('id','name');
            }])->where('times', $times)->get();

        // return response()->json(['boughtItemDetails' => $boughtItemDetails]);
        // return response()->json(['boughtItems' => $boughtItems]);

        return view('buy.bought.edit',compact('boughtItemDetails','boughtItems','short_date','orgbios','currencies','warehouses','customers','ownBanks','preLists','units'));
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
                'billno' => 'required|integer|min:1',
                'account_id' => 'required',
                'total_price' => 'required|integer|min:1',
                'payable' => 'required|integer|min:1',
                'currency_id' => 'required|integer',
            ], [
                'billno.required' => 'بل نمبر ضروری میباشد',
                'account_id.required' => ' حساب شرکت ضروری میباشد',
                'total_price.required' => ' قیمت مجموعی ضروری میباشد',
                'payable.required' => ' قابل پرداخت ضروری میباشد',
                'currency_id.required' => ' کرنسی ضروری میباشد',

            ]);

            $boughtItem = BoughtItem::where('billno', $request->billno)->first();

            $note = "Total: " . ($request->payable ?? 0) . ", Paid: " . ($request->cur_pay ?? 0) . ", Remained: " . ($request->remained ?? 0);

            $boughtItem->total_price = $request->total_price;
            $boughtItem->discount = $request->total_discount;
            $boughtItem->payable = $request->payable;
            $boughtItem->cur_pay = $request->cur_pay;
            $boughtItem->remained = $request->remained;
            $boughtItem->currency_id = $request->currency_id;
            $boughtItem->trans_spend = $request->trans_spend;
            $boughtItem->account_id = $request->account_id;
            $boughtItem->note = $note;
            $boughtItem->save();

            // Flash success message
            Session::flash('notification', [
                'message' => 'موفقانه ویرایش گردید',
                'type' => 'success',
            ]);
            return redirect()->route('boughtList.details', ['times' => $request->times]);
    
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error occurred during the journal update', ['error' => $e]);
    
            // Flash error message
            Session::flash('notification', [
                'message' => 'ویرایش نگردید',
                'type' => 'danger',
            ]);
    
            return redirect()->route('boughtList.details', ['times' => $request->times]);
        }
    }
    
    /**
     * get single record from bought_item_details and amounts from warehouses for edit
     * testing url: http://127.0.0.1:8000/boughtList/getSingleRecordForEdit/150
     */
    public function getSingleRecordForEdit(string $id)
    {
        $units = Unit::select('id','name')->get();
        $boughtItemDetails = BoughtItemDetails::with(['accountRelation','preListRelation','unitRelation'])->where('id', $id)->first();

        if (!$boughtItemDetails) {
            return response()->json(['error' => 'Bought Item not found'], 404);
        }

        \Log::info('Pre List ID:', ['pre_list_id' => $boughtItemDetails->pre_list_id]);

        $warehouseItems = WarehouseItem::with('warehouseRelation')
            ->where('buy_pre_id', (int) $boughtItemDetails->pre_list_id) // Ensure correct type
            ->get();

        //  return response()->json(['boughtItemDetails' => $boughtItemDetails]);
        // return response()->json(['warehouseItems' => $warehouseItems]);
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
            'id'                => 'required|exists:bought_item_details,id',
            'amount'            => 'required|numeric|min:0',
            'bought_up'         => 'required|numeric|min:0',
            'discount'          => 'nullable|numeric|min:0',
            'transport'         => 'nullable|numeric|min:0',
            'unit_id'           => 'required|exists:units,id',
            'warehouse_id'      => 'required|array',
            'warehouse_id.*'    => 'required|exists:warehouse_items,warehouse_id',
            'pre_list_id'       => 'required|exists:warehouse_items,buy_pre_id',
            'increment'         => 'nullable|array',
            'increment.*'       => 'nullable|numeric|min:0',
            'decrement'         => 'nullable|array',
            'decrement.*'       => 'nullable|numeric|min:0',
            'notification_amount' => 'nullable|numeric|min:0',
            'expire_date'       => 'nullable|date',
            'times'             => 'required|string',
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

            // Update warehouse_items
            $insertedBy = auth()->user()->full_name ?? '';

              // Compare old and new values for Bought Up and Amount
            if ((int) $request->old_bought_up !== (int) $validated['bought_up'] 
               || (int)$request->old_amount !== (int)$validated['amount']) 
            {

                foreach ($validated['warehouse_id'] as $index => $warehouseId) 
                {
                    $WarehouseItem = WarehouseItem::where('warehouse_id', $warehouseId)
                        ->where('buy_pre_id', $validated['pre_list_id'])
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
                        $new_bought_up = ($new_in_amount > 0) ? ($new_total / $new_in_amount) : 0;

                        $WarehouseItem->update([
                            'in_amount' => $new_in_amount,
                            'available_amount' => $available_amount,
                            'bought_up' => $new_bought_up, // Updated weighted average price
                            'total' => $new_total, // Updated total value
                            'notification_amount' => $validated['notification_amount'] ?? 0,
                            'unit_id' => $validated['unit_id'],
                            'inserted_by' => $insertedBy,
                            'expire_date' => $validated['expire_date'] ?? null,
                            'times' => $validated['times'],
                        ]);
                    } 
                    // decreate the amounts
                    else if (!empty($validated['decrement'][$index]) && $validated['decrement'][$index] > 0) {

                        // Calculate how much value needs to be removed from the total
                        $removed_total = $WarehouseItem->bought_up * $validated['decrement'][$index];

                        // Adjust stock quantities
                        $new_in_amount = $WarehouseItem->in_amount - $validated['decrement'][$index];
                        $available_amount  = $WarehouseItem->available_amount - $validated['decrement'][$index];

                        // Ensure stock never goes negative
                        if ($new_in_amount < 0 || $available_amount < 0) {
                            throw new \Exception("Stock cannot be negative for warehouse ID: $warehouseId");
                        }

                        // Adjust total by subtracting the removed total instead of adding
                        $new_total = $WarehouseItem->total - $removed_total;

                        // Recalculate bought_up only if there's still stock left
                        $new_bought_up = ($new_in_amount > 0) ? ($new_total / $new_in_amount) : 0;
                        
    
                        $WarehouseItem->update([
                            'in_amount' => $new_in_amount,
                            'available_amount' => $available_amount,
                            'bought_up' => $new_bought_up,
                            'total' => $new_total, // Use adjusted total
                            'notification_amount' => $validated['notification_amount'] ?? 0,
                            'unit_id' => $validated['unit_id'],
                            'inserted_by' => $insertedBy,
                            'expire_date' => $validated['expire_date'] ?? null,
                            'times' => $validated['times'],
                        ]);
                    }
                    else 
                    {

                        // ممکن قیمت فی واحد تغیر کرده باشد
                        /**
                         * به تعداد ۲۰ دانه با فی قیمت ۱۰۰ افغانی ثبت داشتیم
                         * حالا به تعداد ۵ دانه را به قیمت ۱۲۰ افغانی باید ثبت نماییم
                         * باید اول قیمت فی واحد دریافت گردد بعدا ضرب تعداد شود تا مجموع قیمت را بکشد
                         * 
                         * suppose I have 100 items in stock and bought unit price was 150; total is 1500
                         * now i have bought 5 items and bought unit price is 200 and I have stored and this 5 items combined with 100 items and right now the total amount is = 105 and the unit price is 152.38
                         * if a customer edit the last record and increase the unit price of 5 items to 220 ?
                         * now how to update the stock ?
                         * 
                         * Initial stock: 100 items with a unit price of 150 (total value = 100 * 150 = 1500).
                         * Recently bought: 5 items with a unit price of 200 (total value = 5 * 200 = 1000).
                         * Combined stock: 105 items with a unit price of 152.38 (total value = 105 * 152.38 = 1590).
                         * Ask from ChatGPT
                         */
                        // ---------------- Update new unit price -----------------------------
                        if ((int) $request->old_bought_up !== (int) $validated['bought_up']) {
                            // Calculate the new total for the bought items
                            $new_total = $validated['bought_up'] * $validated['amount'][$index];
                            $new_in_amount = $WarehouseItem->in_amount;
                            $available_amount = $WarehouseItem->available_amount;
                        
                            // Ensure stock never goes negative
                            if ($new_in_amount < 0 || $available_amount < 0) {
                                throw new \Exception("Stock cannot be negative for warehouse ID: $warehouseId");
                            }
                        
                            // Check if the bought_up has increased or decreased
                            if ($validated['bought_up'] > $request->old_bought_up) {
                                // Case: Increase in bought_up
                        
                                // Update the bought_up and total values accordingly
                                $new_bought_up = ($new_in_amount > 0) ? (($WarehouseItem->total + $new_total) / $new_in_amount) : 0;
                        
                                $WarehouseItem->update([
                                    'bought_up' => $new_bought_up,
                                    'total' => $new_bought_up * $new_in_amount,
                                    'notification_amount' => $validated['notification_amount'] ?? 0,
                                    'unit_id' => $validated['unit_id'],
                                    'inserted_by' => $insertedBy,
                                    'expire_date' => $validated['expire_date'] ?? null,
                                    'times' => $validated['times'],
                                ]);

                            } elseif ($validated['bought_up'] < $request->old_bought_up) {
                                // Case: Decrease in bought_up
                        
                                // Calculate the difference in total value due to the decrease
                                $difference = $request->old_bought_up * $validated['amount'][$index] - $new_total;
                                // Ensure the new total value doesn't go negative
                                $new_total_value = $WarehouseItem->total - $difference;
                        
                                // Calculate the new bought_up value
                                $new_bought_up = ($new_in_amount > 0) ? ($new_total_value / $new_in_amount) : 0;
                        
                                // Update the warehouse item with the decreased values
                                $WarehouseItem->update([
                                    'bought_up' => $new_bought_up,
                                    'total' => $new_bought_up * $new_in_amount,
                                    'notification_amount' => $validated['notification_amount'] ?? 0,
                                    'unit_id' => $validated['unit_id'],
                                    'inserted_by' => $insertedBy,
                                    'expire_date' => $validated['expire_date'] ?? null,
                                    'times' => $validated['times'],
                                ]);
                            }
                        }
                        // ---------------- / Update new unit price -----------------------------


    
                    }
                }

            }
            


            DB::commit();
            Session::flash('notification', [
                'message' => 'موفقانه ویرایش گردید',
                'type' => 'success',
            ]);

            return redirect()->route('boughtList.edit', ['times' => $validated['times']]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error in updateItemAndWarehouseItems: ' . $e->getMessage());

            Session::flash('notification', [
                'message' => 'ویرایش نگردید',
                'type' => 'danger',
            ]);

            return redirect()->route('boughtList.edit', ['times' => $validated['times']]);
        }
    }

    
    
    /**
     * get single record from from warehouses for delete or decrement
     * from this page : http://127.0.0.1:8000/boughtList/edit/1739721412
     * for testing: http://127.0.0.1:8000/boughtList/getWarehouseListForDelete/150
     */
    public function getWarehouseListForDelete(string $id)
    {
        $units = Unit::select('id','name')->get();
        $boughtItemDetails = BoughtItemDetails::with(['accountRelation','preListRelation','unitRelation'])->where('id', $id)->first();

        if (!$boughtItemDetails) {
            return response()->json(['error' => 'Bought Item not found'], 404);
        }

        $boughtItemDetailsId = $boughtItemDetails->id ?? 0;
        $boughtItemDetailsAmount = $boughtItemDetails->amount ?? 0 ;
        $preListId = $boughtItemDetails->pre_list_id ?? 0 ;
        $times = $boughtItemDetails->times ?? 0 ;


        $warehouseItems = WarehouseItem::with('warehouseRelation')
            ->where('buy_pre_id', (int) $boughtItemDetails->pre_list_id) // Ensure correct type
            ->get();

        //  return response()->json(['boughtItemDetails' => $boughtItemDetails]);
        // return response()->json(['warehouseItems' => $warehouseItems]);
        //  return response()->json(['boughtItemDetailsAmount' => $boughtItemDetailsAmount,'boughtItemDetailsId' => $boughtItemDetailsId]);

        return view('buy.bought.deleteModalContent', compact('warehouseItems', 'boughtItemDetailsId', 'boughtItemDetailsAmount','preListId','times'));
    }

    /**
     * delete a single item during buying form
     */

    public function deleteSingleItem(Request $request)
    {
        DB::beginTransaction();
    
        try {
            // Get BoughtItemDetails
            $boughtItemDetails = BoughtItemDetails::findOrFail($request->delete_id);
            $boughtItemDetailsTotal = $boughtItemDetails->total ?? 0;
            $boughtItemDetails->delete();
    
            // Ensure the current user ID or another identifier is set for inserted_by
            $insertedBy = auth()->user()->id;
    
            // Process WarehouseItems
            foreach ($request->warehouse_id as $index => $warehouseId) 
            {
                $WarehouseItem = WarehouseItem::where('warehouse_id', $warehouseId)
                    ->where('buy_pre_id', $request->preListId)
                    ->first();
    
                if (!$WarehouseItem) {
                    continue; // Skip if not found
                }
    
                // Handle deletion or decrement
                if ($request->delete_amount > 0) 
                {
                    if ((int)$request->decrement[$index] === (int)$WarehouseItem->available) {
                        $WarehouseItem->delete(); // Delete if available matches decrement
                    } 
                    elseif ($WarehouseItem->available > $request->decrement[$index]) 
                    {
                        // Adjust stock values
                        $newTotal = $WarehouseItem->total - $boughtItemDetailsTotal;
                        $newInAmount = $WarehouseItem->in_amount - $request->decrement[$index];
                        $availableAmount = $WarehouseItem->available_amount - $request->decrement[$index];
    
                        // Ensure stock never goes negative
                        if ($newInAmount < 0 || $availableAmount < 0) {
                            throw new \Exception("Stock cannot be negative for warehouse ID: $warehouseId");
                        }
    
                        $newBoughtUp = ($newInAmount > 0) ? (($WarehouseItem->total + $newTotal) / $newInAmount) : 0;
    
                        // Update WarehouseItem
                        $WarehouseItem->update([
                            'in_amount' => $newInAmount,
                            'available_amount' => $availableAmount,
                            'total' => $newTotal,
                            'inserted_by' => $insertedBy // Ensure this is defined
                        ]);
                    }
                }
            }
    
            DB::commit();
    
            Session::flash('notification', [
                'message' => 'موفقانه حذف گردید',
                'type' => 'success',
            ]);
    
            return redirect()->route('boughtList.edit', ['times' => $request->times]);
        } catch (\Exception $e) {
            DB::rollBack();
    
            \Log::error('Error deleting records: ' . $e->getMessage());
    
            Session::flash('notification', [
                'message' => ' حذف نگردید',
                'type' => 'danger',
            ]);
    
            return redirect()->route('boughtList.edit', ['times' => $request->times] );
        }
    }
    

    /**
    * Remove the specified resource from storage.
    */
    public function destroy(string $billno)
    {
        DB::beginTransaction();
        try {
            // Delete all related records directly
            BoughtItemDetails::where('billno', $billno)->delete();
            BoughtItem::where('billno', $billno)->delete();
    
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

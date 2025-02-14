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

use App\Models\Setting\Account;
use Yajra\DataTables\Facades\DataTables;


class BoughtDetailsController2 extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $boughtList = BoughtItemDetails::with(['boughtItemRelation','preListRelation'])->get();
        // return response()->json($boughtItems);
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


        // return response()->json($preLists);
        return view('buy.bought.create',compact('currencies','customers','todaysDate','ownBanks','preLists','units','warehouses'));
    }

    /**
     * Store a newly created resource in storage.
    */
    public function store(Request $request)
    {
        // return response()->json(['data' => $request->all()]);
        // "data": {
        //     "_token": "wqEcKxgTEbPs8dgsBmdKRP0gNpLp1xS5gcXjVayo",
        //     "customer_account_id": "24",
        //     "todays_date": "1403-11-16",
        //     "billno": "64",
        //     "from_account_id": "22",
        //     "pre_list_id": "8",
        //     "amount": "65",
        //     "unit_id": "3",
        //     "bought_up": "8",
        //     "expire_date": "1403-11-25",
        //     "discount": "17",
        //     "transport_amount": "76",
        //     "note": "Ut animi minus aut",
        //     "warehouse_id": [
        //         "15",
        //         "14"
        //     ],
        //     "warehouse_amount": [
        //         "37",
        //         "33"
        //     ],
        //     "warehouse_up": [
        //         "59",
        //         "22"
        //     ]
        // }

        // 1: add data in to bought_item_details table

        $short_date = $request->todays_date ?? Jalalian::now()->format('Y-m-d');
        $date = explode('-', $short_date);
        $year = $date[0];
        $month = $date[1];
        $day = $date[2];
        $full_date =  $year.'-'.$month.'-'.$day.' '.Date('H:i:s A');
    
        // $journalCode = Journal::latest('code')->value('code');
        // $newJournalCode = $journalCode ? $journalCode + 1 : 1;
        $newJournalCode = Journal::max('code') + 1;
        $times = time();

        
         $note = "Total:".$request->payable.", Paid:".$request->cur_pay.", Remained:".$request->remained."";
         $preLists = BuyPreList::select('branch_id')->where('id',$request->pre_list_id)->first();
         $branch_id = $preLists->branch_id;
         // Start the transaction
         DB::beginTransaction();
    
         try {
            //  insert in to bought_items table 
             $BoughtItem = $this->insertBoughtItems($request);
             
             // Process each row of the submitted form
             if($BoughtItem) 
             {
                 foreach ($request->pre_list_id as $index => $preListId) {
                     BoughtItemDetails::create([
                         'billno'     => $request->billno,
                         'bought_item_id'    => $BoughtItem->id,
                         'pre_list_id' => $preListId,
                         'amount' => $request->amount[$index],
                         'unit_id' => $request->unit_id[$index],
                         'bought_up' => $request->bought_up[$index],
                         'total' => $request->total[$index],
                         'expire_date' => $request->expire_date[$index] ?? null,
                         'is_moved' => 0,
                         'times' => $times
                     ]);
                 }
             }


           DB::commit();
           return response()->json(['status' => 'success'], 200);

        } catch (\Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollBack();
            // Optionally, log the error for debugging
            \Log::error('Error storing journal entry', ['error' => $e]);

            return response()->json(['status' => 'success'], 404);
        }


        // 2: add data in to  warehouse_items table
        // 3: fetch inserted data from bought_item_details
        
    }

    public function insertBoughtItems($request,$branch_id,$newJournalCode,)
    {
        BoughtItem::create([
            'customer_account_id' => $request->customer_account_id, 
            'branch_id'           => $branch_id,
            'billno'              => $request->billno,
            'journal_code'        => $newJournalCode,
            'total_price'         => $request->total_price,
            'discount'            => $request->discount ?? 0,
            'payable'             => $request->payable,
            'cur_pay'             => $request->cur_pay,
            'remained'            => $request->remained,
            'account_id'          => $request->from_account_id,
            'currency_id'         => $request->currency_id,
            'trans_spend'         => $request->trans_spend  ?? 0, 
            'trans_account_id'    => $request->trans_account_id ?? 0,
            'note'                => $note,     
            'idate'               => $short_date,    
            'year'                => $year, 
            'month'               => $month, 
            'day'                 => $day, 
            'iby'                 => Session::get('name', ''), 
            'times'               => $times
        ]); 
    }


    /**
     * Store a newly created resource in storage.
    */
    public function store2(Request $request)
    {
        // return response()->json(['data' => $request->all()]);

        // Validate the request data
        $request->validate([
            'pre_list_id' => 'required|array',
            'pre_list_id.*' => 'required|integer|exists:bought_item_pre_lists,id',
            'amount' => 'required|array',
            'amount.*' => 'required|numeric|min:0.01',
            'unit_id' => 'required|array',
            'unit_id.*' => 'required|integer|exists:units,id',
            'bought_up' => 'required|array',
            'bought_up.*' => 'required|numeric|min:0.01',
            'total' => 'required|array',
            'total.*' => 'required|numeric|min:0.01',
            'expire_date' => 'nullable|array',
            'expire_date.*' => 'nullable|date',

            'billno'   => 'required|integer|min:0',
            'customer_account_id' => 'required|integer|exists:accounts,id',
            'total_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'payable' => 'required|numeric|min:0',
            'cur_pay' => 'required|numeric|min:0',
            'remained' => 'required|numeric|min:0',
            'from_account_id' => 'required|integer|exists:accounts,id',
            'currency_id' => 'required|integer|exists:currencies,id',
            'trans_spend' => 'nullable|numeric|min:0',
            'trans_account_id' => 'nullable|integer|exists:accounts,id',
            'note' => 'nullable|string|max:255',
        ]);
            

        $short_date = $request->todays_date ?? Jalalian::now()->format('Y-m-d');
        $date = explode('-', $short_date);
        $year = $date[0];
        $month = $date[1];
        $day = $date[2];
        $full_date =  $year.'-'.$month.'-'.$day.' '.Date('H:i:s A');
    
        // $journalCode = Journal::latest('code')->value('code');
        // $newJournalCode = $journalCode ? $journalCode + 1 : 1;
        $newJournalCode = Journal::max('code') + 1;
        $times = time();

        
         $note = "Total:".$request->payable.", Paid:".$request->cur_pay.", Remained:".$request->remained."";
         $preLists = BuyPreList::select('branch_id')->where('id',$request->pre_list_id)->first();
         $branch_id = $preLists->branch_id;
         // Start the transaction
        //  DB::beginTransaction();
    
        //  try {
            //  insert in to bought_items table 
             $BoughtItem =  BoughtItem::create([
                 'customer_account_id' => $request->customer_account_id, 
                 'branch_id'           => $branch_id,
                 'billno'              => $request->billno,
                 'journal_code'        => $newJournalCode,
                 'total_price'         => $request->total_price,
                 'discount'            => $request->discount ?? 0,
                 'payable'             => $request->payable,
                 'cur_pay'             => $request->cur_pay,
                 'remained'            => $request->remained,
                 'account_id'          => $request->from_account_id,
                 'currency_id'         => $request->currency_id,
                 'trans_spend'         => $request->trans_spend  ?? 0, 
                 'trans_account_id'    => $request->trans_account_id ?? 0,
                 'note'                => $note,     
                 'idate'               => $short_date,    
                 'year'                => $year, 
                 'month'               => $month, 
                 'day'                 => $day, 
                 'iby'                 => Session::get('name', ''), 
                 'times'               => $times
             ]); 
             
             // Process each row of the submitted form
             if($BoughtItem) 
             {
                 foreach ($request->pre_list_id as $index => $preListId) {
                     BoughtItemDetails::create([
                         'billno'     => $request->billno,
                         'bought_item_id'    => $BoughtItem->id,
                         'pre_list_id' => $preListId,
                         'amount' => $request->amount[$index],
                         'unit_id' => $request->unit_id[$index],
                         'bought_up' => $request->bought_up[$index],
                         'total' => $request->total[$index],
                         'expire_date' => $request->expire_date[$index] ?? null,
                         'is_moved' => 0,
                         'times' => $times
                     ]);
                 }
             }

            
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
            if(intval($request->cur_pay) === 0 && intval($request->remained) === intval($request->payable))
            { 
                // ثبت قرضه خزانه = Loan Recieved
                Journal::create([
                    'bill_no' => $request->billno,
                    'code' => $newJournalCode,
                    'account_id' => $request->from_account_id,
                    'branch_id' => $branch_id,
                    'amount' => $request->payable,
                    'currency_id' => $request->currency_id,
                    'transaction_type' => 1,
                    'payment_type' => 2,
                    'user_id' => Session::get('userId', 0),
                    'year' => $year,
                    'month' => $month,
                    'day' => $day,
                    'inserted_short_date' => $short_date,
                    'inserted_full_date' => $full_date,
                    'details' => ' قرضه خرید - بل '.' BUY_'.$request->billno,
                    'status' => 3,  
                    'times' => $times,
                    'is_single_record' => 1,
                ]);

                // ثبت طلب مشتری
                Journal::create([
                    'bill_no' => $request->billno,
                    'code' => $newJournalCode,
                    'account_id' => $request->customer_account_id,
                    'branch_id' => $branch_id,
                    'amount' => $request->payable,
                    'currency_id' => $request->currency_id,
                    'transaction_type' => 2,
                    'payment_type' => 2,
                    'user_id' => Session::get('userId', 0),
                    'year' => $year,
                    'month' => $month,
                    'day' => $day,
                    'inserted_short_date' => $short_date,
                    'inserted_full_date' => $full_date,
                    'details' => ' طلب فروشات - بل '.' BUY_'.$request->billno,
                    'status' => 3,  
                    'times' => $times,
                    'is_single_record' => 1,
                ]);
            }
             // کمی شانرا پرداخت کرده و متباقی شانرا قرض انتخاب کرده است
            else if(intval($request->remained) > 0 && intval($request->cur_pay) > 0) 
            {
                // ثبت پرداخت نقدی خزانه = Cache paid
                Journal::create([
                    'bill_no' => $request->billno,
                    'code' => $newJournalCode,
                    'account_id' => $request->from_account_id,
                    'branch_id' => $branch_id,
                    'amount' => $request->cur_pay,
                    'currency_id' => $request->currency_id,
                    'transaction_type' => 2,
                    'payment_type' => 1,
                    'user_id' => Session::get('userId', 0),
                    'year' => $year,
                    'month' => $month,
                    'day' => $day,
                    'inserted_short_date' => $short_date,
                    'inserted_full_date' => $full_date,
                    'details' => 'پرداخت خرید - بل  '.' BUY_'.$request->billno,
                    'status' => 3,  
                    'times' => $times,
                    'is_single_record' => 1,
                ]);

                // ثبت قرضه خزانه = Loan Recieved 
                Journal::create([
                    'bill_no' => $request->billno,
                    'code' => $newJournalCode,
                    'account_id' => $request->from_account_id,
                    'branch_id' => $branch_id,
                    'amount' => $request->remained,
                    'currency_id' => $request->currency_id,
                    'transaction_type' => 1,
                    'payment_type' => 2,
                    'user_id' => Session::get('userId', 0),
                    'year' => $year,
                    'month' => $month,
                    'day' => $day,
                    'inserted_short_date' => $short_date,
                    'inserted_full_date' => $full_date,
                    'details' => ' قرضه خرید - بل '.' BUY_'.$request->billno,
                    'status' => 3,  
                    'times' => $times,
                    'is_single_record' => 1,
                ]);

                // ثبت طلب مشتری = Paid Loan
                Journal::create([
                    'bill_no' => $request->billno,
                    'code' => $newJournalCode,
                    'account_id' => $request->customer_account_id,
                    'branch_id' => $branch_id,
                    'amount' => $request->remained,
                    'currency_id' => $request->currency_id,
                    'transaction_type' => 2,
                    'payment_type' => 2,
                    'user_id' => Session::get('userId', 0),
                    'year' => $year,
                    'month' => $month,
                    'day' => $day,
                    'inserted_short_date' => $short_date,
                    'inserted_full_date' => $full_date,
                    'details' => ' طلب فروشات - بل '.' BUY_'.$request->billno,
                    'status' => 3,  
                    'times' => $times,
                    'is_single_record' => 1,
                ]);
            }
             // قرضدار نمانده است و مکمل پرداخت کرده است
             // تنها از حساب خزانه کم شود
            else if(intval($request->remained) === 0 && intval($request->cur_pay) === intval($request->payable)) 
            {
                // ثبت پرداخت نقدی خزانه = Cache paid
                Journal::create([
                    'bill_no' => $request->billno,
                    'code' => $newJournalCode,
                    'account_id' => $request->from_account_id,
                    'branch_id' => $branch_id,
                    'amount' => $request->cur_pay,
                    'currency_id' => $request->currency_id,
                    'transaction_type' => 2,
                    'payment_type' => 1,
                    'user_id' => Session::get('userId', 0),
                    'year' => $year,
                    'month' => $month,
                    'day' => $day,
                    'inserted_short_date' => $short_date,
                    'inserted_full_date' => $full_date,
                    'details' => 'پرداخت خرید - بل  '.' BUY_'.$request->billno,
                    'status' => 3,  
                    'times' => $times,
                    'is_single_record' => 1,
                ]);
            }

            // ثبت مصارف ترانسپورت
            if(intval($request->trans_spend) > 0 && intval($request->trans_account_id) > 0) 
            {
                // رفت پول نقد از بابت ترانسپورت = Cache paid
                Journal::create([
                    'bill_no' => $request->billno,
                    'code' => $newJournalCode,
                    'account_id' => $request->trans_account_id,
                    'branch_id' => $branch_id,
                    'amount' => $request->trans_spend,
                    'currency_id' => $request->currency_id,
                    'transaction_type' => 2,
                    'payment_type' => 1,
                    'user_id' => Session::get('userId', 0),
                    'year' => $year,
                    'month' => $month,
                    'day' => $day,
                    'inserted_short_date' => $short_date,
                    'inserted_full_date' => $full_date,
                    'details' => 'پرداخت خرید - بل  '.' BUY_'.$request->billno,
                    'status' => 3,  
                    'times' => $times,
                    'is_single_record' => 1,
                ]);
            }

            // DB::commit();
            Session::flash('notification', [
                'message' => 'موفقانه ثبت گردید',
                'type' => 'success',
            ]);
            return redirect()->route('boughtList.index'); 

        // } catch (\Exception $e) {
        //     // Rollback the transaction if an error occurs
        //     DB::rollBack();
        //     // Optionally, log the error for debugging
        //     \Log::error('Error storing journal entry', ['error' => $e]);
    
        //     // Use MessageService to return error message
        //     Session::flash('notification', [
        //         'message' => ' ثبت نگردید',
        //         'type' => 'danger',
        //     ]);
        //      return back();
        // }
    }
    

    /**
    * Store a newly created resource in storage.
    */
    public function store3(Request $request)
    {
        $this->validateRequest($request);
        
        DB::beginTransaction();
        try {
            $times = time();
            $newJournalCode = Journal::max('code') + 1;
            $short_date = $request->todays_date ?? Jalalian::now()->format('Y-m-d');
            $date = explode('-', $short_date);
            $year = $date[0];
            $month = $date[1];
            $day = $date[2];
            $full_date =  $year.'-'.$month.'-'.$day.' '.Date('H:i:s A');

            $branch_id = $this->getBranchId($request->pre_list_id);
            
            $BoughtItem = $this->createBoughtItem($request, $newJournalCode, $short_date, $branch_id, $times);
            
            $this->storeBoughtItemDetails($request, $BoughtItem->id, $times);
            
            $this->handleJournalEntry($request, $short_date, $full_date, $branch_id,  $newJournalCode, $times);
            
            DB::commit();
            Session::flash('notification', [
                'message' => 'موفقانه ثبت گردید',
                'type' => 'success',
            ]);
            return redirect()->route('boughtList.index'); 

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
             return back();
        }
    }

    private function validateRequest($request)
    {
        
        // Define the validation rules
        $rules = [
            'pre_list_id' => 'required|array',
            'pre_list_id.*' => 'required|integer',
            'amount' => 'required|array',
            'amount.*' => 'required|numeric|min:0.01',
            'unit_id' => 'required|array',
            'unit_id.*' => 'required|integer',
            'bought_up' => 'required|array',
            'bought_up.*' => 'required|numeric|min:0.01',
            'total' => 'required|array',
            'total.*' => 'required|numeric|min:0.01',
            'expire_date' => 'nullable|array',
            'expire_date.*' => 'nullable|date',

            'billno'   => 'required|integer|min:0',
            'customer_account_id' => 'required|integer',
            'total_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'payable' => 'required|numeric|min:0',
            'cur_pay' => 'required|numeric|min:0',
            'remained' => 'required|numeric|min:0',
            'from_account_id' => 'required|integer',
            'currency_id' => 'required|integer',
            'trans_spend' => 'nullable|numeric|min:0',
            'trans_account_id' => 'nullable|integer',
            'note' => 'nullable|string|max:255',
        ];

        // Define custom error messages in Persian
        $messages = [
            'required' => ':attribute الزامی است.',
            'integer' => ':attribute باید یک عدد صحیح باشد.',
            'numeric' => ':attribute باید یک مقدار عددی باشد.',
            'min' => ':attribute نباید کمتر از :min باشد.',
            'exists' => ':attribute انتخاب شده معتبر نیست.',
            'date' => ':attribute باید یک تاریخ معتبر باشد.',
            'string' => ':attribute باید متن باشد.',
            'max' => ':attribute نباید بیشتر از :max کاراکتر باشد.',
            
            'attributes' => [
                'pre_list_id' => 'لیست پیش خرید',
                'pre_list_id.*' => 'لیست پیش خرید',
                'amount' => 'مقدار',
                'amount.*' => 'مقدار',
                'unit_id' => 'واحد',
                'unit_id.*' => 'واحد',
                'bought_up' => 'قیمت خرید',
                'bought_up.*' => 'قیمت خرید',
                'total' => 'جمع کل',
                'total.*' => 'جمع کل',
                'expire_date' => 'تاریخ انقضا',
                'expire_date.*' => 'تاریخ انقضا',
                
                'billno' => 'شماره فاکتور',
                'customer_account_id' => 'حساب مشتری',
                'total_price' => 'مجموع قیمت',
                'discount' => 'تخفیف',
                'payable' => 'قابل پرداخت',
                'cur_pay' => 'مبلغ پرداخت شده',
                'remained' => 'مانده',
                'from_account_id' => 'از حساب',
                'currency_id' => 'واحد پول',
                'trans_spend' => 'هزینه انتقال',
                'trans_account_id' => 'حساب انتقال',
                'note' => 'یادداشت',
            ],
        ];

        // Perform the validation manually
        $validator = Validator::make($request->all(), $rules, $messages);

        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
        }
}


    private function getBranchId($preListIds)
    {
        return BuyPreList::whereIn('id', $preListIds)->pluck('branch_id')->first();
    }

    private function createBoughtItem($request, $newJournalCode, $short_date, $branch_id, $times)
    {
        $date = explode('-', $short_date);
        $year = $date[0];
        $month = $date[1];
        $day = $date[2];
        $note = "Total:".$request->payable.", Paid:".$request->cur_pay.", Remained:".$request->remained."";
        $BoughtItem =  BoughtItem::create([
            'customer_account_id' => $request->customer_account_id, 
            'branch_id'           => $branch_id,
            'billno'              => $request->billno,
            'journal_code'        => $newJournalCode,
            'total_price'         => $request->total_price,
            'discount'            => $request->discount ?? 0,
            'payable'             => $request->payable,
            'cur_pay'             => $request->cur_pay,
            'remained'            => $request->remained,
            'account_id'          => $request->from_account_id,
            'currency_id'         => $request->currency_id,
            'trans_spend'         => $request->trans_spend  ?? 0, 
            'trans_account_id'    => $request->trans_account_id ?? 0,
            'note'                => $note,     
            'idate'               => $short_date,    
            'year'                => $year, 
            'month'               => $month, 
            'day'                 => $day, 
            'iby'                 => auth()->user()->name ?? '',
            'times'               => $times
        ]); 
    }

    private function storeBoughtItemDetails($request, $boughtItemId, $times)
    {
        $boughtItems = [];
        foreach ($request->pre_list_id as $index => $preListId) {
            $boughtItems[] = [
                'billno' => $request->billno,
                'bought_item_id' => $boughtItemId,
                'pre_list_id' => $preListId,
                'amount' => $request->amount[$index],
                'unit_id' => $request->unit_id[$index],
                'bought_up' => $request->bought_up[$index],
                'total' => $request->total[$index],
                'expire_date' => $request->expire_date[$index] ?? null,
                'is_moved' => 0,
                'times' => $times
            ];
        }
        BoughtItemDetails::insert($boughtItems);
    }

    private function handleJournalEntry($request, $short_date, $full_date, $branch_id, $newJournalCode, $times)
    {
            $date = explode('-', $short_date);
            $year = $date[0];
            $month = $date[1];
            $day = $date[2];
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
                // ثبت قرضه خزانه = Loan Recieved
                $details =  ' قرضه خرید - بل '.' BUY_'.$request->billno;
                $this->createJournalEntry($request, $newJournalCode, $request->from_account_id,   $request->payable, $branch_id, $ttype = "1", $ptype="2", $date, $short_date,
                $full_date, $details, $times);
                
                // ثبت طلب مشتری
                $details =  ' طلب فروشات - بل '.' BUY_'.$request->billno;
                $this->createJournalEntry($request, $newJournalCode, $request->customer_account_id,  $request->payable, $branch_id, $ttype = "2", $ptype="2", $date, $short_date,
                $full_date, $details, $times);
            }

            // کمی شانرا پرداخت کرده و متباقی شانرا قرض انتخاب کرده است
            else if(intval($request->remained) > 0 && intval($request->cur_pay) > 0) 
            {
                // ثبت پرداخت نقدی خزانه = Cache paid
                $details =  'پرداخت خرید - بل  '.' BUY_'.$request->billno;
                $this->createJournalEntry($request, $newJournalCode, $request->from_account_id, $request->cur_pay, $branch_id, $ttype = "2", $ptype="1", $date, $short_date,
                $full_date, $details, $times);

                // ثبت قرضه خزانه = Loan Recieved 
                $details =  ' قرضه خرید - بل '.' BUY_'.$request->billno;
                $this->createJournalEntry($request, $newJournalCode, $request->from_account_id, $request->remained, $request->from_account_id,$branch_id, $ttype = "1", $ptype="2", $date, $short_date,
                $full_date, $details, $times);
               
                // ثبت طلب مشتری = Paid Loan
                $details =  ' طلب فروشات - بل '.' BUY_'.$request->billno;
                $this->createJournalEntry($request, $newJournalCode, $request->customer_account_id, $request->remained, $branch_id, $ttype = "2", $ptype="2", $date, $short_date,
                $full_date, $details, $times);
            }

             // قرضدار نمانده است و مکمل پرداخت کرده است
             // تنها از حساب خزانه کم شود
            else if(intval($request->remained) === 0 && intval($request->cur_pay) === intval($request->payable)) 
            {
                // ثبت پرداخت نقدی خزانه = Cache paid
                $details =  'پرداخت خرید - بل  '.' BUY_'.$request->billno;
                $this->createJournalEntry($request, $newJournalCode, $request->from_account_id, $request->cur_pay, $branch_id, $ttype = "2", $ptype="1", $date, $short_date,
                $full_date, $details, $times);
            }

            // ثبت مصارف ترانسپورت
            if(intval($request->trans_spend) > 0 && intval($request->trans_account_id) > 0) 
            {
                // رفت پول نقد از بابت ترانسپورت = Cache paid
                $details =  'پرداخت خرید - بل  '.' BUY_'.$request->billno;
                $this->createJournalEntry($request, $newJournalCode, $request->trans_account_id, $request->trans_spend, $branch_id, $ttype = "2", $ptype="1", $date, $short_date,
                $full_date, $details, $times);
            }
            
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
             return back();
        }
    }

    private function createJournalEntry($request, $newJournalCode, $account_id, $amount, $branch_id, $ttype = "2", $ptype="2", $date, $short_date, $full_date, $details, $times)
    {
        Journal::create([
            'bill_no' => $request->billno,
            'code' => $newJournalCode,
            'account_id' => $account_id,
            'branch_id' => $branch_id,
            'amount' => $amount,
            'currency_id' => $request->currency_id,
            'transaction_type' => $ttype,
            'payment_type' => $ptype,
            'user_id' => auth()->user()->id() ?? '',
            'year' =>  $date[0],
            'month' =>  $date[1],
            'day' =>  $date[2],
            'inserted_short_date' => $short_date,
            'inserted_full_date' => $full_date,
            'details' => $details,
            'status' => 3,  
            'times' => $times,
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
    public function update(Request $request, string $id)
    {
        //
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

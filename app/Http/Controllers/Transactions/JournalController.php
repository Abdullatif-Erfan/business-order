<?php

namespace App\Http\Controllers\Transactions;

use App\Services\MessageService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting\Account;
use App\Models\Setting\Currency;
use App\Models\Transaction\Journal;
use App\Models\Setting\Branch;
use App\Models\Setting\OrgBio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Morilog\Jalali\Jalalian;
use Yajra\DataTables\Facades\DataTables;
use App\Services\NumberToWordsService;


class JournalController extends Controller
{
    protected $branch_id, $isAdmin, $full_name, $numberToWordsService;

    // Inject the message service into the controller
    public function __construct(NumberToWordsService $numberToWordsService)
    {
        $this->numberToWordsService = $numberToWordsService;
        if(auth()->check())
        {
            $this->branch_id = session('branch_id', auth()->check() ? auth()->user()->branch_id : 0);
            $this->isAdmin = session('isAdmin', auth()->check() ? auth()->user()->isAdmin == 1 : false);
            $this->full_name = session('full_name', auth()->check() ? auth()->user()->full_name : 0);
            
        } else {
            $this->branch_id = 0;
            $this->isAdmin = false;
            $this->full_name = '';
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $journals = Journal::with(['accountRelation' => function($query){
        //     $query->select('id','name');
        // },'currencyRelation' => function($query){
        //     $query->select('id','name','symbols','color');
        // }])
        // ->select('id','code','bill_no','amount','account_id','transaction_type','currency_id','details','inserted_short_date','status','times')
        // ->orderBy('id', 'DESC')
        // ->get();

        // return response()->json(['data' => $journals]);

        // $user = auth()->user();
        // $branch_id = $user->branch_id ?? 0;
        // $this->branch_id = $branch_id;

        // dd(session('notification'));

        $accounts = Account::get();
        $currencies = Currency::all();
        $orgbios = OrgBio::all();

        // return response()->json(['data' =>  $orgbios[0]->header]);
        
        return view('transactions.journals.list',compact('accounts','currencies','orgbios'));
    }

    /**
     * Show the journal data
     */
    public function getData(Request $request)
    {
        // $user = auth()->user();
        // $branch_id = $user->branch_id ?? 0;

        /**
         * status: 1: old journal, 2: journal, 3:income, 4:expense, 5:salary, 6:participants, 7:buy, 8:sales, 9:other
         */
        // $journals = Journal::with(['accountRelation' => function($query){
        //     $query->select('id','name');
        // },'currencyRelation' => function($query){
        //     $query->select('id','name','symbols','color');
        // }])

        // $journals = Journal::with(['accountRelation','currencyRelation'])
        // ->select('id','code','bill_no','amount','account_id','transaction_type','payment_type','options','option_label','currency_id','details','inserted_short_date','status','times','is_single_record','journals.branch_id')
        // ->where('accountRelation.branch_id', $this->branch_id)
        // ->orderBy('id', 'DESC');

        $journals = Journal::with(['accountRelation', 'currencyRelation'])
        ->select('id', 'code', 'bill_no', 'amount', 'account_id', 'transaction_type', 'payment_type', 'options', 'option_label', 'currency_id', 'details', 'idate', 'status', 'times', 'is_single_record')
        ->orderBy('id', 'DESC');

        // Apply filters if provided
        if ($request->account_id) {
            $journals->where('account_id', $request->account_id);
        }
        if ($request->currency_id) {
            $journals->where('currency_id', $request->currency_id);
        }
       
        if ($request->start_date && $request->end_date) {
            $journals->whereBetween('idate', [$request->start_date, $request->end_date]);
        } elseif ($request->start_date) {
            $journals->whereDate('idate', '=', $request->start_date);
        } elseif ($request->end_date) {
            $journals->whereDate('idate', '>=', $request->end_date); // Until today
        }

        if ($request->code_number) {
            $journals->where('code', 'LIKE', "%{$request->code_number}%");
        }
        if ($request->bill_number) {
            $journals->where('bill_no', 'LIKE', "%{$request->bill_number}%");
        }
        
         // check if searched_account_id is belongs to company accounts
         $isCompanyAccount = Account::whereIn('account_type_id', [1,6])->where('id', $request->account_id)->exists();

        return DataTables::of($journals)
            
            ->addIndexColumn()
           
            ->addColumn('accountRelation', function ($journal) {
                return optional($journal->accountRelation)->name ?? '';
            })
            
            // cacheRecieved = t1p1 = دریافت نقد
            ->addColumn('cacheRecieved', function ($journal) {
                if (($journal->transaction_type == 1 && $journal->payment_type == 1)) {  return number_format($journal->amount,2); }
            })

               // cachePaid  = t2p1 = پرداخت نقد
            ->addColumn('cachePaid', function ($journal) {
                if (($journal->transaction_type == 2 && $journal->payment_type == 1)) {  return number_format($journal->amount,2); }
            })
            
            // loanRecieved = t1p2 = قرضه
            ->addColumn('loanRecieved', function ($journal) {
               if (($journal->transaction_type == 1 && $journal->payment_type == 2)) {  return number_format($journal->amount,2); }
            })
            

           // loanPaid = t2p2 = طلب
            ->addColumn('loanPaid', function ($journal) {
               if (($journal->transaction_type == 2 && $journal->payment_type == 2)) {  return number_format($journal->amount,2); }
           })
            

            ->addColumn('currency', function ($journal) {
                return '<i style="font-size:14px;color:'.optional($journal->currencyRelation)->color.'">'.optional($journal->currencyRelation)->symbols.'</i>';
            })
            ->addColumn('actions', function ($journal) {
                return $journal->status == 2 ? '<a href="journal/details/'.$journal->times.'" class="hidden-print"><i class="fas fa-eye viewAccount" data-id="' . $journal->id . '" style="font-size:20px;"></i></a>' : '';
            })
            ->rawColumns(['actions','currency'])
            ->with([
                'isCompanyAccount' => $isCompanyAccount ?? 0 
            ])

            ->setRowClass(function ($journal) {
                return $journal->status == 11 ? 'clearance-row bg-green' : ''; // Example: Add class if status is 9
            })
            
            ->make(true);
    }


    /**
     * Show journal details
     */
    public function details(Request $request, $times)
    {
        // dd(Journal::with('userRelation')->whereNotNull('user_id')->get()->toArray());

        // $journals = Journal::with(['accountRelation' => function($query){
        //     $query->select('id','name');
        // },'currencyRelation' => function($query){
        //     $query->select('id','name','symbols','color');
        // },'userRelation' => function($query) {
        //    $query->select('id', 'full_name')->addSelect('id');
        // }])
        // ->select('id','code','bill_no','amount','account_id','transaction_type','currency_id','details','inserted_full_date','status','times','is_single_record')
        // ->where('times',$times)
        // ->orderBy('id', 'DESC')->get();


        // dd($this->numberToWordsService->convertNumber(1000));  // Should be "یک هزار"
        // dd($this->numberToWordsService->convertNumber(5000));  // Should be "پنج هزار"
        // dd($this->numberToWordsService->convertNumber(1000000.00)); // Should be "یک میلیون"


        $journals = Journal::with(['accountRelation', 'currencyRelation','branchRelation'])
        ->where('times', $times)
        ->where('branch_id', $this->branch_id)
        ->orderBy('id', 'DESC')
        ->get();

        // Convert amount to words
        foreach ($journals as $journal) {
            $journal->amount_in_words = $this->numberToWordsService->convertNumber($journal->amount);
        }

        // return response()->json(['data' => $journals]);
        // return response()->json(['data' => $journals[0]->accountRelation->name]);

        return view('transactions.journals.details',compact('journals'));
        
    }

    /**
     * Show create form
     */
    public function create()
    {
        // $query = $this->db->get('currency'); 		   
        // $data['currency'] = $query->result_array();
        // $data['accounts'] = $this->journals->getAccounts();
        // $data['customers'] = $this->journals->getCustomers();

        // $this->db->order_by('id','DESC');
        // $query = $this->db->get('branch'); 		   
        // $data['branch'] = $query->result_array();

        $accounts = Account::where('branch_id', $this->branch_id)->get();
        $currencies = Currency::all();
        $branchs = Branch::where('id',$this->branch_id)->get();
        $todaysDate = Jalalian::now()->format('Y-m-d');
        $default_currency = Currency::select('id','name','symbols')->where('is_base','=','yes')->first();

        return view('transactions.journals.create',compact('accounts','currencies','branchs','default_currency','todaysDate'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // return ['formData' => $request->all()];

        $this->journalValidation($request);
        $newJournalCode = DB::table('journals')->where('journals.branch_id', $this->branch_id)->lockForUpdate()->max('code') + 1;
        // Start the transaction
        DB::beginTransaction();
        try 
        {

        //    if(intval($request->options) === 4)
        //    {
        //        $checkCode = $this->checkPrevCode($request);
        //         if ($checkCode['status'] == 'failed') {
        //             DB::rollBack();
        //             Session::flash('notification', [
        //                 'message' => $checkCode['message'],
        //                 'type' => 'danger',
        //             ]);
        //             return back();
        //         }
        //    }

           $check = $this->handleJournalEntry($request,$newJournalCode);

           if(!$check)
           { 
                DB::rollBack();
                Session::put('notification', [
                    'message' => __('common.added_successfully'),
                    'type' => 'danger',
                ]);
                return back();
           }

            // Commit the transaction
            DB::commit();
            // Session::flash('notification', [
            //     'message' => 'موفقانه ثبت گردید',
            //     'type' => 'success',
            // ]);

            session()->put('notification', [
                'message' => __('common.added_successfully'),
                'type' => 'success',
            ]);

            return redirect()->route('journal.index'); 

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error storing journal entry: ' . $e->getMessage());
            Session::put('notification', [
                'message' => __('common.add_failed'),
                'type' => 'danger',
            ]);
             return back();
        }
    }
    
    private function journalValidation($request)
    {   
        // Remove commas from 'from_amount' and 'to_amount'
        $request->merge([
            'from_amount' => str_replace(',', '', $request->from_amount),
            'to_amount' => str_replace(',', '', $request->to_amount),
        ]);
    
        // Validation
        $validated = $request->validate([
            'options'  => 'required|numeric',
            'bill_no' => 'nullable|numeric|min:1',
            
            'from_account_id' => 'required|exists:accounts,id',
            'from_amount' => 'required|numeric|min:1',  
            'from_currency_id' => 'required|exists:currencies,id',
            'from_details' => 'required|string|max:255',
            
            'to_account_id' => 'required|exists:accounts,id',
            'to_currency_id' => 'required|exists:currencies,id',
            'to_amount' => 'required|numeric|min:1',
            'to_details' => 'required|string|max:255',

            'doc' => 'nullable|mimes:jpg,jpeg,png,pdf,docx,xlsx|max:2048', // Optional file field, max 2MB
        ]);
    }

    private function handleJournalEntry($request,$newJournalCode)
    {
        $todaysDate = $request->todays_date;
        $date = explode('-', $todaysDate);
        $year = $date[0];
        $month = $date[1];
        $day = $date[2];
        $full_date =  $year.'-'.$month.'-'.$day.' '.Date('h:i:s A');

        $times = time();
    
         /**
         * ================================== Journal Roles ========================
         * status:           1: old journal,  2: journal, 3:income, 4:expense, 5:salary, 6:participants, 7:buy, 8:sales, 9:other
         * 
         * transaction_type: 1: recieved      2:paid = 
         * payment_type:     1: cache,        2: loan
         * options:          1: cache2cache,  2:loan2loan, 3:cache2loan, 4:loan2cache
         * 
         * Recieved Loan = قرض گرفتن = t1p2
         * Paid Loan = طلب = t2p2
         * Cache Recieved = دریافت نقد = t1p1
         * Cache Paid = پرداخت نقد = t2p1
         * 
         */
            

        // Start the transaction
        DB::beginTransaction();
    
        try 
        {
            $from_amount = str_replace(',', '', $request->from_amount);
            $from_currency = $request->from_currency_id;
            $from_account_id = $request->from_account_id;
            $from_details = $request->from_details;

            $to_amount = str_replace(',', '', $request->to_amount);
            $to_currency = $request->to_currency_id;
            $to_account_id = $request->to_account_id;
            $to_details = $request->to_details;


            $filePath = null;
            if ($request->hasFile('doc')) {
                $file = $request->file('doc');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('documents'), $fileName);
                $filePath = 'documents/' . $fileName;
            }

            //  if($request->conversion_flag == 1)
            // {
            //     $from_currency = $request->to_currency_id;
            //     $from_amount = str_replace(',', '', $request->to_amount);
            // }

             // معاملات نقد به نقد
             if(intval($request->options) === 1) 
             {
                // ثبت پرداخت توسط پرداخت کننده = paid(ttype=2), cache(ptype=1) 
                $optionLable = __('validate.cache_payment');
                $this->createJournalEntry($request, $optionLable, $from_account_id, $from_currency, $from_amount,
                                          $ttype = "2", $ptype="1", $full_date, $date, $from_details, $newJournalCode, $times, $filePath);
                
     
                 // ثبت دریافت توسط دریافت کننده = recieved(ttype=1) cache(ptype=1)
                 $optionLable = __('validate.cache_recieved');
                 $this->createJournalEntry($request, $optionLable, $to_account_id, $to_currency, $to_amount,
                                          $ttype = "1", $ptype="1", $full_date, $date, $to_details, $newJournalCode, $times, $filePath);
             } 
          
             // معاملات نسیه به نسیه
             else if(intval($request->options) === 2)
             {
                 // ثبت طلب توسط پرداخت کننده = paid(ttype=2), loan(ptype=2) 
                $optionLable = __('validate.loan_paid');
                $this->createJournalEntry($request, $optionLable, $from_account_id, $from_currency, $from_amount,
                                          $ttype = "2", $ptype="2", $full_date, $date, $from_details, $newJournalCode, $times, $filePath);
                
                 // ثبت قرض توسط دریافت کننده = recieved(ttype=1) loan(ptype=2)
                 $optionLable = __('validate.loan_recieved');
                 $this->createJournalEntry($request, $optionLable, $to_account_id, $to_currency, $to_amount,
                                          $ttype = "1", $ptype="2", $full_date, $date, $to_details, $newJournalCode, $times, $filePath);
             }
             // معاملات نقد به نسیه
             else if(intval($request->options) === 3)
             {

                /**
                 * اگر خزانه یا حساب شرکت باشد یعنی خزانه جدیدا قرض  پرداخت میکند که باید نقد از خزانه کم شود 
                 * if(from_account_id === company_account_id) { Paid Cache = p1t2 } 
                 * ومشتری باید قرضدار ثبت گردد
                 * else { Recieved Loan = t1p2 }
                 * 
                 * اگر شرکت از مشتری پول قرض بیگیرد در اینصورت شرکت یا خزانه باید نقد دریافت کنند
                 * if(to_account_id === company_account_id) { Cache Recieved = p1t1 }
                 * و مشتری باید طلب ثبت گردد
                 * else { Paid Loan = p2t2 }
                 * 
                 */

                // Fetch both account types in a single query
                $companyAccounts = Account::whereIn('account_type_id', [1,6])
                ->whereIn('id', [$request->from_account_id, $request->to_account_id])
                ->where('branch_id', $this->branch_id)
                ->pluck('id')
                ->toArray();

                $isFromCompanyAccount = in_array($request->from_account_id, $companyAccounts);
                $isToCompanyAccount = in_array($request->to_account_id, $companyAccounts);
                
                if($isFromCompanyAccount) // خزانه خودش قرض میدهد
                {
                    // ثبت پرداخت نقد توسط پرداخت کننده = paid(ttype=2), cache(ptype=1) 
                    $optionLable = __('validate.cache_payment');
                    $this->createJournalEntry($request, $optionLable, $from_account_id, $from_currency, $from_amount,
                                             $ttype = "2", $ptype="1", $full_date, $date, $from_details, $newJournalCode, $times, $filePath);
    
                    // ثبت قرض توسط دریافت کننده = recieved(ttype=1) loan(ptype=2)
                    $optionLable = __('validate.loan_recieved');
                    $this->createJournalEntry($request, $optionLable, $to_account_id, $to_currency, $to_amount,
                                            $ttype = "1", $ptype="2", $full_date, $date, $to_details,  $newJournalCode, $times, $filePath);
                } 
                else if($isToCompanyAccount)  // خزانه خودش قرض میگیرد
                {
                    // دریافت نقد توسط خزانه بطور قرض  = Recieved(ttype=1), Caceh(ptype=1) 
                    $optionLable = __('validate.loan_recieved');
                    $this->createJournalEntry($request, $optionLable, $to_account_id, $to_currency, $to_amount,
                    $ttype = "1", $ptype="1", $full_date, $date, $to_details, $newJournalCode, $times, $filePath);
                    
                    // ثبت طلب توسط  مشتری = Paid (ttype=2) loan(ptype=2)
                    $optionLable = __('validate.talab_save');
                    $this->createJournalEntry($request, $optionLable, $from_account_id, $from_currency, $from_amount,
                                            $ttype = "2", $ptype="2", $full_date, $date, $from_details,  $newJournalCode, $times, $filePath);
                }
                else
                {
                    return false;
                }

            }
            // معاملات نسیه به نقد
            else if(intval($request->options) === 4)
            {
                 /**
                 * اگر خزانه یا حساب شرکت باشد یعنی خزانه قبلا قرضدار بوده و حالا قرض خود را پرداخت میکند یعنی پرداخت نقد 
                 * if(to_account_id === company_account_id) { Paid Cache = p1t2 } 
                 * ومشتری باید قرضدار ثبت گردد تااینکه طلب مشتری کم گرد
                 * else { Recieved Loan = t1p2 }
                 * 
                 * اگر خزانه قرض شانرا 
                 * if(to_account_id === company_account_id) { Cache Recieved = p1t1 }
                 * و مشتری باید طلب ثبت گردد
                 * else { Paid Loan = p2t2 }
                 * 
                 */

                // Fetch both account types in a single query
                $companyAccounts = Account::whereIn('account_type_id', [1,6])
                ->whereIn('id', [$request->from_account_id, $request->to_account_id])
                ->where('branch_id', $this->branch_id)
                ->pluck('id')
                ->toArray();

                $isFromCompanyAccount = in_array($request->from_account_id, $companyAccounts);
                $isToCompanyAccount = in_array($request->to_account_id, $companyAccounts);

                if($isToCompanyAccount) // پرداخت کننده قرض مشتری میباشد
                {
                    // بردگی نقد خزانه یا دریافت کننده = recieved(ttype=1) cache(ptype=1)
                    $optionLable = __('validate.cache_recieved'); 
                    $this->createJournalEntry($request, $optionLable, $to_account_id, $to_currency, $to_amount,
                         $ttype = "1", $ptype="1", $full_date, $date, $to_details,  $newJournalCode, $times, $filePath);

                    // ثبت رسیدگی قرض مشتری یا پرداخت کننده = paid(ttype=2), loan(ptype=2) 
                    $optionLable = __('validate.loan_get');
                    $this->createJournalEntry($request, $optionLable, $from_account_id, $from_currency, $from_amount,
                             $ttype = "2", $ptype="2", $full_date, $date, $from_details, $newJournalCode, $times, $filePath);    
                }
                else if($isFromCompanyAccount)  // پرداخت کننده قرض خزانه میباشد
                {
                    // پرداخت نقد از خزانه = paid(ttype=2), cache(ptype=1)
                    $optionLable = __('validate.cache_payment'); 
                    $this->createJournalEntry($request, $optionLable, $from_account_id, $from_currency, $from_amount,
                              $ttype = "2", $ptype="1", $full_date, $date, $from_details, $newJournalCode, $times, $filePath);
                    
                    //  دریافت قرض = Received (ttype=1), loan(ptype=2) 
                    $optionLable = __('validate.loan_get');
                    $this->createJournalEntry($request, $optionLable, $to_account_id, $to_currency, $to_amount,
                           $ttype = "1", $ptype="2", $full_date, $date, $to_details,  $newJournalCode, $times, $filePath);
                } 
                else 
                {
                    return false;
                }

            }

            // Commit the transaction
            DB::commit();
            return true; 

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error storing journal entry: ' . $e->getMessage());
             return false;
        }
    }
      

    private function createJournalEntry($request, $optionLable, $account_id, $currency_id, $amount, $ttype, $ptype,  
        $full_date, $date, $details, $code, $times, $filePath = null)
    {
            $account_type_id = Account::where('id', $account_id)->value('account_type_id');
            $new_details = $request->bijak_code > 0 ? $details.' BN_'.$request->bijak_code : $details;
            /**
             * if conversion_flag == 1, means that foreign currency conversion is done
             *  check if $currency_id != $default_currency_id, means in this recrod should insert 
             *                           (converted_currency,converted_amount,converted_curr_symbol)
             */

            $converted_currency = null;
            $converted_amount   = null;
            $converted_curr_symbol = null;

            // Create the Journal entry
            Journal::create([
                'bill_no' => $request->billno,
                'code' => $code,
                'account_type_id' => $account_type_id,
                'account_id' => $account_id,
                'branch_id' => $request->branch_id,
                'amount' => $amount,
                'currency_id' => $currency_id,

                'converted_currency' => $converted_currency,
                'converted_amount'   => $converted_amount,
                'converted_curr_symbol' => $converted_curr_symbol,

                'transaction_type' => $ttype,
                'payment_type' => $ptype,
                'options' => $request->options,
                'option_label' => $optionLable,
                'dynamic_type' => $request->prev_code ?? null,
                'dt_comment' => $request->prev_code ? 'کد قبلی این معامله': null,
                'is_middle' => 0,
                'user' => auth()->user()->full_name ?? '',
                'year' => $date[0],
                'month' => $date[1],
                'day' => $date[2],
                'inserted_short_date' => $request->todays_date,
                'inserted_full_date' => $full_date,
                'details' => $details,
                'status' => 2,  
                'doc' => $filePath,
                'times' => $times,
                'is_single_record' => 1,
            ]);

            // Log::info('Journal entry created successfully.');
    }


    /**
     * Display the specified resource to print
     */
    public function print(string $times)
    {
        $orgbios = OrgBio::all();
        $journals = Journal::with(['accountRelation', 'currencyRelation','branchRelation'])
        ->where('times', $times)
        ->where('branch_id', $this->branch_id)
        ->orderBy('id', 'DESC')
        ->get();

        // Convert amount to words
        foreach ($journals as $journal) {
            $journal->amount_in_words = $this->numberToWordsService->convertNumber($journal->amount);
        }

        // return response()->json(['data' => $journals]);
        // return response()->json(['data' => $journals[0]->accountRelation->name]);
        
        return view('transactions.journals.print',compact('journals','orgbios'));
    }

  
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $times)
    {
        $accounts = Account::where('branch_id', $this->branch_id)->get();
        $currencies = Currency::all();
        $branchs = Branch::where('id', $this->branch_id)->get();
        $default_currency = Currency::select('id','name','symbols')->where('is_base','=','yes')->first();
        $journals = Journal::with(['accountRelation', 'currencyRelation','branchRelation'])
        ->where('times', $times)
        ->where('branch_id', $this->branch_id)
        ->orderBy('id', 'ASC')
        ->get();

        // return response()->json(['data' => $journals]);
        return view('transactions.journals.edit',compact('accounts','default_currency','currencies','branchs','journals'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        // return [ 'formData' => $request->all() ];
        $this->journalValidation($request);
       
        // Start the transaction
        DB::beginTransaction();
        try 
        {
            if (!$request->from_id || !$request->to_id) {
                return back()->with('notification', [
                    'message' => 'Invalid journal IDs',
                    'type' => 'danger',
                ]);
            }

            // Get the journal entry using the `times` field to locate the correct entry
            $journal1 = Journal::where('id', $request->from_id)->first();
            $journal2 = Journal::where('id', $request->to_id)->first(); 

            $from_account_type_id = Account::where('id', $request->from_account_id)->value('account_type_id');
            $to_account_type_id = Account::where('id', $request->to_account_id)->value('account_type_id');

            if (!$journal1 || !$journal2) {
                Session::put('notification', [
                    'message' => __('common.not_found'),
                    'type' => 'danger',
                ]);
                return back();
            }

            $filePath = null;
            if ($request->hasFile('doc')) {
                $file = $request->file('doc');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = 'documents/' . $fileName;
                $file->move(public_path('documents'), $fileName);
            }

                    

            // Get the current date and time
            $todaysDate = $request->todays_date;
            $date = explode('-', $todaysDate);
            $year = $date[0];
            $month = $date[1];
            $day = $date[2];
            $full_date =  $year.'-'.$month.'-'.$day.' '.Date('h:i:s A');

            $from_amount = str_replace(',', '', $request->from_amount);
            $to_amount = str_replace(',', '', $request->to_amount);
            $from_currency =  $request->from_currency_id;

            if($request->conversion_flag == 1)
            {
                $from_currency = $request->to_currency_id;
                $from_amount = str_replace(',', '', $request->to_amount);
            }

            // Update the first journal entry ("paid cache")
            $journal1->bill_no = $request->bill_no ?? 0;
            $journal1->inserted_full_date = $full_date;
            $journal1->inserted_short_date = $todaysDate;
            $journal1->year = $year;
            $journal1->month = $month;
            $journal1->day = $day;
            $journal1->account_id = $request->from_account_id;
            $journal1->account_type_id = $from_account_type_id;
            $journal1->amount = $from_amount;
            // $journal1->currency_id = $request->from_currency_id;
            $journal1->currency_id = $from_currency;
            $journal1->details = $request->from_details;
            $journal1->user = $this->full_name ?? '';
            $journal1->doc = $filePath;

            $journal1->save();
        
            // =========== Update the second journal entry ("received cache") ======================
            $journal2->bill_no = $request->bill_no;
            $journal2->inserted_full_date = $full_date;
            $journal2->inserted_short_date = $todaysDate;
            $journal2->user = $this->full_name ?? '';
            $journal2->year = $year;
            $journal2->month = $month;
            $journal2->day = $day;
            $journal2->account_type_id = $to_account_type_id;
            $journal2->account_id = $request->to_account_id;
            $journal2->amount = $to_amount;
            $journal2->currency_id = $request->to_currency_id;
            $journal2->details = $request->to_details;
            $journal2->doc = $filePath;
            $journal2->save();


            // Commit the transaction
            DB::commit();
           

            session()->put('notification', [
                'message' => __('common.updated_successfully'),
                'type' => 'success',
            ]);

            return redirect()->route('journal.index'); 

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error  Updating journal entry: ' . $e->getMessage());
            Session::put('notification', [
                'message' => __('common.update_failed'),
                'type' => 'danger',
            ]);
             return back();
        }
    }
    
    private function checkPrevCodeAndUpdate($request)
    {
         // Validate if previous code is provided
        if (empty($request->prev_code) || intval($request->prev_code) <= 0) {
            return [
                'status' => 'failed',
                'message' => 'کد نمبر قرض قبلی را بنویسید',
            ];
        }

        if(empty($request->increment) && empty($request->decrement))
        {
            return [
                'status' => 'success',
                'message' => 'مقدار تغیر نکرده و نیاز به ویرایش نیست',
            ];
        }

        // Remove commas and convert amount to integer
        $from_amount = intval(str_replace(',', '', $request->from_amount));

        // Fetch previous journal entry
        $prev_journal = Journal::select('id', 'amount')
            ->where('code', $request->prev_code)
            ->where('currency_id', $request->from_currency_id)
            ->where('branch_id', $this->branch_id)
            ->where('transaction_type', 2) // Paid
            ->where('payment_type', 2)  // Loan
            ->where('options', 3)  // Had 3 records
            ->where('status', 2) // Belongs to journal entry
            ->where('is_middle', 1) 
            ->first();

        // Check if journal entry exists
        if (!$prev_journal) {
            \Log::error('Journal record not found for prev_code: ' . $request->prev_code);
            return [
                'status' => 'failed',
                'message' => 'ریکارد یافت نشد لطفا کد قرض قبلی را درست وارد نمایید',
            ];
        }

        // Convert previous amount to integer
        $prev_amount = intval($prev_journal->amount);

        // Ensure amounts are valid
        if ($prev_amount <= 0 || $from_amount <= 0) {
            \Log::error('Invalid amount values: prev_amount=' . $prev_amount . ', from_amount=' . $from_amount);
            return [
                'status' => 'failed',
                'message' => 'مقادیر مبلغ معتبر نیستند',
            ];
        }

        // Log before update
        // \Log::info('Updating journal ID: ' . $prev_journal->id . ' | Old Amount: ' . $prev_amount . ' | Deducting: ' . $from_amount);

        // Update the journal amount based on conditions
        if ($prev_amount === $from_amount) {
            $prev_journal->amount = 0;
        } 
        elseif ($prev_amount > $from_amount) 
        {
            if(!empty($request->increment) && intval($request->increment) > 0)
            {
                $prev_journal->amount = $prev_amount - $request->increment;
            }
            else if(!empty($request->decrement) && intval($request->decrement) > 0)
            {
                $prev_journal->amount = $prev_amount + $request->decrement;
            } 
            else 
            {
                return [
                    'status' => 'success',
                    'message' => 'نیاز به آپدیت نیست',
                ];
            }
        } 
        else
        {
            return [
                'status' => 'failed',
                'message' => 'مبلغ وارد شده بالاتر از مبلغ قرضداری قبلی شما میباشد لطفا به اندازه قرضه خود مبلغ را وارد نمایید',
            ];
        }

        // Attempt to save changes
        if ($prev_journal->save()) {
            \Log::info('Journal successfully updated. New Amount: ' . $prev_journal->amount);
            return [
                'status' => 'success',
                'message' => 'بروزرسانی موفقانه انجام شد',
            ];
        } else {
            \Log::error('Journal update failed for ID: ' . $prev_journal->id);
            return [
                'status' => 'failed',
                'message' => 'خطا در بروزرسانی اطلاعات، لطفا دوباره تلاش نمایید',
            ];
        }
    }


    public function update_document(Request $request)
    {
        $request->validate([
            'doc' => 'required|file|mimes:jpg,jpeg,png,pdf,docx,xlsx|max:4048',
        ]);
    
        // Fetch all journals based on the given times
        $journals = Journal::where('times', $request->times)->where('journals.branch_id', $this->branch_id)->get();
    
        // Check if any journals are found
        if ($journals->isEmpty()) {
            return redirect()->back()->with('error', 'No journals found with the given times.');
        }
    
        // Handle file upload if a new file is uploaded
        if ($request->hasFile('doc')) {

            $file = $request->file('doc');
            $fileName = time() . '_' . $file->getClientOriginalName(); 
            $file->move(public_path('documents'), $fileName); 
            $docPath = 'documents/' . $fileName; 
            
            // Loop through each journal and update the doc field
            foreach ($journals as $journal) {
                $journal->doc = $docPath;
                $journal->save();
            }
        }
    
        // Redirect or return response
        return redirect()->route('journal.details', ['times' => $request->times])->with('success', 'Documents updated successfully!');

    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $times)
    {
        // Start the transaction
        DB::beginTransaction();

        try {
            // Find all journal records with the same 'times' value
            $journals = Journal::where('times', $times)
                ->where('branch_id', $this->branch_id)
                ->get();
            
            // return ['data' => $journals];

            // If journals are not empty, proceed with the deletion
            if ($journals->isNotEmpty()) {
                // Loop through each journal and delete its associated file
                foreach ($journals as $journal) {
                   
                        $doc = $journal->doc;
                        if ($doc) {
                            $filePath = public_path('documents/' . $doc);
                    
                            // Make sure it is a file and not a directory
                            if (file_exists($filePath) && is_file($filePath)) {
                                unlink($filePath);
                            }
                        }

                    // Delete the journal record
                    $journal->delete();
                }
             
                  

                // Commit the transaction if everything goes well
                DB::commit();
                session()->put('notification', [
                    'type' => 'success',
                    'message' => __('common.deleted_successfully'),
                ]);
                return redirect()->route('journal.index');

            }

        } catch (\Exception $e) {
            // Rollback the transaction in case of any exception
            DB::rollBack();

            // Log the error
            \Log::error('Error during destroy operation: ' . $e->getMessage());

            // Return error message
            return [
                'status' => 'failed',
                'message' => __('common.deleted_successfully'),
            ];
        }
    }


    
}

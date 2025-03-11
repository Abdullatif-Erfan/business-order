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
    protected $branch_id, $isAdmin, $numberToWordsService;

    // Inject the message service into the controller
    public function __construct(NumberToWordsService $numberToWordsService)
    {
        $this->numberToWordsService = $numberToWordsService;
        if(auth()->check())
        {
            $this->branch_id = session('branch_id', auth()->check() ? auth()->user()->branch_id : 0);
            $this->isAdmin = session('isAdmin', auth()->check() ? auth()->user()->isAdmin == 1 : false);
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

        $accounts = Account::where('branch_id', $this->branch_id)->get();
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
        ->select('id', 'code', 'bill_no', 'amount', 'account_id', 'transaction_type', 'payment_type', 'options', 'option_label', 'currency_id', 'details', 'inserted_short_date', 'status', 'times', 'is_single_record')
        ->whereHas('accountRelation', function($query) {
            $query->where('branch_id', $this->branch_id);
        })
        ->orderBy('id', 'DESC');


        // Apply filters if provided
        if ($request->account_id) {
            $journals->where('account_id', $request->account_id);
        }
        if ($request->currency_id) {
            $journals->where('currency_id', $request->currency_id);
        }
       
        if ($request->start_date && $request->end_date) {
            $journals->whereBetween('inserted_short_date', [$request->start_date, $request->end_date]);
        } elseif ($request->start_date) {
            $journals->whereDate('inserted_short_date', '=', $request->start_date);
        } elseif ($request->end_date) {
            $journals->whereDate('inserted_short_date', '>=', $request->end_date); // Until today
        }

        if ($request->code_number) {
            $journals->where('code', 'LIKE', "%{$request->code_number}%");
        }
        if ($request->bill_number) {
            $journals->where('bill_no', 'LIKE', "%{$request->bill_number}%");
        }
        
         // check if searched_account_id is belongs to company accounts
         $isCompanyAccount = Account::whereIn('account_type_id', [1,6])->where('id', $request->account_id)->where('branch_id', $this->branch_id)->exists();

        return DataTables::of($journals)
            
            ->addIndexColumn()
           
            ->addColumn('accountRelation', function ($journal) {
                return $journal->accountRelation ? $journal->accountRelation->name : '';
            })
            
            // cacheRecieved = t1p1 = دریافت نقد
            ->addColumn('cacheRecieved', function ($journal) {
                if (($journal->transaction_type == 1 && $journal->payment_type == 1)) {  return number_format($journal->amount); }
            })

               // cachePaid  = t2p1 = پرداخت نقد
            ->addColumn('cachePaid', function ($journal) {
                if (($journal->transaction_type == 2 && $journal->payment_type == 1)) {  return number_format($journal->amount); }
            })
            
            // loanRecieved = t1p2 = قرضه
            ->addColumn('loanRecieved', function ($journal) {
               if (($journal->transaction_type == 1 && $journal->payment_type == 2)) {  return number_format($journal->amount); }
            })
            

           // loanPaid = t2p2 = طلب
            ->addColumn('loanPaid', function ($journal) {
               if (($journal->transaction_type == 2 && $journal->payment_type == 2)) {  return number_format($journal->amount); }
           })
            

            ->addColumn('currency', function ($journal) {
                return '<i style="font-size:14px;color:'.$journal->currencyRelation->color.'">'.$journal->currencyRelation->symbols.'</i>';
            })
            ->addColumn('actions', function ($journal) {
                return $journal->status == 2 ? '<a href="journal/details/'.$journal->times.'" class="hidden-print"><i class="fas fa-eye viewAccount" data-id="' . $journal->id . '" style="font-size:20px;"></i></a>' : '';
            })
            ->rawColumns(['actions','currency'])
            ->with([
                'isCompanyAccount' => $isCompanyAccount ?? 0 
            ])
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


        $journals = Journal::with(['accountRelation', 'currencyRelation'])
        ->where('times', $times)
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

        return view('transactions.journals.create',compact('accounts','currencies','branchs','todaysDate'));
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
           $check = $this->handleJournalEntry($request,$newJournalCode);

           if(!$check)
           { 
                DB::rollBack();
                Session::flash('notification', [
                    'message' => ' ثبت نگردید',
                    'type' => 'danger',
                ]);
                return back();
           }

            // Commit the transaction
            DB::commit();
            Session::flash('notification', [
                'message' => 'موفقانه ثبت گردید',
                'type' => 'success',
            ]);
            return redirect()->route('journal.index'); 

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error storing journal entry: ' . $e->getMessage());
            Session::flash('notification', [
                'message' => ' ثبت نگردید',
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
            'branch_id' => 'required|exists:branches,id',
            'bill_no' => 'nullable|numeric|min:1',
            'from_account_id' => 'required|exists:accounts,id',
            'to_account_id' => 'required|exists:accounts,id',

            'from_amount' => 'required|numeric|min:1',  
            'from_currency_id' => 'required|exists:currencies,id',
            'to_amount' => 'required|numeric|min:1',

            'to_currency_id' => 'required|exists:currencies,id',
            'from_details' => 'required|string|max:255',
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
         * transaction_type: 1:recieved       2:paid
         * payment_type:     1: cache,        2: loan
         * options:          1: cache2cache,  2:loan2loan, 3:cache2loan, 4:loan2cache
         * 
         * Recieved Loan = قرض گرفتن
         * Paid Loan = طلب
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

             // معاملات نقد به نقد
             if(intval($request->options) === 1) 
             {
                // ثبت پرداخت توسط پرداخت کننده = paid(ttype=2), cache(ptype=1) 
                $optionLable = 'پرداخت نقد';
                $this->createJournalEntry($request, $optionLable, $from_account_id, $from_currency, $from_amount,
                                          $ttype = "2", $ptype="1", $full_date, $date, $from_details, $newJournalCode, $times);
                
     
                 // ثبت دریافت توسط دریافت کننده = recieved(ttype=1) cache(ptype=1)
                 $optionLable = 'دریافت نقد';
                 $this->createJournalEntry($request, $optionLable, $to_account_id, $to_currency, $to_amount,
                                          $ttype = "1", $ptype="1", $full_date, $date, $to_details, $newJournalCode, $times);
             } 
          
             // معاملات نسیه به نسیه
             else if(intval($request->options) === 2)
             {
                 // ثبت طلب توسط پرداخت کننده = paid(ttype=2), loan(ptype=2) 
                $optionLable = 'پرداخت قرض';
                $this->createJournalEntry($request, $optionLable, $from_account_id, $from_currency, $from_amount,
                                          $ttype = "2", $ptype="2", $full_date, $date, $from_details, $newJournalCode, $times);
                
                 // ثبت قرض توسط دریافت کننده = recieved(ttype=1) loan(ptype=2)
                 $optionLable = 'دریافت قرض';
                 $this->createJournalEntry($request, $optionLable, $to_account_id, $to_currency, $to_amount,
                                          $ttype = "1", $ptype="2", $full_date, $date, $to_details, $newJournalCode, $times);
             }
             // معاملات نقد به نسیه
             else if(intval($request->options) === 3)
             {
                 // ثبت پرداخت نقد توسط پرداخت کننده = paid(ttype=2), cache(ptype=1) 
                 $optionLable = 'پرداخت نقد';
                 $this->createJournalEntry($request, $optionLable, $from_account_id, $from_currency, $from_amount,
                                          $ttype = "2", $ptype="1", $full_date, $date, $from_details, $newJournalCode, $times);

                  // ثبت  طلب برای  پرداخت کننده = paid(ttype=2), loan(ptype=2) 
                 $optionLable = 'ثبت طلب';
                 $this->createJournalEntry($request, $optionLable, $from_account_id, $from_currency, $from_amount,
                                          $ttype = "2", $ptype="2", $full_date, $date, $from_details, $newJournalCode, $times);


                 // ثبت قرض توسط دریافت کننده = recieved(ttype=1) loan(ptype=2)
                 $optionLable = 'دریافت قرض';
                 $this->createJournalEntry($request, $optionLable, $to_account_id, $to_currency, $to_amount,
                                        $ttype = "1", $ptype="2", $full_date, $date, $to_details,  $newJournalCode, $times);

            }
            // معاملات نسیه به نقد
            else if(intval($request->options) === 4)
            {
                /**
                * پرداخت نقد مشتری
                * باید همین مبلغ در جمع  رسیدگی قرض مشتری علاوه شود تا از قرضه شان کم شود
                * باید همین مبلغ در حساب خزانه جمع شود زیرا نقد دریافت کرده وباید حساب شان افزایش یابد

                * بردگی نقد خزانه
                * رسیدگی قرض مشتری یا پرداخت کننده
                */

                // ثبت رسیدگی قرض مشتری یا پرداخت کننده = paid(ttype=2), loan(ptype=2) 
                $optionLable = 'رسیدگی قرض ';
                $this->createJournalEntry($request, $optionLable, $from_account_id, $from_currency, $from_amount,
                                        $ttype = "2", $ptype="2", $full_date, $date, $from_details, $newJournalCode, $times);


                // بردگی نقد خزانه یا دریافت کننده = recieved(ttype=1) cache(ptype=1)
                $optionLable = 'دریافت طلب'; 
                $this->createJournalEntry($request, $optionLable, $to_account_id, $to_currency, $to_amount,
                                    $ttype = "1", $ptype="1", $full_date, $date, $to_details,  $newJournalCode, $times);
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
        $full_date, $date, $details, $code, $times)
    {
            // Handle the file upload
            $docPath = '';
            if ($request->hasFile('doc')) {
                $docPath = $request->file('doc')->store('documents', 'public');
                Log::info('Document uploaded', ['path' => $docPath]);
            }

            // Create the Journal entry
            Journal::create([
                'bill_no' => $request->billno,
                'code' => $code,
                'account_id' => $account_id,
                'branch_id' => $request->branch_id,
                'amount' => $amount,
                'currency_id' => $currency_id,
                'transaction_type' => $ttype,
                'payment_type' => $ptype,
                'options' => $request->options,
                'option_label' => $optionLable,
                'user' => auth()->user()->full_name ?? '',
                'year' => $date[0],
                'month' => $date[1],
                'day' => $date[2],
                'inserted_short_date' => $request->todays_date,
                'inserted_full_date' => $full_date,
                'details' => $details,
                'status' => 2,  
                'doc' => $docPath,
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
        $journals = Journal::with(['accountRelation', 'currencyRelation'])
        ->where('times', $times)
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
        $accounts = Account::all();
        $currencies = Currency::all();
        $branchs = Branch::all();

        $journals = Journal::with(['accountRelation', 'currencyRelation','branchRelation'])
        ->where('times', $times)
        ->orderBy('id', 'ASC')
        ->get();
        // return response()->json(['data' => $journals]);
        return view('transactions.journals.edit',compact('accounts','currencies','branchs','journals'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $this->journalValidation($request);
       
        $records = Journal::where('times', $request->times)
        ->where('branch_id', $this->branch_id)
        ->orderBy('id') // Order by ID to delete the oldest first
        ->limit(3)
        ->get();

        // Delete each record found (even if it's 1, 2, or 3)
        foreach ($records as $record) {
            $record->delete();
        }

        // Start the transaction
        DB::beginTransaction();
        try 
        {
           $check = $this->handleJournalEntry($request, $request->code);

           if(!$check)
           { 
                DB::rollBack();
                Session::flash('notification', [
                    'message' => ' ویرایش نگردید',
                    'type' => 'danger',
                ]);
                return back();
           }

            // Commit the transaction
            DB::commit();
            Session::flash('notification', [
                'message' => 'موفقانه ویرایش گردید',
                'type' => 'success',
            ]);
            return redirect()->route('journal.index'); 

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error  Updating journal entry: ' . $e->getMessage());
            Session::flash('notification', [
                'message' => ' ویرایش نگردید',
                'type' => 'danger',
            ]);
             return back();
        }
    }
    
   
    public function update_document(Request $request)
    {
        $request->validate([
            'doc' => 'required|file|mimes:jpg,jpeg,png,pdf,docx,xlsx|max:2048',
        ]);
    
        // Fetch all journals based on the given times
        $journals = Journal::where('times', $request->times)->where('journals.branch_id', $this->branch_id)->get();
    
        // Check if any journals are found
        if ($journals->isEmpty()) {
            return redirect()->back()->with('error', 'No journals found with the given times.');
        }
    
        // Handle file upload if a new file is uploaded
        if ($request->hasFile('doc')) {
            $docPath = $request->file('doc')->store('documents', 'public');
            
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
        // Find all journal records with the same 'times' value
        $journals = Journal::where('times', $times)->where('journals.branch_id', $this->branch_id)->get();

        if ($journals->isNotEmpty()) {
            // Loop through each journal and delete its associated file
            foreach ($journals as $journal) {
                // Optionally delete the associated file if needed
                if (Storage::exists('public/documents/' . $journal->doc)) {
                    Storage::delete('public/documents/' . $journal->doc);
                }

                // Delete the journal record
                $journal->delete();
            }

            // Optionally, flash a success message to session
            session()->flash('notification', [
                'type' => 'success',
                'message' => 'موفقانه حذف گردید',
            ]);

            // Redirect to the journal listing page (or wherever you want)
            return redirect()->route('journal.index');
        } else {
            // If no journals found with the given 'times' value, return back with error message
            session()->flash('notification', [
                'type' => 'danger',
                'message' => 'حذف نگردید',
            ]);

            return back();
        }
    }

    
}

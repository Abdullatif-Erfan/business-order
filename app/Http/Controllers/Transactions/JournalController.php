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
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Morilog\Jalali\Jalalian;
use Yajra\DataTables\Facades\DataTables;
use App\Services\NumberToWordsService;


class JournalController extends Controller
{

    protected $messageService, $numberToWordsService;

    // Inject the message service into the controller
    public function __construct(
        MessageService $messageService, 
        NumberToWordsService $numberToWordsService)
    {
        $this->messageService = $messageService;
        $this->numberToWordsService = $numberToWordsService;
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


        $accounts = Account::all();
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
        /**
         * status: 1: old journal, 2: journal, 3:income, 4:expense, 5:salary, 6:participants, 7:buy, 8:sales, 9:other
         */
        $journals = Journal::with(['accountRelation' => function($query){
            $query->select('id','name');
        },'currencyRelation' => function($query){
            $query->select('id','name','symbols','color');
        }])
        // $journals = Journal::with(['accountRelation','currencyRelation'])
        ->select('id','code','bill_no','amount','account_id','transaction_type','payment_type','currency_id','details','inserted_short_date','status','times','is_single_record')
        // ->where('journals.status','<=',2)
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
        

        return DataTables::of($journals)
            
            ->addIndexColumn()
           
            ->addColumn('accountRelation', function ($journal) {
                return $journal->accountRelation ? $journal->accountRelation->name : '';
            })
            
         

            // در این حالت در رفت / قرض نشان داده شود
            //  transaction_type == 2 and payment_type = 1 = paid cache
            // transaction_type == 1 and payment_type = 2 = recieved loan
            ->addColumn('transaction_type_1', function ($journal) {
                $amount = $journal->amount;
                $formattedAmount = (fmod($amount, 1) == 0) ? number_format($amount, 0) : number_format($amount, 2);

                if ($journal->status == 1) { // رسید حساب سابق 
                    return $journal->transaction_type == 2 ? $formattedAmount : '';
                } 
                else 
                {
                    // دو معامله ای
                    if (($journal->transaction_type == 2 && $journal->payment_type == 1) || 
                    ($journal->transaction_type == 1 && $journal->payment_type == 2)) {
                        return $formattedAmount;
                    }
                }
                return '';
            })
            
            
            // در این حالت در طلب / و آمد نشان داده شود
            // transaction_type == 2 and payment_type = 2 = paid loan
            // transaction_type == 1 and payment_type = 1 = recieved cache
            ->addColumn('transaction_type_2', function ($journal) {
                $amount = $journal->amount;
                $formattedAmount = (fmod($amount, 1) == 0) ? number_format($amount, 0) : number_format($amount, 2);

                if ($journal->status == 1) {
                    return $journal->transaction_type == 1 ? $formattedAmount : '';
                } 
                else {
                    if (($journal->transaction_type == 2 && $journal->payment_type == 2) || 
                        ($journal->transaction_type == 1 && $journal->payment_type == 1)) {
                        return $formattedAmount;
                    }
                }
                return '';
            })
            

            ->addColumn('currency', function ($journal) {
                return '<i style="font-size:14px;color:'.$journal->currencyRelation->color.'">'.$journal->currencyRelation->symbols.'</i>';
            })
            ->addColumn('actions', function ($journal) {
                return $journal->is_single_record == 1 ? '<a href="journal/details/'.$journal->times.'" class="hidden-print"><i class="fas fa-eye viewAccount" data-id="' . $journal->id . '" style="font-size:20px;"></i></a>' : '';
            })
            ->rawColumns(['actions','currency'])
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


        $journals = Journal::with(['accountRelation', 'currencyRelation', 'userRelation'])
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

        $accounts = Account::all();
        $currencies = Currency::all();
        $branchs = Branch::all();
        $todaysDate = Jalalian::now()->format('Y-m-d');

        return view('transactions.journals.create',compact('accounts','currencies','branchs','todaysDate'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Remove commas from 'from_amount' and 'to_amount'
        $request->merge([
            'from_amount' => str_replace(',', '', $request->from_amount),
            'to_amount' => str_replace(',', '', $request->to_amount),
        ]);
    
        // Validation
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'bill_no' => 'nullable|numeric|min:1',
            'from_account_id' => 'required|exists:accounts,id',
            'to_account_id' => 'required|exists:accounts,id',

            'from_amount' => 'required|decimal:2',  // Decimal with up to 2 decimal places
            'from_currency_id' => 'required|exists:currencies,id',
            'to_amount' => 'required|decimal:2',  // Decimal with up to 2 decimal places

            'to_currency_id' => 'required|exists:currencies,id',
            'from_details' => 'required|string|max:255',
            'to_details' => 'required|string|max:255',
            'doc' => 'nullable|mimes:jpg,jpeg,png,pdf,docx,xlsx|max:2048', // Optional file field, max 2MB
        ]);
    
        $todaysDate = $request->todays_date;
        $date = explode('-', $todaysDate);
        $year = $date[0];
        $month = $date[1];
        $day = $date[2];
        $full_date =  $year.'-'.$month.'-'.$day.' '.Date('h:i:s A');
    
        $journalCode = Journal::latest('code')->value('code');
        $newJournalCode = $journalCode ? $journalCode + 1 : 1;
        $times = time();
    
        // Start the transaction
        DB::beginTransaction();
    
        try {
            // Store the journal entry for the "paid cache" record
            $journal1 = new Journal();
            $journal1->bill_no = $request->bill_no ?? 0;
            $journal1->code = $newJournalCode;
            $journal1->branch_id = $request->branch_id;
            $journal1->inserted_full_date = $full_date;
            $journal1->inserted_short_date = $todaysDate;
            $journal1->user_id = Session::get('userId', 0);
            $journal1->year = $year;
            $journal1->month = $month;
            $journal1->day = $day;
            $journal1->status = 2;
            $journal1->times = $times;
            $journal1->is_single_record = 1; // 0: single, 1: pair; 
    
            // Handle the file upload
            if ($request->hasFile('doc')) {
                $docPath = $request->file('doc')->store('documents', 'public');
                $journal1->doc = $docPath;
            }
    
            $journal1->account_id = $request->from_account_id;
            $journal1->amount = $request->from_amount;
            $journal1->currency_id = $request->from_currency_id;
            $journal1->details = $request->from_details;
            $journal1->transaction_type = 2; // 1: received, 2: paid
            $journal1->payment_type = 1; // 1: cache, 2: loan, 3: Talab
            $check1 = $journal1->save();
    
            // Store the journal entry for the "received cache" record
            $journal2 = new Journal();
            $journal2->bill_no = $request->bill_no;
            $journal2->code = $newJournalCode;
            $journal2->branch_id = $request->branch_id;
            $journal2->inserted_full_date = $full_date;
            $journal2->inserted_short_date = $todaysDate;
            $journal2->user_id = Session::get('userId', 0);
            $journal2->year = $year;
            $journal2->month = $month;
            $journal2->day = $day;
            $journal2->status = 2;
            $journal2->times = $times;
            $journal2->is_single_record = 1; // 0: single, 1: pair
    
            // Handle the file upload
            if ($request->hasFile('doc')) {
                $docPath = $request->file('doc')->store('documents', 'public');
                $journal2->doc = $docPath;
            }
    
            // Fill data for "received cache" record
            $journal2->account_id = $request->to_account_id;
            $journal2->amount = $request->to_amount;
            $journal2->currency_id = $request->to_currency_id;
            $journal2->details = $request->to_details;
            $journal2->transaction_type = 1; // 1: received, 2: paid
            $journal2->payment_type = 1; // 1: cache, 2: loan, 3: Talab
            $check2 = $journal2->save();
    
            // Commit the transaction
            DB::commit();
    
            Session::flash('notification', [
                'message' => 'موفقانه ثبت گردید',
                'type' => 'success',
            ]);
            return redirect()->route('journal.index'); 

        } catch (\Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollBack();
            // Optionally, log the error for debugging
            \Log::error('Error storing journal entry: ' . $e->getMessage());
    
            // Use MessageService to return error message
            Session::flash('notification', [
                'message' => ' ثبت نگردید',
                'type' => 'danger',
            ]);
             return back();
        }
    }
    
    
    /**
     * Display the specified resource to print
     */
    public function print(string $times)
    {
        $orgbios = OrgBio::all();
        $journals = Journal::with(['accountRelation', 'currencyRelation', 'userRelation'])
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

        $journals = Journal::with(['accountRelation', 'currencyRelation', 'userRelation','branchRelation'])
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
        // Validate input
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'bill_no' => 'nullable|numeric|min:1',
            'from_account_id' => 'required|exists:accounts,id',
            'to_account_id' => 'required|exists:accounts,id',
        
            'from_amount' => 'required|decimal:2',  // Decimal with up to 2 decimal places
            'from_currency_id' => 'required|exists:currencies,id',
            'to_amount' => 'required|decimal:2',  // Decimal with up to 2 decimal places

            'to_currency_id' => 'required|exists:currencies,id',
            'from_details' => 'required|string|max:255',
            'to_details' => 'required|string|max:255',
            'doc' => 'nullable|mimes:jpg,jpeg,png,pdf,docx,xlsx|max:2048', // Optional file field, max 2MB
        ]);
    
        // Remove commas from 'from_amount' and 'to_amount'
        $request->merge([
            'from_amount' => str_replace(',', '', $request->from_amount),
            'to_amount' => str_replace(',', '', $request->to_amount),
        ]);
    
        // Get the journal entry using the `times` field to locate the correct entry
        $journal1 = Journal::where('times', $request->times)->where('transaction_type', 2)->first(); // 1 for paid cache (from)
        $journal2 = Journal::where('times', $request->times)->where('transaction_type', 1)->first(); // 1 for received cache (to)
    
        if (!$journal1 || !$journal2) {
            // If no journal entries are found, return an error
            $this->messageService->showMessage(2); // Error: 2 = ثبت نگردید
            return back();
        }
    
        // Get the current date and time
        $times = $request->times;
        $todaysDate = $request->todays_date;
        $date = explode('-', $todaysDate);
        $year = $date[0];
        $month = $date[1];
        $day = $date[2];
        $full_date =  $year.'-'.$month.'-'.$day.' '.Date('h:i:s A');
    
        // Update the first journal entry ("paid cache")
        $journal1->bill_no = $request->bill_no ?? 0;
        $journal1->branch_id = $request->branch_id;
        $journal1->inserted_full_date = $full_date;
        $journal1->inserted_short_date = $todaysDate;
        $journal1->user_id = Session::get('userId', 0);
        $journal1->year = $year;
        $journal1->month = $month;
        $journal1->day = $day;
        $journal1->status = 2;
        $journal1->times = $times; 
        $journal1->is_single_record = 1;
    
        // Handle the file upload if new file is uploaded
        if ($request->hasFile('doc')) {
            $docPath = $request->file('doc')->store('documents', 'public');
            $journal1->doc = $docPath;
        }
    
        // Update the journal entry fields for "paid cache"
        $journal1->account_id = $request->from_account_id;
        $journal1->amount = $request->from_amount;
        $journal1->currency_id = $request->from_currency_id;
        $journal1->details = $request->from_details;
        $journal1->transaction_type = 2; // 2: paid
        $journal1->payment_type = 1; // 1: cash
    
        $check1 = $journal1->save();
    
        // Update the second journal entry ("received cache")
        $journal2->bill_no = $request->bill_no;
        $journal2->branch_id = $request->branch_id;
        $journal2->inserted_full_date = $full_date;
        $journal2->inserted_short_date = $todaysDate;
        $journal2->user_id = Session::get('userId', 0);
        $journal2->year = $year;
        $journal2->month = $month;
        $journal2->day = $day;
        $journal2->status = 2;
        $journal2->times = $times;
        $journal2->is_single_record = 1;
    
        // Handle the file upload if new file is uploaded
        if ($request->hasFile('doc')) {
            $docPath = $request->file('doc')->store('documents', 'public');
            $journal2->doc = $docPath;
        }
    
        // Update the journal entry fields for "received cache"
        $journal2->account_id = $request->to_account_id;
        $journal2->amount = $request->to_amount;
        $journal2->currency_id = $request->to_currency_id;
        $journal2->details = $request->to_details;
        $journal2->transaction_type = 1; // 1: received
        $journal2->payment_type = 1; // 1: cash
    
        $check2 = $journal2->save();
    
        // Commit the transaction if both entries were saved successfully
        if ($check1 && $check2) {
            DB::commit();
            Session::flash('notification', [
                'message' => 'موفقانه ویرایش گردید',
                'type' => 'success',
            ]);
            return redirect()->route('journal.index'); 
        } else {
            DB::rollBack();
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
        $journals = Journal::where('times', $request->times)->get();
    
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
        return redirect()->route('transactions.journal.details', ['times' => $request->times])->with('success', 'Documents updated successfully!');

    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $times)
    {
        // Find all journal records with the same 'times' value
        $journals = Journal::where('times', $times)->get();

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

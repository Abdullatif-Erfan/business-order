<?php
namespace App\Http\Controllers\Transactions;

use App\Services\MessageService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting\Account;
use App\Models\Setting\Currency;
use App\Models\Transaction\Journal;
use App\Models\Setting\Branch;
use App\Models\Setting\IncomeType;
use App\Models\Setting\OrgBio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Morilog\Jalali\Jalalian;
use Yajra\DataTables\Facades\DataTables;
use App\Services\NumberToWordsService;

class IncomeController extends Controller
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
     * status: 1: old income, 2: journal, 3:income, 4:expense, 5:salary, 6:participants, 7:buy, 8:sales, 9:other
     */
    public function index()
    {
        // $incomes = Journal::with(['accountRelation' => function($query){
        //     $query->select('id','name');
        // },'currencyRelation' => function($query){
        //     $query->select('id','name','symbols','color');
        // }])
        // ->select('id','code','bill_no','amount','account_id','transaction_type','currency_id','details','inserted_short_date','status','times')
        // ->orderBy('id', 'DESC')
        // ->get();

        // return response()->json(['data' => $incomes]);

        $types = IncomeType::all();
        $accounts = Account::all();
        $currencies = Currency::all();
        $orgbios = OrgBio::all();

        // return response()->json(['data' =>  $orgbios[0]->header]);
        
        return view('transactions.income.list',compact('accounts','currencies','orgbios','types'));
    }

    /**
     * Show the income data
     */
    public function getData(Request $request)
    {
        /**
         * status: 1: old income, 2: journal, 3:income, 4:expense, 5:salary, 6:participants, 7:buy, 8:sales, 9:other
         */
        $incomes = Journal::with(['accountRelation' => function($query){
            $query->select('id','name');
        },'currencyRelation' => function($query){
            $query->select('id','name','symbols','color');
        },'incomeTypeRelation' => function($query){
            $query->select('id','name');
        }])
        // $incomes = Journal::with(['accountRelation','currencyRelation','incomeTypeRelation'])
        ->select('id','code','bill_no','amount','account_id','transaction_type','payment_type','currency_id','details','inserted_short_date','status','times','is_single_record','dynamic_type','doc')
        ->where('journals.status','=',3)
        ->orderBy('id', 'DESC');


        // Apply filters if provided
        if ($request->type_id) {
            $incomes->where('dynamic_type', $request->type_id);
        }
        if ($request->currency_id) {
            $incomes->where('currency_id', $request->currency_id);
        }
       
        if ($request->start_date && $request->end_date) {
            $incomes->whereBetween('inserted_short_date', [$request->start_date, $request->end_date]);
        } elseif ($request->start_date) {
            $incomes->whereDate('inserted_short_date', '=', $request->start_date);
        } elseif ($request->end_date) {
            $incomes->whereDate('inserted_short_date', '>=', $request->end_date); // Until today
        }

        if ($request->code_number) {
            $incomes->where('code', 'LIKE', "%{$request->code_number}%");
        }
        if ($request->bill_number) {
            $incomes->where('bill_no', 'LIKE', "%{$request->bill_number}%");
        }
        

        return DataTables::of($incomes)
            
            ->addIndexColumn()
           
            ->addColumn('accountRelation', function ($income) {
                return $income->accountRelation ? $income->accountRelation->name : '';
            })

            ->addColumn('incomeTypeRelation', function ($income) {
                return $income->incomeTypeRelation ? $income->incomeTypeRelation->name : '';
            })

            // recieved and recieveable is belongs to income
            ->addColumn('transaction_type_2', function ($income) {
                $amount = $income->amount;
                $formattedAmount = (fmod($amount, 1) == 0) ? number_format($amount, 0) : number_format($amount, 2);
                return $formattedAmount;     
            })
            

            ->addColumn('currency', function ($income) {
                return '<i style="font-size:14px;color:'.$income->currencyRelation->color.'">'.$income->currencyRelation->symbols.'</i>';
            })

            // ->addColumn('doc', function ($income) {
            //     return '<i class="fas fa-file">'.$income->doc.'</i>';
            // })


            ->addColumn('doc', function ($income) {
                if ($income->doc) {
                    $url = asset('storage/' . $income->doc); // Assuming the file is stored in 'storage/app/public/'
                    return '<a href="' . $url . '" target="_blank">
                                <i class="fa fa-download"></i>
                            </a>';
                }
                return '-';
            })

            ->addColumn('edit', function ($income) {
                return  '<a href="income/edit/'.$income->id.'" class="hidden-print"><i class="fas fa-pen-square editIcon" data-id="' . $income->id . '" style="font-size:20px;"></i></a>';
            })

            ->addColumn('delete', function ($income) {
                return '<a href="income/destroy/'.$income->id.'" class="hidden-print" 
                            onClick="return confirm(\'آیا میخواهید حذف نمایید ؟\')">
                            <i class="fas fa-trash-alt danger deleteIcon" data-id="' . $income->id . '" style="font-size:20px;"></i>
                        </a>';
            })

            ->rawColumns(['edit','delete','doc','currency'])
            ->make(true);
    }


    /**
     * Show journal details
     */
  
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


        $incomeTypes = IncomeType::all();
        $customers = Account::select('id','name')->where('account_type_id','>',1)->get();
        $ownBanks = Account::select('id','name')->where('account_type_id',1)->orderBy('is_pre_select','DESC')->get();

        if(!$ownBanks) {
            return "لطفا یکی از حساب های شرکت را پیش فرض انتخاب نمایید ";
            die();
        }

        $currencies = Currency::all();
        $branchs = Branch::all();
        $todaysDate = Jalalian::now()->format('Y-m-d');

        return view('transactions.income.create',compact('customers','ownBanks','currencies','branchs','todaysDate','incomeTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // return ['formData' => $request->all()];

        // Validation
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'bill_no' => 'nullable|numeric|min:0',
            'reciever_account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric',  // Decimal with up to 2 decimal places
            'currency_id' => 'required|exists:currencies,id',
            'dynamic_type' => 'required|numeric',
            'details' => 'required|string|max:255',
            'doc' => 'nullable|mimes:jpg,jpeg,png,pdf,docx,xlsx|max:2048',
        ]);
    
        $todaysDate = $request->todays_date;
        $date = explode('-', $todaysDate);
        $year = $date[0];
        $month = $date[1];
        $day = $date[2];
        $full_date =  $year.'-'.$month.'-'.$day.' '.Date('h:i:s A');
    
        $newJournalCode = Journal::max('code') + 1;
        $times = time();
    
        // Start the transaction
        DB::beginTransaction();
    
        try {
            // Store the journal entry for the "paid cache" record
            $journal = new Journal();
            $journal->bill_no = $validated['bill_no'] ?? 0;
            $journal->code = $newJournalCode;
            $journal->branch_id = $validated['branch_id'];
            $journal->inserted_full_date = $full_date;
            $journal->inserted_short_date = $todaysDate;
            $journal->dynamic_type = $validated['dynamic_type'];
            $journal->user = auth()->user()->full_name ?? '';
            $journal->year = $year;
            $journal->month = $month;
            $journal->day = $day;
            $journal->status = 3;  // 1: old income, 2: income, 3:income, 4:expense, 5:salary, 6:participants, 7:buy, 8:sales, 9:other
            $journal->times = $times;
            $journal->is_single_record = 0; // 0: single, 1: pair; 
    
            // Handle the file upload
            if ($request->hasFile('doc')) {
                $docPath = $request->file('doc')->store('documents', 'public');
                $journal->doc = $docPath;
            }
    
            $journal->account_id = $validated['reciever_account_id'];
            $journal->amount = $validated['amount'];
            $journal->currency_id = $validated['currency_id'];
            $journal->details = $validated['details'];
            $journal->transaction_type = 1; // 1: received, 2: paid
            $journal->payment_type = 1; // 1: cache, 2: loan, 3: Talab
            $journal->option_label = 'دریافت عواید';
            $journal->save();
    
            // Commit the transaction
            DB::commit();
    
            Session::flash('notification', [
                'message' => 'موفقانه ثبت گردید',
                'type' => 'success',
            ]);
            return redirect()->route('income.index'); 

        } catch (\Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollBack();
            // Optionally, log the error for debugging
            \Log::error('Error storing income entry: ' . $e->getMessage());
    
            // Use MessageService to return error message
            Session::flash('notification', [
                'message' => ' ثبت نگردید',
                'type' => 'danger',
            ]);
             return back();
        }
    }
    

  
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $incomeTypes = IncomeType::all();
        $currencies = Currency::all();
        $branchs = Branch::all();

        $income = Journal::with(['accountRelation', 'currencyRelation', 'userRelation','branchRelation'])
        ->where('id', $id)
        ->orderBy('id', 'ASC')
        ->get();
        // return response()->json(['data' => $income]);
        return view('transactions.income.edit',compact('currencies','branchs','income','incomeTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try{
            // Validate input
            $validated = $request->validate([
                'branch_id' => 'required|exists:branches,id',
                'bill_no' => 'nullable|numeric|min:0',
                'amount' => 'required|numeric',  // Decimal with up to 2 decimal places
                'currency_id' => 'required|exists:currencies,id',
                'dynamic_type' => 'required|numeric',
                'details' => 'required|string|max:255',
                'doc' => 'nullable|mimes:jpg,jpeg,png,pdf,docx,xlsx|max:2048',
            ]);
        
         
            // Get the income entry 
            $journal = Journal::where('id', $id)->first(); 
        
            if (!$journal) {
                
                Session::flash('notification', [
                    'message' => 'ویرایش نگردید',
                    'type' => 'success',
                ]);
                return back();
            }
        
            // Get the current date and time
            $todaysDate = $request->todays_date;
            $date = explode('-', $todaysDate);
            $year = $date[0];
            $month = $date[1];
            $day = $date[2];
            $full_date =  $year.'-'.$month.'-'.$day.' '.Date('h:i:s A');
        
            $newJournalCode = Journal::max('code') + 1;
            $times = time();
        
            // Update the first journal entry ("paid cache")
            $journal->bill_no = $validated['bill_no'] ?? 0;
            $journal->branch_id = $validated['branch_id'];
            $journal->inserted_full_date = $full_date;
            $journal->inserted_short_date = $todaysDate;
            $journal->user =  auth()->user()->full_name ?? '';
            $journal->dynamic_type = $validated['dynamic_type'];
            $journal->year = $year;
            $journal->month = $month;
            $journal->day = $day; 
        
            // Handle the file upload if new file is uploaded
            if ($request->hasFile('doc')) {
                $docPath = $request->file('doc')->store('documents', 'public');
                $journal->doc = $docPath;
            }
        
            // Update the journal entry fields for "paid cache"
            $journal->amount = $validated['amount'];
            $journal->currency_id = $validated['currency_id'];
            $journal->details = $validated['details'];  
            $journal->save();
            
            // Commit the transaction if both entries were saved successfully
          
                DB::commit();
                Session::flash('notification', [
                    'message' => 'موفقانه ویرایش گردید',
                    'type' => 'success',
                ]);
                return redirect()->route('income.index'); 
        } 
        catch (\Exception $e) 
        { 
            DB::rollBack();
            \Log::error('Error occured in income update' . $e->getMessage());
            Session::flash('notification', [
                'message' => ' ویرایش نگردید',
                'type' => 'danger',
            ]);
            return back();
        }
       
        
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find all income records with the same 'times' value
        $journal = Journal::where('id', $id)->get();

        if ($journal->isNotEmpty()) {
            // Loop through each income and delete its associated file
            foreach ($journal as $docs) {
                // Optionally delete the associated file if needed
                if (Storage::exists('public/documents/' . $docs->doc)) {
                    Storage::delete('public/documents/' . $docs->doc);
                }

                // Delete the docs record
                $docs->delete();
            }

            // Optionally, flash a success message to session
            session()->flash('notification', [
                'type' => 'success',
                'message' => 'موفقانه حذف گردید',
            ]);

            // Redirect to the income listing page (or wherever you want)
            return redirect()->route('income.index');
        } else {
            // If no journal found with the given 'times' value, return back with error message
            session()->flash('notification', [
                'type' => 'danger',
                'message' => 'حذف نگردید',
            ]);

            return back();
        }
    }

    
}

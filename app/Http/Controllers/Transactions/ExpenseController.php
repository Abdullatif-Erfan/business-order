<?php
namespace App\Http\Controllers\Transactions;

use App\Services\MessageService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting\Account;
use App\Models\Setting\Currency;
use App\Models\Transaction\Journal;
use App\Models\Setting\Branch;
use App\Models\Setting\ExpenseType;
use App\Models\Setting\OrgBio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Morilog\Jalali\Jalalian;
use Yajra\DataTables\Facades\DataTables;

class ExpenseController extends Controller
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

    public function index()
    {
        $types = ExpenseType::all();
        $accounts = Account::where('branch_id', $this->branch_id)->get();
        $currencies = Currency::all();
        $orgbios = OrgBio::all();
        return view('transactions.expense.list',compact('accounts','currencies','orgbios','types'));
    }

    /**
     * Show the expense data
     */
    public function getData(Request $request)
    {
        /**
         * status: 1: old income, 2: journal, 3:income, 4:expense, 5:salary, 6:participants, 7:buy, 8:sales, 9:other
         */
        $expenses = Journal::with(['accountRelation' => function($query){
            $query->select('id','name');
        },'currencyRelation' => function($query){
            $query->select('id','name','symbols','color');
        },'expenseTypeRelation' => function($query){
            $query->select('id','name');
        }])
        // $expenses = Journal::with(['accountRelation','currencyRelation','expenseTypeRelation'])
        ->select('id','code','bill_no','amount','account_id','transaction_type','payment_type','currency_id','details','inserted_short_date','status','times','is_single_record','dynamic_type','doc')
        ->where('journals.status','=',4)
        ->whereHas('accountRelation', function($query) {
            $query->where('branch_id', $this->branch_id);
        })
        ->orderBy('id', 'DESC');


        // Apply filters if provided
        if ($request->type_id) {
            $expenses->where('dynamic_type', $request->type_id);
        }
        if ($request->currency_id) {
            $expenses->where('currency_id', $request->currency_id);
        }
       
        if ($request->start_date && $request->end_date) {
            $expenses->whereBetween('inserted_short_date', [$request->start_date, $request->end_date]);
        } elseif ($request->start_date) {
            $expenses->whereDate('inserted_short_date', '=', $request->start_date);
        } elseif ($request->end_date) {
            $expenses->whereDate('inserted_short_date', '>=', $request->end_date); // Until today
        }

        if ($request->code_number) {
            $expenses->where('code', 'LIKE', "%{$request->code_number}%");
        }
        if ($request->bill_number) {
            $expenses->where('bill_no', 'LIKE', "%{$request->bill_number}%");
        }
        

        return DataTables::of($expenses)
            
            ->addIndexColumn()
           
            ->addColumn('accountRelation', function ($expense) {
                return $expense->accountRelation ? $expense->accountRelation->name : '';
            })

            ->addColumn('expenseTypeRelation', function ($expense) {
                return $expense->expenseTypeRelation ? $expense->expenseTypeRelation->name : '';
            })

            // recieved and recieveable is belongs to expense
            ->addColumn('transaction_type_2', function ($expense) {
                $amount = $expense->amount;
                $formattedAmount = (fmod($amount, 1) == 0) ? number_format($amount, 0) : number_format($amount, 2);
                return $formattedAmount;     
            })
            

            ->addColumn('currency', function ($expense) {
                return '<i style="font-size:14px;color:'.$expense->currencyRelation->color.'">'.$expense->currencyRelation->symbols.'</i>';
            })

            // ->addColumn('doc', function ($expense) {
            //     return '<i class="fas fa-file">'.$expense->doc.'</i>';
            // })


            ->addColumn('doc', function ($expense) {
                if ($expense->doc) {
                    $url = asset('storage/' . $expense->doc); // Assuming the file is stored in 'storage/app/public/'
                    return '<a href="' . $url . '" target="_blank">
                                <i class="fa fa-download"></i>
                            </a>';
                }
                return '-';
            })

            ->addColumn('edit', function ($expense) {
                return  '<a href="expense/edit/'.$expense->id.'" class="hidden-print"><i class="fas fa-pen-square editIcon" data-id="' . $expense->id . '" style="font-size:20px;"></i></a>';
            })

            ->addColumn('delete', function ($expense) {
                return '<a href="expense/destroy/'.$expense->id.'" class="hidden-print" 
                            onClick="return confirm(\'آیا میخواهید حذف نمایید ؟\')">
                            <i class="fas fa-trash-alt danger deleteIcon" data-id="' . $expense->id . '" style="font-size:20px;"></i>
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


        $expenseTypes = ExpenseType::all();
        $customers = Account::select('id','name')->whereIn('account_type_id',[3,4])->where('branch_id', $this->branch_id)->get();
        $ownBanks = Account::select('id','name')->whereIn('account_type_id',[1,6])->where('branch_id', $this->branch_id)->orderBy('is_pre_select','DESC')->get();

        if(!$ownBanks) {
            return "لطفا یکی از حساب های شرکت را پیش فرض انتخاب نمایید ";
            die();
        }

        $currencies = Currency::all();
        $branchs = Branch::where('id',$this->branch_id)->get();
        $todaysDate = Jalalian::now()->format('Y-m-d');

        return view('transactions.expense.create',compact('customers','ownBanks','currencies','branchs','todaysDate','expenseTypes'));
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
    
        $newJournalCode = Journal::where('branch_id', $this->branch_id)->max('code') + 1;
        $times = time();
    
        // Start the transaction
        DB::beginTransaction();
    
        try {

            $account_type_id = Account::where('id', $validated['reciever_account_id'])->value('account_type_id');
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
            $journal->status = 4;  // 1: old journal, 2: journal, 3:income, 4:expense, 5:salary, 6:participants, 7:buy, 8:sales, 9:other
            $journal->times = $times;
            $journal->is_single_record = 0; // 0: single, 1: pair; 
    
            // Handle the file upload
            if ($request->hasFile('doc')) {
                $docPath = $request->file('doc')->store('documents', 'public');
                $journal->doc = $docPath;
            }
            
            $journal->account_type_id = $account_type_id;
            $journal->account_id = $validated['reciever_account_id'];
            $journal->amount = $validated['amount'];
            $journal->currency_id = $validated['currency_id'];
            $journal->details = $validated['details'];
            $journal->transaction_type = 2; // 1: received, 2: paid
            $journal->payment_type = 1; // 1: cache, 2: loan, 3: Talab
            $journal->option_label = 'پرداخت مصارف';
            $journal->save();
    
            // Commit the transaction
            DB::commit();
    
            Session::flash('notification', [
                'message' => 'موفقانه ثبت گردید',
                'type' => 'success',
            ]);
            return redirect()->route('expense.index'); 

        } catch (\Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollBack();
            // Optionally, log the error for debugging
            \Log::error('Error storing expense entry: ' . $e->getMessage());
    
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
        $expenseTypes = ExpenseType::all();
        $currencies = Currency::all();
        $branchs = Branch::where('id',$this->branch_id)->get();

        $expense = Journal::with(['accountRelation', 'currencyRelation','branchRelation'])
        ->where('id', $id)
        ->orderBy('id', 'ASC')
        ->first();
        // return response()->json(['data' => $expense]);
        return view('transactions.expense.edit',compact('currencies','branchs','expense','expenseTypes'));
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
        
         
            // Get the expense entry 
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
        
            $newJournalCode = Journal::where('branch_id', $this->branch_id)->max('code') + 1;
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
                return redirect()->route('expense.index'); 
        } 
        catch (\Exception $e) 
        { 
            DB::rollBack();
            \Log::error('Error occured in expense update' . $e->getMessage());
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
        // Find all expense records with the same 'times' value
        $journal = Journal::where('id', $id)->get();

        if ($journal->isNotEmpty()) {
            // Loop through each expense and delete its associated file
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

            // Redirect to the expense listing page (or wherever you want)
            return redirect()->route('expense.index');
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

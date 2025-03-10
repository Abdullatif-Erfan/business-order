<?php

namespace App\Http\Controllers\Hr;

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

class SalaryController extends Controller
{
    protected $branch_id, $isAdmin;

    // Inject the message service into the controller
    public function __construct()
    {
        // Ensure user authentication before setting the branch ID
        if (auth()->check()) {
            $user = auth()->user();
            $this->branch_id = $user->branch_id ?? 0;
            $this->isAdmin = $user->isAdmin == 1 ? true : false;
        } else {
            $this->branch_id = 0;
            $this->isAdmin = false;
        }
    }

    public function index()
    {
        $accounts = Account::where('branch_id', $this->branch_id)->get();
        $currencies = Currency::all();
        $orgbios = OrgBio::all();
        $months = array(
            '1' => 'حمل',
            '2' => 'ثور',
            '3' => 'جوزا',
            '4' => 'سرطان',
            '5' => 'اسد',
            '6' => 'سنبله',
            '7' => 'میزان',
            '8' => 'عقرب',
            '9' => 'قوس',
            '10' => 'جدی',
            '11' => 'دلو',
            '12' => 'حوت',
        );
        return view('hr.salary.list',compact('accounts','currencies','orgbios','months'));
    }

    /**
     * Show the expense data
     */
    public function getData(Request $request)
    {
        /**
         * status: 1: old income, 2: journal, 3:income, 4:expense, 5:salary, 6:participants, 7:buy, 8:sales, 9:other
         */
        $salary = Journal::with(['accountRelation' => function($query){
            $query->select('id','name');
        },'currencyRelation' => function($query){
            $query->select('id','name','symbols','color');
        }])
        // $salary = Journal::with(['accountRelation','currencyRelation','expenseTypeRelation'])
        ->select('id','code','bill_no','amount','account_id','currency_id','details','year','month','inserted_short_date','status','times')
        ->where('journals.status','=',5)
        ->where('journals.dynamic_type','=',1) // show just employee records
        ->where('journals.branch_id', $this->branch_id)
        ->orderBy('id', 'DESC');


        // Apply filters if provided

        if ($request->employee_name) {
            $salary->whereHas('accountRelation', function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->employee_name . '%'); // Use LIKE for partial search
            });
        }

        if ($request->year) {
            $salary->where('year', $request->year);
        }
        if ($request->month) {
            $salary->where('month', $request->month);
        }
        if ($request->currency_id) {
            $salary->where('currency_id', $request->currency_id);
        }
        if ($request->code_number) {
            $salary->where('code', 'LIKE', "%{$request->code_number}%");
        }
        

        return DataTables::of($salary)
            
            ->addIndexColumn()
           
            ->addColumn('accountRelation', function ($salary) {
                return $salary->accountRelation ? $salary->accountRelation->name : '';
            })

            // recieved and recieveable is belongs to salary
            ->addColumn('amount', function ($salary) {
                $amount = $salary->amount;
                $formattedAmount = (fmod($amount, 1) == 0) ? number_format($amount, 0) : number_format($amount, 2);
                return $formattedAmount;     
            })
            
            ->addColumn('currency', function ($salary) {
                return '<i style="font-size:14px;color:'.$salary->currencyRelation->color.'">'.$salary->currencyRelation->name.'</i>';
            })


            ->addColumn('edit', function ($salary) {
                return  '<a href="salary/edit/'.$salary->id.'" class="hidden-print"><i class="fas fa-pen-square editIcon" data-id="' . $salary->id . '" style="font-size:20px;"></i></a>';
            })

            ->addColumn('delete', function ($salary) {
                return '<a href="salary/destroy/'.$salary->times.'" class="hidden-print" 
                            onClick="return confirm(\'آیا میخواهید حذف نمایید ؟\')">
                            <i class="fas fa-trash-alt danger deleteIcon" data-id="' . $salary->id . '" style="font-size:20px;"></i>
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

        $employees = Account::select('id','name')->where('account_type_id',2)->where('branch_id', $this->branch_id)->get();
        $ownBanks = Account::select('id','name')->whereIn('account_type_id',[1,6])->where('branch_id', $this->branch_id)->orderBy('is_pre_select','DESC')->get();

        if(!$ownBanks) {
            return "لطفا یکی از حساب های شرکت را پیش فرض انتخاب نمایید ";
            die();
        }

        $months = array(
            '1' => 'حمل',
            '2' => 'ثور',
            '3' => 'جوزا',
            '4' => 'سرطان',
            '5' => 'اسد',
            '6' => 'سنبله',
            '7' => 'میزان',
            '8' => 'عقرب',
            '9' => 'قوس',
            '10' => 'جدی',
            '11' => 'دلو',
            '12' => 'حوت',
        );

        $currencies = Currency::all();
        $branchs = Branch::where('id',$this->branch_id)->get();
        $todaysDate = Jalalian::now()->format('Y-m-d');
        $cur_year = Jalalian::now()->format('Y');
        $cur_month = Jalalian::now()->format('n');


        return view('hr.salary.create',compact('ownBanks','currencies','branchs','todaysDate','employees','months','cur_year','cur_month'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // return ['formData' => $request->all()];

        // Validation
        $validated = $request->validate([
            'from_account_id' => 'required|exists:accounts,id',
            'to_account_id' => 'required|exists:accounts,id',
            'branch_id' => 'required|exists:branches,id',
            'amount' => 'required|numeric',  
            'currency_id' => 'required|exists:currencies,id',
            'year' => 'required|numeric',
            'month' => 'required|numeric',
            'details' => 'nullable|string|max:255',
        ]);
    
        $todaysDate = $request->todays_date;
        $date = explode('-', $todaysDate);
        $year = $validated['year'];
        $month = $validated['month'];
        $day = $date[2];
        $full_date =  $year.'-'.$month.'-'.$day.' '.Date('h:i:s A');
    
        $newJournalCode = Journal::max('code') + 1;
        $times = time();
    
        // Start the transaction
        DB::beginTransaction();
    
        try {
            // ثبت ژورنال برای پرداخت خزانه
            // Paid Cache = t2p1
            $journal1 = new Journal();
            $journal1->bill_no =  0;
            $journal1->code = $newJournalCode;
            $journal1->branch_id = $validated['branch_id'];
            $journal1->inserted_full_date = $full_date;
            $journal1->inserted_short_date = $todaysDate;
            $journal1->user = auth()->user()->full_name ?? '';
            $journal1->year = $year;
            $journal1->month = $month;
            $journal1->day = $day;
            $journal1->status = 5;  // 1: old journal, 2: journal, 3:income, 4:expense, 5:salary, 6:participants, 7:buy, 8:sales, 9:other
            $journal1->times = $times;
            $journal1->is_single_record = 1; // 0: single, 1: pair; 
            $journal1->dynamic_type = null ; // for khazan store null to not be shown in the salary list
            $journal1->account_id = $validated['from_account_id'];
            $journal1->amount = $validated['amount'];
            $journal1->currency_id = $validated['currency_id'];
            $journal1->details = $validated['details'] ?? 'پرداخت معاش به کارمند';
            $journal1->transaction_type = 2; // 1: received, 2: paid
            $journal1->payment_type = 1; // 1: cache, 2: loan
            $journal1->option_label = 'پرداخت معاش';
            $journal1->save();
    

            // ثبت ژورنال برای کارمند 
            // Recieved Cache = T1P1
            $journal2 = new Journal();
            $journal2->bill_no =  0;
            $journal2->code = $newJournalCode;
            $journal2->branch_id = $validated['branch_id'];
            $journal2->inserted_full_date = $full_date;
            $journal2->inserted_short_date = $todaysDate;
            $journal2->user = auth()->user()->full_name ?? '';
            $journal2->year = $year;
            $journal2->month = $month;
            $journal2->day = $day;
            $journal2->status = 5;  // 1: old journal, 2: journal, 3:income, 4:expense, 5:salary, 6:participants, 7:buy, 8:sales, 9:other
            $journal2->times = $times;
            $journal2->is_single_record = 1; // 0: single, 1: pair; 
            $journal2->dynamic_type = 1 ; // for employee store 1 to be shown in the salary list
            $journal2->account_id = $validated['to_account_id'];
            $journal2->amount = $validated['amount'];
            $journal2->currency_id = $validated['currency_id'];
            $journal2->details = $validated['details'] ?? 'دریافت معاش';
            $journal2->transaction_type = 1; // 1: received, 2: paid
            $journal2->payment_type = 1; // 1: cache, 2: loan
            $journal2->option_label = 'دریافت معاش';
            $journal2->save();

            // Commit the transaction
            DB::commit();
    
            Session::flash('notification', [
                'message' => 'موفقانه ثبت گردید',
                'type' => 'success',
            ]);
            return redirect()->route('salary.index'); 

        } catch (\Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollBack();
            // Optionally, log the error for debugging
            \Log::error('Error storing salary entry: ' . $e->getMessage());
    
            // Use MessageService to return error message
            Session::flash('notification', [
                'message' => ' ثبت نگردید',
                'type' => 'danger',
            ]);
            return redirect()->route('salary.index'); 
        }
    }
    

  
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $employees = Account::select('id','name')->where('account_type_id',2)->where('branch_id', $this->branch_id)->get();
        $ownBanks = Account::select('id','name')->whereIn('account_type_id',[1,6])->where('branch_id', $this->branch_id)->orderBy('is_pre_select','DESC')->get();


        if(!$ownBanks) {
            return "لطفا یکی از حساب های شرکت را پیش فرض انتخاب نمایید ";
            die();
        }

        $months = array(
            '1' => 'حمل',
            '2' => 'ثور',
            '3' => 'جوزا',
            '4' => 'سرطان',
            '5' => 'اسد',
            '6' => 'سنبله',
            '7' => 'میزان',
            '8' => 'عقرب',
            '9' => 'قوس',
            '10' => 'جدی',
            '11' => 'دلو',
            '12' => 'حوت',
        );

      
        $todaysDate = Jalalian::now()->format('Y-m-d');
        $cur_year = Jalalian::now()->format('Y');
        $cur_month = Jalalian::now()->format('n');
        $currencies = Currency::all();
        $branchs = Branch::where('id',$this->branch_id)->get();

        $salary = Journal::with(['accountRelation', 'currencyRelation','branchRelation'])
        ->where('id', $id)
        ->orderBy('id', 'ASC')
        ->first();
        // return response()->json(['data' => $salary]);
        return view('hr.salary.edit',compact('currencies','ownBanks','employees','branchs','salary','months','cur_year','cur_month','todaysDate'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        DB::beginTransaction(); // Start transaction

        try {
            // Validate input
            $validated = $request->validate([
                'from_account_id' => 'required|exists:accounts,id',
                'to_account_id' => 'required|exists:accounts,id',
                'branch_id' => 'required|exists:branches,id',
                'amount' => 'required|numeric',  
                'currency_id' => 'required|exists:currencies,id',
                'year' => 'required|numeric',
                'month' => 'required|numeric',
                'details' => 'nullable|string|max:255',
            ]);

            // Get the salary entry
            $journal1 = Journal::findOrFail($request->id); 

            // Prepare date values
            $todaysDate = $request->todays_date;
            $dateParts = explode('-', $todaysDate);
            $year = $validated['year'];
            $month = $validated['month'];
            $day = $dateParts[2] ?? now()->day; 
            $short_date = "{$year}-{$month}-{$day}";
            $full_date = "{$year}-{$month}-{$day} " . now()->format('H:i:s');

            // Update first journal entry
            $journal1->update([
                'account_id' => $validated['to_account_id'],
                'branch_id' => $validated['branch_id'],
                'inserted_full_date' => $full_date,
                'inserted_short_date' => $short_date,
                'user' => auth()->user()->full_name ?? '',
                'year' => $year,
                'month' => $month,
                'day' => $day,
                'amount' => $validated['amount'],
                'currency_id' => $validated['currency_id'],
                'details' => $validated['details'],
            ]);

            // Get related journal entry
            $journal2 = Journal::where('code', $journal1->code)
                ->where('times', $journal1->times)
                ->whereNull('dynamic_type')
                ->first();

            if ($journal2) {
                $journal2->update([
                    'account_id' => $validated['from_account_id'],
                    'branch_id' => $validated['branch_id'],
                    'inserted_full_date' => $full_date,
                    'inserted_short_date' => $short_date,
                    'user' => auth()->user()->full_name ?? '',
                    'year' => $year,
                    'month' => $month,
                    'day' => $day,
                    'amount' => $validated['amount'],
                    'currency_id' => $validated['currency_id'],
                    'details' => $validated['details'],
                ]);
            }

            DB::commit(); // Commit transaction

            Session::flash('notification', [
                'message' => 'موفقانه ویرایش گردید',
                'type' => 'success',
            ]);
            return redirect()->route('salary.index'); 
        } 
        catch (\Exception $e) 
        { 
            DB::rollBack(); // Rollback on error
            \Log::error('Error occurred in salary update: ' . $e->getMessage());
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
    public function destroy(string $times)
    {
        // Start a transaction for safety
        DB::beginTransaction();
        
        try {
            // Find all journal records with the given 'times' value
            $journals = Journal::where('times', $times)->get();

            if ($journals->isNotEmpty()) {
                // Delete all found journal entries
                Journal::where('times', $times)->delete();

                // Commit transaction
                DB::commit();

                session()->flash('notification', [
                    'type' => 'success',
                    'message' => 'موفقانه حذف گردید',
                ]);

                return redirect()->route('salary.index');
            } else {
                // Rollback transaction in case no records were found
                DB::rollBack();

                session()->flash('notification', [
                    'type' => 'danger',
                    'message' => 'حذف نگردید',
                ]);

                return back();
            }
        } catch (\Exception $e) {
            // Rollback transaction in case of error
            DB::rollBack();

            \Log::error('Error deleting journal entry: ' . $e->getMessage());

            session()->flash('notification', [
                'type' => 'danger',
                'message' => 'حذف نشد، خطا رخ داد!',
            ]);

            return back();
        }
    }


    
}

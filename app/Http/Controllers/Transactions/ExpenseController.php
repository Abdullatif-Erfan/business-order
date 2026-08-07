<?php
namespace App\Http\Controllers\Transactions;

use App\Services\MessageService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting\Account;
use App\Models\Setting\Currency;
use App\Models\Transaction\Journal;
use App\Models\Setting\ExpenseType;
use App\Models\Setting\OrgBio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class ExpenseController extends Controller
{
    protected $isAdmin, $userId, $carIds;
    public function __construct()
    {
        if (auth()->check()) {
            $this->isAdmin = session('isAdmin', auth()->user()->isAdmin == 1);
            $this->userId = session('userId', auth()->user()->id);
            $this->carIds = session('carIds', []);
        } 
        else 
        {
            $this->isAdmin = false;
            $this->userId = 0;
            $this->carIds = [];
        }
    }

    public function index()
    {
        $types = ExpenseType::all();
        // $accounts = Account::get();
        $currencies = Currency::all();
        $orgbios = OrgBio::all();
        return view('transactions.expense.list', compact('currencies', 'orgbios', 'types'));
    }

    /**
     * Show the expense data
     */
    public function getData(Request $request)
    {
        /**
         * status: 1: old income, 2: journal, 3:income, 4:expense, 5:salary, 6:participants, 7:buy, 8:sales, 9:other
         */
        $expenses = Journal::with([
            'accountRelation' => function($query) {
                $query->select('id', 'name','user_account_id');
            },
            'currencyRelation' => function($query) {
                $query->select('id', 'name', 'symbols', 'color');
            },
            'expenseTypeRelation' => function($query) {
                $query->select('id', 'name');
            }
        ])
        ->select('id', 'code', 'bill_no', 'amount', 'account_id', 'transaction_type', 
                'payment_type', 'currency_id', 'details', 'idate', 'status', 'times', 
                'is_single_record', 'dynamic_type', 'doc')
        ->where('journals.status', '=', 4)
        // ->whereHas('accountRelation', function($query) {
        //     if(!$this->isAdmin) {
        //         $query->where('user_account_id', $this->userId);
        //     }
        // })
        ->orderBy('id', 'DESC');

          if(!$this->isAdmin) {
                $expenses->where('user_id',$this->userId);
        }

        // Apply filters if provided
        if ($request->type_id) {
            $expenses->where('dynamic_type', $request->type_id);
        }
        
        if ($request->currency_id) {
            $expenses->where('currency_id', $request->currency_id);
        }
       
        if ($request->start_date && $request->end_date) {
            $expenses->whereBetween('idate', [$request->start_date, $request->end_date]);
        } elseif ($request->start_date) {
            $expenses->whereDate('idate', '=', $request->start_date);
        } elseif ($request->end_date) {
            //   Use <= instead of >= for end_date
            $expenses->whereDate('idate', '<=', $request->end_date);
        }

       if ($request->code_number) {
            $journals->where('code', $request->code_number);
        }
        if ($request->bill_number) {
            $journals->where('bill_no', $request->bill_number);
        }

        return DataTables::of($expenses)
            ->addIndexColumn()
            ->addColumn('accountRelation', function ($expense) {
                return $expense->accountRelation ? $expense->accountRelation->name : '';
            })
            ->addColumn('expenseTypeRelation', function ($expense) {
                return $expense->expenseTypeRelation ? $expense->expenseTypeRelation->name : '';
            })
            ->addColumn('transaction_type_2', function ($expense) {
                $amount = $expense->amount;
                return (fmod($amount, 1) == 0) ? number_format($amount, 0) : number_format($amount, 2);
            })
            ->addColumn('currency', function ($expense) {
                $color = $expense->currencyRelation->color ?? '#000';
                $symbol = $expense->currencyRelation->symbols ?? '';
                return '<i style="font-size:14px;color:' . $color . '">' . $symbol . '</i>';
            })
           ->addColumn('doc', function ($expense) {
                    if ($expense->doc) {
                        // Use storage URL for files stored with Storage facade
                        $url = asset('storage/' . $expense->doc);
                        return '<a href="' . $url . '" target="_blank" title="Download Document">
                                    <i class="fa fa-file-pdf" style="font-size:18px;color:#dc3545;"></i>
                                </a>';
                    }
                    return '-';
            })
            ->addColumn('edit', function ($expense) {
                return '<a href="' . route('expense.edit', $expense->id) . '" class="hidden-print">
                            <i class="fas fa-pen-square editIcon" data-id="' . $expense->id . '" style="font-size:20px;color:#4a6cf7;"></i>
                        </a>';
            })
            ->addColumn('delete', function ($expense) {
                return '<a href="' . route('expense.destroy', $expense->id) . '" class="hidden-print" 
                            onClick="return confirm(\'' . __("common.delete_confirm") . '\')">
                            <i class="fas fa-trash-alt danger deleteIcon" data-id="' . $expense->id . '" style="font-size:20px;color:#dc3545;"></i>
                        </a>';
            })
            ->rawColumns(['edit', 'delete', 'doc', 'currency'])
            ->make(true);
    }

    /**
     * Show create form
     */
    public function create()
    {
        $expenseTypes = ExpenseType::all();
        $accounts = Account::select('id', 'name')->whereIn('account_type_id', [1,2,7])
            ->orderBy('is_pre_select', 'DESC')
            ->get();

        if ($accounts->isEmpty()) {
            Session::put('notification', [
                'message' => __('journal.default_account'),
                'type' => 'warning',
            ]);
            return redirect()->route('expense.index');
        }

        $currencies = Currency::all();
        $todaysDate = Carbon::now()->format('Y-m-d');

        return view('transactions.expense.create', compact('accounts', 'currencies', 'todaysDate', 'expenseTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation
        $validated = $request->validate([
            'bill_no' => 'nullable|numeric|min:0',
            'reciever_account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'currency_id' => 'required|exists:currencies,id',
            'dynamic_type' => 'required|numeric|exists:expense_types,id',
            'details' => 'nullable|string|max:255',
            'todays_date' => 'required|date',
            'doc' => 'nullable|mimes:jpg,jpeg,png,pdf,docx,xlsx|max:2048',
        ]);

        $todaysDate = $request->todays_date ?? Carbon::now()->format('Y-m-d');
        $date = Carbon::parse($todaysDate);
        $year = $date->year;
        $month = $date->month;
        $day = $date->day;

        $new_journal_code = Journal::lockForUpdate()->max('code');
        $newJournalCode = $new_journal_code + 1;
        $times = time();

        DB::beginTransaction();

        try {
            $account_type_id = Account::where('id', $validated['reciever_account_id'])->value('account_type_id');

            $journal = new Journal();
            $journal->bill_no = $validated['bill_no'] ?? 0;
            $journal->code = $newJournalCode;
            $journal->idate = $todaysDate;
            $journal->dynamic_type = $validated['dynamic_type'];
            $journal->user_name = auth()->user()->full_name ?? '';
            $journal->user_id = auth()->user()->id ?? '';
            $journal->year = $year;
            $journal->month = $month;
            $journal->day = $day;
            $journal->status = 4; // Expense
            $journal->times = $times;
            $journal->is_single_record = 0;
            $journal->account_type_id = $account_type_id;
            $journal->account_id = $validated['reciever_account_id'];
            $journal->amount = $validated['amount'];
            $journal->currency_id = $validated['currency_id'];
            $journal->details = $validated['details'];
           
             if((int)$account_type_id === 1 || (int)$account_type_id === 6) 
            {
                $journal->transaction_type = 2; // Paid
                $journal->payment_type = 1; // Cash
                $journal->option_label = __('journal.store_expense_option_label');
            } 
            else 
            {
                $journal->transaction_type = 3; // Expense for just cars and employees
                $journal->payment_type = 1; // Cash
                $journal->option_label = __('journal.store_expense_option_label');
            }

            // Handle file upload
            // if ($request->hasFile('doc')) {
            //     $docPath = $request->file('doc')->store('documents', 'public');
            //     $journal->doc = $docPath;
            // }

            $filePath = null;
            if ($request->hasFile('doc')) {
                $file = $request->file('doc');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('documents', $fileName, 'public');
                $journal->doc = $filePath;
            }

            $journal->save();

            DB::commit();

            Session::put('notification', [
                'message' => __('common.added_successfully'),
                'type' => 'success',
            ]);
            return redirect()->route('expense.index');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error storing expense entry: ' . $e->getMessage());

            Session::put('notification', [
                'message' => __('common.add_failed') . ': ' . $e->getMessage(),
                'type' => 'danger',
            ]);
            return back()->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $expense = Journal::with([
            'accountRelation',
            'currencyRelation',
            'expenseTypeRelation'
        ])->find($id);

        if (!$expense) {
            Session::put('notification', [
                'message' => __('common.record_not_found'),
                'type' => 'danger',
            ]);
            return redirect()->route('expense.index');
        }

        $expenseTypes = ExpenseType::all();
        $currencies = Currency::all();
        $accounts = Account::select('id', 'name')->whereIn('account_type_id', [1,2,7])->get();

        return view('transactions.expense.edit', compact('currencies', 'expense', 'expenseTypes', 'accounts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        
         $validated = $request->validate([
                'bill_no' => 'nullable|numeric|min:0',
                'amount' => 'required|numeric|min:0.01',
                'reciever_account_id' => 'required|exists:accounts,id',
                'currency_id' => 'required|exists:currencies,id',
                'dynamic_type' => 'required|numeric|exists:expense_types,id',
                'details' => 'required|string|max:255',
                'todays_date' => 'required|date',
                'doc' => 'nullable|mimes:jpg,jpeg,png,pdf,docx,xlsx|max:2048',
            ]);

            $todaysDate = $request->todays_date ?? Carbon::now()->format('Y-m-d');
            $date = Carbon::parse($todaysDate);
            $year = $date->year;
            $month = $date->month;
            $day = $date->day;

        
        DB::beginTransaction();
        
        try {
            $account_type_id = Account::where('id', $validated['reciever_account_id'])->value('account_type_id');
           

            $journal = Journal::find($id);

            if (!$journal) {
                DB::rollBack();
                Session::put('notification', [
                    'message' => __('common.record_not_found'),
                    'type' => 'danger',
                ]);
                return redirect()->route('expense.index');
            }


            // Update journal entry
            $journal->bill_no = $validated['bill_no'] ?? 0;
            $journal->idate = $todaysDate;
            $journal->user_name = auth()->user()->full_name ?? '';
            $journal->user_id = auth()->user()->id ?? '';
            $journal->dynamic_type = $validated['dynamic_type'];
            $journal->year = $year;
            $journal->month = $month;
            $journal->day = $day;
            $journal->amount = $validated['amount'];
            $journal->currency_id = $validated['currency_id'];
            $journal->account_id = $validated['reciever_account_id'];
            $journal->details = $validated['details'];

             if((int)$account_type_id === 1 || (int)$account_type_id === 6) 
            {
                $journal->transaction_type = 2; // Paid
                $journal->payment_type = 1; // Cash
                $journal->option_label = __('journal.store_expense_option_label');
            } 
            else 
            {
                $journal->transaction_type = 3; // Expense for just cars and employees
                $journal->payment_type = 1; // Cash
                $journal->option_label = __('journal.store_expense_option_label');
            }

            // Handle file upload - delete old if new uploaded
            if ($request->hasFile('doc')) {
                // Delete old file if exists
                if ($journal->doc && Storage::disk('public')->exists($journal->doc)) {
                    Storage::disk('public')->delete($journal->doc);
                }
                
                // Upload new file
                $file = $request->file('doc');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $docPath = $file->storeAs('documents', $fileName, 'public');
                $journal->doc = $docPath;
            }

            $journal->save();

            DB::commit();

            Session::put('notification', [
                'message' => __('common.updated_successfully'),
                'type' => 'success',
            ]);
            return redirect()->route('expense.index');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error occurred in expense update: ' . $e->getMessage());

            Session::put('notification', [
                'message' => __('common.update_failed') . ': ' . $e->getMessage(),
                'type' => 'danger',
            ]);
            return back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // ✅ FIX: Use find() for single record
        $journal = Journal::find($id);

        if (!$journal) {
            Session::put('notification', [
                'type' => 'danger',
                'message' => __('common.record_not_found'),
            ]);
            return redirect()->route('expense.index');
        }

        try {
            // ✅ FIX: Delete associated file if exists
            if ($journal->doc && Storage::disk('public')->exists($journal->doc)) {
                Storage::disk('public')->delete($journal->doc);
            }

            // Delete the record
            $journal->delete();

            Session::put('notification', [
                'type' => 'success',
                'message' => __('common.deleted_successfully'),
            ]);

        } catch (\Exception $e) {
            \Log::error('Error deleting expense: ' . $e->getMessage());
            
            Session::put('notification', [
                'type' => 'danger',
                'message' => __('common.delete_failed') . ': ' . $e->getMessage(),
            ]);
        }

        return redirect()->route('expense.index');
    }

    /**
     * Get expense type details
     */
    public function getExpenseType($id)
    {
        $expenseType = ExpenseType::find($id);
        
        if ($expenseType) {
            return response()->json([
                'success' => true,
                'data' => $expenseType
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Expense type not found'
        ], 404);
    }
}
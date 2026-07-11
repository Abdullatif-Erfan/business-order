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
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class SalaryController extends Controller
{
    protected $isAdmin;
    
    public function __construct()
    {
        if (auth()->check()) {
            $this->isAdmin = session('isAdmin', auth()->user()->isAdmin == 1);
        } else {
            $this->isAdmin = false;
        }
    }

    public function index()
    {
        $currencies = Currency::all();
        $orgbios = OrgBio::all();
        $months = $this->getTranslatedMonthName();
        return view('hr.salary.list', compact('currencies', 'orgbios', 'months'));
    }

    public function getTranslatedMonthName()
    {
        $locale = app()->getLocale();
        $months = array();
        
        if ($locale == "fa") {
            $months = array(
                1  => 'جنوری',
                2  => 'فبروری',
                3  => 'مارچ',
                4  => 'اپریل',
                5  => 'می',
                6  => 'جون',
                7  => 'جولای',
                8  => 'اگست',
                9  => 'سپتمبر',
                10 => 'اکتوبر',
                11 => 'نومبر',
                12 => 'دسمبر',
            );
        } else if ($locale == "pa") {
            $months = array(
                1 => 'وری',
                2 => 'غویی',
                3 => 'غبرګولی',
                4 => 'چنګاښ',
                5 => 'زمری',
                6 => 'وږی',
                7 => 'تله',
                8 => 'لړم',
                9 => 'ليندۍ',
                10 => 'مرغومی',
                11 => 'سلواغه',
                12 => 'کب',
            );
        } else {
            $months = array(
                1 => 'January',
                2 => 'February',
                3 => 'March',
                4 => 'April',
                5 => 'May',
                6 => 'June',
                7 => 'July',
                8 => 'August',
                9 => 'September',
                10 => 'October',
                11 => 'November',
                12 => 'December',
            );
        }
        return $months;
    }

    /**
     * Show the salary data
     */
    public function getData(Request $request)
    {
        /**
         * status: 5 = salary
         * dynamic_type: 1 = employee salary records
         */
        $salary = Journal::with([
            'accountRelation' => function($query) {
                $query->select('id', 'name');
            },
            'currencyRelation' => function($query) {
                $query->select('id', 'name', 'symbols', 'color');
            }
        ])
        ->select('id', 'code', 'bill_no', 'amount', 'account_id', 'currency_id', 'details', 'year', 'month', 'idate', 'status', 'times')
        ->where('journals.status', '=', 5)
        ->where('journals.dynamic_type', '=', 1) // ✅ Show just employee records
        ->orderBy('id', 'DESC');

        // Apply filters
        if ($request->employee_name) {
            $salary->whereHas('accountRelation', function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->employee_name . '%');
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
            ->addColumn('amount', function ($salary) {
                $amount = $salary->amount;
                return (fmod($amount, 1) == 0) ? number_format($amount, 0) : number_format($amount, 2);
            })
            ->addColumn('currency', function ($salary) {
                $color = $salary->currencyRelation->color ?? '#000';
                $name = $salary->currencyRelation->name ?? '';
                return '<i style="font-size:14px;color:' . $color . '">' . $name . '</i>';
            })
             ->addColumn('month', function ($salary) {
                return $salary->month ? $this->getMonthName($salary->month)  : '';
            })
            ->addColumn('edit', function ($salary) {
                return '<a href="' . route('salary.edit', $salary->id) . '" class="hidden-print">
                            <i class="fas fa-pen-square editIcon" data-id="' . $salary->id . '" style="font-size:20px;color:#4a6cf7;"></i>
                        </a>';
            })
            ->addColumn('delete', function ($salary) {
                return '<a href="' . route('salary.destroy', $salary->times) . '" class="hidden-print" 
                            onClick="return confirm(\'' . __("common.delete_confirm") . '\')">
                            <i class="fas fa-trash-alt danger deleteIcon" data-id="' . $salary->id . '" style="font-size:20px;color:#dc3545;"></i>
                        </a>';
            })
            ->rawColumns(['edit', 'delete', 'currency']) // ✅ Removed 'doc' since not used
            ->make(true);
    }

     function getMonthName($month=1)
    {
        $months = array(
                1  => 'جنوری',    // January
                2  => 'فبروری',    // February
                3  => 'مارچ',    // March
                4  => 'اپریل',    // April
                5  => 'می',    // May
                6  => 'جون',   // June
                7  => 'جولای',  // July
                8  => 'اگست',    // August
                9  => 'سپتمبر',  // September
                10 => 'اکتوبر',  // October
                11 => 'نومبر',   // November
                12 => 'دسمبر',    // December
            );
        return $months[$month];
    }

    /**
     * Show create form
     */
    public function create()
    {
        $employees = Account::select('id', 'name')->where('account_type_id', 2)->get();
        $ownBanks = Account::select('id', 'name')->whereIn('account_type_id', [1, 6])
            ->orderBy('is_pre_select', 'DESC')
            ->get();

        if ($ownBanks->isEmpty()) {
            Session::put('notification', [
                'message' => __('journal.default_account'),
                'type' => 'warning',
            ]);
            return redirect()->route('salary.index');
        }

        $months = $this->getTranslatedMonthName();
        $currencies = Currency::all();
        $todaysDate = Carbon::now()->format('Y-m-d');
        $cur_year = Carbon::now()->format('Y');
        $cur_month = Carbon::now()->format('n');

        return view('hr.salary.create', compact('ownBanks', 'currencies', 'todaysDate', 'employees', 'months', 'cur_year', 'cur_month'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation
        $validated = $request->validate([
            'from_account_id' => 'required|exists:accounts,id',
            'to_account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'currency_id' => 'required|exists:currencies,id',
            'year' => 'required|numeric',
            'month' => 'required|numeric',
            'details' => 'nullable|string|max:255',
            'todays_date' => 'required|date',
        ]);

        $todaysDate = $request->todays_date;
        $date = explode('-', $todaysDate);
        $year = $validated['year'];
        $month = $validated['month'];
        $day = $date[2] ?? date('d');
        $full_date = $year . '-' . $month . '-' . $day . ' ' . date('h:i:s A');

        $newJournalCode = Journal::max('code') + 1;
        $times = time();

        $company_account_type_id = Account::where('id', $request->from_account_id)->value('account_type_id');
        $customer_account_type_id = Account::where('id', $request->to_account_id)->value('account_type_id');

        DB::beginTransaction();

        try {
            // ✅ FIXED: Journal 1 - Company account (Paid from bank)
            $journal1 = new Journal();
            $journal1->bill_no = 0;
            $journal1->code = $newJournalCode;
            $journal1->idate = $todaysDate;
            $journal1->user_name = auth()->user()->full_name ?? ''; // ✅ FIXED: Using $journal1
            $journal1->user_id = auth()->id() ?? 0; // ✅ FIXED: Using $journal1
            $journal1->year = $year;
            $journal1->month = $month;
            $journal1->day = $day;
            $journal1->status = 5;
            $journal1->times = $times;
            $journal1->is_single_record = 1;
            $journal1->dynamic_type = null; // Null for company side (not shown in salary list)
            $journal1->account_id = $validated['from_account_id'];
            $journal1->amount = $validated['amount'];
            $journal1->account_type_id = $company_account_type_id;
            $journal1->currency_id = $validated['currency_id'];
            $journal1->details = $validated['details'] ?? __('validate.salary_payment');
            $journal1->transaction_type = 2; // Paid
            $journal1->payment_type = 1; // Cash
            $journal1->option_label = __('validate.salary_payment');
            $journal1->save();

            // ✅ Journal 2 - Employee account (Received salary)
            $journal2 = new Journal();
            $journal2->bill_no = 0;
            $journal2->code = $newJournalCode;
            $journal2->idate = $todaysDate;
            $journal2->user_name = auth()->user()->full_name ?? '';
            $journal2->user_id = auth()->id() ?? 0;
            $journal2->year = $year;
            $journal2->month = $month;
            $journal2->day = $day;
            $journal2->status = 5;
            $journal2->times = $times;
            $journal2->is_single_record = 1;
            $journal2->dynamic_type = 1; // For employee (shown in salary list)
            $journal2->account_id = $validated['to_account_id'];
            $journal2->amount = $validated['amount'];
            $journal2->account_type_id = $customer_account_type_id;
            $journal2->currency_id = $validated['currency_id'];
            $journal2->details = $validated['details'] ?? __('validate.salary_recieve');
            $journal2->transaction_type = 1; // Received
            $journal2->payment_type = 1; // Cash
            $journal2->option_label = __('validate.salary_recieve');
            $journal2->save();

            DB::commit();

            Session::put('notification', [
                'message' => __('common.added_successfully'),
                'type' => 'success',
            ]);
            return redirect()->route('salary.index');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error storing salary entry: ' . $e->getMessage());

            Session::put('notification', [
                'message' => __('common.add_failed') . ': ' . $e->getMessage(),
                'type' => 'danger',
            ]);
            return redirect()->route('salary.index')->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $employees = Account::select('id', 'name')->where('account_type_id', 2)->get();
        $ownBanks = Account::select('id', 'name')->whereIn('account_type_id', [1, 6])
            ->orderBy('is_pre_select', 'DESC')
            ->get();

        if ($ownBanks->isEmpty()) {
            Session::put('notification', [
                'message' => __('validate.select_default_account'),
                'type' => 'warning',
            ]);
            return redirect()->route('salary.index');
        }

        $months = $this->getTranslatedMonthName();
        $todaysDate = Carbon::now()->format('Y-m-d');
        $cur_year = Carbon::now()->format('Y');
        $cur_month = Carbon::now()->format('n');
        $currencies = Currency::all();

        $salary = Journal::with(['accountRelation', 'currencyRelation'])
            ->where('id', $id)
            ->first();

        if (!$salary) {
            Session::put('notification', [
                'message' => __('common.record_not_found'),
                'type' => 'danger',
            ]);
            return redirect()->route('salary.index');
        }

        return view('hr.salary.edit', compact('currencies', 'ownBanks', 'employees', 'salary', 'months', 'cur_year', 'cur_month', 'todaysDate'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'id' => 'required|exists:journals,id',
                'from_account_id' => 'required|exists:accounts,id',
                'to_account_id' => 'required|exists:accounts,id',
                'amount' => 'required|numeric|min:0.01',
                'currency_id' => 'required|exists:currencies,id',
                'year' => 'required|numeric',
                'month' => 'required|numeric',
                'details' => 'nullable|string|max:255',
                'todays_date' => 'required|date',
            ]);

            // Get the salary entry (employee side)
            $journal1 = Journal::findOrFail($request->id);

            $todaysDate = $request->todays_date;
            $dateParts = explode('-', $todaysDate);
            $year = $validated['year'];
            $month = $validated['month'];
            $day = $dateParts[2] ?? date('d');
            $short_date = $year . '-' . $month . '-' . $day;

            // Update employee journal entry
            $journal1->update([
                'account_id' => $validated['to_account_id'],
                'idate' => $short_date,
                'user_name' => auth()->user()->full_name ?? '',
                'user_id' => auth()->id() ?? 0,
                'year' => $year,
                'month' => $month,
                'day' => $day,
                'amount' => $validated['amount'],
                'currency_id' => $validated['currency_id'],
                'details' => $validated['details'] ?? $journal1->details,
            ]);

            // Update company journal entry (same code, same times)
            $journal2 = Journal::where('code', $journal1->code)
                ->where('times', $journal1->times)
                ->whereNull('dynamic_type')
                ->first();

            if ($journal2) {
                $journal2->update([
                    'account_id' => $validated['from_account_id'],
                    'idate' => $short_date,
                    'user_name' => auth()->user()->full_name ?? '',
                    'user_id' => auth()->id() ?? 0,
                    'year' => $year,
                    'month' => $month,
                    'day' => $day,
                    'amount' => $validated['amount'],
                    'currency_id' => $validated['currency_id'],
                    'details' => $validated['details'] ?? $journal2->details,
                ]);
            }

            DB::commit();

            Session::put('notification', [
                'message' => __('common.updated_successfully'),
                'type' => 'success',
            ]);
            return redirect()->route('salary.index');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error occurred in salary update: ' . $e->getMessage());

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
    public function destroy(string $times)
    {
        DB::beginTransaction();

        try {
            $journals = Journal::where('times', $times)->get();

            if ($journals->isNotEmpty()) {
                // Delete all found journal entries
                Journal::where('times', $times)->delete();

                DB::commit();

                Session::put('notification', [
                    'type' => 'success',
                    'message' => __('common.deleted_successfully'),
                ]);

                return redirect()->route('salary.index');
            } else {
                DB::rollBack();

                Session::put('notification', [
                    'type' => 'danger',
                    'message' => __('common.record_not_found'),
                ]);

                return back();
            }
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Error deleting salary entry: ' . $e->getMessage());

            Session::put('notification', [
                'type' => 'danger',
                'message' => __('common.delete_failed') . ': ' . $e->getMessage(),
            ]);

            return back();
        }
    }
}
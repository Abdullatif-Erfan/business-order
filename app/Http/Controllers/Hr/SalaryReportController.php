<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting\Account;
use App\Models\Setting\Currency;
use App\Models\Transaction\Journal;
use App\Models\Setting\OrgBio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class SalaryReportController extends Controller
{
    protected $isAdmin, $userId;
    public function __construct()
    {
        if (auth()->check()) {
            $this->isAdmin = session('isAdmin', auth()->user()->isAdmin == 1);
            $this->userId = session('userId', auth()->user()->id);
        } else {
            $this->isAdmin = false;
            $this->userId = 0;
        }
    }

    public function index()
    {
        if($this->isAdmin) {
            $accounts   = Account::where('account_type_id',2)->get();
        } else {
            $accounts   = Account::where('account_type_id',2)->where('user_account_id',$this->userId)->get();
        }
        $currencies = Currency::all();
        $orgbios = OrgBio::all();

        return view('hr.report.list',compact('accounts','currencies','orgbios'));
    }

    public function getTranslatedMonthName()
    {
        $locale = app()->getLocale();
        $months = array();
        if($locale == "fa")
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

        }
        else if ($locale == "pa") 
        {
            $months = array(
                '1' => 'وری',
                '2' => 'غویی',
                '3' => 'غبرګولی',
                '4' => 'چنګاښ',
                '5' => 'زمری',
                '6' => 'وږی',
                '7' => 'تله',
                '8' => 'لړم',
                '9' => 'ليندۍ',
                '10' => 'مرغومی',
                '11' => 'سلواغه',
                '12' => 'کب',
            );
        }
        else
        {
            $months = array(
                '1' => 'January',
                '2' => 'February',
                '3' => 'March',
                '4' => 'April',
                '5' => 'May',
                '6' => 'June',
                '7' => 'July',
                '8' => 'August',
                '9' => 'September',
                '10' => 'October',
                '11' => 'November',
                '12' => 'December',
            );
        }
        return $months;
    }

    public function getData(Request $request)
    {
        /**
         * ================================== Journal Status ========================
         * status: 1: old journal, 2: journal, 3:income, 4:expense, 5:salary, 6:participants, 7:buy, 8:sales, 9:other
         * 
         * ================================== Transaction Types ====================
         * transaction_type: 1: recieved (دریافت)      2: paid (پرداخت)
         * payment_type:     1: cache (نقد)            2: loan (قرض/طلب)
         * 
         * ================================== Balance Logic ========================
         * For Employee Accounts (account_type_id = 2):
         * 
         * Receivable (طلب) = Loan Paid + Cache Paid     → Makes balance POSITIVE (+)
         * Payable (قرض)    = Cache Received + Loan Received → Makes balance NEGATIVE (-)
         * 
         * Balance = Receivable - Payable
         * 
         * Positive Balance = Company owes employee (طلبات)
         * Negative Balance = Employee owes company (قرض)
         */
        
        $currency_id = $request->currency_id ?? 0;
        $account_id = $request->account_id ?? 0;


        $journals = Journal::with(['accountRelation:id,name,user_account_id'])
            ->select('id', 'code', 'bill_no', 'amount', 'account_id', 'transaction_type', 
                    'payment_type', 'options', 'option_label', 'currency_id', 'details', 
                    'idate', 'status', 'times', 'is_single_record', 'user_name')
            ->where('account_type_id', 2)  // Enforce just employees
            ->where('currency_id', $request->currency_id) 
            ->whereHas('accountRelation', function($query) {
            if(!$this->isAdmin) {
                $query->where('user_account_id', $this->userId);
            }
            })
            ->orderBy('id', 'ASC');

        // Apply optional filters
        if ($request->start_date && $request->end_date) {
            $journals->whereBetween('idate', [$request->start_date, $request->end_date]);
        } elseif ($request->start_date) {
            $journals->whereDate('idate', '=', $request->start_date);
        } elseif ($request->end_date) {
            $journals->whereDate('idate', '<=', $request->end_date);
        }
        if ($request->code_number) {
            $journals->where('code', 'LIKE', "%{$request->code_number}%");
        }
        if ($request->bill_number) {
            $journals->where('bill_no', 'LIKE', "%{$request->bill_number}%");
        }

        if ($request->filled('account_id')) {
            $journals->where('journals.account_id', $request->account_id);
        }

        // Initialize running balance for employee
        $runningBalance = 0;

        return DataTables::of($journals)
            ->addIndexColumn()
            
            // Account Name
            ->addColumn('accountRelation', function ($journal) {
                return $journal->accountRelation ? $journal->accountRelation->name : '';
            })
            
            /**
             * CACHE RECEIVED (دریافت نقد)
             * transaction_type = 1 (Received), payment_type = 1 (Cash)
             * When employee receives cash from company
             */
            ->addColumn('cacheRecieved', function ($journal) {
                return ($journal->transaction_type == 1 && $journal->payment_type == 1) 
                    ? number_format($journal->amount, 2) 
                    : null;
            })
            
            /**
             * CACHE PAID (پرداخت نقد)
             * transaction_type = 2 (Paid), payment_type = 1 (Cash)
             * When employee pays cash to company
             */
            ->addColumn('cachePaid', function ($journal) {
                return ($journal->transaction_type == 2 && $journal->payment_type == 1) 
                    ? number_format($journal->amount, 2) 
                    : null;
            })
            
            /**
             * LOAN RECEIVED (قرض)
             * transaction_type = 1 (Received), payment_type = 2 (Loan)
             * When employee takes loan from company
             */
            ->addColumn('loanRecieved', function ($journal) {
                return ($journal->transaction_type == 1 && $journal->payment_type == 2) 
                    ? number_format($journal->amount, 2) 
                    : null;
            })
            
            /**
             * LOAN PAID (طلب)
             * transaction_type = 2 (Paid), payment_type = 2 (Loan)
             * When employee pays back loan to company
             */
            ->addColumn('loanPaid', function ($journal) {
                return ($journal->transaction_type == 2 && $journal->payment_type == 2) 
                    ? number_format($journal->amount, 2) 
                    : null;
            })
                
            /**
             * USER NAME (نام کاربر)
             */
            ->addColumn('full_name', function ($journal) {
                return $journal->user_name ?? '';
            })
            
            /**
             * RUNNING BALANCE (بیلانس)
             * 
             * For Employee Accounts:
             * - Receivable (طلب) = Loan Paid + Cache Paid → Makes balance POSITIVE (+)
             * - Payable (قرض)    = Cache Received + Loan Received → Makes balance NEGATIVE (-)
             * 
             * Balance = Receivable - Payable
             * 
             * Positive Balance = Company owes employee (طلبات)
             * Negative Balance = Employee owes company (قرض)
             */
            ->addColumn('balance', function ($journal) use (&$runningBalance) {
                // دریافت نقد - Cash Received
                $cacheRecieved = ($journal->transaction_type == 1 && $journal->payment_type == 1) 
                    ? $journal->amount : 0;
                
                // پرداخت نقد - Cash Paid
                $cachePaid = ($journal->transaction_type == 2 && $journal->payment_type == 1) 
                    ? $journal->amount : 0;
                
                // قرض - Loan Received (Employee takes loan)
                $loanRecieved = ($journal->transaction_type == 1 && $journal->payment_type == 2) 
                    ? $journal->amount : 0;
                
                // طلب - Loan Paid (Employee pays back loan)
                $loanPaid = ($journal->transaction_type == 2 && $journal->payment_type == 2) 
                    ? $journal->amount : 0;

                // Receivable = Loan Paid + Cache Paid (Company owes employee)
                $receivable = $loanPaid + $cachePaid;
                
                // Payable = Cache Received + Loan Received (Employee owes company)
                $payable = $cacheRecieved + $loanRecieved;

                // Balance change = Receivable - Payable
                $balanceChange = $receivable - $payable;
                
                // Update running balance
                $runningBalance += $balanceChange;
                
                return number_format($runningBalance, 2);
            })
            
            /**
             * ROW CLASS (کلاس ردیف)
             * Highlight clearance rows (status = 11)
             */
            ->setRowClass(function ($journal) {
                return $journal->status == 11 ? 'clearance-row' : '';
            })
            
            // ->rawColumns()
            ->make(true);
    }

    /**
     * Show the expense data
     */
    public function getDataV1(Request $request)
    {
        /**
         * status: 1: old income, 2: journal, 3:income, 4:expense, 5:salary, 6:participants, 7:buy, 8:sales, 9:other
         */
        $account_id = $request->account_id ?? 0;
        $currency_id = $request->currency_id ?? 0;

         // Check if account_id and currency_id are provided
         if (!$request->has('account_id') && !$request->has('currency_id')) {
            return response()->json([
                'data' => [],
            ]);
        }

        $salary = Journal::with(['accountRelation' => function($query){
            $query->select('id','name');
        },'currencyRelation' => function($query){
            $query->select('id','name','symbols','color');
        }])
        // $salary = Journal::with(['accountRelation','currencyRelation','expenseTypeRelation'])
        ->select('id','code','bill_no','amount','account_id','currency_id','details','year','month','idate','status','times','user_name')
        ->where('journals.status','=',5)
        ->where('journals.currency_id','=',$currency_id)
        // ->where('journals.account_id','=',$account_id)
        ->when(!empty($account_id), function ($query) use ($account_id) {
                return $query->where('journal.account_id', $account_id);
        })
        ->where('journals.dynamic_type','=',1) // show just employee records
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
            ->addColumn('month', function ($salary) {
                return $salary->month ? $this->getMonthName($salary->month)  : '';
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



            ->rawColumns(['currency'])
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



}

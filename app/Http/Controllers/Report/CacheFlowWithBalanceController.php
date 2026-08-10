<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting\Account;
use App\Models\Setting\Currency;
use App\Models\Transaction\Journal;
use App\Models\Setting\OrgBio;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CacheFlowWithBalanceController extends Controller
{
    protected $isAdmin, $customerIds, $carIds, $userId, $userName;
    public function __construct()
    {
        if(auth()->check()) {
            $this->isAdmin = session('isAdmin', auth()->user()->isAdmin == 1);
            $this->customerIds = session('customerIds', []);
            $this->carIds = session('carIds', []);
            $this->userId = session('userId', auth()->user()->id);
            $this->userName = auth()->user()->full_name;
        } else {
            $this->isAdmin = false;
            $this->customerIds = [];
            $this->carIds = [];
            $this->userId = 0;
            $this->userName ='';
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // نمایش لیست مشتریان و خزانه ها و فروشنده گان
        // $accounts = Account::whereIn('account_type_id',[1,2,3,4,5,6,7])->get();
        if($this->isAdmin) 
        {
            $accounts = Account::get();
        } 
        else 
        {
            $accounts = Account::select('id', 'name', 'emp_car_id')
            ->whereIn('emp_car_id', $this->carIds)
            ->where('account_type_id', 7)
            ->orWhere(function($query) {
                $query->where('user_account_id', $this->userId)
                    ->where('account_type_id', 2);
            })
            ->get();
        }
        $currencies = Currency::all();
        $orgbios = OrgBio::all();
        
        return view('report.cacheflow.list_with_balance',compact('accounts','currencies','orgbios'));
    }

    private function showFooterReport($currency_id, $account_id)
    {
        $sums = DB::table('journals')
        ->select(
            DB::raw('SUM(CASE WHEN transaction_type = 1 AND payment_type = 1 
            AND currency_id='.$currency_id.' AND account_id='.$account_id.' THEN amount ELSE 0 END) as sumCacheRecieved'),
            DB::raw('SUM(CASE WHEN transaction_type = 2 AND payment_type = 1
            AND currency_id='.$currency_id.' AND account_id='.$account_id.' THEN amount ELSE 0 END) as sumCachePaid'),
            DB::raw('SUM(CASE WHEN transaction_type = 1 AND payment_type = 2
            AND currency_id='.$currency_id.' AND account_id='.$account_id.' THEN amount ELSE 0 END) as sumLoanRecieved'),
            DB::raw('SUM(CASE WHEN transaction_type = 2 AND payment_type = 2
            AND currency_id='.$currency_id.' AND account_id='.$account_id.' THEN amount ELSE 0 END) as sumLoanPaid')
        )
        ->first();
        return $sums;
    }
    /**
     * Show the journal data
     */  
    public function getData(Request $request)
    {
        /**
         * ============================================================
         * Journal Status
         * ============================================================
         * status: 1: old journal, 2: journal, 3:income, 4:expense, 
         *         5:salary, 6:participants, 7:buy, 8:sales, 9:other
         * 
         * ============================================================
         * Transaction Types
         * ============================================================
         * transaction_type: 1: Received (دریافت)  2: Paid (پرداخت)
         * payment_type:     1: Cash (نقد)         2: Loan (قرض/طلب)
         * 
         * ============================================================
         * Balance Logic
         * ============================================================
         * For Company Accounts (account_type_id: 1, 6):
         *   - Positive Balance = Company has more cash
         *   - Negative Balance = Company owes
         * 
         * For Other Accounts (Customers, Suppliers, etc.):
         *   - Positive Balance = They owe company (طلبات)
         *   - Negative Balance = Company owes them (قرض)
         * 
         * ============================================================
         * Formula:
         *   Balance Change = (cacheRecieved + loanPaid) - (cachePaid + loanRecieved)
         *   cacheRecieved and loanRecieved are money COMING IN
         *   cachePaid and loanPaid are money GOING OUT
         * ============================================================
         */

        // Validate required parameters
        if (!$request->has('account_id') || !$request->has('currency_id')) {
            return response()->json([
                'data' => [],
                'isCompanyAccount' => false,
            ]);
        }

        // Get account type to determine balance direction
        $account = Account::select('account_type_id', 'is_pre_select')
            ->where('id', $request->account_id)
            ->first();

        $isCompanyAccount = $account && in_array($account->account_type_id, [1, 6]);

        // Build query
        $journals = Journal::with(['accountRelation:id,name', 'currencyRelation:id,name,symbols,color'])
            ->select('id', 'code', 'bill_no', 'amount', 'account_id', 'transaction_type', 
                    'payment_type', 'options', 'currency_id', 'details', 
                    'idate', 'status', 'times', 'user_name')
            ->where('account_id', $request->account_id)
            ->where('currency_id', $request->currency_id)
            ->orderBy('id', 'ASC');

        // Apply filters
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

        // Get all journals
        $journalsCollection = $journals->get();

        // Calculate running balance
        $runningBalance = 0;

        $journalsWithBalance = $journalsCollection->map(function ($journal) use ($isCompanyAccount, &$runningBalance) {
            // Get transaction amounts
            $cacheRecieved = 0;
            $cachePaid = 0;
            $loanRecieved = 0;
            $loanPaid = 0;
            $expense = 0;

            // Determine transaction types based on account type
            if ($isCompanyAccount) {
                // Company Account: transaction_type 1 = Received, 2 = Paid
                $cacheRecieved = ($journal->transaction_type == 1 && $journal->payment_type == 1) ? $journal->amount : 0;
                $cachePaid = ($journal->transaction_type == 2 && $journal->payment_type == 1) ? $journal->amount : 0;
                $loanRecieved = ($journal->transaction_type == 1 && $journal->payment_type == 2) ? $journal->amount : 0;
                $loanPaid = ($journal->transaction_type == 2 && $journal->payment_type == 2) ? $journal->amount : 0;
                $expense = 0;
            } else {
                // Non-Company Account (Customer, Supplier, etc.)
                // For these accounts, the perspective is reversed
                $cacheRecieved = ($journal->transaction_type == 1 && $journal->payment_type == 1) ? $journal->amount : 0;
                $cachePaid = ($journal->transaction_type == 2 && $journal->payment_type == 1) ? $journal->amount : 0;
                $loanRecieved = ($journal->transaction_type == 1 && $journal->payment_type == 2) ? $journal->amount : 0;
                $loanPaid = ($journal->transaction_type == 2 && $journal->payment_type == 2) ? $journal->amount : 0;
                $expense = ($journal->transaction_type == 3 && $journal->payment_type == 1) ? $journal->amount : 0;
            }

            // Calculate balance change
            // Positive = Money coming in, Negative = Money going out
            $balanceChange = ($cachePaid + $loanPaid + $expense) - ($cacheRecieved + $loanRecieved);
            $runningBalance += $balanceChange;


            // Attach calculated balance to journal
            $journal->calculated_balance = number_format($runningBalance, 2);

            // Store calculated values for columns
            $journal->cacheRecievedValue = $cacheRecieved;
            $journal->cachePaidValue = $cachePaid;
            $journal->loanRecievedValue = $loanRecieved;
            $journal->loanPaidValue = $loanPaid;
            $journal->expense = $expense;

            return $journal;
        });

        return DataTables::of($journalsWithBalance)
            ->addIndexColumn()
            
            // Account Name
            ->addColumn('accountRelation', function ($journal) {
                return $journal->accountRelation ? $journal->accountRelation->name : '';
            })
            
            // Cash Received (آمد نقد)
            ->addColumn('cacheRecieved', function ($journal) {
                return $journal->cacheRecievedValue > 0 
                    ? number_format($journal->cacheRecievedValue, 2) 
                    : null;
            })
            
            // Cash Paid (رفت نقد)
            ->addColumn('cachePaid', function ($journal) {
                return $journal->cachePaidValue > 0 
                    ? number_format($journal->cachePaidValue, 2) 
                    : null;
            })
            
            // Loan Received (قرض)
            ->addColumn('loanRecieved', function ($journal) {
                return $journal->loanRecievedValue > 0 
                    ? number_format($journal->loanRecievedValue, 2) 
                    : null;
            })
            
            // Loan Paid (طلب)
            ->addColumn('loanPaid', function ($journal) {
                return $journal->loanPaidValue > 0 
                    ? number_format($journal->loanPaidValue, 2) 
                    : null;
            })

             // Expense (مصارف)
            ->addColumn('expense', function ($journal) {
                return $journal->expense > 0 
                    ? number_format($journal->expense, 2) 
                    : null;
            })
            
            // Currency
            ->addColumn('currency', function ($journal) {
                return $journal->currencyRelation 
                    ? '<i style="font-size:14px;color:'.$journal->currencyRelation->color.'">'.$journal->currencyRelation->symbols.'</i>' 
                    : '';
            })
            
            // User Name
            ->addColumn('full_name', function ($journal) {
                return $journal->user_name ?? '';
            })
            
            // Balance (بیلانس)
            ->addColumn('balance', function ($journal) {
                return $journal->calculated_balance;
            })
            
            ->rawColumns(['currency'])
            ->with([
                'isCompanyAccount' => $isCompanyAccount
            ])
            ->setRowClass(function ($journal) {
                return $journal->status == 12 ? 'clearance-row bg-green' : '';
            })
            ->make(true);
    }


}

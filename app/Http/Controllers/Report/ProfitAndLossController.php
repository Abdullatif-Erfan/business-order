<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth; 
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Warehouse\WarehouseSales;
use App\Models\Buy\BoughtItem;
use App\Models\Setting\Currency;
use App\Models\Setting\Account;
use App\Models\Journal\Journal;
use App\Models\Warehouse\SalesDetails;
use App\Models\Setting\OrgBio;
use Illuminate\Support\Facades\Log;

class ProfitAndLossController extends Controller
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
        $orgbios = OrgBio::all();
        $currencies = Currency::all();
        
        // Set default date range to null (no filter)
        $data['start_date'] = "";
        $data['end_date'] = "";
        $data['currency_id'] = $currencies->first()->id ?? 1;
        $data['currency_name'] = $currencies->first()->name ?? 'AFN';
        $data['currency_symbol'] = $currencies->first()->symbol ?? 'AFN';

        return view('report.profitAndLoss.list', compact(
            'orgbios',
            'currencies',
            'data'
        ));
    }

    /**
     * Get Profit and Loss Data via AJAX
     */
    public function getData(Request $request)
    {
        try {
            $currency_id = $request->input('currency_id');
            $start_date = $request->input('start_date');
            $end_date = $request->input('end_date');

            // Set defaults if not provided
            if (!$currency_id) {
                $currency = Currency::first();
                $currency_id = $currency->id ?? 1;
            }

            // Only set default dates if both are empty
            // If one is empty and the other has value, use the provided one
            if (empty($start_date) && empty($end_date)) {
                // No date filter - get all data
                $start_date = null;
                $end_date = null;
            } elseif (empty($start_date)) {
                // Only end_date provided, set start_date to beginning of time or a very old date
                $start_date = '1970-01-01';
            } elseif (empty($end_date)) {
                // Only start_date provided, set end_date to today
                $end_date = Carbon::now()->format('Y-m-d');
            }

            // Log the request for debugging
            Log::info('ProfitAndLoss getData called', [
                'currency_id' => $currency_id,
                'start_date' => $start_date,
                'end_date' => $end_date
            ]);

            // Get all data with date filters
            $transactionSummary = $this->getTransactionSummary($currency_id, $start_date, $end_date);
            $salesProfit = $this->getSalesProfit($currency_id, $start_date, $end_date);
            $khazanaReport = $this->getKhazanaReport($currency_id, $start_date, $end_date);

            // Calculate totals
            $totalIncome = ($transactionSummary->total_income ?? 0) + ($salesProfit->total_profit ?? 0);
            $totalExpense = ($transactionSummary->total_salary ?? 0) + ($transactionSummary->total_expense ?? 0);
            $finalNetIncome = $totalIncome - $totalExpense;

            return response()->json([
                'status' => 'success',
                'message' => 'Data retrieved successfully',
                'data' => [
                    'transaction_summary' => $transactionSummary,
                    'sales_profit' => $salesProfit,
                    'khazanaReport' => $khazanaReport,
                    'total_income' => $totalIncome,
                    'total_expense' => $totalExpense,
                    'final_net_income' => $finalNetIncome,
                    'start_date' => $start_date ?? 'All Time',
                    'end_date' => $end_date ?? 'All Time',
                    'currency_id' => $currency_id
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('ProfitAndLoss getData error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Sales Profit with optional Date Range
     */
    private function getSalesProfit($currency_id, $start_date, $end_date)
    {
        try {
            $query = DB::table('sales_details')
                ->join('warehouse_sales', 'warehouse_sales.id', '=', 'sales_details.warehouse_sales_id')
                ->where('warehouse_sales.currency_id', $currency_id);
            
            // Apply date filters only if provided
            if ($start_date && $end_date) {
                $query->whereBetween('warehouse_sales.idate', [$start_date, $end_date]);
            } elseif ($start_date) {
                $query->where('warehouse_sales.idate', '>=', $start_date);
            } elseif ($end_date) {
                $query->where('warehouse_sales.idate', '<=', $end_date);
            }
            
            $profitData = $query->selectRaw('COALESCE(SUM(sales_details.profit), 0) as total_profit')
                ->first();

            return (object) [
                'total_profit' => $profitData->total_profit ?? 0,
            ];
        } catch (\Exception $e) {
            Log::error('getSalesProfit error: ' . $e->getMessage());
            return (object) ['total_profit' => 0];
        }
    }

    /**
     * Get Khazana Report with optional Date Range
     */
    private function getKhazanaReport($currency_id, $start_date, $end_date)
    {
        try {
            $query = DB::table('journals')
                ->selectRaw("
                    COALESCE(SUM(CASE WHEN journals.transaction_type = 1 AND payment_type = 1 THEN amount ELSE 0 END), 0) as cache_recieved,
                    COALESCE(SUM(CASE WHEN journals.transaction_type = 2 AND payment_type = 1 THEN amount ELSE 0 END), 0) as cache_paid,
                    COALESCE(SUM(CASE WHEN journals.transaction_type = 2 AND payment_type = 2 THEN amount ELSE 0 END), 0) as loan_paid,
                    COALESCE(SUM(CASE WHEN journals.transaction_type = 1 AND payment_type = 2 THEN amount ELSE 0 END), 0) as loan_recieved
                ")
                ->join('accounts', 'accounts.id', '=', 'journals.account_id')
                ->whereIn('accounts.account_type_id', [1, 6])
                ->where('journals.currency_id', $currency_id)
                ->where('journals.is_cleared', 0);
            
            // Apply date filters only if provided
            if ($start_date && $end_date) {
                $query->whereBetween('journals.idate', [$start_date, $end_date]);
            } elseif ($start_date) {
                $query->where('journals.idate', '>=', $start_date);
            } elseif ($end_date) {
                $query->where('journals.idate', '<=', $end_date);
            }
            
            $bankReport = $query->first();

            // Calculate individual values
            $cashReceived = (float)($bankReport->cache_recieved ?? 0);
            $cashPaid = (float)($bankReport->cache_paid ?? 0);
            $loanPaid = (float)($bankReport->loan_paid ?? 0);
            $loanReceived = (float)($bankReport->loan_recieved ?? 0);

            return (object) [
                'totalIncome' => $cashReceived,
                'totalOutcome' => $cashPaid,
                'cashBalance' => $cashReceived - $cashPaid,
                'totalTalab' => $loanPaid,
                'totalLoan' => $loanReceived,
                'loanTalabBalance' => $loanPaid - $loanReceived,
                'finalBalance' => ($cashReceived - $cashPaid) + ($loanPaid - $loanReceived),
            ];
        } catch (\Exception $e) {
            Log::error('getKhazanaReport error: ' . $e->getMessage());
            return (object) [
                'totalIncome' => 0,
                'totalOutcome' => 0,
                'cashBalance' => 0,
                'totalTalab' => 0,
                'totalLoan' => 0,
                'loanTalabBalance' => 0,
                'finalBalance' => 0,
            ];
        }
    }

    /**
     * Get Transaction Summary with optional Date Range
     */
    private function getTransactionSummary($currency_id, $start_date, $end_date)
    {
        try {
            $company_account_type_id = 1;
            $banks_account_type_id = 6;

            // Others Expense Query
            $othersExpenseQuery = DB::table('journals')
                ->selectRaw("
                    COALESCE(SUM(CASE WHEN journals.transaction_type = 3 AND payment_type = 1 AND status = 4 THEN amount ELSE 0 END), 0) as total_expense
                ")
                ->where('journals.currency_id', $currency_id)
                ->where('journals.is_cleared', 0);
            
            // Apply date filters only if provided
            if ($start_date && $end_date) {
                $othersExpenseQuery->whereBetween('journals.idate', [$start_date, $end_date]);
            } elseif ($start_date) {
                $othersExpenseQuery->where('journals.idate', '>=', $start_date);
            } elseif ($end_date) {
                $othersExpenseQuery->where('journals.idate', '<=', $end_date);
            }
            
            $othersExpense = $othersExpenseQuery->first();

            // Transaction Data Query
            $transactionQuery = DB::table('journals')
                ->selectRaw("
                    COALESCE(SUM(CASE WHEN journals.status = 4 THEN amount ELSE 0 END), 0) as total_expense,
                    COALESCE(SUM(CASE WHEN journals.status = 3 THEN amount ELSE 0 END), 0) as total_income,
                    COALESCE(SUM(CASE WHEN journals.status = 5 THEN amount ELSE 0 END), 0) as total_salary,
                    COALESCE(SUM(CASE WHEN journals.status = 7 THEN amount ELSE 0 END), 0) as total_bought,
                    COALESCE(SUM(CASE WHEN journals.status = 8 THEN amount ELSE 0 END), 0) as total_sold,
                    COALESCE(SUM(CASE WHEN journals.transaction_type = 1 AND payment_type = 1 THEN amount ELSE 0 END), 0) as total_cache_in,
                    COALESCE(SUM(CASE WHEN journals.transaction_type = 2 AND payment_type = 1 THEN amount ELSE 0 END), 0) as total_cache_out,
                    COALESCE(SUM(CASE WHEN journals.transaction_type = 2 AND payment_type = 2 THEN amount ELSE 0 END), 0) as total_talabat,
                    COALESCE(SUM(CASE WHEN journals.transaction_type = 1 AND payment_type = 2 THEN amount ELSE 0 END), 0) as total_loan
                ")
                ->whereIn('journals.account_type_id', [$company_account_type_id, $banks_account_type_id])
                ->where('journals.currency_id', $currency_id)
                ->where('journals.is_cleared', 0);
            
            // Apply date filters only if provided
            if ($start_date && $end_date) {
                $transactionQuery->whereBetween('journals.idate', [$start_date, $end_date]);
            } elseif ($start_date) {
                $transactionQuery->where('journals.idate', '>=', $start_date);
            } elseif ($end_date) {
                $transactionQuery->where('journals.idate', '<=', $end_date);
            }
            
            $transactionData = $transactionQuery->first();
            
            $finalExpense = (float)($transactionData->total_expense ?? 0) + (float)($othersExpense->total_expense ?? 0);
            
            return (object) [
                'total_expense' => $finalExpense,
                'total_income' => $transactionData->total_income ?? 0,
                'total_salary' => $transactionData->total_salary ?? 0,
                'total_bought' => $transactionData->total_bought ?? 0,
                'total_sold' => $transactionData->total_sold ?? 0,
                'total_cache_in' => $transactionData->total_cache_in ?? 0,
                'total_cache_out' => $transactionData->total_cache_out ?? 0,
                'total_talabat' => $transactionData->total_talabat ?? 0,
                'total_loan' => $transactionData->total_loan ?? 0,
            ];
        } catch (\Exception $e) {
            Log::error('getTransactionSummary error: ' . $e->getMessage());
            return (object) [
                'total_expense' => 0,
                'total_income' => 0,
                'total_salary' => 0,
                'total_bought' => 0,
                'total_sold' => 0,
                'total_cache_in' => 0,
                'total_cache_out' => 0,
                'total_talabat' => 0,
                'total_loan' => 0,
            ];
        }
    }
}
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
        
        // Get all data
        $transactionSummary = $this->getTransactionSummary();
        // $warehouseValue = $this->getWarehouseValue();
        $salesProfit = $this->getSalesProfit();
        // $talabat = $this->getTalabat();
        
        $participant_account_type_id = 5;
        $base_currency = 1;
        // $participant_accounts = $this->getSellersAndCustomersReport($base_currency, $participant_account_type_id, 0);

        return view('report.profitAndLoss.list', compact(
            'transactionSummary',
            'currencies',
            'salesProfit',
            'orgbios',
        ));

        // return ['transactionSummary' => $transactionSummary,
        //     'currencies' => $currencies,
        //     'warehouseValue' => $warehouseValue,
        //     'salesProfit' => $salesProfit,
        //     'orgbios' =>$orgbios,
        //     'talabat' => $talabat,
        //     'participant_accounts' => $participant_accounts
        // ];
    }

    /**
     * Get Sales Profit (Single Currency)
     */
    private function getSalesProfit()
    {
        $profitData = DB::table('sales_details')
            ->join('warehouse_sales', 'warehouse_sales.id', '=', 'sales_details.warehouse_sales_id')
            ->selectRaw('SUM(sales_details.profit) as total_profit')
            ->first();

        return (object) [
            'total_profit' => $profitData->total_profit ?? 0,
        ];
    }

    /**
     * Get Warehouse Value (Single Currency)
     */
    private function getWarehouseValue()
    {
        $warehouseData = DB::table('warehouse_items')
            ->selectRaw('SUM(available_total) as total_value')
            ->first();

        $totalValue = ($warehouseData->total_value ?? 0);

        return (object) [
            'total_warehouse_value' => $totalValue,
        ];
    }

    /**
     * Get Talabat (Receivables) and Loans (Single Currency)
     */
    private function getTalabat()
    {
        $talabatData = DB::table('journals')
            ->selectRaw("
                SUM(CASE WHEN journals.transaction_type = 1 AND payment_type = 1 THEN amount ELSE 0 END) as cache_recieved,
                SUM(CASE WHEN journals.transaction_type = 2 AND payment_type = 1 THEN amount ELSE 0 END) as cache_paid,
                SUM(CASE WHEN journals.transaction_type = 2 AND payment_type = 2 THEN amount ELSE 0 END) as loan_paid,
                SUM(CASE WHEN journals.transaction_type = 1 AND payment_type = 2 THEN amount ELSE 0 END) as loan_recieved
            ")
            ->join('accounts', 'accounts.id', '=', 'journals.account_id')
            ->whereIn('accounts.account_type_id', [3, 4])
            ->where('journals.is_cleared', 0)
            ->first();

        return (object) [
            'total_talabat' => ($talabatData->cache_recieved ?? 0) + ($talabatData->loan_recieved ?? 0),
            'total_loan' => ($talabatData->cache_paid ?? 0) + ($talabatData->loan_paid ?? 0),
        ];
    }

    /**
     * Get Transaction Summary (Single Currency)
     */
    private function getTransactionSummary()
    {
        $company_account_type_id = 1; // Company treasury
        $banks_account_type_id = 6;   // Banks and exchanges

        $transactionData = DB::table('journals')
            ->selectRaw("
                SUM(CASE WHEN journals.status = 4 THEN amount ELSE 0 END) as total_expense,
                SUM(CASE WHEN journals.status = 3 THEN amount ELSE 0 END) as total_income,
                SUM(CASE WHEN journals.status = 5 THEN amount ELSE 0 END) as total_salary,
                SUM(CASE WHEN journals.status = 7 THEN amount ELSE 0 END) as total_bought,
                SUM(CASE WHEN journals.status = 8 THEN amount ELSE 0 END) as total_sold,
                SUM(CASE WHEN journals.transaction_type = 1 AND payment_type = 1 THEN amount ELSE 0 END) as total_cache_in,
                SUM(CASE WHEN journals.transaction_type = 2 AND payment_type = 1 THEN amount ELSE 0 END) as total_cache_out,
                SUM(CASE WHEN journals.transaction_type = 2 AND payment_type = 2 THEN amount ELSE 0 END) as total_talabat,
                SUM(CASE WHEN journals.transaction_type = 1 AND payment_type = 2 THEN amount ELSE 0 END) as total_loan
            ")
            ->whereIn('journals.account_type_id', [$company_account_type_id, $banks_account_type_id])
            ->where('journals.is_cleared', 0)
            ->first();

        return (object) [
            'total_expense' => $transactionData->total_expense ?? 0,
            'total_income' => $transactionData->total_income ?? 0,
            'total_salary' => $transactionData->total_salary ?? 0,
            'total_bought' => $transactionData->total_bought ?? 0,
            'total_sold' => $transactionData->total_sold ?? 0,
            'total_cache_in' => $transactionData->total_cache_in ?? 0,
            'total_cache_out' => $transactionData->total_cache_out ?? 0,
            'total_talabat' => $transactionData->total_talabat ?? 0,
            'total_loan' => $transactionData->total_loan ?? 0,
        ];
    }

    /**
     * Get Income Section Data (Single Currency)
     */
    private function getIncomeSection()
    {
        $company_account_type_id = 1; // Company treasury
        $banks_account_type_id = 6;   // Banks and exchanges

        // Total Goods in Warehouse
        $total_warehouse_value = DB::table('warehouse_items')
            ->selectRaw('
                SUM(available_total) as total_value,
                SUM(wastage_total) as total_wastage
            ')
            ->where('is_cleared', 0)
            ->first();

        $totalWarehouseValue = ($total_warehouse_value->total_value ?? 0) - ($total_warehouse_value->total_wastage ?? 0);

        // Transaction Summary
        $result = DB::table('journals')
            ->selectRaw("
                SUM(CASE WHEN journals.status = 4 THEN amount ELSE 0 END) as total_expense,
                SUM(CASE WHEN journals.status = 3 THEN amount ELSE 0 END) as total_income,
                SUM(CASE WHEN journals.status = 5 THEN amount ELSE 0 END) as total_salary,
                SUM(CASE WHEN journals.status = 7 THEN amount ELSE 0 END) as total_bought,
                SUM(CASE WHEN journals.status = 8 THEN amount ELSE 0 END) as total_sold,
                SUM(CASE WHEN journals.transaction_type = 1 AND payment_type = 1 THEN amount ELSE 0 END) as total_cache_in,
                SUM(CASE WHEN journals.transaction_type = 2 AND payment_type = 1 THEN amount ELSE 0 END) as total_cache_out,
                SUM(CASE WHEN journals.transaction_type = 2 AND payment_type = 2 THEN amount ELSE 0 END) as total_talabat,
                SUM(CASE WHEN journals.transaction_type = 1 AND payment_type = 2 THEN amount ELSE 0 END) as total_loan
            ")
            ->join('accounts', 'accounts.id', '=', 'journals.account_id')
            ->whereIn('accounts.account_type_id', [$company_account_type_id, $banks_account_type_id])
            ->where('journals.is_cleared', 0)
            ->first();

        // Sold Profits
        $sold_profits = SalesDetails::selectRaw('SUM(profit) as total_profit')
            ->join('warehouse_sales', 'warehouse_sales.id', '=', 'sales_details.warehouse_sales_id')
            ->where('warehouse_sales.is_cleared', 0)
            ->first();

        return (object) [
            'transaction_summary' => $result,
            'total_warehouse_value' => $totalWarehouseValue,
            'sold_profits' => $sold_profits->total_profit ?? 0,
        ];
    }

    /**
     * Get Customer Accounts Report (Single Currency)
     */
    private function getSellersAndCustomersReport($currencyId = null, $account_type_id, $banks_account_type_id)
    {
        $currency_id = $currencyId ?? 1;
        
        $accounts = DB::table('accounts')
            ->leftJoin('journals', function ($join) use ($currency_id) { 
                $join->on('accounts.id', '=', 'journals.account_id')
                    ->where('journals.currency_id', $currency_id);
            })
            ->where('accounts.account_type_id', $account_type_id)
            ->when($banks_account_type_id > 0, function ($query) use ($banks_account_type_id) {
                return $query->orWhere('accounts.account_type_id', $banks_account_type_id);
            })
            ->select([
                'accounts.id as accountId',
                'accounts.name',
                'accounts.percent',
                DB::raw("SUM(CASE 
                            WHEN journals.transaction_type = 1 
                            AND journals.payment_type = 1 
                            AND journals.is_cleared = 0 
                            THEN journals.amount ELSE 0 END) as cache_recieved"),
                DB::raw("SUM(CASE 
                            WHEN journals.transaction_type = 2 
                            AND journals.payment_type = 1 
                            AND journals.is_cleared = 0 
                            THEN journals.amount ELSE 0 END) as cache_paid"),
                DB::raw("SUM(CASE 
                            WHEN journals.transaction_type = 1 
                            AND journals.payment_type = 2 
                            AND journals.is_cleared = 0 
                            THEN journals.amount ELSE 0 END) as loan_recieved"),
                DB::raw("SUM(CASE 
                            WHEN journals.transaction_type = 2 
                            AND journals.payment_type = 2 
                            AND journals.is_cleared = 0 
                            THEN journals.amount ELSE 0 END) as loan_paid"),
            ])
            ->groupBy('accounts.id', 'accounts.name', 'accounts.percent')
            ->get();
    
        return $accounts;
    }
}
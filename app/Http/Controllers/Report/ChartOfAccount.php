<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Setting\Currency;
use App\Models\Setting\OrgBio;

class ChartOfAccount extends Controller
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
    /**
     * Display a listing of the resource.
     */
    public function index(?string $id=null)
    {
        $khazana_account_type_id = 1;
        $employee_account_type_id = 2;
        $customer_account_type_id = 3;
        $supplier_account_type_id = 4;
        $participants_account_type_id = 5;
        $banks_account_type_id = 6;

        $branch_id = $this->branch_id ?? 0;

        $orgbios = OrgBio::all();
        $currencies = Currency::select('id','name')->get();
        // حسابات نقده شرکت
        $company_accounts = $this->getCompanyAccountsReport($id, $khazana_account_type_id, $branch_id, $banks_account_type_id);
        // حسابات فروشنده گان
        $supplier_accounts = $this->companyAccounts($id, $supplier_account_type_id, $branch_id, 0);
        // حسابات مشتریان
        $customer_accounts = $this->companyAccounts($id, $customer_account_type_id, $branch_id, 0);

        // return ['company_accounts' => $company_accounts,'currencies' => $currencies];

        return view('report.chart_of_account.home', [
            'currency_id' => $id ?? 1,
            'currencies' => $currencies,
            'company_accounts' => $company_accounts,
            'supplier_accounts' => $supplier_accounts,
            'customer_accounts' => $customer_accounts,
            'orgbios' => $orgbios,
        ]);

    }


    private function getCompanyAccountsReport($currencyId = null, $account_type_id, $branch_id, $banks_account_type_id)
    {
        $currency_id = $currencyId ?? 1;
        
        $accounts = DB::table('accounts')
            ->join('journals', function ($join) use ($currency_id,$branch_id) { 
                $join->on('accounts.id', '=', 'journals.account_id')
                    ->where('journals.currency_id', $currency_id)
                    ->where('journals.branch_id', $branch_id);
            })
            ->where('accounts.branch_id', $branch_id)
            ->whereIn('accounts.account_type_id', [1,6])
            ->select([
                'accounts.id as accountId',
                'accounts.name',
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
            ->groupBy('accounts.id', 'accounts.name')
            ->get();
    
        return $accounts;
    }

    // get customer accounts report
    private function companyAccounts($currencyId = null, $account_type_id, $branch_id, $banks_account_type_id)
    {
        $currency_id = $currencyId ?? 1;
        
        $accounts = DB::table('accounts')
            ->leftJoin('journals', function ($join) use ($currency_id,$branch_id) { 
                $join->on('accounts.id', '=', 'journals.account_id')
                    ->where('journals.currency_id', $currency_id)
                    ->where('journals.branch_id', $branch_id);
            })
            ->where('accounts.branch_id', $branch_id)
            ->where('accounts.account_type_id', $account_type_id)
            ->when($banks_account_type_id > 0, function ($query) {
                return $query->orWhere('accounts.account_type_id', 6);
            })
            ->select([
                'accounts.id as accountId',
                'accounts.name',
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
            ->groupBy('accounts.id', 'accounts.name')
            ->get();
    
        return $accounts;
    }
    
    
}

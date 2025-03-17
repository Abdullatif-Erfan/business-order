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
        $supplier_accounts = $this->getSellersAndCustomersReport($id, $supplier_account_type_id, $branch_id, 0);
        // حسابات مشتریان
        $customer_accounts = $this->getSellersAndCustomersReport($id, $customer_account_type_id, $branch_id, 0);

        // return ['company_accounts' => $company_accounts,'currencies' => $currencies];
        // return ['company_accounts' => $company_accounts,'supplier_accounts' => $supplier_accounts];

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
        
        // $CacheReport = DB::table('accounts')
        //     ->join('journals', function ($join) use ($currency_id, $branch_id) { 
        //         $join->on('accounts.id', '=', 'journals.account_id')
        //             ->where('journals.currency_id', $currency_id)
        //             ->where('journals.branch_id', $branch_id);
        //     })
        //     ->where('accounts.branch_id', $branch_id)
        //     ->whereIn('accounts.account_type_id', [1,6])
        //     ->select([
        //         'accounts.id as accountId',
        //         'accounts.name',
        //         DB::raw("SUM(CASE 
        //                     WHEN journals.transaction_type = 1 
        //                     AND journals.payment_type = 1 
        //                     AND journals.is_cleared = 0 
        //                     THEN journals.amount ELSE 0 END) as cache_recieved"),
        //         DB::raw("SUM(CASE 
        //                     WHEN journals.transaction_type = 2 
        //                     AND journals.payment_type = 1 
        //                     AND journals.is_cleared = 0 
        //                     THEN journals.amount ELSE 0 END) as cache_paid"),
        //         DB::raw("(SELECT SUM(j2.amount) 
        //                 FROM journals AS j2 
        //                 INNER JOIN accounts AS a2 ON a2.id = j2.account_id
        //                 WHERE j2.transaction_type = 1 
        //                 AND j2.payment_type = 2 
        //                 AND j2.is_cleared = 0 
        //                 AND (a2.account_type_id = 3 OR a2.account_type_id = 4)
        //                 ) as loan_recieved"),

        //         DB::raw("(SELECT SUM(j3.amount) 
        //                 FROM journals AS j3 
        //                 INNER JOIN accounts AS a3 ON a3.id = j3.account_id
        //                 WHERE j3.transaction_type = 2 
        //                 AND j3.payment_type = 2 
        //                 AND j3.is_cleared = 0 
        //                 AND (a3.account_type_id = 3 OR a3.account_type_id = 4)
        //                 ) as loan_paid"),
        //     ])
        //     ->groupBy('accounts.id', 'accounts.name')
        //     ->get();

        $CacheReport = DB::table('accounts')
        ->join('journals', function ($join) use ($currency_id, $branch_id) { 
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
            
            // Show loan_received only for the first record
            DB::raw("(SELECT SUM(j2.amount) 
                    FROM journals AS j2 
                    INNER JOIN accounts AS a2 ON a2.id = j2.account_id
                    WHERE j2.transaction_type = 1 
                    AND j2.payment_type = 2 
                    AND j2.is_cleared = 0 
                    AND (a2.account_type_id = 3 OR a2.account_type_id = 4)
                    LIMIT 1
                    ) * (CASE WHEN accounts.id = (SELECT MIN(id) FROM accounts WHERE branch_id = $branch_id) THEN 1 ELSE NULL END) 
                    as loan_paid"),

            // Show loan_paid only for the first record
            DB::raw("(SELECT SUM(j3.amount) 
                    FROM journals AS j3 
                    INNER JOIN accounts AS a3 ON a3.id = j3.account_id
                    WHERE j3.transaction_type = 2 
                    AND j3.payment_type = 2 
                    AND j3.is_cleared = 0 
                    AND (a3.account_type_id = 3 OR a3.account_type_id = 4)
                    LIMIT 1
                    ) * (CASE WHEN accounts.id = (SELECT MIN(id) FROM accounts WHERE branch_id = $branch_id) THEN 1 ELSE NULL END) 
                    as  loan_recieved")
        ])
        ->groupBy('accounts.id', 'accounts.name')
        ->get();

        return $CacheReport;
    }

    // get customer accounts report
    private function getSellersAndCustomersReport($currencyId = null, $account_type_id, $branch_id, $banks_account_type_id)
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

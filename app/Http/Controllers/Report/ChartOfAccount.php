<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Setting\Currency;

class ChartOfAccount extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(?string $id=null)
    {
        $company_account_type_id = 1;
        $employee_account_type_id = 2;
        $customer_account_type_id = 3;
        $supplier_account_type_id = 4;
        $participants_account_type_id = 5;
        
        $currencies = Currency::select('id','name')->get();
        // حسابات نقده شرکت
        $company_accounts = $this->companyAccounts($id, $company_account_type_id);
        // حسابات فروشنده گان
        $supplier_accounts = $this->companyAccounts($id, $supplier_account_type_id);
        // حسابات مشتریان
        $customer_accounts = $this->companyAccounts($id, $customer_account_type_id);

        // return ['company_accounts' => $company_accounts,'currencies' => $currencies];

        return view('report.chart_of_account.home', [
            'currency_id' => $id ?? 1,
            'currencies' => $currencies,
            'company_accounts' => $company_accounts,
            'supplier_accounts' => $supplier_accounts,
            'customer_accounts' => $customer_accounts,
        ]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


    // get customer accounts report
    private function companyAccounts($currencyId=null,$account_type_id)
    {
        $currency_id = $currencyId ?? 1;
        $accounts = DB::table('accounts')
            ->leftJoin('journals', function ($join) use ($currency_id) { 
                $join->on('accounts.id', '=', 'journals.account_id')
                    ->where('journals.currency_id', $currency_id);
            })
            ->where('accounts.account_type_id', $account_type_id)
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

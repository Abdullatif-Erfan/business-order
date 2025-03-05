<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting\Account;
use App\Models\Setting\AccountType;
use App\Models\Setting\Currency;
use App\Models\Transaction\Journal;
use App\Models\Setting\OrgBio;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class BalanceSheetController extends Controller
{
    

      /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $accounts = Account::all();
        $currencies = Currency::all();
        $orgbios = OrgBio::all();
        $accountTypes = AccountType::all();
        // $sums = $this->showFooterReport(1,33);
        // return response()->json(['sums' =>  $sums]);

        // $journals = Journal::with(['accountRelation:id,name', 'currencyRelation:id,name,symbols,color','userRelation:id,full_name'])
        //     ->select('id', 'code', 'bill_no', 'amount', 'account_id', 'transaction_type', 
        //              'payment_type', 'options', 'option_label', 'currency_id', 'details', 
        //              'inserted_short_date', 'status', 'times', 'is_single_record')
        //     ->orderBy('id', 'DESC')->get();

        // return response()->json(['journals' =>  optional($journals->userRelation->full_name]));
        
        return view('report.balancesheet.list',compact('accounts','currencies','orgbios','accountTypes'));
    }

    
    /**
     * Show the journal data
     */  

    public function getData(Request $request)
    {
        // Retrieve request values
        $currency_id = $request->currency_id ?? 0;
        $account_type_id = $request->account_type_id ?? 0;
        $balance = 0 ;

        // Fetch currency details
        $currency = Currency::find($currency_id);
        $currency_symbol = $currency ? $currency->symbols : '';
        $currency_color = $currency ? $currency->color : '#000';

          // Check if account_id and currency_id are provided
          if (!$request->has('account_type_id') && !$request->has('currency_id')) {
            return response()->json([
                'data' => [],
            ]);
        }

    
        // Fetch account details for today only
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
            ->groupBy('accounts.id', 'accounts.name');

            // \Log::info($accounts->toSql());
            // \Log::info($accounts->getBindings());

        if ($request->account_id) {
            $accounts->where('account_id', $request->account_id);
        }
        
        if ($request->start_date && $request->end_date) {
            $accounts->whereBetween('inserted_short_date', [$request->start_date, $request->end_date]);
        } elseif ($request->start_date) {
            $accounts->whereDate('inserted_short_date', '=', $request->start_date);
        } elseif ($request->end_date) {
            $accounts->whereDate('inserted_short_date', '>=', $request->end_date);
        }
      
        return DataTables::of($accounts)
            ->addIndexColumn()
            ->addColumn('name', function ($account) {
                return $account->name ?: '';
            })
            ->addColumn('cache_recieved', function ($account) {
                return $account->cache_recieved ? number_format($account->cache_recieved) : null;
            })
            ->addColumn('cache_paid', function ($account) {
                return $account->cache_paid ? number_format($account->cache_paid) : null;
            })
            ->addColumn('loan_recieved', function ($account) {
                return $account->loan_recieved ? number_format($account->loan_recieved) : null;
            })
            ->addColumn('loan_paid', function ($account) {
                return $account->loan_paid ? number_format($account->loan_paid) : null;
            })
            ->addColumn('balance', function ($account) use($account_type_id) {
                
                $balance = $account_type_id == 1  
                    ? ($account->cache_recieved + $account->loan_paid) - ($account->cache_paid + $account->loan_recieved)
                    : ($account->cache_paid + $account->loan_paid) - ($account->cache_recieved + $account->loan_recieved);

                $account->computed_balance = $balance; // Store it in the object
                return number_format($balance);
            })
            ->addColumn('currency', function ($account) use ($currency_symbol, $currency_color) {
                return '<i style="font-size:14px;color:'.$currency_color.'">'.$currency_symbol.'</i>';
            })
            ->addColumn('result_label', function ($account) {
                // Reuse the precomputed balance
                return $account->computed_balance == 0 ? 'تصفیه' : ($account->computed_balance > 0 ? 'طلب' : 'باقی');
            })
            ->rawColumns(['currency'])
            ->make(true);
    }

    
}

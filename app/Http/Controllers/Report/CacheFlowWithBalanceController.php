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
    protected  $isAdmin;
    public function __construct()
    {
        if (auth()->check()) {
            $this->isAdmin = session('isAdmin', auth()->user()->isAdmin == 1);
        } else {
            $this->isAdmin = false;
        }
    }
      /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // نمایش لیست مشتریان و خزانه ها و فروشنده گان
        $accounts = Account::whereIn('account_type_id',[1,3,4,6])->get();
        $currencies = Currency::all();
        $orgbios = OrgBio::all();
        // $sums = $this->showFooterReport(1,33);
        // return response()->json(['sums' =>  $sums]);

        // $journals = Journal::with(['accountRelation:id,name', 'currencyRelation:id,name,symbols,color','userRelation:id,full_name'])
        //     ->select('id', 'code', 'bill_no', 'amount', 'account_id', 'transaction_type', 
        //              'payment_type', 'options', 'option_label', 'currency_id', 'details', 
        //              'idate', 'status', 'times', 'is_single_record')
        //     ->orderBy('id', 'DESC')->get();

        // return response()->json(['journals' =>  optional($journals->userRelation->full_name]));
        
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
         * status: 1: old journal, 2: journal, 3:income, 4:expense, 5:salary, 6:participants, 7:buy, 8:sales, 9:other
         */
        
         /*
         * Recieved Loan = قرض  = t1p2
         * Paid Loan = طلب = t2p2
         * Cache Recieved = دریافت نقد | آمد نقد = t1p1
         * Cache Paid = پرداخت نقد | رفت نقد= t2p1
         * 
         */

        $total_talabat = 0;
        $total_loans = 0;
        $currency_id = $request->currency_id ?? 0;
        $account_id = $request->account_id ?? 0;
    
        // Check if account_id and currency_id are provided
        if (!$request->has('account_id') || !$request->has('currency_id')) {
            return response()->json([
                'data' => [],
                'sumCacheRecieved' => 0,
                'sumCachePaid' => 0,
                'sumLoanRecieved' => 0,
                'sumLoanPaid' => 0,
                'isCompanyAccount' => false,
            ]);
        }
    
        $journals = Journal::with(['accountRelation:id,name', 'currencyRelation:id,name,symbols,color'])
            ->select('id', 'code', 'bill_no', 'amount', 'account_id', 'transaction_type', 
                     'payment_type', 'options', 'option_label', 'currency_id', 'details', 
                     'idate', 'status', 'times', 'is_single_record','user_name')
            ->where('account_id', $request->account_id)
            ->where('currency_id', $request->currency_id) 
            ->orderBy('id', 'ASC');
    
        $companyAccount = Account::where('id', $request->account_id)
            ->select('account_type_id', 'is_pre_select')
            ->first();
    
        $isCompanyAccount = $companyAccount && in_array($companyAccount->account_type_id, [1, 6]);
        $isKhazana = $companyAccount && $companyAccount->account_type_id == 1 && $companyAccount->is_pre_select == 1;
        
        // Apply optional filters
        if ($request->start_date && $request->end_date) {
            $journals->whereBetween('idate', [$request->start_date, $request->end_date]);
        } elseif ($request->start_date) {
            $journals->whereDate('idate', '=', $request->start_date);
        } elseif ($request->end_date) {
            $journals->whereDate('idate', '>=', $request->end_date);
        }
        if ($request->code_number) {
            $journals->where('code', 'LIKE', "%{$request->code_number}%");
        }
        if ($request->bill_number) {
            $journals->where('bill_no', 'LIKE', "%{$request->bill_number}%");
        }
    
        // Get the journals collection first
        $journalsCollection = $journals->get();
    
      
        // Calculate running balance
        $runningBalance = 0;
        
        // Process each journal to add balance
        $journalsWithBalance = $journalsCollection->map(function ($journal) use ($isCompanyAccount, &$runningBalance) {
            $cacheRecieved = 0;
            $cachePaid = 0;
            $loanRecieved = ($journal->transaction_type == 1 && $journal->payment_type == 2) ? $journal->amount : 0;
            $loanPaid = ($journal->transaction_type == 2 && $journal->payment_type == 2) ? $journal->amount : 0;
            
            if ($isCompanyAccount) {
                // For company accounts
                $cacheRecieved = ($journal->transaction_type == 1 && $journal->payment_type == 1) ? $journal->amount : 0;
                $cachePaid = ($journal->transaction_type == 2 && $journal->payment_type == 1) ? $journal->amount : 0;
                // $balanceChange = ($cacheRecieved + $loanPaid) - ($cachePaid + $loanRecieved);
            } else {
                // For customer/other accounts
                $cacheRecieved = ($journal->transaction_type == 2 && $journal->payment_type == 1) ? $journal->amount : 0;
                $cachePaid = ($journal->transaction_type == 1 && $journal->payment_type == 1) ? $journal->amount : 0;
                // $balanceChange = ($cacheRecieved + $loanPaid) - ($cachePaid + $loanRecieved);
            }
            
            $balanceChange = ($cacheRecieved + $loanPaid) - ($cachePaid + $loanRecieved);
            // Update running balance
            $runningBalance += $balanceChange;
            
            // Store the balance with the journal
            $journal->calculated_balance = number_format($runningBalance, 2);
            
            return $journal;
        });
    
        return DataTables::of($journalsWithBalance)
            ->addIndexColumn()
            ->addColumn('accountRelation', function ($journal) {
                return $journal->accountRelation ? $journal->accountRelation->name : '';
            })
            // آمد نقد
            ->addColumn('cacheRecieved', function ($journal) use ($isCompanyAccount) {
                return ($journal->transaction_type == 1 && $journal->payment_type == 1) ? number_format($journal->amount,2) : null;
            })
            // رفت نقد
            ->addColumn('cachePaid', function ($journal) use ($isCompanyAccount) {
                return ($journal->transaction_type == 2 && $journal->payment_type == 1) ? number_format($journal->amount,2) : null;
            })
            // قرض
            ->addColumn('loanRecieved', function ($journal) {
                return ($journal->transaction_type == 1 && $journal->payment_type == 2) ? number_format($journal->amount,2) : null;
            })
            // طلب
            ->addColumn('loanPaid', function ($journal) {
                return ($journal->transaction_type == 2 && $journal->payment_type == 2) ? number_format($journal->amount,2) : null;
            })
            ->addColumn('currency', function ($journal) {
                return '<i style="font-size:14px;color:'.$journal->currencyRelation->color.'">'.$journal->currencyRelation->symbols.'</i>';
            })
            ->addColumn('full_name', function ($journal) {
                return $journal->user_name ? $journal->user_name : '';
            })
            ->addColumn('belance', function ($journal) {
                return $journal->calculated_balance;
            })
            ->rawColumns(['currency'])
            ->with([
                'isCompanyAccount' => $isCompanyAccount
            ])
            ->setRowClass(function ($journal) {
                return $journal->status == 11 ? 'clearance-row bg-green' : '';
            })
            ->make(true);
    }

    // public function getData(Request $request)
    // {
    //     /**
    //      * status: 1: old journal, 2: journal, 3:income, 4:expense, 5:salary, 6:participants, 7:buy, 8:sales, 9:other
    //      */
    
    //      $branch_id = $this->branch_id ?? 0 ;
    //      $total_talabat = 0;
    //     $total_loans = 0;
    //     $currency_id = $request->currency_id ?? 0;
    //     $account_id = $request->account_id ?? 0;
    //     $belance = 200;

    //     // Check if account_id and currency_id are provided
    //     if (!$request->has('account_id') || !$request->has('currency_id')) {
    //         return response()->json([
    //             'data' => [],
    //             'sumCacheRecieved' => 0,
    //             'sumCachePaid' => 0,
    //             'sumLoanRecieved' => 0,
    //             'sumLoanPaid' => 0,
    //             'isCompanyAccount' => false,
    //         ]);
    //     }
    
    //     $journals = Journal::with(['accountRelation:id,name', 'currencyRelation:id,name,symbols,color'])
    //         ->select('id', 'code', 'bill_no', 'amount', 'account_id', 'transaction_type', 
    //                  'payment_type', 'options', 'option_label', 'currency_id', 'details', 
    //                  'idate', 'status', 'times', 'is_single_record','user_name')
    //         ->where('account_id', $request->account_id)  // Enforce account_id filter
    //         ->where('currency_id', $request->currency_id) 
    //         ->where('branch_id', $this->branch_id) 
    //         ->orderBy('id', 'ASC');

    //     // check if searched_account_id is belongs to company accounts
    //     //  $isCompanyAccount = Account::whereIn('account_type_id', [1,6])->where('id', $request->account_id)
    //     //  ->where('branch_id', $this->branch_id)->exists();
        
    //     // $isKhazana = Account::where('account_type_id',1)
    //     //  ->where('id', $request->account_id)
    //     //  ->where('is_pre_select',1)
    //     //  ->where('branch_id', $this->branch_id)
    //     //  ->exists();

    //     $companyAccount = Account::where('id', $request->account_id)
    //         ->where('branch_id', $this->branch_id)
    //         ->select('account_type_id', 'is_pre_select')
    //         ->first();

    //     $isCompanyAccount = $companyAccount && in_array($companyAccount->account_type_id, [1, 6]);
    //     $isKhazana = $companyAccount && $companyAccount->account_type_id == 1 && $companyAccount->is_pre_select == 1;
        
    
    //     // Apply optional filters
    //     if ($request->start_date && $request->end_date) {
    //         $journals->whereBetween('idate', [$request->start_date, $request->end_date]);
    //     } elseif ($request->start_date) {
    //         $journals->whereDate('idate', '=', $request->start_date);
    //     } elseif ($request->end_date) {
    //         $journals->whereDate('idate', '>=', $request->end_date);
    //     }
    //     if ($request->code_number) {
    //         $journals->where('code', 'LIKE', "%{$request->code_number}%");
    //     }
    //     if ($request->bill_number) {
    //         $journals->where('bill_no', 'LIKE', "%{$request->bill_number}%");
    //     }
    
    //     if($isCompanyAccount)
    //     {
    //         $sumsKhazana = DB::table('journals')
    //         ->where('account_id', $request->account_id)
    //         ->where('currency_id', $request->currency_id)
    //         ->where('branch_id', $this->branch_id)
    //         ->where('is_cleared', 0)
    //         ->select(
    //             DB::raw('SUM(CASE WHEN transaction_type = 1 AND payment_type = 1 THEN amount ELSE 0 END) as sumCacheRecieved'),
    //             DB::raw('SUM(CASE WHEN transaction_type = 2 AND payment_type = 1 THEN amount ELSE 0 END) as sumCachePaid'),
    //         )
    //         ->first();

    //         $loanAndTalab = DB::table('accounts')
    //         ->join('journals', function ($join) use ($currency_id, $branch_id) {
    //             $join->on('accounts.id', '=', 'journals.account_id')
    //                 ->where('journals.currency_id', $currency_id)  
    //                 ->where('journals.branch_id', $branch_id);  
    //         })
    //         ->whereIn('accounts.account_type_id', [3,4])
    //         ->select([
    //             DB::raw("SUM(CASE 
    //                         WHEN journals.transaction_type = 1 
    //                         AND journals.payment_type = 1 
    //                         AND journals.is_cleared = 0 
    //                         THEN journals.amount ELSE 0 END) as cache_recieved"),
    //             DB::raw("SUM(CASE 
    //                         WHEN journals.transaction_type = 2 
    //                         AND journals.payment_type = 1 
    //                         AND journals.is_cleared = 0 
    //                         THEN journals.amount ELSE 0 END) as cache_paid"),
    //             DB::raw("SUM(CASE 
    //                         WHEN journals.transaction_type = 1 
    //                         AND journals.payment_type = 2 
    //                         AND journals.is_cleared = 0 
    //                         THEN journals.amount ELSE 0 END) as loan_recieved"),
    //             DB::raw("SUM(CASE 
    //                         WHEN journals.transaction_type = 2 
    //                         AND journals.payment_type = 2 
    //                         AND journals.is_cleared = 0 
    //                         THEN journals.amount ELSE 0 END) as loan_paid")
    //         ])
    //         ->first(); // Get a single row instead of a collection

    //         $total_loans = ($loanAndTalab) ? ($loanAndTalab->cache_paid + $loanAndTalab->loan_paid) : 0;
    //         $total_talabat = ($loanAndTalab) ? ($loanAndTalab->cache_recieved + $loanAndTalab->loan_recieved) : 0;
    //     }
    //     else
    //     {
    //         $else_account = DB::table('journals')
    //         ->where('account_id', $request->account_id)
    //         ->where('currency_id', $request->currency_id)
    //         ->where('branch_id', $this->branch_id)
    //         ->where('is_cleared', 0)
    //         ->select(
    //             DB::raw('SUM(CASE WHEN transaction_type = 1 AND payment_type = 1 THEN amount ELSE 0 END) as sumCachePaid'),
    //             DB::raw('SUM(CASE WHEN transaction_type = 2 AND payment_type = 1 THEN amount ELSE 0 END) as sumCacheRecieved'),
    //             DB::raw('SUM(CASE WHEN transaction_type = 1 AND payment_type = 2 THEN amount ELSE 0 END) as sumLoanRecieved'),
    //             DB::raw('SUM(CASE WHEN transaction_type = 2 AND payment_type = 2 THEN amount ELSE 0 END) as sumLoanPaid')
    //         )
    //         ->first();
    //     }
    
    //     return DataTables::of($journals)
    //         ->addIndexColumn()
    //         ->addColumn('accountRelation', function ($journal) {
    //             return $journal->accountRelation ? $journal->accountRelation->name : '';
    //         })
          

    //         // آمد نقد
    //         ->addColumn('cacheRecieved', function ($journal) use ($isCompanyAccount) {
    //             if($isCompanyAccount) {
    //                 return ($journal->transaction_type == 1 && $journal->payment_type == 1) ? number_format($journal->amount,2) : null; // آمد نقد حسابات شرکت
    //             } else {
    //                 return ($journal->transaction_type == 2 && $journal->payment_type == 1) ? number_format($journal->amount,2) : null; // رفت نقد حسابات دیگران                   
    //             }
    //         })

    //         // رفت نقد
    //         ->addColumn('cachePaid', function ($journal) use ($isCompanyAccount) {
    //             if($isCompanyAccount) {
    //                 return ($journal->transaction_type == 2 && $journal->payment_type == 1) ? number_format($journal->amount,2) : null;
    //             } else {
    //                 return ($journal->transaction_type == 1 && $journal->payment_type == 1) ? number_format($journal->amount,2) : null;                    
    //             }
    //         })
    //         // قرض
    //         ->addColumn('loanRecieved', function ($journal) {
    //             return ($journal->transaction_type == 1 && $journal->payment_type == 2) ? number_format($journal->amount,2) : null;
    //         })
    //         // طلب
    //         ->addColumn('loanPaid', function ($journal) {
    //             return ($journal->transaction_type == 2 && $journal->payment_type == 2) ? number_format($journal->amount) : null;
    //         })
    //         ->addColumn('currency', function ($journal) {
    //             return '<i style="font-size:14px;color:'.$journal->currencyRelation->color.'">'.$journal->currencyRelation->symbols.'</i>';
    //         })
    //         ->addColumn('full_name', function ($journal) {
    //             return $journal->user_name ? $journal->user_name  : '';
    //         })

    //         // ->addColumn('belance', function ($journal) use ($belance) {
    //         //     return $belance;
    //         // })

    //           // Callculate belance

    //           ->addColumn('belance', function ($journal) use ($isCompanyAccount, $belance) {
    //             $cacheRecieved = 0;
    //             $cachePaid  = 0;
    //             $loanRecieved = ($journal->transaction_type == 1 && $journal->payment_type == 2) ? $journal->amount : 0; 
    //             $loanPaid = ($journal->transaction_type == 2 && $journal->payment_type == 2) ? $journal->amount : 0;
    //              if($this->getRowIndex() === 1) {
    //                 //  if it is belongs to company account
    //                 if($isCompanyAccount) 
    //                 {
    //                     // بیلانس = آمد نقد + طلب  - رفت نقد + قرض
    //                     $cacheRecieved = ($journal->transaction_type == 1 && $journal->payment_type == 1) ? $journal->amount : 0;
    //                     $cachePaid = ($journal->transaction_type == 2 && $journal->payment_type == 1) ? $journal->amount : 0;
    //                     $calculate = (($cacheRecieved + $loanPaid) - ($cachePaid + $loanRecieved));
    //                     $belance = number_format($calculate,2);
    //                 }
    //                 // belongs to customers or other accounts
    //                 else 
    //                 {
    //                     // بیلانس = آمد نقد + طلب  - رفت نقد + قرض
    //                     $cacheRecieved = ($journal->transaction_type == 2 && $journal->payment_type == 1) ? $journal->amount : 0;
    //                     $cachePaid = ($journal->transaction_type == 1 && $journal->payment_type == 1) ? $journal->amount : 0;
    //                     $calculate = (($cacheRecieved + $loanPaid) - ($cachePaid + $loanRecieved));
    //                     $belance = number_format($calculate,2);
    //                 }
                    
    //              } 
    //              else 
    //              {

    //              }
    //              return $belance;
    //         })


    //         ->rawColumns(['currency'])
    //         ->with([
    //             'sumCacheRecieved' => $isCompanyAccount ? number_format($sumsKhazana->sumCacheRecieved,2) : number_format($else_account->sumCacheRecieved,2 ?? 0),
    //             'sumCachePaid' => $isCompanyAccount ? number_format($sumsKhazana->sumCachePaid,2) : number_format($else_account->sumCachePaid,2 ?? 0),
    //             'sumLoanRecieved' => $isCompanyAccount ? number_format($loanAndTalab->cache_paid + $loanAndTalab->loan_paid,2) : number_format($else_account->sumLoanRecieved,2 ?? 0),
    //             'sumLoanPaid' => $isCompanyAccount ? number_format($total_talabat,2) : number_format($else_account->sumLoanPaid,2 ?? 0),
    //             'isKhazana' => $isKhazana ? true : false,
    //             'isCompanyAccount' => $isCompanyAccount
    //         ])
    //         ->setRowClass(function ($journal) {
    //             return $journal->status == 9 ? 'clearance-row bg-green' : ''; // Example: Add class if status is 9
    //         })
    //         ->make(true);
    // }

}

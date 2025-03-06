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

class CacheFlowController extends Controller
{
      /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // نمایش لیست مشتریان و خزانه ها و فروشنده گان
        $accounts = Account::whereIn('account_type_id',[1,3,4])->get();
        $currencies = Currency::all();
        $orgbios = OrgBio::all();
        // $sums = $this->showFooterReport(1,33);
        // return response()->json(['sums' =>  $sums]);

        // $journals = Journal::with(['accountRelation:id,name', 'currencyRelation:id,name,symbols,color','userRelation:id,full_name'])
        //     ->select('id', 'code', 'bill_no', 'amount', 'account_id', 'transaction_type', 
        //              'payment_type', 'options', 'option_label', 'currency_id', 'details', 
        //              'inserted_short_date', 'status', 'times', 'is_single_record')
        //     ->orderBy('id', 'DESC')->get();

        // return response()->json(['journals' =>  optional($journals->userRelation->full_name]));
        
        return view('report.cacheflow.list',compact('accounts','currencies','orgbios'));
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
                     'inserted_short_date', 'status', 'times', 'is_single_record','user')
            ->where('account_id', $request->account_id)  // Enforce account_id filter
            ->where('currency_id', $request->currency_id) // Enforce currency_id filter
            ->orderBy('id', 'DESC');

         // check if searched_account_id is belongs to company accounts
         $isCompanyAccount = Account::where('account_type_id', 1)->where('id', $request->account_id)->exists();
        
    
        // Apply optional filters
        if ($request->start_date && $request->end_date) {
            $journals->whereBetween('inserted_short_date', [$request->start_date, $request->end_date]);
        } elseif ($request->start_date) {
            $journals->whereDate('inserted_short_date', '=', $request->start_date);
        } elseif ($request->end_date) {
            $journals->whereDate('inserted_short_date', '>=', $request->end_date);
        }
        if ($request->code_number) {
            $journals->where('code', 'LIKE', "%{$request->code_number}%");
        }
        if ($request->bill_number) {
            $journals->where('bill_no', 'LIKE', "%{$request->bill_number}%");
        }
    
        $sums = DB::table('journals')
            ->where('account_id', $request->account_id)
            ->where('currency_id', $request->currency_id)
            ->select(
                DB::raw('SUM(CASE WHEN transaction_type = 1 AND payment_type = 1 THEN amount ELSE 0 END) as sumCacheRecieved'),
                DB::raw('SUM(CASE WHEN transaction_type = 2 AND payment_type = 1 THEN amount ELSE 0 END) as sumCachePaid'),
                DB::raw('SUM(CASE WHEN transaction_type = 1 AND payment_type = 2 THEN amount ELSE 0 END) as sumLoanRecieved'),
                DB::raw('SUM(CASE WHEN transaction_type = 2 AND payment_type = 2 THEN amount ELSE 0 END) as sumLoanPaid')
            )
            ->first();
    
        return DataTables::of($journals)
            ->addIndexColumn()
            ->addColumn('accountRelation', function ($journal) {
                return $journal->accountRelation ? $journal->accountRelation->name : '';
            })
            ->addColumn('cacheRecieved', function ($journal) {
                return ($journal->transaction_type == 1 && $journal->payment_type == 1) ? number_format($journal->amount) : null;
            })
            ->addColumn('cachePaid', function ($journal) {
                return ($journal->transaction_type == 2 && $journal->payment_type == 1) ? number_format($journal->amount) : null;
            })
            ->addColumn('loanRecieved', function ($journal) {
                return ($journal->transaction_type == 1 && $journal->payment_type == 2) ? number_format($journal->amount) : null;
            })
            ->addColumn('loanPaid', function ($journal) {
                return ($journal->transaction_type == 2 && $journal->payment_type == 2) ? number_format($journal->amount) : null;
            })
            ->addColumn('currency', function ($journal) {
                return '<i style="font-size:14px;color:'.$journal->currencyRelation->color.'">'.$journal->currencyRelation->symbols.'</i>';
            })
            ->addColumn('full_name', function ($journal) {
                return $journal->user ? $journal->user  : '';
            })
            ->rawColumns(['currency'])
            ->with([
                'sumCacheRecieved' => number_format($sums->sumCacheRecieved ?? 0),
                'sumCachePaid' => number_format($sums->sumCachePaid ?? 0),
                'sumLoanRecieved' => number_format($sums->sumLoanRecieved ?? 0),
                'sumLoanPaid' => number_format($sums->sumLoanPaid ?? 0),
                'isCompanyAccount' => $isCompanyAccount ?? 0 
            ])
            ->make(true);
    }

    
    // public function getData(Request $request)
    // {
    //     /**
    //      * status: 1: old journal, 2: journal, 3:income, 4:expense, 5:salary, 6:participants, 7:buy, 8:sales, 9:other
    //      */

    //     $journals = Journal::with(['accountRelation:id,name', 'currencyRelation:id,name,symbols,color'])
    //     ->select('id', 'code', 'bill_no', 'amount', 'account_id', 'transaction_type', 
    //              'payment_type', 'options', 'option_label', 'currency_id', 'details', 
    //              'inserted_short_date', 'status', 'times', 'is_single_record')
    //     ->orderBy('id', 'DESC');

    //     // Apply filters
    //     if ($request->account_id) {
    //         $journals->where('account_id', $request->account_id);
    //     }
    //     if ($request->currency_id) {
    //         $journals->where('currency_id', $request->currency_id);
    //     }
    //     if ($request->start_date && $request->end_date) {
    //         $journals->whereBetween('inserted_short_date', [$request->start_date, $request->end_date]);
    //     } elseif ($request->start_date) {
    //         $journals->whereDate('inserted_short_date', '=', $request->start_date);
    //     } elseif ($request->end_date) {
    //         $journals->whereDate('inserted_short_date', '>=', $request->end_date);
    //     }
    //     if ($request->code_number) {
    //         $journals->where('code', 'LIKE', "%{$request->code_number}%");
    //     }
    //     if ($request->bill_number) {
    //         $journals->where('bill_no', 'LIKE', "%{$request->bill_number}%");
    //     }

    //     $sumCacheRecieved = 1000;
    //     $sumCachePaid = 2000;
    //     $sumLoanRecieved = 3000;
    //     $sumLoanPaid = 4000;

    //     // \Log::info($journals->toSql());
    //     // \Log::info($journals->getBindings());

    //     return DataTables::of($journals)
            
    //         ->addIndexColumn()
           
    //         ->addColumn('accountRelation', function ($journal) {
    //             return $journal->accountRelation ? $journal->accountRelation->name : '';
    //         })
            
    //         // cacheRecieved = t1p1 = دریافت نقد
    //         ->addColumn('cacheRecieved', function ($journal) {
    //             if (($journal->transaction_type == 1 && $journal->payment_type == 1)) {  return number_format($journal->amount); }
    //         })

    //            // cachePaid  = t2p1 = پرداخت نقد
    //         ->addColumn('cachePaid', function ($journal) {
    //             if (($journal->transaction_type == 2 && $journal->payment_type == 1)) {  return number_format($journal->amount); }
    //         })
            
    //         // loanRecieved = t1p2 = قرضه
    //         ->addColumn('loanRecieved', function ($journal) {
    //            if (($journal->transaction_type == 1 && $journal->payment_type == 2)) {  return number_format($journal->amount); }
    //         })
            

    //        // loanPaid = t2p2 = طلب
    //         ->addColumn('loanPaid', function ($journal) {
    //            if (($journal->transaction_type == 2 && $journal->payment_type == 2)) {  return number_format($journal->amount); }
    //        })
            

    //         ->addColumn('currency', function ($journal) {
    //             return '<i style="font-size:14px;color:'.$journal->currencyRelation->color.'">'.$journal->currencyRelation->symbols.'</i>';
    //         })
    //         ->addColumn('actions', function ($journal) {
    //             return $journal->status == 2 ? '<a href="journal/details/'.$journal->times.'" class="hidden-print"><i class="fas fa-eye viewAccount" data-id="' . $journal->id . '" style="font-size:20px;"></i></a>' : '';
    //         })
    //         ->rawColumns(['actions','currency'])
    //         ->with([
    //                 'sumCacheRecieved' => number_format($sumCacheRecieved),
    //                 'sumCachePaid' => number_format($sumCachePaid),
    //                 'sumLoanRecieved' => number_format($sumLoanRecieved),
    //                 'sumLoanPaid' => number_format($sumLoanPaid)
    //          ])
    //         ->make(true);
    // }

}

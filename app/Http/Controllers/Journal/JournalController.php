<?php

namespace App\Http\Controllers\Journal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting\Account;
use App\Models\Setting\Currency;
use App\Models\Journal\Journal;
use App\Models\Setting\OrgBio;



use Morilog\Jalali\Jalalian;
use Yajra\DataTables\Facades\DataTables;


class JournalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $journals = Journal::with(['accountRelation' => function($query){
        //     $query->select('id','name');
        // },'currencyRelation' => function($query){
        //     $query->select('id','name','symbols','color');
        // }])
        // ->select('id','code','bill_no','amount','account_id','transaction_type','currency_id','details','inserted_short_date','status','times')
        // ->orderBy('id', 'DESC')
        // ->get();

        // return response()->json(['data' => $journals]);


        $accounts = Account::all();
        $currencies = Currency::all();
        $orgbios = OrgBio::all();

        // return response()->json(['data' =>  $orgbios[0]->header]);
        
        return view('journals.list',compact('accounts','currencies','orgbios'));
    }

    /**
     * Show the journal data
     */
    public function getData(Request $request)
    {
        $journals = Journal::with(['accountRelation' => function($query){
            $query->select('id','name');
        },'currencyRelation' => function($query){
            $query->select('id','name','symbols','color');
        }])
        ->select('id','code','bill_no','amount','account_id','transaction_type','currency_id','details','inserted_short_date','status','times')
        ->orderBy('id', 'DESC');


        // Apply filters if provided
        if ($request->account_id) {
            $journals->where('account_id', $request->account_id);
        }
        if ($request->currency_id) {
            $journals->where('currency_id', $request->currency_id);
        }
        if ($request->start_date) {
            $journals->whereDate('inserted_short_date', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $journals->whereDate('inserted_short_date', '<=', $request->end_date);
        }
        if ($request->code_number) {
            $journals->where('code', 'LIKE', "%{$request->code_number}%");
        }
        if ($request->bill_number) {
            $journals->where('bill_no', 'LIKE', "%{$request->bill_number}%");
        }
        

        return DataTables::of($journals)
            
            ->addIndexColumn()
           
            ->addColumn('transaction_type_1', function ($journal) {
                return $journal->transaction_type == 1 ? number_format($journal->amount,2) : '';
            })
            ->addColumn('transaction_type_2', function ($journal) {
                return $journal->transaction_type == 2 ? number_format($journal->amount,2) : '';
            })

            ->addColumn('currency', function ($journal) {
                return '<i style="font-size:14px;color:'.$journal->currencyRelation->color.'">'.$journal->currencyRelation->symbols.'</i>';
            })
            ->addColumn('actions', function ($journal) {
                return '<a href="journal/details/'.$journal->id.'" class="hidden-print"><i class="fas fa-eye viewAccount" data-id="' . $journal->id . '" style="font-size:20px;"></i></a>';
            })
            ->rawColumns(['actions','currency'])
            ->make(true);
    }


    /**
     * Show journal details
     */
    public function details(Request $request, $id)
    {
       
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
}

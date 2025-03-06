<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting\Account;
use App\Models\Setting\Currency;
use App\Models\Transaction\Journal;
use App\Models\Setting\Branch;
use App\Models\Setting\ExpenseType;
use App\Models\Setting\OrgBio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Morilog\Jalali\Jalalian;
use Yajra\DataTables\Facades\DataTables;

class SalaryReportController extends Controller
{
    public function index()
    {
        $currencies = Currency::all();
        $orgbios = OrgBio::all();
        $employees = Account::select('id','name')->where('account_type_id',2)->get();
        $months = array(
            '1' => 'حمل',
            '2' => 'ثور',
            '3' => 'جوزا',
            '4' => 'سرطان',
            '5' => 'اسد',
            '6' => 'سنبله',
            '7' => 'میزان',
            '8' => 'عقرب',
            '9' => 'قوس',
            '10' => 'جدی',
            '11' => 'دلو',
            '12' => 'حوت',
        );
        return view('hr.report.list',compact('employees','currencies','orgbios','months'));
    }

    /**
     * Show the expense data
     */
    public function getData(Request $request)
    {
        /**
         * status: 1: old income, 2: journal, 3:income, 4:expense, 5:salary, 6:participants, 7:buy, 8:sales, 9:other
         */
        $account_id = $request->account_id ?? 0;
        $currency_id = $request->currency_id ?? 0;

         // Check if account_id and currency_id are provided
         if (!$request->has('account_id') && !$request->has('currency_id')) {
            return response()->json([
                'data' => [],
            ]);
        }

        $salary = Journal::with(['accountRelation' => function($query){
            $query->select('id','name');
        },'currencyRelation' => function($query){
            $query->select('id','name','symbols','color');
        }])
        // $salary = Journal::with(['accountRelation','currencyRelation','expenseTypeRelation'])
        ->select('id','code','bill_no','amount','account_id','currency_id','details','year','month','inserted_short_date','status','times','user')
        ->where('journals.status','=',5)
        ->where('journals.currency_id','=',$currency_id)
        ->where('journals.account_id','=',$account_id)
        ->where('journals.dynamic_type','=',1) // show just employee records
        ->orderBy('id', 'DESC');


        // Apply filters if provided

        if ($request->employee_name) {
            $salary->whereHas('accountRelation', function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->employee_name . '%'); // Use LIKE for partial search
            });
        }

        if ($request->year) {
            $salary->where('year', $request->year);
        }
        if ($request->month) {
            $salary->where('month', $request->month);
        }
        if ($request->currency_id) {
            $salary->where('currency_id', $request->currency_id);
        }
        if ($request->code_number) {
            $salary->where('code', 'LIKE', "%{$request->code_number}%");
        }
        

        return DataTables::of($salary)
            
            ->addIndexColumn()
           
            ->addColumn('accountRelation', function ($salary) {
                return $salary->accountRelation ? $salary->accountRelation->name : '';
            })

            // recieved and recieveable is belongs to salary
            ->addColumn('amount', function ($salary) {
                $amount = $salary->amount;
                $formattedAmount = (fmod($amount, 1) == 0) ? number_format($amount, 0) : number_format($amount, 2);
                return $formattedAmount;     
            })
            
            ->addColumn('currency', function ($salary) {
                return '<i style="font-size:14px;color:'.$salary->currencyRelation->color.'">'.$salary->currencyRelation->name.'</i>';
            })



            ->rawColumns(['currency'])
            ->make(true);
    }



}

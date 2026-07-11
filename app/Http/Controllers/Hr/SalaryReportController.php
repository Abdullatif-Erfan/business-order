<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting\Account;
use App\Models\Setting\Currency;
use App\Models\Transaction\Journal;
use App\Models\Setting\ExpenseType;
use App\Models\Setting\OrgBio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class SalaryReportController extends Controller
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

    public function index()
    {
        $currencies = Currency::all();
        $orgbios = OrgBio::all();
        $employees = Account::select('id','name')->where('account_type_id',2)->get();
        $months = $this->getTranslatedMonthName();
        return view('hr.report.list',compact('employees','currencies','orgbios','months'));
    }

    public function getTranslatedMonthName()
    {
        $locale = app()->getLocale();
        $months = array();
        if($locale == "fa")
        {
            $months = array(
                1  => 'جنوری',    // January
                2  => 'فبروری',    // February
                3  => 'مارچ',    // March
                4  => 'اپریل',    // April
                5  => 'می',    // May
                6  => 'جون',   // June
                7  => 'جولای',  // July
                8  => 'اگست',    // August
                9  => 'سپتمبر',  // September
                10 => 'اکتوبر',  // October
                11 => 'نومبر',   // November
                12 => 'دسمبر',    // December
            );

        }
        else if ($locale == "pa") 
        {
            $months = array(
                '1' => 'وری',
                '2' => 'غویی',
                '3' => 'غبرګولی',
                '4' => 'چنګاښ',
                '5' => 'زمری',
                '6' => 'وږی',
                '7' => 'تله',
                '8' => 'لړم',
                '9' => 'ليندۍ',
                '10' => 'مرغومی',
                '11' => 'سلواغه',
                '12' => 'کب',
            );
        }
        else
        {
            $months = array(
                '1' => 'January',
                '2' => 'February',
                '3' => 'March',
                '4' => 'April',
                '5' => 'May',
                '6' => 'June',
                '7' => 'July',
                '8' => 'August',
                '9' => 'September',
                '10' => 'October',
                '11' => 'November',
                '12' => 'December',
            );
        }
        return $months;
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
        ->select('id','code','bill_no','amount','account_id','currency_id','details','year','month','idate','status','times','user_name')
        ->where('journals.status','=',5)
        ->where('journals.currency_id','=',$currency_id)
        // ->where('journals.account_id','=',$account_id)
        ->when(!empty($account_id), function ($query) use ($account_id) {
                return $query->where('journal.account_id', $account_id);
        })
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
            ->addColumn('month', function ($salary) {
                return $salary->month ? $this->getMonthName($salary->month)  : '';
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

    function getMonthName($month=1)
    {
        $months = array(
                1  => 'جنوری',    // January
                2  => 'فبروری',    // February
                3  => 'مارچ',    // March
                4  => 'اپریل',    // April
                5  => 'می',    // May
                6  => 'جون',   // June
                7  => 'جولای',  // July
                8  => 'اگست',    // August
                9  => 'سپتمبر',  // September
                10 => 'اکتوبر',  // October
                11 => 'نومبر',   // November
                12 => 'دسمبر',    // December
            );
        return $months[$month];
    }



}

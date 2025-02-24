<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
// use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session; // Import Session facade
use Illuminate\Support\Facades\Auth; // Import Auth facade
// use App\Helpers\ManagementHelper;
// use App\Helpers\FunctionHelper;
use Morilog\Jalali\Jalalian;
use Illuminate\Support\Facades\DB;
use App\Models\Warehouse\WarehouseSales;
use App\Models\Buy\BoughtItem;
use App\Models\Setting\Currency;
use App\Models\Setting\Account;
use App\Models\Journal\Journal;
use App\Models\Warehouse\SalesDetails;


// class HomeController extends BaseController
class HomeController extends Controller
{
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data['year'] = $request->input('year') ?? Jalalian::now()->format('Y');
        $data['month'] = $request->input('month') ?? Jalalian::now()->format('n');
        $data['day'] = $request->input('day') ?? Jalalian::now()->format('d');
        $data['currency'] = Currency::select('id','name')->orderBy('id','ASC')->get()->toArray();
        
        if (request()->has('currency_id')) {
            $data['currency_id'] = request()->input('currency_id');
            
            $data['cur_currency'] = Currency::select('id', 'name')
                ->where('id', $data['currency_id'])
                ->orderBy('id', 'ASC')
                ->get()
                ->toArray();
        
            $data['currency_name'] = $data['cur_currency'][0]->name ?? null; // Handle cases where no result is found
        } else {
            $data['currency_id'] = $data['currency'][0]['id'] ?? null;
            $data['currency_name'] = $data['currency'][0]['name'] ?? null;
        }


        /**
         * transaction_type: 1: recieved , 2:paid
         * payment_type    : 1: cache,    2:loan
         */

        // first tab
        $data['todays_sold_income'] = $this->getTodaysSoldIncome($data['year'],$data['month'],$data['day'],$data['currency_id']);
        $data['getTodaysBoughtItems'] = $this->getTodaysBoughtData($data['year'],$data['month'],$data['day'],$data['currency_id']);
        $data['cashIncomeOutcome'] = $this->getCashIncomeOutcome($data['year'],$data['month'],$data['day'],$data['currency_id']);
        
        // Third Tab
        // $data['cache_in_hand'] = $this->getCashInHandAmount($data['currency_id'],$data['year'],$data['month'],$data['day']);
        // return ['data' => $data];


        return view('dashboard.dashboard', compact('data'));
    }


    public function warehouseItemNotifyAmount()
    {
        return 1;
    }

    public function expiredDateNotifyAmount()
    {
        return 1;
    }
    public function warehouseItemList()
    {
        return [];
    }
    public function expiredWarehouseItems()
    {
        return [];
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



    // ----------------- re-usable functions ------------
    function getTodaysSoldIncome($year, $month, $day, $currency_id)
    {
        
        $todays_soled = WarehouseSales::selectRaw('SUM(total_price) as total_price, SUM(total_discount) as total_discount, SUM(payable) as payable, SUM(cur_pay) as cur_pay, SUM(remained) as remained')
        ->where('year','=',$year)
        ->where('month','=',$month)
        ->where('day','=',$day)
        ->where('currency_id','=',$currency_id)
        ->first();

        // $todays_sold_profits = DB::table('sales_details')
        // ->selectRaw('SUM(profit) as profit')
        // ->join('warehouse_sales', 'warehouse_sales.id', '=', 'sales_details.warehouse_sales_id') 
        // ->where('warehouse_sales.currency_id', '=', $currency_id) 
        // ->where('warehouse_sales.year', '=', $year)
        // ->where('warehouse_sales.month', '=', $month)
        // ->where('warehouse_sales.day', '=', $day)
        // ->first();
        
        $todays_sold_profits = SalesDetails::whereHas('warehouseSale', function ($query) use ($currency_id, $year, $month, $day) {
            $query->where('currency_id', $currency_id)
                  ->where('year', $year)
                  ->where('month', $month)
                  ->where('day', $day);
        })
        ->sum('profit');
    
        return [
            'total_price'     => $todays_soled->total_price ?? 0,
            'total_discount'  => $todays_soled->total_discount ?? 0,
            'payable'         => $todays_soled->payable ?? 0,
            'cur_pay'         => $todays_soled->cur_pay ?? 0,
            'remained'        => $todays_soled->remained ?? 0,
            'profit'          => $todays_sold_profits ?? 0
        ];

    }

    function getTodaysBoughtData($year, $month, $day, $currency_id)
    {
        $todays_bought = BoughtItem::selectRaw('SUM(total_price) as total_price, SUM(discount) as discount, SUM(payable) as payable, SUM(cur_pay) as cur_pay, SUM(remained) as remained, SUM(trans_spend) as trans_spend')
        ->where('year','=',$year)
        ->where('month','=',$month)
        ->where('day','=',$day)
        ->where('currency_id','=',$currency_id)
        ->first();

       
        return [
            'total_price'     => $todays_bought->total_price ?? 0,
            'discount'        => $todays_bought->discount ?? 0,
            'payable'         => $todays_bought->payable ?? 0,
            'cur_pay'         => $todays_bought->cur_pay ?? 0,
            'remained'        => $todays_bought->remained ?? 0,
            'trans_spend'     => $todays_bought->trans_spend ?? 0,
        ];

    }


    function getCashIncomeOutcome($year, $month, $day, $currency_id)
    {

        $journal_income = DB::table('journals')
            ->selectRaw('SUM(amount) as total_income')
            ->join('accounts', 'accounts.id', '=', 'journals.account_id') 
            ->where('accounts.account_type_id', '=', 1) 
            ->where('journals.year', '=', $year)
            ->where('journals.month', '=', $month)
            ->where('journals.day', '=', $day)
            ->where('journals.currency_id', '=', $currency_id)
            ->where('journals.transaction_type', '=', 1)
            ->first(); 

        $journal_outcome = DB::table('journals')
            ->selectRaw('SUM(amount) as total_outcome')
            ->join('accounts', 'accounts.id', '=', 'journals.account_id') 
            ->where('accounts.account_type_id', '=', 1) 
            ->where('journals.year', '=', $year)
            ->where('journals.month', '=', $month)
            ->where('journals.day', '=', $day)
            ->where('journals.currency_id', '=', $currency_id)
            ->where('journals.transaction_type', '=', 2)
            ->first(); 


        return [
            'total_income' => $journal_income->total_income ?? 0,
            'total_outcome' => $journal_outcome->total_outcome ?? 0,
        ];
    }

    // --------------------- SECOND TAB ---------------------------
    function getGeneralData4SecondTab($year, $currency_id)
    {
        $banks = 1000; // تمام پول نقد به شمول خزانه ٬ صرافی و بانک ها
        $income = 2000;
        $expense = 3000;
        $customers = 4000; // مشتریان و فروشندگان
        $employees = 5000; // کارمندان
        $khazana_account_id = 4;

        // Total loans
        $total_loan = DB::table('journals')
            ->join('accounts', 'accounts.id', '=', 'journals.account_id')
            ->where('transaction_type', 1)
            ->where('payment_type', 2)
            ->where('year', $year)
            ->where('currency_id', $currency_id)
            // ->where('parent_code', $banks)
            ->where('is_cleared', 0)
            ->sum('amount');

        // Total debts
        $total_talab = DB::table('journals')
            ->join('accounts', 'accounts.id', '=', 'journals.account_id')
            ->where('transaction_type', 2)
            ->where('payment_type', 2)
            ->where('year', $year)
            ->where('currency_id', $currency_id)
            // ->where('parent_code', $banks)
            ->where('is_cleared', 0)
            ->sum('amount');

        // Total general purchases
        $total_bought = DB::table('bought_items')
            ->where('currency_id', $currency_id)
            ->where('year', $year)
            ->sum('payable');

        // Total sales
        $total_sales = DB::table('warehouse_sales')
            ->where('currency_id', $currency_id)
            ->where('year', $year)
            ->sum('payable');

        // Total cash income
        $banks_income = DB::table('journals')
            ->join('accounts', 'accounts.id', '=', 'journals.account_id')
            ->where('transaction_type', 1)
            ->where('payment_type', 1)
            ->where('year', $year)
            ->where('currency_id', $currency_id)
            // ->where('parent_code', $banks)
            ->sum('amount');

        // Total cash expenses
        $banks_outcome = DB::table('journals')
            ->join('accounts', 'accounts.id', '=', 'journals.account_id')
            ->where('transaction_type', 2)
            ->where('payment_type', 1)
            ->where('year', $year)
            ->where('currency_id', $currency_id)
            // ->where('parent_code', $banks)
            ->sum('amount');

        // Sales profit
        $profit = DB::table('warehouse_sales')
            ->where('currency_id', $currency_id)
            ->where('year', $year)
            ->sum('total_price');

        // Return results as an array
        return [
            'total_loan' => $total_loan ?? 0,
            'total_talab' => $total_talab ?? 0,
            'total_bought' => $total_bought ?? 0,
            'medicine_bought' => $medicine_bought ?? 0,
            'total_sales' => $total_sales ?? 0,
            'total_stock' => $total_stock ?? 0,
            'banks_income' => $banks_income ?? 0,
            'banks_outcome' => $banks_outcome ?? 0,
            'profit' => $profit ?? 0,
        ];
    }

    // ----------------- THIRD TAB ----------------
    function getCashInHandAmount($currency_id, $year, $month, $day)
    {
        // day 17 => search where days <= 16
        $last_day = $day - 1;

        $banks = 1000;
        $khazana_account_id = 4;

        // Initialize conditions
        $monthCondition = (intval($month) > 0 && intval($month) <= 12) ? "AND month = '$month'" : '';
        $dayCondition = (intval($day) > 0 && intval($day) <= 31) ? "AND day <= '$last_day'" : '';

        // Get currency details
        $currencies = DB::table('currencies')
            ->select('currencies.id as currencyId', 'currencies.name as currency_name', 'symbols', 'color')
            ->get();

        // Get total paid
        $total_payed = DB::table('journals')
            ->join('accounts', 'accounts.id', '=', 'journals.account_id')
            ->where('journals.account_id', $khazana_account_id)
            ->where('transaction_type', 2)
            ->where('payment_type', 1)
            ->where('journals.currency_id', $currency_id)
            ->where('year', $year)
            ->where('month', $month)
            ->where('day', '<=', $day)
            ->sum('amount');

        // Get total received
        $total_recieved = DB::table('journals')
            ->join('accounts', 'accounts.id', '=', 'journals.account_id')
            ->where('journals.account_id', $khazana_account_id)
            ->where('transaction_type', 1)
            ->where('payment_type', 1)
            ->where('journals.currency_id', $currency_id)
            ->where('year', $year)
            ->where('month', $month)
            ->where('day', '<=', $day)
            ->sum('amount');

        // Map and return results
        $result = $currencies->map(function ($currency) use ($total_payed, $total_recieved) {
            return [
                'currencyId' => $currency->currencyId,
                'currency_name' => $currency->currency_name,
                'symbol' => $currency->symbols,
                'color' => $currency->color,
                'total_payed' => $total_payed ?? 0,
                'total_recieved' => $total_recieved ?? 0,
            ];
        });

        return $result->toArray();
    }

}

<?php

namespace App\Http\Controllers\Home;

// use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session; // Import Session facade
use Illuminate\Support\Facades\Auth; // Import Auth facade
use App\Helpers\ManagementHelper;
use App\Helpers\FunctionHelper;
use Illuminate\Support\Facades\DB;


use App\Models\Setting\Currency;


class HomeControllerBkp extends BaseController
{
    protected $module;
    public function __construct()
    {
        $this->isLoggedIn();
        $this->module = 'dashboard';	
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // --------------- test session data and auth data ------------
        //  // Check if user is logged in
        //  $isLoggedIn = auth()->check();

        //  // Get the currently logged-in user (if any)
        //  $user = auth()->user();
 
        //  // Retrieve session data
        //  $sessionData = Session::all();
 
        //  // Debugging: Display login status, user, and session data
        //  dd([
        //      'isLoggedIn' => $isLoggedIn,
        //      'user' => $user,
        //      'sessionData' => $sessionData,
        //  ]);

        // ----------------- test BaseController Variables -------------
        // return response()->json(['global' =>$this->global]);
        // return response()->json(['isAdmin' => $this->isAdmin]);
        // return response()->json(['isAdmin' => $this->isAdmin()]);
        // return response()->json(['hasListAccess' => $this->hasListAccess()]);
        
        // --------------------- get page data ----------------
        
        // $global_data = ['global' => $this->global];
        // $page_data = ['global' => $data];

        // return view('dashboard.dashboard', compact('global_data','page_data'));

        if(!$this->hasListAccess())
        {
			// pre($this->global);
            $this->loadThis($this->global);
        }
        else
        {
            // dd(
            //     ['message' => 'hasListAccess']
            // );

            $data['year'] = $request->input('year', FunctionHelper::curYear()); 
            $data['month'] = $request->input('month', FunctionHelper::curMonth()); 
            $data['day'] = $request->input('day', FunctionHelper::curDay()); 

    
            // ------------------------- Currency --------------------------------
            
            $data['currency'] = Currency::select('id','name')->orderBy('id','ASC')->get()->toArray();
            // ManagementHelper::pre($data);
            
			// filter by branch if submitted
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
            // ManagementHelper::pre($data);
            
             // ----------------------------- / First Tab ------------------------
             /**
             * transaction_type: 1: recieved , 2:paid
             * payment_type    : 1: cache,    2:loan
             */

			 // first tab
			 $data['todays_sold_income'] = $this->getTodaysSoldIncome($data['year'],$data['month'],$data['day'],$data['currency_id']);
			 $data['getTodaysBoughtItems'] = $this->getTodaysBoughtData($data['year'],$data['month'],$data['day'],$data['currency_id']);
			 $data['cashIncomeOutcome'] = $this->getCashIncomeOutcome($data['year'],$data['month'],$data['day'],$data['currency_id']);
             
             
            if(ManagementHelper::activePackageId() >= 2) 
            {
                 $data['secondTabData'] = $this->getGeneralData4SecondTab($data['year'],$data['currency_id']);
				 $data['cache_in_hand'] = $this->getCashInHandAmount($data['currency_id'],$data['year'],$data['month'],$data['day']);
            }
                
            //  ManagementHelper::pre($data);
			
            // $msg=$this->session->flashdata('msg');    
            $global_data = ['global' => $this->global];
          


            return view('dashboard.dashboard', compact('global_data','data'));
            // $this->messages->showMessage($msg);
		}
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
        
        $results = DB::table('warehouse_sales')
        ->selectRaw('
            year as sold_year, 
            month as sold_month, 
            day as sold_day,
            (SELECT COALESCE(SUM(payable), 0) FROM warehouse_sales WHERE currency_id = ? AND year = warehouse_sales.year ' . (intval($month) ? 'AND month = ? ' : '') . (intval($day) ? 'AND day = ? ' : '') . ') as total_sales,
            (SELECT COALESCE(SUM(cur_pay), 0) FROM warehouse_sales WHERE currency_id = ? AND year = warehouse_sales.year ' . (intval($month) ? 'AND month = ? ' : '') . (intval($day) ? 'AND day = ? ' : '') . ') as cur_pay,
            (SELECT COALESCE(SUM(remained), 0) FROM warehouse_sales WHERE currency_id = ? AND year = warehouse_sales.year ' . (intval($month) ? 'AND month = ? ' : '') . (intval($day) ? 'AND day = ? ' : '') . ') as remained
        ', array_merge(
            [$currency_id], (intval($month) ? [$month] : []), (intval($day) ? [$day] : []),
            [$currency_id], (intval($month) ? [$month] : []), (intval($day) ? [$day] : []),
            [$currency_id], (intval($month) ? [$month] : []), (intval($day) ? [$day] : [])
        ))
        ->where('currency_id', $currency_id)
        ->where('year', $year)
        ->when(intval($month) > 0 && intval($month) <= 12, function ($query) use ($month) {
            $query->where('month', $month);
        })
        ->when(intval($day) > 0 && intval($day) <= 31, function ($query) use ($day) {
            $query->where('day', $day);
        })
        ->limit(1)
        ->get();

        if ($results->isEmpty()) {
            return [
                'sold_year'    => $year,
                'sold_month'   => $month ?: null,
                'sold_day'     => $day ?: null,
                'total_sales'  => 0,
                'cur_pay'      => 0,
                'remained'     => 0,
                'profit'       => 0,
            ];
        }

        return $results;
    }

    function getTodaysBoughtData($year, $month, $day, $currency_id)
    {
        $query = DB::table('bought_items')
            ->selectRaw('
                year as bought_year,
                month as bought_month,
                day as bought_day,
                COALESCE((
                    SELECT SUM(payable)
                    FROM bought_items
                    WHERE currency_id = ? 
                    AND year = bought_items.year 
                    AND (? = 0 OR month = ?)
                    AND (? = 0 OR day = ?)
                ), 0) as total_bought',
                [$currency_id, $month, $month, $day, $day]
            )
            ->selectRaw('
                COALESCE((
                    SELECT SUM(cur_pay)
                    FROM bought_items
                    WHERE currency_id = ? 
                    AND year = bought_items.year 
                    AND (? = 0 OR month = ?)
                    AND (? = 0 OR day = ?)
                ), 0) as cur_pay',
                [$currency_id, $month, $month, $day, $day]
            )
            ->selectRaw('
                COALESCE((
                    SELECT SUM(remained)
                    FROM bought_items
                    WHERE currency_id = ? 
                    AND year = bought_items.year 
                    AND (? = 0 OR month = ?)
                    AND (? = 0 OR day = ?)
                ), 0) as remained',
                [$currency_id, $month, $month, $day, $day]
            )
            ->selectRaw('
                COALESCE((
                    SELECT SUM(trans_spend)
                    FROM bought_items
                    WHERE currency_id = ? 
                    AND year = bought_items.year 
                    AND (? = 0 OR month = ?)
                    AND (? = 0 OR day = ?)
                ), 0) as trans_spend',
                [$currency_id, $month, $month, $day, $day]
            )
            ->where('currency_id', $currency_id)
            ->where('year', $year)
            ->when(intval($month) > 0 && intval($month) <= 12, function ($query) use ($month) {
                $query->where('month', $month);
            })
            ->when(intval($day) > 0 && intval($day) <= 31, function ($query) use ($day) {
                $query->where('day', $day);
            })
            ->limit(1)
            ->get();

        // Return the result, or an empty array if no data is found
        return $query->isEmpty() ? [] : $query->first();
    }


    function getCashIncomeOutcome($year, $month, $day, $currency_id)
    {
        $cache = 1000; // تمام پول نقد به شمول خزانه ٬ صرافی و بانک ها
        $income = 2000;
        $expense = 3000;
        $customers = 4000; // مشتریان و فروشندگان
        $employees = 5000; // کارمندان
        $khazana_account_id = 4;

        $query = DB::table('journals')
            ->join('accounts', 'accounts.id', '=', 'journals.account_id')
            ->where('year', $year)
            ->where('currency', $currency_id);

        if (intval($month) > 0 && intval($month) <= 12) {
            $query->where('month', $month);
        }

        if (intval($day) > 0 && intval($day) <= 31) {
            $query->where('day', $day);
        }

        // Cash income to the treasury
        $khazana_income = DB::table('journals')
            ->join('accounts', 'accounts.id', '=', 'journals.account_id')
            ->where('transaction_type', 1)
            ->where('payment_type', 1)
            ->where('year', $year)
            ->where('currency_id', $currency_id)
            ->where('journals.account_id', $khazana_account_id)
            ->whereRaw(intval($month) > 0 && intval($month) <= 12 ? 'month = ?' : '', [$month])
            ->whereRaw(intval($day) > 0 && intval($day) <= 31 ? 'day = ?' : '', [$day])
            ->sum('amount');

        // Cash outcome from the treasury
        $khazana_outcome = DB::table('journals')
            ->join('accounts', 'accounts.id', '=', 'journals.account_id')
            ->where('transaction_type', 2)
            ->where('payment_type', 1)
            ->where('year', $year)
            ->where('currency_id', $currency_id)
            ->where('journals.account_id', $khazana_account_id)
            ->whereRaw(intval($month) > 0 && intval($month) <= 12 ? 'month = ?' : '', [$month])
            ->whereRaw(intval($day) > 0 && intval($day) <= 31 ? 'day = ?' : '', [$day])
            ->sum('amount');

        // Cash income to the banks
        $banks_income = DB::table('journals')
            ->join('accounts', 'accounts.id', '=', 'journals.account_id')
            ->where('transaction_type', 1)
            ->where('payment_type', 1)
            ->where('year', $year)
            ->where('currency_id', $currency_id)
            // ->where('parent_code', $cache)
            ->where('journals.account_id', '!=', $khazana_account_id)
            ->whereRaw(intval($month) > 0 && intval($month) <= 12 ? 'month = ?' : '', [$month])
            ->whereRaw(intval($day) > 0 && intval($day) <= 31 ? 'day = ?' : '', [$day])
            ->sum('amount');

        // Cash outcome from the banks
        $banks_outcome = DB::table('journals')
            ->join('accounts', 'accounts.id', '=', 'journals.account_id')
            ->where('transaction_type', 2)
            ->where('payment_type', 1)
            ->where('year', $year)
            ->where('currency_id', $currency_id)
            // ->where('parent_code', $cache)
            ->where('journals.account_id', '!=', $khazana_account_id)
            ->whereRaw(intval($month) > 0 && intval($month) <= 12 ? 'month = ?' : '', [$month])
            ->whereRaw(intval($day) > 0 && intval($day) <= 31 ? 'day = ?' : '', [$day])
            ->sum('amount');

        // Total spent cash
        $total_spend = DB::table('journals')
            ->join('accounts', 'accounts.id', '=', 'journals.account_id')
            ->where('transaction_type', 2)
            ->where('payment_type', 1)
            ->where('year', $year)
            ->where('currency_id', $currency_id)
            // ->where('parent_code', $cache)
            ->whereRaw(intval($month) > 0 && intval($month) <= 12 ? 'month = ?' : '', [$month])
            ->whereRaw(intval($day) > 0 && intval($day) <= 31 ? 'day = ?' : '', [$day])
            ->sum('amount');

        // Total income cash
        $total_incomes = DB::table('journals')
            ->join('accounts', 'accounts.id', '=', 'journals.account_id')
            ->where('transaction_type', 1)
            ->where('payment_type', 1)
            ->where('year', $year)
            ->where('currency_id', $currency_id)
            // ->where('parent_code', $cache)
            ->whereRaw(intval($month) > 0 && intval($month) <= 12 ? 'month = ?' : '', [$month])
            ->whereRaw(intval($day) > 0 && intval($day) <= 31 ? 'day = ?' : '', [$day])
            ->sum('amount');

        // Prepare the result
        $result = [
            'khazana_income' => $khazana_income ?? 0,
            'khazana_outcome' => $khazana_outcome ?? 0,
            'banks_income' => $banks_income ?? 0,
            'banks_outcome' => $banks_outcome ?? 0,
            'total_spend' => $total_spend ?? 0,
            'total_incomes' => $total_incomes ?? 0,
        ];

        // If no data is found, return 0
        if (empty($result)) {
            return 0;
        }

        return $result;
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

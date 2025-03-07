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
use App\Models\Setting\OrgBio;


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
        $data['currency'] = Currency::select('id', 'name')->orderBy('id', 'ASC')->get()->toArray();
    
        if ($request->has('currency_id')) {
            $data['currency_id'] = $request->input('currency_id');
    
            $cur_currency = Currency::find($data['currency_id']);
    
            $data['currency_name'] = $cur_currency->name ?? null;
            $data['currency_id'] = $cur_currency->id ?? null;
        } else {
            $data['currency_id'] = $data['currency'][0]['id'] ?? null;
            $data['currency_name'] = $data['currency'][0]['name'] ?? null;
        }


        /**
         * transaction_type: 1: recieved , 2:paid
         * payment_type    : 1: cache,    2:loan
         */

        $orgBio = OrgBio::first();

        // first tab
        $data['todays_sold_income'] = $this->getTodaysSoldIncome($data['year'],$data['month'],$data['day'],$data['currency_id']);
        $data['getTodaysBoughtItems'] = $this->getTodaysBoughtData($data['year'],$data['month'],$data['day'],$data['currency_id']);
        $data['cashIncomeOutcome'] = $this->getCashIncomeOutcome($data['year'],$data['month'],$data['day'],$data['currency_id']);
        // return ['data' => $data];

        // Second Tab
        $secondTab = $this->getSecondTabReport($data['year'],$data['currency_id']);
        // return ['secondTab', $secondTab];

        // Third Tab
        $thirdTab = $this->getCashInHandAmount($data['year'],$data['month'],$data['day']);
        // $thirdTab = $this->getCashInHandAmount($data['year'],100,100);
        // return ['thirdTab', $thirdTab];

        // $auth = auth()->user();
        // return ['auth' => auth()->user()->photo ];

        // Get the Jalali date object for the provided date
        // $baseDate = Jalalian::fromFormat('Y-m-d', $data['year'] . '-' . $data['month'] . '-' . $data['day']);
        // Get the names of the past 7 days
        // $days = $this->getPast7Days($baseDate);

       

        return view('dashboard.dashboard', compact('data','orgBio','secondTab','thirdTab'));
    }


     /**
     * Get the past 7 days' names.
     *
     * @param \Morilog\Jalali\Jalalian $baseDate
     * @return array
     */
    private function getPast7Days(Jalalian $baseDate)
    {
        // Check if the current baseDate is the same as the previous one in the session
        $prevBaseDate = session('baseDate');
        $days = [];

        if ($prevBaseDate !== $baseDate->format('Y-m-d')) {
            // If the baseDate has changed, recalculate the days
            for ($i = 0; $i < 7; $i++) {
                $date = $baseDate->subDays($i); // Subtract i days from the base date
                $dayName = $date->format('l'); // Get the day name (e.g., شنبه, یکشنبه, etc.)
                $days[] = $dayName;
            }

            // Store the new baseDate and calculated days in the session
            session(['baseDate' => $baseDate->format('Y-m-d'), 'days' => $days]);
        } else {
            // If the baseDate is the same, use the stored days
            $days = session('days');
        }

        return $days;
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
    




    // ----------------- re-usable functions ------------
    function getTodaysSoldIncome($year, $month, $day, $currency_id)
    {
        $todays_soled = WarehouseSales::selectRaw('SUM(total_price) as total_price, SUM(total_discount) as total_discount, SUM(payable) as payable, SUM(cur_pay) as cur_pay, SUM(remained) as remained')
        ->where('year', '=', $year)
        ->when($month != 100, function ($query) use ($month) {
            return $query->where('month', '=', $month);
        })
        ->when($day != 100, function ($query) use ($day) {
            return $query->where('day', '=', $day);
        })
        ->where('currency_id', '=', $currency_id)
        ->first();

        $todays_sold_profits = SalesDetails::whereHas('warehouseSale', function ($query) use ($currency_id, $year, $month, $day) {
                $query->where('currency_id', $currency_id)
                    ->where('year', $year)
                    ->when($month != 100, function ($query) use ($month) {
                        return $query->where('month', $month);
                    })
                    ->when($day != 100, function ($query) use ($day) {
                        return $query->where('day', $day);
                    });
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
            ->where('year', '=', $year)
            ->when($month != 100, function ($query) use ($month) {
                return $query->where('month', '=', $month);
            })
            ->when($day != 100, function ($query) use ($day) {
                return $query->where('day', '=', $day);
            })
            ->where('currency_id', '=', $currency_id)
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
        $company_account_type_id = 1;
    
        // Cache Recieved =  total_incomes = p1t1
        // Cache Paid     =  total_outcome = p1t2
        $result = DB::table('journals')
            ->selectRaw("
                SUM(CASE WHEN journals.status = 4 THEN amount ELSE 0 END) as total_expense,
                SUM(CASE WHEN journals.status = 3 THEN amount ELSE 0 END) as total_income,
                SUM(CASE WHEN journals.transaction_type = 1 AND payment_type = 1 THEN amount ELSE 0 END) as total_incomes,
                SUM(CASE WHEN journals.transaction_type = 2 AND payment_type = 1 THEN amount ELSE 0 END) as total_outcomes
            ")
            ->join('accounts', 'accounts.id', '=', 'journals.account_id')
            ->where('accounts.account_type_id', '=', $company_account_type_id)
            ->where('journals.year', '=', $year)
            ->when($month != 100, function ($query) use ($month) {
                return $query->where('journals.month', '=', $month);
            })
            ->when($day != 100, function ($query) use ($day) {
                return $query->where('journals.day', '=', $day);
            })
            ->where('journals.currency_id', '=', $currency_id)
            ->where('journals.is_cleared', '=', 0)
            ->first();
    
        return [
            'total_expense' => $result->total_expense ?? 0,
            'total_outcomes' => $result->total_outcomes ?? 0,
            'total_incomes' => $result->total_incomes ?? 0,
            'total_income' => $result->total_income ?? 0,
        ];
    }
    


    // --------------------- SECOND TAB ---------------------------
    function getSecondTabReport($year, $currency_id)
    {
        $company_account_type_id = 1; // صرف خزانه شرکت
        $banks_account_type_id = 6; //   صرافی و بانک ها


        // Total Goads in Warehouse
        $total_warehouse_value = DB::table('warehouse_items')
            ->where('year', $year)
            ->where('currency_id', $currency_id)
            ->selectRaw('SUM(available_amount * avg_up) as total_value, SUM(wastage_total) as total_wastage')
            ->first();
        
        /**
         * دریافت پول نقد شرکت = Cache Recieved = p1t1
         * پرداخت پول نقد شرکت = Cache Paid = p1t2
         * طلبات شرکت = Paid Loan = p2t2
         * قرضه شرکت = Recieved Loan = p2t1
         */
        $result = DB::table('journals')
            ->selectRaw("
                SUM(CASE WHEN journals.transaction_type = 1 AND payment_type = 1 THEN amount ELSE 0 END) as total_incomes,
                SUM(CASE WHEN journals.transaction_type = 2 AND payment_type = 1 THEN amount ELSE 0 END) as total_outcomes,
                SUM(CASE WHEN journals.transaction_type = 2 AND payment_type = 2 THEN amount ELSE 0 END) as total_talabat,
                SUM(CASE WHEN journals.transaction_type = 1 AND payment_type = 2 THEN amount ELSE 0 END) as total_loan
            ")
            ->join('accounts', 'accounts.id', '=', 'journals.account_id')
            ->whereIn('accounts.account_type_id', [$company_account_type_id, $banks_account_type_id]) // ✅ Fixes issue
            ->where('journals.year', '=', $year)
            ->where('journals.currency_id', '=', $currency_id)
            ->where('journals.is_cleared', '=', 0)
            ->first();

        // مفاد فروشات سالانه
        $sold_profits = SalesDetails::whereHas('warehouseSale', function ($query) use ($currency_id, $year) {
            $query->where('currency_id', $currency_id)->where('year', $year);
        })->sum('profit');

        return [
            'total_warehouse_value' => $total_warehouse_value->total_value ?? 0,
            'total_warehouse_wastage' => $total_warehouse_value->total_wastage ?? 0,
            'total_income' => $result->total_incomes ?? 0, 
            'total_outcome' => $result->total_outcomes ?? 0, 
            'total_talabat' => $result->total_talabat ?? 0,
            'total_loan' => $result->total_loan ?? 0,
            'sold_profits' => $sold_profits ?? 0,
        ];
    }



    // ----------------- THIRD TAB ----------------
    function getCashInHandAmount($year, $month, $day)
    {
        // day 17 => search where days <= 16
        $last_day = $day - 1;

        $khazana_account_type_id = 1;

        // Get currency details
        $currencies = DB::table('currencies')
            ->select('currencies.id as currencyId', 'currencies.name as currency_name', 'symbols', 'color')
            ->get();

        // Get total paid and total received in a single query
        $total_amounts = DB::table('journals')
            ->join('accounts', 'accounts.id', '=', 'journals.account_id')
            ->select(
                'journals.currency_id',
                DB::raw('SUM(CASE WHEN journals.transaction_type = 2 AND journals.payment_type = 1 THEN amount ELSE 0 END) as total_paid'),
                DB::raw('SUM(CASE WHEN journals.transaction_type = 1 AND journals.payment_type = 1 THEN amount ELSE 0 END) as total_recieved')
            )
            ->where('accounts.account_type_id', $khazana_account_type_id)
            ->when($year != 100, function ($query) use ($year) {
                return $query->where('journals.year', $year);
            })
            ->when($month != 100, function ($query) use ($month) {
                return $query->where('journals.month', $month);
            })
            ->where('journals.day', '<=', $day)
            ->groupBy('journals.currency_id')
            ->get();

        // Map and return results
        $result = $currencies->map(function ($currency) use ($total_amounts) {
            // Find the corresponding total for the currency
            $amount = $total_amounts->firstWhere('currency_id', $currency->currencyId);
            
            return [
                'currencyId' => $currency->currencyId,
                'currency_name' => $currency->currency_name,
                'symbol' => $currency->symbols,
                'color' => $currency->color,
                'total_paid' => $amount->total_paid ?? 0,
                'total_recieved' => $amount->total_recieved ?? 0,
            ];
        });

        return $result->toArray();
    }


    // function getCashInHandAmount($year, $month, $day)
    // {
    //     // day 17 => search where days <= 16
    //     $last_day = $day - 1;

    //     $khazana_account_type_id = 1;

    //     // Initialize conditions
    //     $monthCondition = (intval($month) > 0 && intval($month) <= 12) ? "AND month = '$month'" : '';
    //     $dayCondition = (intval($day) > 0 && intval($day) <= 31) ? "AND day <= '$last_day'" : '';

    //     // Get currency details
    //     $currencies = DB::table('currencies')
    //         ->select('currencies.id as currencyId', 'currencies.name as currency_name', 'symbols', 'color')
    //         ->get();

    //     // Get total paid and total received in a single query
    //     $total_amounts = DB::table('journals')
    //         ->join('accounts', 'accounts.id', '=', 'journals.account_id')
    //         ->select(
    //             'journals.currency_id',
    //             DB::raw('SUM(CASE WHEN journals.transaction_type = 2 AND journals.payment_type = 1 THEN amount ELSE 0 END) as total_paid'),
    //             DB::raw('SUM(CASE WHEN journals.transaction_type = 1 AND journals.payment_type = 1 THEN amount ELSE 0 END) as total_recieved')
    //         )
    //         ->where('accounts.account_type_id', $khazana_account_type_id)
    //         ->where('journals.year', $year)
    //         ->where('journals.month', $month)
    //         ->where('journals.day', '<=', $day)
    //         ->groupBy('journals.currency_id')
    //         ->get();

    //     // Map and return results
    //     $result = $currencies->map(function ($currency) use ($total_amounts) {
    //         // Find the corresponding total for the currency
    //         $amount = $total_amounts->firstWhere('currency_id', $currency->currencyId);
            
    //         return [
    //             'currencyId' => $currency->currencyId,
    //             'currency_name' => $currency->currency_name,
    //             'symbol' => $currency->symbols,
    //             'color' => $currency->color,
    //             'total_paid' => $amount->total_paid ?? 0,
    //             'total_recieved' => $amount->total_recieved ?? 0,
    //         ];
    //     });

    //     return $result->toArray();
    // }


}

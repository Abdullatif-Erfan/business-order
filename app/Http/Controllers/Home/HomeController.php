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
use App\Models\Warehouse\WarehouseItem;
use App\Models\Buy\BoughtItem;
use App\Models\Setting\Currency;
use App\Models\Setting\Account;
use App\Models\Journal\Journal;
use App\Models\Warehouse\SalesDetails;
use App\Models\Setting\OrgBio;
use App\Models\Setting\Branch;

use Carbon\Carbon;
use Morilog\Jalali\CalendarUtils;


// class HomeController extends BaseController
class HomeController extends Controller
{
    protected $branch_id, $isAdmin, $package_type;

    // Inject the message service into the controller
    public function __construct()
    {
        // Ensure user authentication before setting the branch ID
        // if (auth()->check()) {
        //     $user = auth()->user();
        //     $this->branch_id = $user->branch_id ?? 0;
        //     $this->isAdmin = $user->isAdmin == 1 ? true : false;
        // } else {
        //     $this->branch_id = 0;
        //     $this->isAdmin = false;
        // }
        if (auth()->check()) {
            $this->branch_id = session('branch_id', auth()->user()->branch_id ?? 0);
            $this->isAdmin = session('isAdmin', auth()->user()->isAdmin == 1);
            $this->package_type = session('package_type', 0);
        } else {
            $this->branch_id = 0;
            $this->isAdmin = false;
            $this->package_type = 0;
        }
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $accessInfo = Session::get('accessInfo', []);
        // return ['accessInfo' => $accessInfo];

        // $auth = auth()->user();
        // return ['auth' => $auth];
        // $user = auth()->user();
        // $branch_id = $this->branch_id;
        // $isAdmin = $this->isAdmin;

        // **Get branch_id from the session instead of the user model**
        // $branch_id = session('branch_id');
        // $isAdmin = session('isAdmin');
        if(empty($this->package_type) && $this->package_type <= 0)
        {
            echo "لطفا یکی از پکیج هارا انتخاب نمایید"; die();
        }

        $branch_id = $this->branch_id ?? 0;
        $isAdmin = $this->isAdmin ?? 0;
        $package_type = $this->package_type ?? 0;

        if(!$branch_id)
        {
            return redirect()->route('login');
        }

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
        $branches = Branch::all();

        // first tab
        $data['todays_sold_income'] = $this->getTodaysSoldIncome($data['year'],$data['month'],$data['day'],$data['currency_id'],$branch_id);
        $data['getTodaysBoughtItems'] = $this->getTodaysBoughtData($data['year'],$data['month'],$data['day'],$data['currency_id'],$branch_id);
        $data['cashIncomeOutcome'] = $this->getCashIncomeOutcome($data['year'],$data['month'],$data['day'],$data['currency_id'],$branch_id);
        // return ['data' => $data];

        // Second Tab
        $secondTab = $this->getSecondTabReport($data['year'],$data['currency_id'],$branch_id);
        // return ['secondTab', $secondTab];

        // Third Tab
        $thirdTab = $this->getCashInHandAmount($data['year'],$data['month'],$data['day'],$branch_id);
        // $thirdTab = $this->getCashInHandAmount($data['year'],100,100);
        // return ['thirdTab', $thirdTab];

        // $auth = auth()->user();
        // return ['auth' => auth()->user()->photo ];

        // Get the Jalali date object for the provided date
        // $baseDate = Jalalian::fromFormat('Y-m-d', $data['year'] . '-' . $data['month'] . '-' . $data['day']);
        // Get the names of the past 7 days
        // $days = $this->getPast7Days($baseDate);

       
        return view('dashboard.dashboard', compact('data','orgBio','secondTab','thirdTab','branches','isAdmin','branch_id','package_type'));
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
        $warehouseNotifyAbleAmount = WarehouseItem::whereColumn('notification_amount', '>=', 'available_amount')  
        ->where('branch_id', $this->branch_id)->count();
        return view('notify.warehouse_item_amount', ['records' => $warehouseNotifyAbleAmount]);
    }

    public function warehouseItemList()
    {
        // $warehouseList = WarehouseItem::
        // $response = $this->load->view('notify/warehouse_item_list',$data,TRUE);
        // echo $response;	
        
        $WarehouseItems = DB::table('warehouse_items')
        ->join('warehouses', 'warehouses.id', '=', 'warehouse_items.warehouse_id')
        ->join('bought_item_pre_lists', 'bought_item_pre_lists.id', '=', 'warehouse_items.buy_pre_id')
        ->join('units', 'units.id', '=', 'warehouse_items.unit_id')
        ->select(
            'warehouses.name as wname',
            'bought_item_pre_lists.name as item_name',
            'warehouse_items.available_amount',
            'units.name as unit_name',
            'notification_amount'
        )
        ->where('notification_amount', '>=', 0) // Optional, remove if unnecessary
        ->whereColumn('notification_amount', '>=', 'warehouse_items.available_amount') // Correct column comparison
        ->where('warehouse_items.branch_id', $this->branch_id)
        ->get();

        return view('notify.warehouse_item_list', ['records' => $WarehouseItems]);
            
    }

    private function getExpiredWarehouseItems()
    {
        $today = Jalalian::now();
        $orgBio = OrgBio::first(); 
        $notification_days = $orgBio->expired_after_days ?? 30;

        return WarehouseItem::with(['warehouseRelation:id,name', 'preListRelation:id,name'])
            ->select('id', 'warehouse_id', 'buy_pre_id', 'expire_date', 'branch_id')
            ->where('branch_id', $this->branch_id)
            ->whereNotNull('expire_date')
            ->get()
            ->map(function ($item) use ($today) {
                try {
                    $expireDate = Jalalian::fromFormat('Y-m-d', $item->expire_date)->toCarbon();
                    $daysDifference = floor(Carbon::now()->diffInDays($expireDate, false));
                    $item->expired_days = $daysDifference;
                } catch (\Exception $e) {
                    $item->expired_days = null;
                }
                return $item;
            })
            ->filter(function ($item) use ($notification_days) {
                return $item->expired_days !== null && $item->expired_days < $notification_days;
            });
    }

    public function expiredWarehouseItems()
    {
        $expiredAbleItems = $this->getExpiredWarehouseItems();
        return view('notify.expired_item_list', ['records' => $expiredAbleItems]);
    }

    public function expiredDateNotifyAmount()
    {
        $expiredItemCount = $this->getExpiredWarehouseItems()->count();
        return view('notify.expired_items_amount', ['records' => $expiredItemCount]);
    }




    // ----------------- re-usable functions ------------
    function getTodaysSoldIncome($year, $month, $day, $currency_id, $branch_id)
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
        ->where('branch_id', '=', $branch_id)
        ->first();

        $todays_sold_profits = SalesDetails::where('branch_id', $branch_id) // Ensure branch_id is directly filtered in SalesDetails
        ->whereHas('warehouseSale', function ($query) use ($currency_id, $year, $month, $day) {
            $query->where('currency_id', $currency_id)
                ->when($year, function ($query) use ($year) {
                    return $query->where('year', $year);
                })
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

    function getTodaysBoughtData($year, $month, $day, $currency_id, $branch_id)
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
            ->where('branch_id', '=', $branch_id)
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
    


    function getCashIncomeOutcome($year, $month, $day, $currency_id,$branch_id)
    {
        $company_account_type_id = 1;
    
        // Cache Recieved =  total_incomes = p1t1
        // Cache Paid     =  total_outcome = p1t2
        $result = DB::table('journals')
            ->selectRaw("
                SUM(CASE WHEN journals.status = 4 THEN amount ELSE 0 END) as total_expense,
                SUM(CASE WHEN journals.status = 3 THEN amount ELSE 0 END) as total_income,
                SUM(CASE WHEN journals.status = 5 THEN amount ELSE 0 END) as total_salary,
                SUM(CASE WHEN journals.transaction_type = 1 AND payment_type = 1 THEN amount ELSE 0 END) as total_incomes,
                SUM(CASE WHEN journals.transaction_type = 2 AND payment_type = 1 THEN amount ELSE 0 END) as total_outcomes
            ")
            ->where('journals.account_type_id', '=', $company_account_type_id)
            ->where('journals.year', '=', $year)
            ->when($month != 100, function ($query) use ($month) {
                return $query->where('journals.month', '=', $month);
            })
            ->when($day != 100, function ($query) use ($day) {
                return $query->where('journals.day', '=', $day);
            })
            ->where('journals.currency_id', '=', $currency_id)
            ->where('journals.branch_id', '=', $branch_id)
            ->where('journals.is_cleared', '=', 0)
            ->first();
    
        return [
            'total_expense' => $result->total_expense ?? 0,
            'total_outcomes' => $result->total_outcomes ?? 0,
            'total_salary' => $result->total_salary ?? 0,
            'total_incomes' => $result->total_incomes ?? 0,
            'total_income' => $result->total_income ?? 0,
        ];
    }
    


    // --------------------- SECOND TAB ---------------------------
    function getSecondTabReport($year, $currency_id, $branch_id)
    {
        $company_account_type_id = 1; // صرف خزانه شرکت
        $banks_account_type_id = 6; //   صرافی و بانک ها


        // Total Goads in Warehouse
        $total_warehouse_value = DB::table('warehouse_items')
            ->where('year', $year)
            ->where('currency_id', $currency_id)
            ->where('branch_id', '=', $branch_id)
            ->where('is_cleared', '=', 0)
            ->selectRaw('SUM(available_amount * avg_up) as total_value, SUM(wastage_total) as total_wastage')
            ->first();
        
        /**
         * دریافت پول نقد شرکت = Cache Recieved = p1t1
         * پرداخت پول نقد شرکت = Cache Paid = p1t2
         * طلبات شرکت = Paid Loan = p2t2
         * قرضه شرکت = Recieved Loan = p2t1
         */
        $Cache = DB::table('journals')
        ->selectRaw("
            SUM(CASE WHEN journals.status = 3 THEN amount ELSE 0 END) as total_income,
            SUM(CASE WHEN journals.status = 4 THEN amount ELSE 0 END) as total_expense,
            SUM(CASE WHEN journals.status = 5 THEN amount ELSE 0 END) as total_salary,
            SUM(CASE WHEN journals.transaction_type = 1 AND payment_type = 1 THEN amount ELSE 0 END) as total_incomes,
            SUM(CASE WHEN journals.transaction_type = 2 AND payment_type = 1 THEN amount ELSE 0 END) as total_outcomes
        ")
        ->whereIn('journals.account_type_id', [$company_account_type_id, $banks_account_type_id]) 
        ->where('journals.year', '=', $year)
        ->where('journals.currency_id', '=', $currency_id)
        ->where('journals.branch_id', '=', $branch_id)
        ->where('journals.is_cleared', '=', 0)
        ->first();

        $Loans = DB::table('journals')
        ->selectRaw("
            SUM(CASE WHEN journals.transaction_type = 1 AND payment_type = 1 THEN amount ELSE 0 END) as cache_recieved,
            SUM(CASE WHEN journals.transaction_type = 2 AND payment_type = 1 THEN amount ELSE 0 END) as cache_paid,
            SUM(CASE WHEN journals.transaction_type = 1 AND payment_type = 2 THEN amount ELSE 0 END) as talabat,
            SUM(CASE WHEN journals.transaction_type = 2 AND payment_type = 2 THEN amount ELSE 0 END) as loans
        ")
        ->whereIn('journals.account_type_id', [3, 4]) 
        ->where('journals.year', '=', $year)
        ->where('journals.currency_id', '=', $currency_id)
        ->where('journals.branch_id', '=', $branch_id)
        ->where('journals.is_cleared', '=', 0)
        ->first();

        // مفاد فروشات سالانه
        $sold_profits = SalesDetails::where('branch_id', $branch_id)->whereHas('warehouseSale', function ($query) use ($currency_id, $year) {
            $query->where('currency_id', $currency_id)->where('year', $year)->where('is_cleared', '=', 0);
        })->sum('profit');

        return [
            'total_warehouse_value' => $total_warehouse_value->total_value ?? 0,
            'total_warehouse_wastage' => $total_warehouse_value->total_wastage ?? 0,
            'total_income' => $Cache->total_income ?? 0, 
            'total_expense' => $Cache->total_expense ?? 0, 
            'total_salary' => $Cache->total_salary ?? 0, 
            'total_incomes' => $Cache->total_incomes ?? 0, 
            'total_outcome' => $Cache->total_outcomes ?? 0, 
            'cache_recieved' => $Loans->cache_recieved ?? 0,
            'cache_paid' => $Loans->cache_paid ?? 0,
            'talabat' => $Loans->talabat ?? 0,
            'loans' => $Loans->loans ?? 0,
            'total_loan' => $Loans->total_loan ?? 0,
            'sold_profits' => $sold_profits ?? 0,
        ];
    }



    // ----------------- THIRD TAB ----------------
    function getCashInHandAmount($year, $month, $day, $branch_id)
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
            ->select(
                'journals.currency_id',
                DB::raw('SUM(CASE WHEN journals.transaction_type = 2 AND journals.payment_type = 1 AND journals.branch_id='.$branch_id.' THEN amount ELSE 0 END) as total_paid'),
                DB::raw('SUM(CASE WHEN journals.transaction_type = 1 AND journals.payment_type = 1 AND journals.branch_id='.$branch_id.' THEN amount ELSE 0 END) as total_recieved')
            )
            ->where('journals.account_type_id', $khazana_account_type_id)
            ->where('journals.branch_id', $branch_id)
            ->where('is_cleared', '=', 0)
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


    public function cleanAll()
    {
        // List of tables to truncate
        $tables = [
            'journals',
            'bought_items',
            'bought_item_details',
            // 'bought_item_pre_lists',
            'qalams',
            'models',
            'clearances',
            'sales_details',
            'warehouse_items',
            'warehouse_sales',
            'warehouse_wastage',
        ];

        try {
            // Start the transaction
            DB::beginTransaction();

            foreach ($tables as $table) {
                // Check if the table exists before truncating
                if (DB::getSchemaBuilder()->hasTable($table)) {
                    DB::table($table)->truncate();
                }
            }

            // Commit the transaction
            DB::commit();

            session()->put('notification', [
                'type' => 'success',
                'message' => __('common.deleted_successfully'),
            ]);

            return redirect()->route('home');

        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            DB::rollBack();

            // Log the error
            \Log::error('Error truncating tables: ' . $e->getMessage());

            session()->put('notification', [
                'type' => 'danger',
                'message' => __('common.delete_failed'),
            ]);
            return redirect()->route('home');
        }

    }


    public function currencyConverter(Request $request)
    {
        // Validate input
        $request->validate([
            'from_currency' => 'required|integer',
            'to_currency'   => 'required|integer',
            'fromAmount'    => 'required|numeric|min:0.01'
        ]);

        $fromCurrency = $request->input('from_currency');
        $toCurrency   = $request->input('to_currency');
        $fromAmount   = $request->input('fromAmount');
        $newRate   = $request->input('newRate');


        if($fromCurrency == $toCurrency)
        {
            return response()->json([
                'convertedAmount' => number_format($fromAmount, 2),
                'exchangeRate'    => 0,
            ]);  
        }

        $rate = DB::table('rates')
        ->whereIn('from_currency_id', [$fromCurrency,$toCurrency])
        ->whereIn('to_currency_id', [$fromCurrency,$toCurrency])
        ->first();

        if (!$rate) {
            return response()->json(['error' => __('validate.currency_not_found_converter')], 400);
        }

        
        /**
         * from is payer and no need for conversion
         * to is reciever which may recieve other currency
         * 
         * 1: check if to_currency is equal to greater_account_id, it is greater currency and should use division
         * 2: else it is smaller currency and should use multiplication
         *
         */
        if($rate->greater_account_id == $toCurrency)
        {
            if(!empty($newRate) && intval($newRate) > 0)
            {
               $convertedAmount = $fromAmount / $newRate;
            }
            else
            {
                $convertedAmount = $fromAmount / $rate->to_currency_amount;
            }
        }
        else 
        {
            if(!empty($newRate) && intval($newRate) > 0)
            {
               $convertedAmount = $fromAmount * $newRate;
            }
            else
            {
                $convertedAmount = $fromAmount * $rate->to_currency_amount;
            }
        }

        return response()->json([
            'convertedAmount' => bcdiv($convertedAmount, '1', 2),
            'exchangeRate'    => floatval($newRate) > 0 ? floatval($newRate) : $rate->to_currency_amount,
        ]);       
    }

    public function getBalance(Request $request)
    {
        // Validate input
        $request->validate([
            'currency_id' => 'required|integer',
            'account_id'   => 'required|integer',
        ]);

        $currencyId = $request->input('currency_id');
        $accountId   = $request->input('account_id');
        $branch_id = $this->branch_id;

        if(empty($currencyId) || empty($accountId))
        {
            return response()->json([
                'cur_balance'    => 0,
            ]);  
        }
       
        $totalBalance = 0;
        $finalBalance = 0;
        $isCompanyAccount = Account::whereIn('account_type_id', [1,6])->where('id', $request->account_id)->where('branch_id', $this->branch_id)->exists();

        if($isCompanyAccount)
        {
            // صرف موضوع نقدی حسابات شرکت محاسبه گردد و بیلانس شان کشیده شود
            $totalBalance = DB::table('journals')
            ->select(
                DB::raw('SUM(CASE WHEN journals.transaction_type = 2 AND journals.payment_type = 1 AND journals.branch_id='.$branch_id.' THEN amount ELSE 0 END) as total_paid'),
                DB::raw('SUM(CASE WHEN journals.transaction_type = 1 AND journals.payment_type = 1 AND journals.branch_id='.$branch_id.' THEN amount ELSE 0 END) as total_recieved')
            )
            ->where('journals.account_id', $accountId)
            ->where('journals.branch_id', $branch_id)
            ->where('journals.currency_id', $currencyId)
            ->where('is_cleared', '=', 0)
            ->first();

            $finalBalance = $totalBalance->total_recieved - $totalBalance->total_paid;
        }
        else 
        {
            $totalBalance = DB::table('journals')
            ->select(
                DB::raw('SUM(CASE WHEN transaction_type = 1 AND payment_type = 1 AND journals.branch_id='.$branch_id.' THEN amount ELSE 0 END) as sumCachePaid'),
                DB::raw('SUM(CASE WHEN transaction_type = 2 AND payment_type = 1 AND journals.branch_id='.$branch_id.' THEN amount ELSE 0 END) as sumCacheRecieved'),
                DB::raw('SUM(CASE WHEN transaction_type = 1 AND payment_type = 2 AND journals.branch_id='.$branch_id.' THEN amount ELSE 0 END) as sumLoanRecieved'),
                DB::raw('SUM(CASE WHEN transaction_type = 2 AND payment_type = 2 AND journals.branch_id='.$branch_id.' THEN amount ELSE 0 END) as sumLoanPaid')
            )
            ->where('journals.account_id', $accountId)
            ->where('journals.branch_id', $branch_id)
            ->where('journals.currency_id', $currencyId)
            ->where('is_cleared', '=', 0)
            ->first();
            
            $finalBalance = (($totalBalance->sumCacheRecieved + $totalBalance->sumLoanPaid) - 
            ($totalBalance->sumCachePaid + $totalBalance->sumLoanRecieved));
        }

        
        return response()->json([
            // 'cur_balance' => floatval($totalBalance),
            'cur_balance' => $finalBalance,
            // 'cur_balance' => $totalBalance->sumLoanRecieved,
        ]);  
    }
    

}

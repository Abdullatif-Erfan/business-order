<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth; 
use Morilog\Jalali\Jalalian;
use Illuminate\Support\Facades\DB;
use App\Models\Warehouse\WarehouseSales;
use App\Models\Buy\BoughtItem;
use App\Models\Setting\Currency;
use App\Models\Setting\Account;
use App\Models\Journal\Journal;
use App\Models\Warehouse\SalesDetails;
use App\Models\Setting\OrgBio;

class ProfitAndLossController extends Controller
{
    public function index()
    {
        $data = $this->getIncomeSection();
        $talabat = $this->getTalabat();
        // return ['talabat' => $talabat];

        return view('report.profitAndLoss.list', $talabat);
    }

    private function getTalabat()
    {
        $company_account_type_id = 1; // صرف خزانه شرکت
        $banks_account_type_id = 6; // صرافی و بانک ها
        
            $currencies = DB::table('currencies')->pluck('name', 'id');
            $result = DB::table('journals')
                        ->selectRaw("
                            journals.currency_id,
                            SUM(CASE WHEN journals.transaction_type = 2 AND payment_type = 2 THEN amount ELSE 0 END) as total_talabat
                        ")
                        ->join('accounts', 'accounts.id', '=', 'journals.account_id')
                        ->whereIn('accounts.account_type_id', [$company_account_type_id, $banks_account_type_id])
                        ->where('journals.is_cleared', 0)
                        ->groupBy('journals.currency_id')
                        ->get()
                        ->map(function ($item) use ($currencies) {
                            $item->currency_name = $currencies[$item->currency_id] ?? 'Unknown';
                            return $item;
                    });
         return $result;
    }

    function getIncomeSection()
    {
        $company_account_type_id = 1; // صرف خزانه شرکت
        $banks_account_type_id = 6; // صرافی و بانک ها

        // Fetch currency names
        $currencies = DB::table('currencies')->pluck('name', 'id');

        // Total Goods in Warehouse
        $total_warehouse_value = DB::table('warehouse_items')
            ->selectRaw('currency_id, SUM(available_amount * avg_up) as total_value, SUM(wastage_total) as total_wastage')
            ->where('is_cleared', 0)
            ->groupBy('currency_id')
            ->get()
            ->map(function ($item) use ($currencies) {
                $item->currency_name = $currencies[$item->currency_id] ?? 'Unknown';
                return $item;
            });

        /**
         * دریافت پول نقد شرکت = Cache Recieved = p1t1
         * پرداخت پول نقد شرکت = Cache Paid = p1t2
         * طلبات شرکت = Paid Loan = p2t2
         * قرضه شرکت = Recieved Loan = p2t1
         */

        $result = DB::table('journals')
            ->selectRaw("
                journals.currency_id,
                SUM(CASE WHEN journals.status = 4 THEN amount ELSE 0 END) as total_expense,
                SUM(CASE WHEN journals.status = 3 THEN amount ELSE 0 END) as total_income,
                SUM(CASE WHEN journals.status = 5 THEN amount ELSE 0 END) as total_salary,
                SUM(CASE WHEN journals.status = 7 THEN amount ELSE 0 END) as total_bought,
                SUM(CASE WHEN journals.status = 8 THEN amount ELSE 0 END) as total_sold,
                SUM(CASE WHEN journals.transaction_type = 1 AND payment_type = 1 THEN amount ELSE 0 END) as total_cache_in,
                SUM(CASE WHEN journals.transaction_type = 2 AND payment_type = 1 THEN amount ELSE 0 END) as total_cache_out,
                SUM(CASE WHEN journals.transaction_type = 2 AND payment_type = 2 THEN amount ELSE 0 END) as total_talabat,
                SUM(CASE WHEN journals.transaction_type = 1 AND payment_type = 2 THEN amount ELSE 0 END) as total_loan
            ")
            ->join('accounts', 'accounts.id', '=', 'journals.account_id')
            ->whereIn('accounts.account_type_id', [$company_account_type_id, $banks_account_type_id])
            ->where('journals.is_cleared', 0)
            ->groupBy('journals.currency_id')
            ->get()
            ->map(function ($item) use ($currencies) {
                $item->currency_name = $currencies[$item->currency_id] ?? 'Unknown';
                return $item;
            });

        // Sold Profits   =  مفاد فروشات 
        $sold_profits = SalesDetails::selectRaw('warehouse_sales.currency_id, SUM(profit) as total_profit')
            ->join('warehouse_sales', 'warehouse_sales.id', '=', 'sales_details.warehouse_sales_id')
            ->where('warehouse_sales.is_cleared', 0)
            ->groupBy('warehouse_sales.currency_id')
            ->get()
            ->map(function ($item) use ($currencies) {
                $item->currency_name = $currencies[$item->currency_id] ?? 'Unknown';
                return $item;
            });

        return compact('result', 'sold_profits', 'total_warehouse_value');
    }


}

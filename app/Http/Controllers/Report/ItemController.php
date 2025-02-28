<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting\Currency;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;
use App\Models\Setting\OrgBio;


class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function home()
    {
        return view('report.home');
    }

    /**
     * Show daily reports from bought_items and warehouse_sales
     */
    public function daily(Request $request)
    {
        $orgbios = OrgBio::all();
        $data['year'] = $request->input('year') ?? Jalalian::now()->format('Y');
        $data['month'] = $request->input('month') ?? Jalalian::now()->format('n');
        $data['day'] = Jalalian::now()->format('d');
        $data['currency'] = Currency::select('id', 'name')->orderBy('id', 'ASC')->get()->toArray();
    
        if ($request->has('currency_id')) {
            $data['currency_id'] = $request->input('currency_id');
    
            $cur_currency = Currency::select('id', 'name')
                ->where('id', $data['currency_id'])
                ->orderBy('id', 'ASC')
                ->first(); // Use first() instead of get()->toArray()
    
            $data['currency_name'] = $cur_currency->name ?? null;
            $data['currency_id'] = $cur_currency->id ?? null;
        } else {
            $data['currency_id'] = $data['currency'][0]['id'] ?? null;
            $data['currency_name'] = $data['currency'][0]['name'] ?? null;
        }
    
        $dailyReport = $this->getDailyReports($data['currency_id'],$data['year'],$data['month']);
        // return ['dailyReport', $dailyReport];
        
        return view('report.items.daily', compact('data','dailyReport','orgbios'));
    }
    
    /**
     * Show daily reports from bought_items and warehouse_sales
     */
    public function monthly(Request $request)
    {
        $orgbios = OrgBio::all();
        $data['year'] = $request->input('year') ?? Jalalian::now()->format('Y');
        $data['currency'] = Currency::select('id', 'name')->orderBy('id', 'ASC')->get()->toArray();
        if ($request->has('currency_id')) {
            $data['currency_id'] = $request->input('currency_id');
    
            $cur_currency = Currency::select('id', 'name')
                ->where('id', $data['currency_id'])
                ->orderBy('id', 'ASC')
                ->first(); // Use first() instead of get()->toArray()
    
            $data['currency_name'] = $cur_currency->name ?? null;
            $data['currency_id'] = $cur_currency->id ?? null;
        } else {
            $data['currency_id'] = $data['currency'][0]['id'] ?? null;
            $data['currency_name'] = $data['currency'][0]['name'] ?? null;
        }
    
        $monthlyReport = $this->getMonthlyReports($data['currency_id'],$data['year']);
        // return ['monthlyReport', $monthlyReport];
        
        return view('report.items.monthly', compact('data','monthlyReport','orgbios'));
    }

    /**
     * Show Yearly reports from bought_items and warehouse_sales
     */
    public function yearly(Request $request)
    {
        $orgbios = OrgBio::all();
        $data['currency'] = Currency::select('id', 'name')->orderBy('id', 'ASC')->get()->toArray();
        if ($request->has('currency_id')) {
            $data['currency_id'] = $request->input('currency_id');
    
            $cur_currency = Currency::select('id', 'name')
                ->where('id', $data['currency_id'])
                ->orderBy('id', 'ASC')
                ->first(); // Use first() instead of get()->toArray()
    
            $data['currency_name'] = $cur_currency->name ?? null;
            $data['currency_id'] = $cur_currency->id ?? null;
        } else {
            $data['currency_id'] = $data['currency'][0]['id'] ?? null;
            $data['currency_name'] = $data['currency'][0]['name'] ?? null;
        }
    
        $yearlyReport = $this->getYearlyReports($data['currency_id']);
        // return ['yearlyReport', $yearlyReport];
        
        return view('report.items.yearly', compact('data','yearlyReport','orgbios'));
    }


    /**
     * Get Daily Reports
     */
    private function getDailyReportsBkp($currencyId,$year,$month)
    {
        $query1 = DB::table('warehouse_items')
            ->selectRaw("
                day AS report_day,
                SUM(available_amount * avg_up) AS total_warehouse_value,
                SUM(wastage_total) AS total_warehouse_wastage,
                NULL AS total_sales_payable, NULL AS total_sales_curpay, NULL AS total_sales_remained, NULL AS total_sales_profit,
                NULL AS total_bought_payable, NULL AS total_bought_curpay, NULL AS total_bought_remained, NULL AS total_bought_transport
            ")
            ->where('year', $year)
            ->where('month', $month)
            ->where('currency_id', $currencyId)
            ->groupBy('day');

        $query2 = DB::table('warehouse_sales')
            ->selectRaw("
                day AS report_day,
                NULL AS total_warehouse_value, NULL AS total_warehouse_wastage,
                SUM(payable) AS total_sales_payable,
                SUM(cur_pay) AS total_sales_curpay,
                SUM(remained) AS total_sales_remained,
                (SELECT SUM(profit) FROM sales_details WHERE sales_details.billno = warehouse_sales.billno) AS total_sales_profit,
                NULL AS total_bought_payable, NULL AS total_bought_curpay, NULL AS total_bought_remained, NULL AS total_bought_transport
            ")
            ->where('year', $year)
            ->where('month', $month)
            ->where('currency_id', $currencyId)
            ->groupBy('day', 'billno');

        $query3 = DB::table('bought_items')
            ->selectRaw("
                day AS report_day,
                NULL AS total_warehouse_value, NULL AS total_warehouse_wastage,
                NULL AS total_sales_payable, NULL AS total_sales_curpay, NULL AS total_sales_remained, NULL AS total_sales_profit,
                SUM(payable) AS total_bought_payable,
                SUM(cur_pay) AS total_bought_curpay,
                SUM(remained) AS total_bought_remained,
                SUM(trans_spend) AS total_bought_transport
            ")
            ->where('year', $year)
            ->where('month', $month)
            ->where('currency_id', $currencyId)
            ->groupBy('day');

        // Combine using UNION
        $finalQuery = $query1->union($query2)->union($query3);

        // Execute query
        $results = DB::table(DB::raw("({$finalQuery->toSql()}) as combined_reports"))
            ->mergeBindings($finalQuery)
            ->get();

        return $results;

    }


    public function getDailyReports($currency_id, $year, $month)
    {
        $subQueryAllDays = DB::table('warehouse_items')
            ->select('day as report_day')
            ->where('year', $year)
            ->where('month', $month)
            ->where('currency_id', $currency_id)
            ->union(
                DB::table('warehouse_sales')
                    ->select('day')
                    ->where('year', $year)
                    ->where('month', $month)
                    ->where('currency_id', $currency_id)
            )
            ->union(
                DB::table('bought_items')
                    ->select('day')
                    ->where('year', $year)
                    ->where('month', $month)
                    ->where('currency_id', $currency_id)
            );

        $warehouseQuery = DB::table('warehouse_items')
            ->select(
                'day',
                DB::raw('SUM(available_amount * avg_up) AS total_warehouse_value'),
                DB::raw('SUM(wastage_total) AS total_warehouse_wastage')
            )
            ->where('year', $year)
            ->where('month', $month)
            ->where('currency_id', $currency_id)
            ->groupBy('day');

        $salesQuery = DB::table('warehouse_sales AS ws')
            ->leftJoin('sales_details AS sd', 'ws.billno', '=', 'sd.billno') // Join sales_details to aggregate profit
            ->select(
                'ws.day',
                DB::raw('SUM(ws.payable) AS total_sales_payable'),
                DB::raw('SUM(ws.cur_pay) AS total_sales_curpay'),
                DB::raw('SUM(ws.remained) AS total_sales_remained'),
                DB::raw('SUM(sd.profit) AS total_sales_profit') // Summing profit correctly
            )
            ->where('ws.year', $year)
            ->where('ws.month', $month)
            ->where('ws.currency_id', $currency_id)
            ->groupBy('ws.day'); // Grouping by day only

        $boughtQuery = DB::table('bought_items')
            ->select(
                'day',
                DB::raw('SUM(payable) AS total_bought_payable'),
                DB::raw('SUM(cur_pay) AS total_bought_curpay'),
                DB::raw('SUM(remained) AS total_bought_remained'),
                DB::raw('SUM(trans_spend) AS total_bought_transport')
            )
            ->where('year', $year)
            ->where('month', $month)
            ->where('currency_id', $currency_id)
            ->groupBy('day');

        return DB::table(DB::raw("({$subQueryAllDays->toSql()}) as all_days"))
            ->mergeBindings($subQueryAllDays)
            ->leftJoin(DB::raw("({$warehouseQuery->toSql()}) as w"), 'all_days.report_day', '=', 'w.day')
            ->mergeBindings($warehouseQuery)
            ->leftJoin(DB::raw("({$salesQuery->toSql()}) as s"), 'all_days.report_day', '=', 's.day')
            ->mergeBindings($salesQuery)
            ->leftJoin(DB::raw("({$boughtQuery->toSql()}) as b"), 'all_days.report_day', '=', 'b.day')
            ->mergeBindings($boughtQuery)
            ->select(
                'all_days.report_day',
                DB::raw('COALESCE(w.total_warehouse_value, 0) AS total_warehouse_value'),
                DB::raw('COALESCE(w.total_warehouse_wastage, 0) AS total_warehouse_wastage'),
                DB::raw('COALESCE(s.total_sales_payable, 0) AS total_sales_payable'),
                DB::raw('COALESCE(s.total_sales_curpay, 0) AS total_sales_curpay'),
                DB::raw('COALESCE(s.total_sales_remained, 0) AS total_sales_remained'),
                DB::raw('COALESCE(s.total_sales_profit, 0) AS total_sales_profit'),
                DB::raw('COALESCE(b.total_bought_payable, 0) AS total_bought_payable'),
                DB::raw('COALESCE(b.total_bought_curpay, 0) AS total_bought_curpay'),
                DB::raw('COALESCE(b.total_bought_remained, 0) AS total_bought_remained'),
                DB::raw('COALESCE(b.total_bought_transport, 0) AS total_bought_transport')
            )
            ->orderBy('all_days.report_day')
            ->get();
    }

    /**
    * Get Monthly Reports
    */
    public function getMonthlyReports($currency_id, $year)
    {
        $warehouseQuery = DB::table('warehouse_items')
            ->select(
                'month',
                DB::raw('SUM(available_amount * avg_up) AS total_warehouse_value'),
                DB::raw('SUM(wastage_total) AS total_warehouse_wastage')
            )
            ->where('year', $year)
            ->where('currency_id', $currency_id)
            ->groupBy('month');

        $salesQuery = DB::table('warehouse_sales AS ws')
            ->leftJoin('sales_details AS sd', 'ws.billno', '=', 'sd.billno') // Join sales_details to aggregate profit
            ->select(
                'ws.month',
                DB::raw('SUM(ws.payable) AS total_sales_payable'),
                DB::raw('SUM(ws.cur_pay) AS total_sales_curpay'),
                DB::raw('SUM(ws.remained) AS total_sales_remained'),
                DB::raw('SUM(sd.profit) AS total_sales_profit') // Summing profit correctly
            )
            ->where('ws.year', $year)
            ->where('ws.currency_id', $currency_id)
            ->groupBy('ws.month'); // Grouping by month instead of day

        $boughtQuery = DB::table('bought_items')
            ->select(
                'month',
                DB::raw('SUM(payable) AS total_bought_payable'),
                DB::raw('SUM(cur_pay) AS total_bought_curpay'),
                DB::raw('SUM(remained) AS total_bought_remained'),
                DB::raw('SUM(trans_spend) AS total_bought_transport')
            )
            ->where('year', $year)
            ->where('currency_id', $currency_id)
            ->groupBy('month');

        return DB::table(DB::raw("({$warehouseQuery->toSql()}) as w"))
            ->mergeBindings($warehouseQuery)
            ->leftJoin(DB::raw("({$salesQuery->toSql()}) as s"), 'w.month', '=', 's.month')
            ->mergeBindings($salesQuery)
            ->leftJoin(DB::raw("({$boughtQuery->toSql()}) as b"), 'w.month', '=', 'b.month')
            ->mergeBindings($boughtQuery)
            ->select(
                'w.month',
                DB::raw('COALESCE(w.total_warehouse_value, 0) AS total_warehouse_value'),
                DB::raw('COALESCE(w.total_warehouse_wastage, 0) AS total_warehouse_wastage'),
                DB::raw('COALESCE(s.total_sales_payable, 0) AS total_sales_payable'),
                DB::raw('COALESCE(s.total_sales_curpay, 0) AS total_sales_curpay'),
                DB::raw('COALESCE(s.total_sales_remained, 0) AS total_sales_remained'),
                DB::raw('COALESCE(s.total_sales_profit, 0) AS total_sales_profit'),
                DB::raw('COALESCE(b.total_bought_payable, 0) AS total_bought_payable'),
                DB::raw('COALESCE(b.total_bought_curpay, 0) AS total_bought_curpay'),
                DB::raw('COALESCE(b.total_bought_remained, 0) AS total_bought_remained'),
                DB::raw('COALESCE(b.total_bought_transport, 0) AS total_bought_transport')
            )
            ->orderBy('w.month')
            ->get();
    }

    /**
    * Get yearly Reports
    */
    public function getYearlyReports($currency_id)
    {
        $warehouseQuery = DB::table('warehouse_items')
            ->select(
                'year',
                DB::raw('SUM(available_amount * avg_up) AS total_warehouse_value'),
                DB::raw('SUM(wastage_total) AS total_warehouse_wastage')
            )
            ->where('currency_id', $currency_id)
            ->groupBy('year');

        $salesQuery = DB::table('warehouse_sales AS ws')
            ->leftJoin('sales_details AS sd', 'ws.billno', '=', 'sd.billno') // Join sales_details to aggregate profit
            ->select(
                'ws.year',
                DB::raw('SUM(ws.payable) AS total_sales_payable'),
                DB::raw('SUM(ws.cur_pay) AS total_sales_curpay'),
                DB::raw('SUM(ws.remained) AS total_sales_remained'),
                DB::raw('SUM(sd.profit) AS total_sales_profit') 
            )
            ->where('ws.currency_id', $currency_id)
            ->groupBy('ws.year'); 

        $boughtQuery = DB::table('bought_items')
            ->select(
                'year',
                DB::raw('SUM(payable) AS total_bought_payable'),
                DB::raw('SUM(cur_pay) AS total_bought_curpay'),
                DB::raw('SUM(remained) AS total_bought_remained'),
                DB::raw('SUM(trans_spend) AS total_bought_transport')
            )
            ->where('currency_id', $currency_id)
            ->groupBy('year');

        return DB::table(DB::raw("({$warehouseQuery->toSql()}) as w"))
            ->mergeBindings($warehouseQuery)
            ->leftJoin(DB::raw("({$salesQuery->toSql()}) as s"), 'w.year', '=', 's.year')
            ->mergeBindings($salesQuery)
            ->leftJoin(DB::raw("({$boughtQuery->toSql()}) as b"), 'w.year', '=', 'b.year')
            ->mergeBindings($boughtQuery)
            ->select(
                'w.year',
                DB::raw('COALESCE(w.total_warehouse_value, 0) AS total_warehouse_value'),
                DB::raw('COALESCE(w.total_warehouse_wastage, 0) AS total_warehouse_wastage'),
                DB::raw('COALESCE(s.total_sales_payable, 0) AS total_sales_payable'),
                DB::raw('COALESCE(s.total_sales_curpay, 0) AS total_sales_curpay'),
                DB::raw('COALESCE(s.total_sales_remained, 0) AS total_sales_remained'),
                DB::raw('COALESCE(s.total_sales_profit, 0) AS total_sales_profit'),
                DB::raw('COALESCE(b.total_bought_payable, 0) AS total_bought_payable'),
                DB::raw('COALESCE(b.total_bought_curpay, 0) AS total_bought_curpay'),
                DB::raw('COALESCE(b.total_bought_remained, 0) AS total_bought_remained'),
                DB::raw('COALESCE(b.total_bought_transport, 0) AS total_bought_transport')
            )
            ->orderBy('w.year')
            ->get();
    }

    
}

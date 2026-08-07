<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting\Currency;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Setting\OrgBio;

class ItemController extends Controller
{
    protected $isAdmin;
    
    public function __construct()
    {
        if (auth()->check()) {
            $this->isAdmin = session('isAdmin', auth()->user()->isAdmin == 1);
        } else {
            $this->isAdmin = false;
        }
    }
    
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
        $currencies = Currency::select('id', 'name')->orderBy('id', 'ASC')->get();
        
        // ✅ Set default date range to last 7 days
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays(6); // Last 7 days (including today)
        
        $data['start_date'] = $request->input('start_date') ?? $startDate->format('Y-m-d');
        $data['end_date'] = $request->input('end_date') ?? $endDate->format('Y-m-d');
        $data['currency_id'] = $request->input('currency_id') ?? $currencies->first()->id ?? 1;
        
        // Get currency name
        $currency = Currency::find($data['currency_id']);
        $data['currency_name'] = $currency->name ?? '';

        return view('report.items.daily', compact('data', 'orgbios', 'currencies'));
    }

    /**
     * Get daily report data via AJAX
     */
    public function getDailyData(Request $request)
    {
        $currency_id = $request->input('currency_id');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        // ✅ If no date range provided, use default (last 7 days)
        if (!$start_date || !$end_date) {
            $endDate = Carbon::now();
            $startDate = Carbon::now()->subDays(6);
            $start_date = $startDate->format('Y-m-d');
            $end_date = $endDate->format('Y-m-d');
        }

        // ✅ If no currency selected, use default (first currency)
        if (!$currency_id) {
            $firstCurrency = Currency::first();
            $currency_id = $firstCurrency->id ?? 1;
        }

        // Validate dates
        if ($start_date > $end_date) {
            return response()->json([
                'status' => 'error',
                'message' => __('reports.start_date_cannot_be_after_end_date')
            ], 400);
        }

        $dailyReport = $this->getDailyReports($currency_id, $start_date, $end_date);
        
        // Calculate totals
        $totals = [
            'total_sales_payable' => 0,
            'total_sales_curpay' => 0,
            'total_sales_remained' => 0,
            'total_sales_profit' => 0,
            'total_bought_payable' => 0,
            'total_bought_curpay' => 0,
            'total_bought_remained' => 0,
        ];

        foreach ($dailyReport as $row) {
            $totals['total_sales_payable'] += $row->total_sales_payable ?? 0;
            $totals['total_sales_curpay'] += $row->total_sales_curpay ?? 0;
            $totals['total_sales_remained'] += $row->total_sales_remained ?? 0;
            $totals['total_sales_profit'] += $row->total_sales_profit ?? 0;
            $totals['total_bought_payable'] += $row->total_bought_payable ?? 0;
            $totals['total_bought_curpay'] += $row->total_bought_curpay ?? 0;
            $totals['total_bought_remained'] += $row->total_bought_remained ?? 0;
        }

        // Format data for DataTable
        $formattedData = $dailyReport->map(function ($row) {
            $date = Carbon::parse($row->report_date);
            $dayName = $date->format('Y-m-d') . ' - ' . $date->format('l');
            
            return [
                'report_date' => $row->report_date,
                'day_name' => $dayName,
                'is_today' => ($row->report_date == Carbon::now()->format('Y-m-d')) ? true : false,
                'total_bought_payable' => number_format($row->total_bought_payable ?? 0, 2),
                'total_bought_curpay' => number_format($row->total_bought_curpay ?? 0, 2),
                'total_bought_remained' => number_format($row->total_bought_remained ?? 0, 2),
                'total_sales_payable' => number_format($row->total_sales_payable ?? 0, 2),
                'total_sales_curpay' => number_format($row->total_sales_curpay ?? 0, 2),
                'total_sales_remained' => number_format($row->total_sales_remained ?? 0, 2),
                'total_sales_profit' => number_format($row->total_sales_profit ?? 0, 2),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $formattedData,
            'totals' => $totals,
            'total_days' => $dailyReport->count(),
            'start_date' => $start_date,
            'end_date' => $end_date,
            'currency_id' => $currency_id,
        ]);
    }

    public function getDailyReports($currency_id, $startDate, $endDate)
    {
        // Get all unique days from multiple sources within date range
        $subQueryAllDays = DB::table('warehouse_items')
            ->select(DB::raw("DATE(idate) as report_date"))
            ->where('currency_id', $currency_id)
            ->whereBetween('idate', [$startDate, $endDate])
            ->union(
                DB::table('warehouse_sales')
                    ->select(DB::raw("DATE(idate) as report_date"))
                    ->where('currency_id', $currency_id)
                    ->whereBetween('idate', [$startDate, $endDate])
            )
            ->union(
                DB::table('bought_items')
                    ->select(DB::raw("DATE(idate) as report_date"))
                    ->where('currency_id', $currency_id)
                    ->whereBetween('idate', [$startDate, $endDate])
            );

        // Get sales data per day
        $salesQuery = DB::table('warehouse_sales AS ws')
            ->leftJoin('sales_details AS sd', 'ws.id', '=', 'sd.warehouse_sales_id')
            ->select(
                DB::raw("DATE(ws.idate) as report_date"),
                DB::raw('SUM(ws.total) AS total_sales_payable'),
                DB::raw('SUM(ws.cur_pay) AS total_sales_curpay'),
                DB::raw('SUM(ws.remained) AS total_sales_remained'),
                DB::raw('COALESCE(SUM(sd.profit), 0) AS total_sales_profit')
            )
            ->where('ws.currency_id', $currency_id)
            ->whereBetween('ws.idate', [$startDate, $endDate])
            ->groupBy(DB::raw("DATE(ws.idate)"));

        // Get bought data per day
        $boughtQuery = DB::table('bought_items')
            ->select(
                DB::raw("DATE(idate) as report_date"),
                DB::raw('SUM(total) AS total_bought_payable'),
                DB::raw('SUM(cur_pay) AS total_bought_curpay'),
                DB::raw('SUM(remained) AS total_bought_remained')
            )
            ->where('currency_id', $currency_id)
            ->whereBetween('idate', [$startDate, $endDate])
            ->groupBy(DB::raw("DATE(idate)"));

        // Final Query: Combine all data sources
        return DB::table(DB::raw("({$subQueryAllDays->toSql()}) as all_days"))
            ->mergeBindings($subQueryAllDays)
            ->leftJoin(DB::raw("({$salesQuery->toSql()}) as s"), 'all_days.report_date', '=', 's.report_date')
            ->mergeBindings($salesQuery)
            ->leftJoin(DB::raw("({$boughtQuery->toSql()}) as b"), 'all_days.report_date', '=', 'b.report_date')
            ->mergeBindings($boughtQuery)
            ->select(
                'all_days.report_date',
                DB::raw('COALESCE(s.total_sales_payable, 0) AS total_sales_payable'),
                DB::raw('COALESCE(s.total_sales_curpay, 0) AS total_sales_curpay'),
                DB::raw('COALESCE(s.total_sales_remained, 0) AS total_sales_remained'),
                DB::raw('COALESCE(s.total_sales_profit, 0) AS total_sales_profit'),
                DB::raw('COALESCE(b.total_bought_payable, 0) AS total_bought_payable'),
                DB::raw('COALESCE(b.total_bought_curpay, 0) AS total_bought_curpay'),
                DB::raw('COALESCE(b.total_bought_remained, 0) AS total_bought_remained')
            )
            ->orderBy('all_days.report_date')
            ->get();
    }
}
<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Setting\Account;
use App\Models\Setting\OrgBio;
use App\Models\Order\Order;
use Carbon\Carbon;

class HomeController extends Controller
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
    
    public function index(Request $request)
    {        
        // return ['user',auth()->user()];
        $orgBio = OrgBio::first(); 
        $drivers = Account::select('id','name')->where('account_type_id',2)->get();
        $suppliers = Account::select('id','name')->where('account_type_id',4)->get();
        
        $orders = $this->getDashboardOrdersData($request);
        // return ['orders', $orders];

        return view('dashboard.dashboard', compact('orders', 'orgBio', 'drivers', 'suppliers'));
    }

    public function getDashboardOrders(Request $request)
    {
        try {
            $orders = $this->getDashboardOrdersData($request);

            // render html
            $html = view('dashboard.cards.order',compact('orders'))->render();
            
            return response()->json([
                'status' => 'success',
                'data' => $html
            ]);
        } catch (\Exception $e) {
            Log::error('Dashboard Orders Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get dashboard orders.'
            ], 500);
        }
    }

    private function getDashboardOrdersData(Request $request)
    {
        $query = Order::query();

        // Apply filters
        if ($request->supplier_id) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->driver_id || $request->employee_id) {
            $employeeId = $request->driver_id ?? $request->employee_id;
            $query->where('employee_id', $employeeId);
        }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('idate', [$request->start_date, $request->end_date]);
        } elseif ($request->start_date) {
            $query->where('idate', '>=', $request->start_date);
        } elseif ($request->end_date) {
            $query->where('idate', '<=', $request->end_date);
        }

        // Get summary using distinct ord_num
        $result = $query->select(
            DB::raw("COUNT(DISTINCT ord_num) as total_orders"),
            DB::raw("COUNT(DISTINCT CASE WHEN state = 0 THEN ord_num END) as total_draft"),
            DB::raw("COUNT(DISTINCT CASE WHEN state = 1 THEN ord_num END) as total_new"),
            DB::raw("COUNT(DISTINCT CASE WHEN state = 2 THEN ord_num END) as total_cancelled"),
            DB::raw("COUNT(DISTINCT CASE WHEN state = 3 THEN ord_num END) as total_completed")
        )
        ->first();

        $totalNew = (int) ($result->total_new ?? 0);
        $totalCompleted = (int) ($result->total_completed ?? 0);
        
        // Calculate percentage
        $progressPercentage = $totalNew > 0 ? round(($totalCompleted / $totalNew) * 100) : 0;

        return [
                'total_orders' => (int) ($result->total_orders ?? 0),
                'total_draft' => (int) ($result->total_draft ?? 0),
                'total_new' => (int) ($result->total_new ?? 0),
                'total_cancelled' => (int) ($result->total_cancelled ?? 0),
                'total_completed' => (int) ($result->total_completed ?? 0),
                'progress_percentage' => $progressPercentage,
            ];
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

        if(empty($currencyId) || empty($accountId))
        {
            return response()->json([
                'cur_balance'    => 0,
            ]);  
        }
       
        $totalBalance = 0;
        $finalBalance = 0;
        $isCompanyAccount = Account::whereIn('account_type_id', [1,6])->where('id', $request->account_id)->exists();

        if($isCompanyAccount)
        {
            // صرف موضوع نقدی حسابات شرکت محاسبه گردد و بیلانس شان کشیده شود
            $totalBalance = DB::table('journals')
            ->select(
                DB::raw('SUM(CASE WHEN journals.transaction_type = 2 AND journals.payment_type = 1 THEN amount ELSE 0 END) as total_paid'),
                DB::raw('SUM(CASE WHEN journals.transaction_type = 1 AND journals.payment_type = 1 THEN amount ELSE 0 END) as total_recieved')
            )
            ->where('journals.account_id', $accountId)
            ->where('journals.currency_id', $currencyId)
            ->where('is_cleared', '=', 0)
            ->first();

            $finalBalance = $totalBalance->total_recieved - $totalBalance->total_paid;
        }
        else 
        {
            $totalBalance = DB::table('journals')
            ->select(
                DB::raw('SUM(CASE WHEN transaction_type = 1 AND payment_type = 1  THEN amount ELSE 0 END) as sumCachePaid'),
                DB::raw('SUM(CASE WHEN transaction_type = 2 AND payment_type = 1  THEN amount ELSE 0 END) as sumCacheRecieved'),
                DB::raw('SUM(CASE WHEN transaction_type = 1 AND payment_type = 2  THEN amount ELSE 0 END) as sumLoanRecieved'),
                DB::raw('SUM(CASE WHEN transaction_type = 2 AND payment_type = 2  THEN amount ELSE 0 END) as sumLoanPaid')
            )
            ->where('journals.account_id', $accountId)
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

     public function cleanAll()
    {
        $tables = [
            'journals',
            'bought_items',
            'bought_item_details',
            'clearances',
            'sales_details',
            'warehouse_items',
            'warehouse_sales',
            'warehouse_wastage',
            'buy_invoices',
            'buy_invoice_items',
            'buy_invoice_payments',
            'sales_invoices',
            'sales_invoice_items',
            'sales_invoice_payments',
        ];

        try {
            foreach ($tables as $table) {
                if (DB::getSchemaBuilder()->hasTable($table)) {
                    DB::table($table)->truncate();
                }
            }

            session()->put('notification', [
                'type' => 'success',
                'message' => __('common.deleted_successfully'),
            ]);

        } catch (\Exception $e) {
            \Log::error('Error truncating tables: ' . $e->getMessage());

            session()->put('notification', [
                'type' => 'danger',
                'message' => __('common.delete_failed'),
            ]);
        }

        return redirect()->route('home');
    }

}
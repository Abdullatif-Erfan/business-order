<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Setting\Account;
use App\Models\Setting\OrgBio;
use App\Models\Order\Order;
use App\Models\Buy\BoughtItem;
use App\Models\Buy\BoughtReturn;
use App\Models\Warehouse\WarehouseSales;
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
        $search['start_date'] = $request->input('start_date') ?? Carbon::now()->format('Y-m-d');
        $search['end_date'] = $request->input('end_date');
        $search['supplier_id'] = $request->input('supplier_id') ?? 0;
        $search['driver_id'] = $request->input('driver_id') ?? 0;
        $search['customer_id'] = $request->input('customer_id') ?? 0;

        $orgBio = OrgBio::first(); 
        $drivers = Account::select('id','name')->where('account_type_id',2)->get();
        $suppliers = Account::select('id','name')->where('account_type_id',4)->get();
        $customers = Account::select('id','name')->where('account_type_id',3)->get(); // Assuming account_type_id 3 is for customers
        
        $orders = $this->getDashboardOrdersData($request);
        $bought = $this->getDashboardBoughtsData($request);
        $sales = $this->getDashboardSalesData($request);
        $returns = $this->getDashboardReturnsData($request);

        return view('dashboard.dashboard', compact('orders', 'bought', 'sales', 'orgBio', 'drivers', 'suppliers', 'customers','returns', 'search'));
    }

    // ____ ORDER _______________________________________
    public function getDashboardOrders(Request $request)
    {
        try {
            $orders = $this->getDashboardOrdersData($request);
            $html = view('dashboard.cards.order', compact('orders'))->render();
            
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

        if ($request->supplier_id) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->driver_id) {
            $query->where('employee_id', $request->driver_id);
        }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('idate', [$request->start_date, $request->end_date]);
        } elseif ($request->start_date) {
            $query->where('idate', '>=', $request->start_date);
        } elseif ($request->end_date) {
            $query->where('idate', '<=', $request->end_date);
        }

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
        $totalNewAndCompleted = $totalNew + $totalCompleted;
        // x = done * 100 / totalAmount || x = 1 * 100 / 3 = 33;
        $progressPercentage = $totalNew > 0 ? round(($result->total_completed * 100) / $totalNewAndCompleted) : 0;

        return [
            'total_orders' => (int) ($result->total_orders ?? 0),
            'total_draft' => (int) ($result->total_draft ?? 0),
            'total_new' => (int) ($result->total_new ?? 0),
            'total_cancelled' => (int) ($result->total_cancelled ?? 0),
            'total_completed' => (int) ($result->total_completed ?? 0),
            'progress_percentage' => $progressPercentage,
        ];
    }

    // ____ BUY (BOUGHT) _______________________________________
    public function getDashboardBoughts(Request $request)
    {
        try {
            $bought = $this->getDashboardBoughtsData($request);
            $html = view('dashboard.cards.buy', compact('bought'))->render();
            
            return response()->json([
                'status' => 'success',
                'data' => $html
            ]);
        } catch (\Exception $e) {
            Log::error('Dashboard Bought Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get dashboard bought data.'
            ], 500);
        }
    }

    private function getDashboardBoughtsData(Request $request)
    {
        $query = DB::table('bought_items');

        if ($request->supplier_id) {
            $query->where('supplier_account_id', $request->supplier_id);
        }

        if ($request->driver_id) {
            $query->where('user_id', $request->driver_id);
        }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('idate', [$request->start_date, $request->end_date]);
        } elseif ($request->start_date) {
            $query->where('idate', '>=', $request->start_date);
        } elseif ($request->end_date) {
            $query->where('idate', '<=', $request->end_date);
        }

        $result = $query->select(
            DB::raw("COUNT(*) as total_bought"),
            DB::raw("COALESCE(SUM(total), 0) as total_amount"),
            DB::raw("COALESCE(SUM(cur_pay), 0) as total_paid"),
            DB::raw("COALESCE(SUM(remained), 0) as total_remained"),
            DB::raw("COUNT(CASE WHEN remained = 0 THEN 1 END) as fully_paid"),
            DB::raw("COUNT(CASE WHEN remained > 0 THEN 1 END) as partial_paid")
        )
        ->first();

        $totalAmount = (float) ($result->total_amount ?? 0);
        $totalPaid = (float) ($result->total_paid ?? 0);
        
        $progressPercentage = $totalAmount > 0 ? round(($result->fully_paid * 100) / $result->total_bought) : 0;



        return [
            'total_bought' => (int) ($result->total_bought ?? 0),
            'total_amount' => $totalAmount,
            'total_paid' => $totalPaid,
            'total_remained' => (float) ($result->total_remained ?? 0),
            'fully_paid' => (int) ($result->fully_paid ?? 0),
            'partial_paid' => (int) ($result->partial_paid ?? 0),
            'progress_percentage' => $progressPercentage,
        ];
    }

    // ____ SALES (WAREHOUSE_SALES) _______________________________________
    public function getDashboardSales(Request $request)
    {
        try 
        {
            $sales = $this->getDashboardSalesData($request);
            $html = view('dashboard.cards.sales', compact('sales'))->render();
            
            return response()->json([
                'status' => 'success',
                'data' => $html
            ]);
        } catch (\Exception $e) {
            Log::error('Dashboard Sales Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get dashboard sales data.'
            ], 500);
        }
    }

    private function getDashboardSalesData(Request $request)
    {
        $query = WarehouseSales::query();
        

        // Apply customer filter - driver_id is the customer for sales
        if ($request->filled('driver_id') && $request->driver_id > 0) {
            $query->where('customer_account_id', $request->driver_id);
        }

        // Date filter with proper formatting
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->format('Y-m-d');
            $endDate = Carbon::parse($request->end_date)->format('Y-m-d');
            $query->whereBetween('idate', [$startDate, $endDate]);
        } elseif ($request->filled('start_date')) {
            $startDate = Carbon::parse($request->start_date)->format('Y-m-d');
            $query->where('idate', '>=', $startDate);
        } elseif ($request->filled('end_date')) {
            $endDate = Carbon::parse($request->end_date)->format('Y-m-d');
            $query->where('idate', '<=', $endDate);
        }

        // Log the SQL query for debugging
        // \Log::info('Sales SQL:', [
        //     'sql' => $query->toSql(), 
        //     'bindings' => $query->getBindings()
        // ]);

        // Get summary statistics
        $result = $query->select(
            DB::raw("COUNT(*) as total_sales"),
            DB::raw("COALESCE(SUM(total), 0) as total_amount"),        
            DB::raw("COALESCE(SUM(cur_pay), 0) as total_paid"),        
            DB::raw("COALESCE(SUM(remained), 0) as total_remained"),   
            DB::raw("COUNT(CASE WHEN remained = 0 THEN 1 END) as fully_paid"),
            DB::raw("COUNT(CASE WHEN remained > 0 THEN 1 END) as partial_paid"),
            DB::raw("COUNT(CASE WHEN is_cleared = 1 THEN 1 END) as cleared"),
            DB::raw("COUNT(CASE WHEN is_cleared = 0 THEN 1 END) as not_cleared")
        )
        ->first();

        // Log the result
        // \Log::info('Sales Result:', (array) $result);

        // All values are now guaranteed to be integers or floats (not null)
        $totalSales = (int) ($result->total_sales ?? 0);
        $totalAmount = (float) ($result->total_amount ?? 0);      
        $totalPaid = (float) ($result->total_paid ?? 0);          
        $totalRemained = (float) ($result->total_remained ?? 0);  
        
        $progressPercentage = $totalAmount > 0 ? round(($result->fully_paid  * 100) / $totalSales) : 0;
        

        return [
            'total_sales' => $totalSales,
            'total_amount' => $totalAmount,
            'total_paid' => $totalPaid,
            'total_remained' => $totalRemained,
            'fully_paid' => (int) ($result->fully_paid ?? 0),
            'partial_paid' => (int) ($result->partial_paid ?? 0),
            'not_cleared' => (int) ($result->not_cleared ?? 0),
            'progress_percentage' => $progressPercentage,
        ];
    }


    // ____ RETURNS (BOUGHT_RETURNS) _______________________________________
    public function getDashboardReturns(Request $request)
    {
        try {
            $returns = $this->getDashboardReturnsData($request);
            $html = view('dashboard.cards.returns', compact('returns'))->render();
            
            return response()->json([
                'status' => 'success',
                'data' => $html
            ]);
        } catch (\Exception $e) {
            Log::error('Dashboard Returns Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get dashboard returns data.'
            ], 500);
        }
    }
    
    private function getDashboardReturnsData(Request $request)
    {
        $query = BoughtReturn::query();

        // Apply filters
        if ($request->filled('supplier_id') && $request->supplier_id > 0) {
            $query->where('supplier_account_id', $request->supplier_id);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->format('Y-m-d');
            $endDate = Carbon::parse($request->end_date)->format('Y-m-d');
            $query->whereBetween('return_date', [$startDate, $endDate]);
        } elseif ($request->filled('start_date')) {
            $startDate = Carbon::parse($request->start_date)->format('Y-m-d');
            $query->where('return_date', '>=', $startDate);
        } elseif ($request->filled('end_date')) {
            $endDate = Carbon::parse($request->end_date)->format('Y-m-d');
            $query->where('return_date', '<=', $endDate);
        }

        // Get summary statistics
        $result = $query->select(
            DB::raw("COUNT(*) as total_returns"),
            DB::raw("COALESCE(SUM(quantity), 0) as total_quantity"),
            DB::raw("COALESCE(SUM(total), 0) as total_amount"),
            DB::raw("COUNT(DISTINCT billno) as total_bills"),
            DB::raw("COUNT(DISTINCT supplier_account_id) as total_suppliers"),
            DB::raw("COUNT(DISTINCT user_id) as total_customers")

        )
        ->first();

        $totalReturns = (int) ($result->total_returns ?? 0);
        $totalQuantity = (float) ($result->total_quantity ?? 0);
        $totalAmount = (float) ($result->total_amount ?? 0);

        return [
            'total_returns' => $totalReturns,
            'total_quantity' => $totalQuantity,
            'total_amount' => $totalAmount,
            'total_bills' => (int) ($result->total_bills ?? 0),
            'total_suppliers' => (int) ($result->total_suppliers ?? 0),
            'total_customers' => (int) ($result->total_customers ?? 0),
        ];
    }


    // ____ BALANCE _______________________________________
    public function getBalance(Request $request)
    {
        $request->validate([
            'currency_id' => 'required|integer',
            'account_id' => 'required|integer',
        ]);

        $currencyId = $request->input('currency_id');
        $accountId = $request->input('account_id');

        if(empty($currencyId) || empty($accountId)) {
            return response()->json(['cur_balance' => 0]);  
        }
       
        $finalBalance = 0;
        $isCompanyAccount = Account::whereIn('account_type_id', [1,6])->where('id', $accountId)->exists();

        if($isCompanyAccount) {
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
        } else {
            $totalBalance = DB::table('journals')
                ->select(
                    DB::raw('SUM(CASE WHEN transaction_type = 1 AND payment_type = 1 THEN amount ELSE 0 END) as sumCachePaid'),
                    DB::raw('SUM(CASE WHEN transaction_type = 2 AND payment_type = 1 THEN amount ELSE 0 END) as sumCacheRecieved'),
                    DB::raw('SUM(CASE WHEN transaction_type = 1 AND payment_type = 2 THEN amount ELSE 0 END) as sumLoanRecieved'),
                    DB::raw('SUM(CASE WHEN transaction_type = 2 AND payment_type = 2 THEN amount ELSE 0 END) as sumLoanPaid')
                )
                ->where('journals.account_id', $accountId)
                ->where('journals.currency_id', $currencyId)
                ->where('is_cleared', '=', 0)
                ->first();
            
            $finalBalance = (($totalBalance->sumCacheRecieved + $totalBalance->sumLoanPaid) - 
                ($totalBalance->sumCachePaid + $totalBalance->sumLoanRecieved));
        }

        return response()->json(['cur_balance' => $finalBalance]);  
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
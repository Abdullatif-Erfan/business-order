<?php

namespace App\Http\Controllers\Clearance;
use App\Http\Controllers\Controller;
use App\Models\Clearance\Clearance;
use App\Models\Setting\Account;
use App\Models\Setting\Currency;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

use App\Models\Setting\OrgBio;
use App\Models\Buy\BoughtItem;
use App\Models\Warehouse\WarehouseSales;

use Morilog\Jalali\Jalalian;
use Yajra\DataTables\Facades\DataTables;

class ClearanceController extends Controller
{
    public function index()
    {
        // $clearedData = Clearance::with(['toAccount','currency'])->orderBy('id','DESC')->get();
        // return ['data' => $clearedData];

        /**
         * لیست فروشندگان ایکه شرکت از وی جنس خریده است و ریکارد دارد که تصفیه نشده است
         */
        $accounts = DB::table('accounts')
        ->select('accounts.id','name')
        ->join('bought_items','bought_items.customer_account_id','=','accounts.id')
        ->where('remained', '>', 0)
        ->where('is_cleared', '=', 0)
        ->groupBy('accounts.id','name')
        ->get();

        $currencies = Currency::all();
        $todaysDate = Jalalian::now()->format('Y-m-d');
        $orgbios = OrgBio::all();
        return view('clearance.buy.list', compact('accounts','currencies','todaysDate','orgbios'));
    }


    public function getData(Request $request)
    {
            $clearedData = Clearance::with(['toAccount','currency'])->where('type','buy')->orderBy('id','DESC');

            // Apply filters if provided
            if ($request->customer_name) {
                $clearedData->whereHas('toAccount', function ($query) use ($request) {
                    $query->where('name', 'LIKE', "%{$request->customer_name}%");
                });
            }
            
            if ($request->currency_id) {
                $clearedData->where('currency_id', $request->currency_id);
            }
            

            if ($request->start_date && $request->end_date) {
                $clearedData->whereBetween('dates', [$request->start_date, $request->end_date]);
            } elseif ($request->start_date) {
                $clearedData->whereDate('dates', '=', $request->start_date);
            } elseif ($request->end_date) {
                $clearedData->whereDate('dates', '<=', $request->end_date); // Until today
            }
            
            
            return DataTables::of($clearedData->get())
            
            ->addIndexColumn()
           
            ->addColumn('type', function ($clearedData) {
                return $clearedData->type == 'buy' ? 'فروشات' : 'خرید';
            })
       
            ->addColumn('total', function ($clearedData) {
                $total = $clearedData->total;
                return (fmod($total, 1) == 0) ? number_format($total, 0) : number_format($total, 2);
            })
            
            ->make(true);

    }


    public function create_for_buy(string $currency_id, string $buy_to_account_id)
    {
        if (!$buy_to_account_id || !$currency_id) {
            \Log::error('Missing parameters: buy_to_account_id or currency_id in create_for_buy');
            return response()->json(['error' => 'Customer or currency not found'], 400);
        }
    
        $boughtItem = BoughtItem::select('id', 'billno', 'remained','customer_account_id','currency_id')
            ->where('customer_account_id', $buy_to_account_id)
            ->where('remained', '>', 0)
            ->where('currency_id', '=', $currency_id)
            ->where('is_cleared', '=', 0)
            ->get();
    
        $account = Account::select('name')->where('id', $buy_to_account_id)->first();
        $currency = Currency::select('name')->where('id', $currency_id)->first();
    
        if (!$account || !$currency) {
            return response()->json(['error' => 'Invalid account or currency'], 400);
        }
    
        if(!$boughtItem->count() > 0)
        {
            return "<div>لیست خالی است لطفا فروشنده و واحدپولی را درست انتخاب نمایید</div>";
            die();
        }
        // return [  'account_name' => $account->name, 'currency_name' => $currency->name,'boughtItem' => $boughtItem];

        return view('clearance.buy.create', [
            'account_name' => $account->name,
            'currency_name' => $currency->name,
            'boughtItem' => $boughtItem
        ]);
    }
    

    public function store_for_buy(Request $request)
    {
        DB::beginTransaction(); // Start transaction

        try {
            $checkedBills = [];

            if ($request->has('check')) {
                foreach ($request->input('check') as $key => $value) {
                    if ($value == 1) { // Checkbox is checked
                        $checkedBills[] = [
                            'billno' => (int)$request->input('bill_numbers')[$key],
                            'remained' => (float)$request->input('remained')[$key]
                        ];
                    }
                }
            }

            if (empty($checkedBills)) {
                return back()->with('error', 'No bills selected for clearance.');
            }

            // Ensure the array has valid bill numbers
            $billNumbers = array_column($checkedBills, 'billno');
           
            foreach ($checkedBills as $bill) {
                $boughtItem = BoughtItem::where('billno', $bill['billno'])
                    ->where('customer_account_id', $request->customer_account_id)
                    ->where('currency_id', $request->currency_id)
                    ->first();

                if (!$boughtItem) {
                    Log::error('Missing parameters: customer_account_id or currency_id in store_for_buy');
                    return back()->with('error', 'Customer or currency not found.');
                }

                $boughtItem->cur_pay = $boughtItem->payable;
                $boughtItem->remained = 0;
                $boughtItem->is_cleared = 1;
                $boughtItem->note = 'تصفیه گردید';
                $boughtItem->save();
            }

           
            // Store clearance record
            Clearance::create([
                'from_account_id' => $request->customer_account_id, 
                'to_account_id' => $request->customer_account_id,
                'total' => $request->total,
                'currency_id' => $request->currency_id,
                'details' => '',
                'bill_numbers' => json_encode($billNumbers),
                'dates' => Jalalian::now()->format('Y-m-d'),
                'clearedBy' => auth()->user()->full_name ?? '',
                'type' => 'buy',
            ]);

            DB::commit(); // Commit transaction
           
         
            Session::flash('notification', [
                'message' => 'موفقانه ثبت گردید',
                'type' => 'success',
            ]);
            return redirect()->route('clearance.index');

        } 
        catch (\Exception $e) 
        {
            DB::rollBack(); // Rollback transaction on error
            Log::error('Error in store_for_buy: ' . $e->getMessage());

            Session::flash('notification', [
                'message' => ' ثبت نگردید',
                'type' => 'danger',
            ]);
            return redirect()->route('clearance.index');
        }
    }


    // ============== BELONGS TO SALES ========================
    public function sales_index()
    {
        // $clearedData = Clearance::with(['toAccount','currency'])->orderBy('id','DESC')->get();
        // return ['data' => $clearedData];

        /**
         * لیست فروشندگان ایکه شرکت از وی جنس خریده است و ریکارد دارد که تصفیه نشده است
         */
        $accounts = DB::table('accounts')
        ->select('accounts.id','name')
        ->join('warehouse_sales','warehouse_sales.customer_account_id','=','accounts.id')
        ->where('remained', '>', 0)
        ->where('is_cleared', '=', 0)
        ->groupBy('accounts.id','name')
        ->get();

        // return ['data' => $accounts];

        $currencies = Currency::all();
        $todaysDate = Jalalian::now()->format('Y-m-d');
        $orgbios = OrgBio::all();
        return view('clearance.sales.list', compact('accounts','currencies','todaysDate','orgbios'));
    }

    
    public function getSalesData(Request $request)
    {
            $clearedData = Clearance::with(['toAccount','currency'])->where('type','sell')->orderBy('id','DESC');

            // Apply filters if provided
            if ($request->customer_name) {
                $clearedData->whereHas('toAccount', function ($query) use ($request) {
                    $query->where('name', 'LIKE', "%{$request->customer_name}%");
                });
            }
            
            if ($request->currency_id) {
                $clearedData->where('currency_id', $request->currency_id);
            }
            
            if ($request->bill_number) {
                $clearedData->where('bill_numbers', $request->bill_number);
            }

            if ($request->start_date && $request->end_date) {
                $clearedData->whereBetween('dates', [$request->start_date, $request->end_date]);
            } elseif ($request->start_date) {
                $clearedData->whereDate('dates', '=', $request->start_date);
            } elseif ($request->end_date) {
                $clearedData->whereDate('dates', '<=', $request->end_date); // Until today
            }
            
            
            return DataTables::of($clearedData->get())
            
            ->addIndexColumn()
           
            ->addColumn('type', function ($clearedData) {
                return $clearedData->type == 'buy' ? 'فروشات' : 'خرید';
            })
       
            ->addColumn('total', function ($clearedData) {
                $total = $clearedData->total;
                return (fmod($total, 1) == 0) ? number_format($total, 0) : number_format($total, 2);
            })
            
            ->make(true);

    }

    public function create_for_sales(string $currency_id, string $sales_to_account_id)
    {
        if (!$sales_to_account_id || !$currency_id) {
            \Log::error('Missing parameters: sales_to_account_id or currency_id in create_for_sales');
            return response()->json(['error' => 'Customer or currency not found'], 400);
        }
    
        $salesRecords = WarehouseSales::select('id', 'billno', 'remained','customer_account_id','currency_id')
            ->where('customer_account_id', $sales_to_account_id)
            ->where('remained', '>', 0)
            ->where('currency_id', '=', $currency_id)
            ->where('is_cleared', '=', 0)
            ->get();
    
        $account = Account::select('name')->where('id', $sales_to_account_id)->first();
        $currency = Currency::select('name')->where('id', $currency_id)->first();
    
        if (!$account || !$currency) {
            return response()->json(['error' => 'Invalid account or currency'], 400);
        }
    
        if(!$salesRecords->count() > 0)
        {
            return "<div>لیست خالی است لطفا مشتری و واحدپولی را درست انتخاب نمایید</div>";
            die();
        }
        // return [  'account_name' => $account->name, 'currency_name' => $currency->name,'salesRecords' => $salesRecords];

        return view('clearance.sales.create', [
            'account_name' => $account->name,
            'currency_name' => $currency->name,
            'salesRecords' => $salesRecords
        ]);
    }
    
    public function store_for_sales(Request $request)
    {
        // return ['data' => $request->all()];

        DB::beginTransaction(); // Start transaction

        try {
            $checkedBills = [];

            if ($request->has('check')) {
                foreach ($request->input('check') as $key => $value) {
                    if ($value == 1) { // Checkbox is checked
                        $checkedBills[] = [
                            'billno' => (int)$request->input('bill_numbers')[$key],
                            'remained' => (float)$request->input('remained')[$key]
                        ];
                    }
                }
            }

            if (empty($checkedBills)) {
                return back()->with('error', 'No bills selected for clearance.');
            }

            // Ensure the array has valid bill numbers
            $billNumbers = array_column($checkedBills, 'billno');
           
            foreach ($checkedBills as $bill) 
            {
                $warehouseSales = WarehouseSales::where('billno', $bill['billno'])
                    ->where('customer_account_id', $request->customer_account_id)
                    ->where('currency_id', $request->currency_id)
                    ->first();

                if (!$warehouseSales) {
                    Log::error('Missing parameters: customer_account_id or currency_id in store_for_buy');
                    return back()->with('error', 'Customer or currency not found.');
                }

                $warehouseSales->cur_pay = $warehouseSales->payable;
                $warehouseSales->remained = 0;
                $warehouseSales->is_cleared = 1;
                $warehouseSales->note = 'تصفیه گردید';
                $warehouseSales->save();
            }

           
            // Store clearance record
            Clearance::create([
                'from_account_id' => $request->customer_account_id, 
                'to_account_id' => $request->customer_account_id,
                'total' => $request->total,
                'currency_id' => $request->currency_id,
                'details' => '',
                'bill_numbers' => json_encode($billNumbers),
                'dates' => Jalalian::now()->format('Y-m-d'),
                'clearedBy' => auth()->user()->full_name ?? '',
                'type' => 'sell',
            ]);

            DB::commit(); // Commit transaction
           
         
            Session::flash('notification', [
                'message' => 'موفقانه ثبت گردید',
                'type' => 'success',
            ]);
            return redirect()->route('clearance.sales.index');

        } 
        catch (\Exception $e) 
        {
            DB::rollBack(); // Rollback transaction on error
            Log::error('Error in store_for_sales: ' . $e->getMessage());

            Session::flash('notification', [
                'message' => ' ثبت نگردید',
                'type' => 'danger',
            ]);
            return redirect()->route('clearance..sales.index');
        }
    }



}

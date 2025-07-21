<?php

namespace App\Http\Controllers\Clearance;
use App\Http\Controllers\Controller;
use App\Models\Clearance\Clearance;
use App\Models\Setting\Account;
use App\Models\Setting\Currency;
use App\Models\Transaction\Journal;

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
    protected $branch_id, $isAdmin, $full_name;
    public function __construct()
    {
        if (auth()->check()) {
            $this->branch_id = session('branch_id', auth()->user()->branch_id ?? 0);
            $this->isAdmin = session('isAdmin', auth()->user()->isAdmin == 1);
            $this->full_name = auth()->user()->full_name;
        } else {
            $this->branch_id = 0;
            $this->isAdmin = false;
            $this->full_name = '';
        }
    }
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
        ->where('accounts.branch_id', '=', $this->branch_id)
        ->groupBy('accounts.id','name')
        ->get();

        $currencies = Currency::all();
        $todaysDate = Jalalian::now()->format('Y-m-d');
        $orgbios = OrgBio::all();
        return view('clearance.buy.list', compact('accounts','currencies','todaysDate','orgbios'));
    }


    public function getData(Request $request)
    {
            $clearedData = Clearance::with(['toAccount','currency'])->where('type','buy')->where('branch_id', $this->branch_id)->orderBy('id','DESC');

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
                return $clearedData->type == 'buy' ? __('common.sales') : __('common.buy');
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
            ->where('branch_id', '=', $this->branch_id)
            ->where('is_cleared', '=', 0)
            ->get();
    
        $account = Account::select('name')->where('id', $buy_to_account_id)->where('branch_id', '=', $this->branch_id)->first();
        $ownBanks = Account::select('id','name')->where('account_type_id',1)
        ->where('branch_id', $this->branch_id)->where('is_pre_select','1')->first();
        if(!$ownBanks)
        {
            return "<div>{{__('validate.not_found_account')}}</div>";
            die();
        }

        $currency = Currency::select('name')->where('id', $currency_id)->first();
    
        if (!$account || !$currency) {
            return response()->json(['error' => 'Invalid account or currency'], 400);
        }
    
        if(!$boughtItem->count() > 0)
        {
            return "<div>{{__('validate.supplier_and_currency_is_required')}}</div>";
            die();
        }
        // return [  'account_name' => $account->name, 'currency_name' => $currency->name,'boughtItem' => $boughtItem];

        return view('clearance.buy.create', [
            'account_name' => $account->name,
            'currency_name' => $currency->name,
            'boughtItem' => $boughtItem,
            'ownBanks' => $ownBanks
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
           
            foreach ($checkedBills as $bill) 
            {
                $boughtItem = BoughtItem::where('billno', $bill['billno'])
                    ->where('customer_account_id', $request->customer_account_id)
                    ->where('currency_id', $request->currency_id)
                    ->where('branch_id', $this->branch_id)
                    ->where('is_cleared', 0)
                    ->first();

                if (!$boughtItem) {
                    Log::error('Missing parameters: customer_account_id or currency_id in store_for_buy');
                    return back()->with('error', 'Customer or currency not found.');
                }

                $boughtItem->cur_pay = $boughtItem->payable;
                $boughtItem->remained = 0;
                $boughtItem->is_cleared = 1;
                $boughtItem->note = __('validate.cleared');
                $boughtItem->save();

                // update journal
                $journals = Journal::where('times', $boughtItem->times)
                ->where('code',$boughtItem->journal_code)
                ->where('currency_id', $request->currency_id)
                ->where('branch_id', $this->branch_id)
                ->where('status', 7) // 1: old journal, 2: journal, 3:income, 4:expense, 5:salary, 6:participants, 7:buy, 8:sales, 9:clearance, 10:other
                ->where('is_cleared', 0)
                ->where('dynamic_type', 2) // 2: is clearable flag
                ->get();

                if(!$journals)
                {
                    Log::error('Missing parameters: journal record not found');
                    return back()->with('error', 'Journal Code or BillNo not found.');
                }

                foreach ($journals as $journal) 
                {
                    $journal->is_cleared = 1;
                    $journal->save();
                }
            }

           $details = __('validate.clearance_between').$request->company_account_name.
            __('validate.wa') .$request->customer_account_name.'';
            // Store clearance record
            Clearance::create([
                'customer_account_id' => $request->customer_account_id, 
                'company_account_id' => $request->company_account_id,
                'total' => $request->total,
                'currency_id' => $request->currency_id,
                'branch_id' => $this->branch_id,
                'details' => $details,
                'bill_numbers' => json_encode($billNumbers),
                'dates' => Jalalian::now()->format('Y-m-d'),
                'clearedBy' => $this->full_name ?? '',
                'type' => 'buy',
            ]);

            
            $check = $this->createJournal($request, $details);
            if(!$check)
            {
                DB::rollBack();
                Session::flash('notification', [
                    'message' => __('validate.journal_insertion_failed'),
                    'type' => 'danger',
                ]);
                return redirect()->route('clearance.index');
            }

            DB::commit(); // Commit transaction
           
         
            Session::flash('notification', [
                'message' =>  __('common.added_successfully'),
                'type' => 'success',
            ]);
            return redirect()->route('clearance.index');

        } 
        catch (\Exception $e) 
        {
            DB::rollBack(); // Rollback transaction on error
            Log::error('Error in store_for_buy: ' . $e->getMessage());

            Session::flash('notification', [
                'message' =>  __('common.add_failed'),
                'type' => 'danger',
            ]);
            return redirect()->route('clearance.index');
        }
    }

    private function createJournal($request, $details)
    {
        DB::beginTransaction();
        try{

            $newJournalCode = DB::table('journals')->where('journals.branch_id', $this->branch_id)->lockForUpdate()->max('code') + 1;
            $todaysDate = Jalalian::now()->format('Y-n-d');
            $date = explode('-', $todaysDate);
            $year = $date[0];
            $month = $date[1];
            $day = $date[2];
            $full_date = Jalalian::now()->format('Y-m-d H:i:s'); 
            $times = time();
    
            /**
             * Recieved Loan = t1p2
             * ثبت قرض جهت تنظیم بیلانس طلب شان
             */
            
            $customer_account_type_id = Account::where('id', $request->customer_account_id)->value('account_type_id');
            $company_account_type_id = Account::where('id', $request->company_account_id)->value('account_type_id');

            $check1 =  Journal::create([
                    'bill_no' => 0,
                    'code' => $newJournalCode,
                    'account_type_id' => $customer_account_type_id,
                    'account_id' => $request->customer_account_id,
                    'branch_id' => $this->branch_id,
                    'amount' => $request->total,
                    'currency_id' => $request->currency_id,
                    'transaction_type' => 1,
                    'payment_type' => 2,
                    'option_label' => __('validate.talab_clearance'),
                    'dt_comment' => __('validate.clearance'),
                    'dynamic_type' => 1,
                    'is_cleared' => 1,
                    'user' => $this->full_name ?? '',
                    'year' => $date[0],
                    'month' => $date[1],
                    'day' => $date[2],
                    'inserted_short_date' => $todaysDate,
                    'inserted_full_date' => $full_date,
                    'details' => $details,
                    'status' => 9,  // 1: old journal, 2: journal, 3:income, 4:expense, 5:salary, 6:participants, 7:buy, 8:sales, 9:clearance, 10:other
                    'times' => $times,
                    'is_single_record' => 1,
                ]);
    
                /**
                 * Paid Cache = t2p1
                 * پرداخت نقد خزانه
                 */
                $check2 =  Journal::create([
                    'bill_no' => 0,
                    'code' => $newJournalCode,
                    'account_type_id' => $company_account_type_id,
                    'account_id' => $request->company_account_id,
                    'branch_id' => $this->branch_id,
                    'amount' => $request->total,
                    'currency_id'  => $request->currency_id,
                    'transaction_type' => 2,
                    'payment_type' => 1,
                    'option_label' => __('validate.cache_payment'),
                    'dynamic_type' => 1,
                    'is_cleared'   => 0, // تنها این مبلغ قابل حساب باشد
                    'dt_comment'   => __('validate.clearance'),
                    'user' => $this->full_name ?? '',
                    'year' => $date[0],
                    'month' => $date[1],
                    'day' => $date[2],
                    'inserted_short_date' => $todaysDate,
                    'inserted_full_date' => $full_date,
                    'details' => $details,
                    'status' => 9,  // 1: old journal, 2: journal, 3:income, 4:expense, 5:salary, 6:participants, 7:buy, 8:sales, 9:clearance, 10:other
                    'times' => $times,
                    'is_single_record' => 1,
                ]);
            
                // Ensure both queries succeed
                if (!$check1 || !$check2) {
                    DB::rollBack();
                    return false;
                }

                DB::commit();
                return true;
        } 
        catch (\Exception $e) 
        {
            DB::rollBack(); // Rollback transaction on error
            Log::error('Error in Clearance Journal Entry: ' . $e->getMessage());
            return false;
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
        ->where('accounts.branch_id', $this->branch_id)
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
            $clearedData = Clearance::with(['toAccount','currency'])->where('type','sell')->where('branch_id', $this->branch_id)->orderBy('id','DESC');

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
                return $clearedData->type == 'buy' ? __('validate.sales') : __('validate.buy');
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
            ->where('branch_id', $this->branch_id)
            ->where('is_cleared', '=', 0)
            ->get();
    
        $account = Account::select('name')->where('id', $sales_to_account_id)->where('branch_id', $this->branch_id)->first();
        $ownBanks = Account::select('id','name')->where('account_type_id',1)
        ->where('branch_id', $this->branch_id)->where('is_pre_select','1')->first();
        if(!$ownBanks)
        {
            return "<div>{{__('validate.account_not_found')}}</div>";
            die();
        }
        $currency = Currency::select('name')->where('id', $currency_id)->first();
    
        if (!$account || !$currency) {
            return response()->json(['error' => 'Invalid account or currency'], 400);
        }
    
        if(!$salesRecords->count() > 0)
        {
            return "<div>{{__('validate.supplier_and_currency_is_required')}}</div>";
            die();
        }
        // return [  'account_name' => $account->name, 'currency_name' => $currency->name,'salesRecords' => $salesRecords];

        return view('clearance.sales.create', [
            'account_name' => $account->name,
            'currency_name' => $currency->name,
            'salesRecords' => $salesRecords,
            'ownBanks' => $ownBanks
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
                    ->where('branch_id', $this->branch_id)
                    ->where('is_cleared', 0)
                    ->first();

                if (!$warehouseSales) {
                    Log::error('Missing parameters: customer_account_id or currency_id in store_for_buy');
                    return back()->with('error', 'Customer or currency not found.');
                }

                $warehouseSales->cur_pay = $warehouseSales->payable;
                $warehouseSales->remained = 0;
                $warehouseSales->is_cleared = 1;
                $warehouseSales->note = __('validate.cleared');
                $warehouseSales->save();

                // update journal
                $journals = Journal::where('times', $warehouseSales->times)
                ->where('bill_no',$warehouseSales->billno)
                ->where('currency_id', $request->currency_id)
                ->where('branch_id', $this->branch_id)
                ->where('status', 8) // 1: old journal, 2: journal, 3:income, 4:expense, 5:salary, 6:participants, 7:buy, 8:sales, 9:clearance, 10:other
                ->where('is_cleared', 0)
                ->where('dynamic_type', 2) // 2: is clearable flag
                ->get();

                if(!$journals)
                {
                    Log::error('Missing parameters: journal record not found');
                    return back()->with('error', 'Journal Code or BillNo not found.');
                }

                foreach ($journals as $journal) 
                {
                    $journal->is_cleared = 1;
                    $journal->save();
                }
            }

            $details = __('validate.clearance_between') .$request->company_account_name.
            __('validate.wa').$request->customer_account_name.'';
           
            // Store clearance record
            Clearance::create([
                'customer_account_id' => $request->customer_account_id, 
                'company_account_id' => $request->company_account_id,
                'total' => $request->total,
                'currency_id' => $request->currency_id,
                'branch_id' => $this->branch_id,
                'details' => '',
                'bill_numbers' => json_encode($billNumbers),
                'dates' => Jalalian::now()->format('Y-m-d'),
                'clearedBy' => $this->full_name ?? '',
                'type' => 'sell',
            ]);


            $check = $this->createSalesJournal($request, $details);
            if(!$check)
            {
                DB::rollBack();
                Session::flash('notification', [
                    'message' => __('validate.journal_insertion_failed'),
                    'type' => 'danger',
                ]);
                return redirect()->route('clearance.sales.index');
            }


            DB::commit(); // Commit transaction
           
            Session::flash('notification', [
                'message' =>  __('common.added_successfully'),
                'type' => 'success',
            ]);
            return redirect()->route('clearance.sales.index');

        } 
        catch (\Exception $e) 
        {
            DB::rollBack(); // Rollback transaction on error
            Log::error('Error in store_for_sales: ' . $e->getMessage());

            Session::flash('notification', [
                'message' =>  __('common.add_failed'),
                'type' => 'danger',
            ]);
            return redirect()->route('clearance..sales.index');
        }
    }

    private function createSalesJournal($request, $details)
    {
        DB::beginTransaction();
        try
        {
            $newJournalCode = DB::table('journals')->where('journals.branch_id', $this->branch_id)->lockForUpdate()->max('code') + 1;
            $todaysDate = Jalalian::now()->format('Y-n-d');
            $date = explode('-', $todaysDate);
            $year = $date[0];
            $month = $date[1];
            $day = $date[2];
            $full_date = Jalalian::now()->format('Y-m-d H:i:s'); 
            $times = time();
    
            /**
             * Recieved Loan = t1p2
             * ثبت قرض جهت تنظیم بیلانس طلب شان
             */
            $customer_account_type_id = Account::where('id', $request->customer_account_id)->value('account_type_id');
            $company_account_type_id = Account::where('id', $request->company_account_id)->value('account_type_id');

            $check1 =  Journal::create([
                    'bill_no' => 0,
                    'code' => $newJournalCode,
                    'account_type_id' =>  $company_account_type_id,
                    'account_id' => $request->company_account_id,
                    'branch_id' => $this->branch_id,
                    'amount' => $request->total,
                    'currency_id' => $request->currency_id,
                    'transaction_type' => 1,
                    'payment_type' => 1,
                    'option_label' => __('validate.cache_recieved'),
                    'dt_comment' =>  __('validate.cleared'),
                    'dynamic_type' => 1,
                    'is_cleared' => 0, // تنها این مبلغ قابل حساب باشد
                    'user' => $this->full_name ?? '',
                    'year' => $date[0],
                    'month' => $date[1],
                    'day' => $date[2],
                    'inserted_short_date' => $todaysDate,
                    'inserted_full_date' => $full_date,
                    'details' => $details,
                    'status' => 9,  // 1: old journal, 2: journal, 3:income, 4:expense, 5:salary, 6:participants, 7:buy, 8:sales, 9:clearance, 10:other
                    'times' => $times,
                    'is_single_record' => 1,
                ]);
    
                /**
                 * Paid Cache = t2p1
                 * پرداخت نقد مشتری
                 */
                $check2 =  Journal::create([
                    'bill_no' => 0,
                    'code' => $newJournalCode,
                    'account_type_id' =>  $customer_account_type_id,
                    'account_id' => $request->customer_account_id,
                    'branch_id' => $this->branch_id,
                    'amount' => $request->total,
                    'currency_id'  => $request->currency_id,
                    'transaction_type' => 2,
                    'payment_type' => 1,
                    'option_label' => __('validate.cache_payment'),
                    'dynamic_type' => 1,
                    'is_cleared'   => 1, 
                    'dt_comment'   => __('validate.clearance'),
                    'user' => $this->full_name ?? '',
                    'year' => $date[0],
                    'month' => $date[1],
                    'day' => $date[2],
                    'inserted_short_date' => $todaysDate,
                    'inserted_full_date' => $full_date,
                    'details' => $details,
                    'status' => 9,  // 1: old journal, 2: journal, 3:income, 4:expense, 5:salary, 6:participants, 7:buy, 8:sales, 9:clearance, 10:other
                    'times' => $times,
                    'is_single_record' => 1,
                ]);
            
                // Ensure both queries succeed
                if (!$check1 || !$check2) {
                    DB::rollBack();
                    return false;
                }

                DB::commit();
                return true;
        } 
        catch (\Exception $e) 
        {
            DB::rollBack(); // Rollback transaction on error
            Log::error('Error in Clearance Journal Entry: ' . $e->getMessage());
            return false;
        }
    }



}

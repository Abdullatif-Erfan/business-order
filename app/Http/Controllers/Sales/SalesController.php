<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Setting\Currency;
use App\Models\Setting\Branch;
use Morilog\Jalali\Jalalian;
use App\Models\Setting\OrgBio;
use App\Models\Setting\Unit;
use App\Models\Transaction\Journal;
use App\Models\Warehouse\WarehouseSales;
use App\Models\Warehouse\WarehouseItem;
use App\Models\Warehouse\SalesDetails;

use App\Models\Setting\Account;

use Yajra\DataTables\Facades\DataTables;


class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $currencies = Currency::all();
        $todaysDate = Jalalian::now()->format('Y-m-d');
        $orgbios = OrgBio::all();
        $branchs = Branch::all();
        return view('sales.list',compact('currencies','todaysDate','orgbios','branchs'));
    }

    public function getData(Request $request)
    {
            $soldItems = DB::table('warehouse_sales')
            ->join('accounts', 'accounts.id', '=', 'warehouse_sales.customer_account_id')
            ->join('currencies', 'currencies.id', '=', 'warehouse_sales.currency_id')
            ->select('warehouse_sales.id','billno','factor','warehouse_sales.branch_id','accounts.name as customer_name','total_price','total_discount','payable','cur_pay','is_cleared','remained','currencies.name as currency_name','short_date','iby')
            ->orderBy('warehouse_sales.id','DESC');
            

            // Apply filters if provided
              if ($request->customer_name) {
                 $soldItems->where('accounts.name', 'LIKE', "%{$request->customer_name}%");
            }
            
            if ($request->currency_id) {
                $soldItems->where('currency_id', $request->currency_id);
            }
            
            if ($request->start_date && $request->end_date) {
                $soldItems->whereBetween('short_date', [$request->start_date, $request->end_date]);
            } elseif ($request->start_date) {
                $soldItems->whereDate('short_date', '=', $request->start_date);
            } elseif ($request->end_date) {
                $soldItems->whereDate('short_date', '<=', $request->end_date); // Until today
            }
            
            if ($request->bill_number) {
                $soldItems->where('billno', $request->bill_number);
            }
            
            return DataTables::of($soldItems->get())
            
            ->addIndexColumn()
           
            ->addColumn('billno', function($soldItem) {
                $checkIcon = $soldItem->is_cleared == 1 ? '<i class="fas fa-check-circle success"></i>' : '';
                return $soldItem->billno ? $checkIcon.' '.'SALES_'.$soldItem->billno: 0;
            })

            ->addColumn('total_price', function ($soldItem) {
                $total_price = $soldItem->total_price;
                return (fmod($total_price, 1) == 0) ? number_format($total_price, 0) : number_format($total_price, 2);
            })

            ->addColumn('total_discount', function ($soldItem) {
                $total_discount = $soldItem->total_discount;
                return (fmod($total_discount, 1) == 0) ? number_format($total_discount, 0) : number_format($total_discount, 2);
            })

            ->addColumn('payable', function ($soldItem) {
                $payable = $soldItem->payable;
                return (fmod($payable, 1) == 0) ? number_format($payable, 0) : number_format($payable, 2);
            })

            ->addColumn('cur_pay', function ($soldItem) {
                $cur_pay = $soldItem->cur_pay;
                return (fmod($cur_pay, 1) == 0) ? number_format($cur_pay, 0) : number_format($cur_pay, 2);
            })

            ->addColumn('remained', function ($soldItem) {
                $remained = $soldItem->remained;
                return (fmod($remained, 1) == 0) ? number_format($remained, 0) : number_format($remained, 2);
            })
            
        
            ->addColumn('view', function ($soldItem) {
                return '<a href="sales/details/'.$soldItem->billno.'" class="hidden-print"><i class="fas fa-eye viewItems" 
                data-id="' . $soldItem->id . '" style="font-size:20px;"></i></a>';
            })

            ->rawColumns(['billno','view'])
            ->make(true);

    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $todaysDate = Jalalian::now()->format('Y-m-d');
        // $warehouseItems = WarehouseItem::with(['preListRelation'])->where('available_amount','>',0)->get();
        $warehouseItems = DB::table('warehouse_items')
                        ->join('bought_item_pre_lists', 'bought_item_pre_lists.id', '=', 'warehouse_items.buy_pre_id')
                        ->join('warehouses', 'warehouses.id', '=', 'warehouse_items.warehouse_id')
                        ->join('units', 'units.id', '=', 'warehouse_items.unit_id')
                        ->where('warehouse_items.available_amount', '>', 0)
                        ->select('warehouse_items.id','warehouse_items.unit_id','avg_up','sell_up', 'warehouse_items.available_amount', 'units.name as unit_name','warehouses.id as warehouse_id', 'warehouses.name as warehouse_name', 'bought_item_pre_lists.name as item_name','bought_item_pre_lists.branch_id','bought_item_pre_lists.id as pre_list_id')
                        ->get();

        $customers = Account::select('id','name')->where('account_type_id',3)->orWhere('account_type_id',4)->get();
        $ownBanks = Account::select('id','name')->where('account_type_id',1)->orderBy('is_pre_select','DESC')->get();
        $currencies = Currency::all();
        $billno =  WarehouseSales::max('billno') + 1;
        $journal_code = Journal::max('code') + 1;
        $times = time();
        

        // return response()->json(['data' => $warehouseItems]);
        return view('sales.create.form',compact('todaysDate','warehouseItems','customers','ownBanks','billno','currencies','journal_code','times'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // return response()->json(['data' => $request->all()]);
        
        // Validate the request and return errors if validation fails
        $validator = Validator::make($request->all(), $this->validationRules(), $this->validationMessages());

        if ($validator->fails()) 
        {
            return redirect()->route('sales.create')
                ->withErrors($validator)
                ->withInput(); // Preserve old input
        }

        // Start the transaction
        DB::beginTransaction();

        try 
        {
            
            // create warehouse_sales
            $warehouseSalesId = $this->createWarehouseSales($request);
            
            // create sales_details
            $salesDetails = $this->createSalesDetails($request, $warehouseSalesId);

            // decrease from warehouse_items
            $checkWarehouseItems = $this->decreaseWarehouseItemFromSoldAmount($request);

            $checkJournal =  $this->handleJournalEntry($request);
            if(!$checkJournal || !$salesDetails || !$warehouseSalesId || !$checkWarehouseItems)
            {
                DB::rollBack();
                Session::flash('notification', [
                    'message' => 'ثبت نگردید',
                    'type' => 'danger',
                ]);
                return redirect()->route('sales.create');
            }

            // Flash error message
            DB::commit();
            Session::flash('notification', [
                'message' => 'موفقانه ثبت گردید',
                'type' => 'success',
            ]);
             return redirect()->route('sales.create');
 
 
         } catch (\Exception $e) {
             // Rollback the transaction if an error occurs
             DB::rollBack();
             // Optionally, log the error for debugging
             \Log::error('Error storing SalesController', ['error' => $e]);

            // Flash error message
            Session::flash('notification', [
                'message' => 'ثبت نگردید',
                'type' => 'danger',
            ]);
             return redirect()->route('sales.create');
         }   
    }

    /**
     * Validation rules
     */
    private function validationRules()
    {
        return [
            'customer_account_id' => 'required|integer|exists:accounts,id',
            'times'        => 'required',
            'todays_date' => 'required|date_format:Y-m-d',
            'billno' => 'required|integer',
            'factor' => 'nullable|string',
            'warehouse_id'  => 'required',
            'warehouseItemId' => 'required|array',
            'warehouseItemId.*' => 'required|integer|exists:warehouse_items,id',
            'amount' => 'required|array',
            'amount.*' => 'required|numeric|min:1',
            'unit_id' => 'required|array',
            'unit_id.*' => 'required|integer|exists:units,id',
            'unit_name' => 'required|array',
            'unit_name.*' => 'required|string|max:255',
            'avg_up' => 'required|array',
            'avg_up.*' => 'nullable|numeric|min:0',
            'sell_up' => 'required|array',
            'sell_up.*' => 'nullable|numeric|min:0',
            'discount' => 'required|array',
            'discount.*' => 'nullable|numeric|min:0',
            'profit' => 'required|array',
            'profit.*' => 'nullable|numeric',
            'total' => 'required|array',
            'total.*' => 'nullable|numeric|min:0',
            'total_price' => 'required|numeric|min:0',
            'general_discount' => 'nullable|numeric|min:0',
            'payable' => 'required|numeric|min:0',
            'cur_pay' => 'required|numeric|min:0',
            'remained' => 'required|numeric|min:0',
            'from_account_id' => 'required|integer|exists:accounts,id',
            'currency_id' => 'required|integer|exists:currencies,id',
            'note' => 'nullable|string|max:500',
        ];
    }

    /**
     * Custom validation messages
     */
    private function validationMessages()
    {
        return [
            'customer_account_id.required' => 'انتخاب حساب مشتری الزامی است.',
            'customer_account_id.integer' => 'حساب مشتری باید یک عدد باشد.',
            'customer_account_id.exists' => 'حساب مشتری انتخاب شده معتبر نیست.',
            'todays_date.required' => 'وارد کردن تاریخ امروز الزامی است.',
            'todays_date.date_format' => 'فرمت تاریخ صحیح نیست.',
            'billno.required' => 'شماره فاکتور الزامی است.',
            'billno.integer' => 'شماره فاکتور باید عدد باشد.',
            'factor.string' => 'فاکتور باید یک متن باشد.',
            'warehouseItemId.required' => 'انتخاب حداقل یک کالا الزامی است.',
            'warehouseItemId.array' => 'فرمت کالاها صحیح نیست.',
            'warehouseItemId.*.integer' => 'شناسه کالا باید عدد باشد.',
            'warehouseItemId.*.exists' => 'کالای انتخاب شده معتبر نیست.',
            'amount.required' => 'وارد کردن مقدار الزامی است.',
            'amount.array' => 'فرمت مقدار کالاها صحیح نیست.',
            'amount.*.numeric' => 'مقدار کالا باید عدد باشد.',
            'amount.*.min' => 'مقدار کالا نمی‌تواند کمتر از ۱ باشد.',
            'unit_id.required' => 'انتخاب واحد کالا الزامی است.',
            'unit_id.array' => 'فرمت واحدها صحیح نیست.',
            'unit_id.*.integer' => 'شناسه واحد باید عدد باشد.',
            'unit_id.*.exists' => 'واحد انتخاب شده معتبر نیست.',
            'unit_name.required' => 'نام واحد کالا الزامی است.',
            'unit_name.array' => 'فرمت نام واحدها صحیح نیست.',
            'unit_name.*.string' => 'نام واحد باید متن باشد.',
            'unit_name.*.max' => 'نام واحد نباید بیشتر از ۲۵۵ کاراکتر باشد.',
            'avg_up.array' => 'فرمت قیمت میانگین صحیح نیست.',
            'avg_up.*.numeric' => 'قیمت میانگین باید عدد باشد.',
            'avg_up.*.min' => 'قیمت میانگین نمی‌تواند منفی باشد.',
            'sell_up.array' => 'فرمت قیمت فروش صحیح نیست.',
            'sell_up.*.numeric' => 'قیمت فروش باید عدد باشد.',
            'sell_up.*.min' => 'قیمت فروش نمی‌تواند منفی باشد.',
            'discount.array' => 'فرمت تخفیف‌ها صحیح نیست.',
            'discount.*.numeric' => 'مقدار تخفیف باید عدد باشد.',
            'discount.*.min' => 'مقدار تخفیف نمی‌تواند منفی باشد.',
            'profit.array' => 'فرمت سود صحیح نیست.',
            'profit.*.numeric' => 'سود باید عدد باشد.',
            'total.required' => 'وارد کردن مجموع مبلغ الزامی است.',
            'total.array' => 'فرمت مجموع مبلغ صحیح نیست.',
            'total.*.numeric' => 'مجموع مبلغ باید عدد باشد.',
            'total.*.min' => 'مجموع مبلغ نمی‌تواند منفی باشد.',
            'total_price.required' => 'مبلغ کل الزامی است.',
            'total_price.numeric' => 'مبلغ کل باید عدد باشد.',
            'total_price.min' => 'مبلغ کل نمی‌تواند منفی باشد.',
            'general_discount.numeric' => 'تخفیف کلی باید عدد باشد.',
            'general_discount.min' => 'تخفیف کلی نمی‌تواند منفی باشد.',
            'payable.required' => 'مبلغ قابل پرداخت الزامی است.',
            'payable.numeric' => 'مبلغ قابل پرداخت باید عدد باشد.',
            'payable.min' => 'مبلغ قابل پرداخت نمی‌تواند منفی باشد.',
            'cur_pay.required' => 'مبلغ پرداختی فعلی الزامی است.',
            'cur_pay.numeric' => 'مبلغ پرداختی باید عدد باشد.',
            'cur_pay.min' => 'مبلغ پرداختی نمی‌تواند منفی باشد.',
            'remained.required' => 'مبلغ باقی‌مانده الزامی است.',
            'remained.numeric' => 'مبلغ باقی‌مانده باید عدد باشد.',
            'remained.min' => 'مبلغ باقی‌مانده نمی‌تواند منفی باشد.',
            'from_account_id.required' => 'انتخاب حساب مبدأ الزامی است.',
            'from_account_id.integer' => 'شناسه حساب مبدأ باید عدد باشد.',
            'from_account_id.exists' => 'حساب مبدأ انتخاب شده معتبر نیست.',
            'currency_id.required' => 'انتخاب ارز الزامی است.',
            'currency_id.integer' => 'شناسه ارز باید عدد باشد.',
            'currency_id.exists' => 'ارز انتخاب شده معتبر نیست.',
            'note.string' => 'توضیحات باید به صورت متن باشد.',
            'note.max' => 'توضیحات نباید بیشتر از ۵۰۰ کاراکتر باشد.',
        ];
    }


    /**
    *  Create Warehouse Sales
    */
    private function createWarehouseSales($request)
    {
        
        try {
            // Prepare date and other fields
            $full_date = $request->todays_date . ' ' . now()->format('H:i:s A');
            $insertedBy = auth()->user()->full_name ?? '';
            $short_date = $request->todays_date ?? Jalalian::now()->format('Y-m-d');
            [$year, $month, $day] = explode('-', $short_date);
    
            // // Ensure branch_id is an array before accessing the first element
            $branch_id = is_array($request->branch_id) ? $request->branch_id[0] : $request->branch_id;
            // \Log::info('Start inserting into warehouse sales', ['request' => $request->all()]);
    
            $note = "Total Payable: " . ($request->payable ?? 0) . ", Paid: " . ($request->cur_pay ?? 0) . ", Remained: " . ($request->remained ?? 0);


            // Insert the new warehouse sale record
            $warehouseSales = WarehouseSales::create([
                'billno' => $request->billno, 
                'factor' => $request->factor, 
                'account_id' => $request->from_account_id, 
                'branch_id' => $branch_id, 
                'customer_account_id' => $request->customer_account_id, 
                'total_price' => $request->total_price, 
                'total_discount' => $request->total_discount, 
                'payable' => $request->payable, 
                'cur_pay' => $request->cur_pay,
                'remained' => $request->remained, 
                'currency_id' => $request->currency_id,  
                'note' => $note, 
                'short_date' => $request->todays_date,
                'ifull_date' => $full_date,
                'iby' => $insertedBy, 
                'uby' => '',
                'year' => $year, 
                'month' => $month, 
                'day' => $day,
                'times' => $request->times,
                'is_cleared' => 0,
            ]);
    
            // Check if the insertion was successful
            // if ($warehouseSales) {
            //     \Log::info('Successfully inserted warehouse sales', ['warehouseSales' => $warehouseSales]);
            // } else {
            //     \Log::error('Failed insertion - warehouse sales is null');
            // }
    
            // Return the ID of the inserted record
            return $warehouseSales->id ?? null;
    
        } catch (\Exception $e) {
            // Log the error in case of an exception
            \Log::error('Failed to insert warehouse sales', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);
    
            // Optionally rethrow or return null
            return null;
        }
    }
    
    /**
     * Create Sales Details
     */
    private function createSalesDetails($request, $warehouseSalesId)
    {
        $todays_date = $request->todays_date ?? Jalalian::now()->format('Y-m-d');

        foreach($request->warehouseItemId as $index => $itemId)
        {
             SalesDetails::create([
                'billno' => $request->billno, 
                'branch_id' => $request->branch_id[$index], 
                'warehouse_id' => $request->warehouse_id[$index],
                'warehouse_sales_id' => $warehouseSalesId, 
                'pre_list_id' => $request->pre_list_id[$index], 
                'unit_id' => $request->unit_id[$index], 
                'amount' => $request->amount[$index], 
                'avg_up' => $request->avg_up[$index], 
                'sell_up' => $request->sell_up[$index], 
                'discount' => $request->discount[$index],
                'profit' => $request->profit[$index], 
                'total' => $request->total[$index],  
                'is_returned' => 0, 
                'todays_date' => $todays_date,
            ]);
        }

        return true;
    }

    /**
     * Decrease the amount of items in stock by sold amount
     */
    private function decreaseWarehouseItemFromSoldAmount($request)
    {
        foreach ($request->warehouseItemId as $index => $itemId) {
            $warehouseItem = WarehouseItem::where('id', $itemId)->first(); 

            if ($warehouseItem) {
                $warehouseItem->out_amount += $request->amount[$index];
                $warehouseItem->available_amount -= $request->amount[$index];
                $warehouseItem->save();
            } else {
                return false;
            }
        }
        return true;
    }


    /**
     * Create Journal
     */
    private function handleJournalEntry($request)
    {
            $date = explode('-', $request->todays_date);
            $year = $date[0];
            $month = $date[1];
            $day  = $date[2];
            $full_date =  $year.'-'.$month.'-'.$day.' '.Date('H:i:s A');
             /**
             * ================================== insert in to journal ========================
             * status: 1: old journal, 2: journal, 3:buy, 4:sales, 5:clearance
             * transaction_type: 1:recieved   2:paid
             * payment_type:     1: cache,    2: loan
             */
            
            DB::beginTransaction();
            try {
            /**
             * اگر هیچ پرداخت نکند وتمام شان قرض ثبت گردد
             * خزانه باید طلب ثبت گردد =  paid Loan 
             * مشتری باید قرضدار ثبت گردد = Recieved Loan 
             */
            if(intval($request->cur_pay) === 0 && intval($request->remained) === intval($request->payable))
            { 
                // ثبت طلب خزانه = paid(ttype=2), loan(ptype=2) 
                $details =  ' طلب فروشات - بل '.' SALES_'.$request->billno;
                $optionLabel = 'طلب فروشات';
                $this->createJournalEntry($request,  $optionLabel, $request->from_account_id,  $request->payable, $ttype = "2", $ptype="2", $date,
                $full_date, $details);
                
                // ثبت قرضه مشتری = recieved(ttype=1) loan(ptype=2)
                $details =  ' قرضه فروشات - بل '.' SALES_'.$request->billno;
                $optionLabel = 'قرضه فروشات';
                $this->createJournalEntry($request, $optionLabel, $request->customer_account_id,  $request->payable,
                 $ttype = "1", $ptype="2", $date, $full_date, $details);
            }

            // کمی شانرا پرداخت کرده و متباقی شانرا قرض انتخاب کرده است
            else if(intval($request->remained) > 0 && intval($request->cur_pay) > 0) 
            {
                // ثبت دریافت نقدی خزانه = Cache Recieved = t1p1
                $details =  'دریافت فروشات - بل  '.' SALES_'.$request->billno;
                $optionLabel = 'دریافت نقد';
                $this->createJournalEntry($request, $optionLabel, $request->from_account_id, $request->cur_pay, $ttype = "1", $ptype="1", $date, $full_date, $details);

                // ثبت قرضه مشتری = Loan Recieved = p2t1
                $details =  ' قرضه فروشات - بل '.' SALES_'.$request->billno;
                $optionLabel = 'قرضه فروشات';
                $this->createJournalEntry($request, $optionLabel, $request->customer_account_id, $request->remained,  
                $ttype = "1", $ptype="2", $date, $full_date, $details);
               
                // ثبت طلب خزانه = Paid Loan = t2p2
                $details =  ' طلب فروشات - بل '.' SALES_'.$request->billno;
                $optionLabel = 'طلب فروشات';
                $this->createJournalEntry($request, $optionLabel,  $request->from_account_id, $request->remained,
                $ttype = "2", $ptype="2", $date, $full_date, $details);
            }

             // قرضدار نمانده است و مکمل پرداخت کرده است
             // تنها در حساب خزانه اضافه شود
            else if(intval($request->remained) === 0 && intval($request->cur_pay) === intval($request->payable)) 
            {
                // ثبت دریافت نقدی خزانه = Cache Recieved = t1p1
                $details =  'دریافت فروشات - بل  '.' SALES_'.$request->billno;
                $optionLabel = 'دریافت نقد';
                $this->createJournalEntry($request, $optionLabel, $request->from_account_id, $request->cur_pay,
                $ttype = "1", $ptype="1", $date, $full_date, $details);
            }
        
            DB::commit();
            return true; 

        } catch (\Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollBack();
            // Optionally, log the error for debugging
            \Log::error('Error storing journal entry in SalesController', ['error' => $e->getMessage()]);
    
            // Use MessageService to return error message
            Session::flash('notification', [
                'message' => ' ثبت نگردید',
                'type' => 'danger',
            ]);
             return false;
        }
    }

    private function createJournalEntry($request, $optionLabel, $account_id, $amount, $ttype, $ptype, $date, $full_date, $details)
    {
        $branch_id = is_array($request->branch_id) ? $request->branch_id[0] : $request->branch_id;
        Journal::create([
            'bill_no' => $request->billno,
            'code' =>  $request->code,
            'account_id' => $account_id,
            'branch_id' => $branch_id,
            'amount' => $amount,
            'currency_id' => $request->currency_id,
            'transaction_type' => $ttype,
            'payment_type' => $ptype,
            'option_label' => $optionLabel,
            'user_id' => auth()->user()->id ?? '',
            'year' =>  $date[0],
            'month' =>  $date[1],
            'day' =>  $date[2],
            'inserted_short_date' => $request->todays_date,
            'inserted_full_date' => $full_date,
            'details' => $details,
            'status' => 4,  
            'times' => $request->times,
            'is_single_record' => 1, 
        ]);
    }



    /**
     * Display the specified resource.
    */
    public function details(string $billno)
    {
        $orgbios = OrgBio::all();
        $todaysDate = Jalalian::now()->format('Y-m-d');
        $warehouseSales = WarehouseSales::with(['currencyRelation','accountRelation'])->where('billno',$billno)->get();
        $salesDetails = SalesDetails::with(['preListRelation','unitRelation'])->where('billno',$billno)->get();
        
        // return response()->json(['warehouseSales' => $warehouseSales,'salesDetails'=> $salesDetails]);
        return view('sales.details',compact('warehouseSales','salesDetails','orgbios','todaysDate'));

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $billno)
    {

        $orgbios = OrgBio::all();
        $todaysDate = Jalalian::now()->format('Y-m-d');
        $warehouseSales = WarehouseSales::with(['currencyRelation','accountRelation'])->where('billno',$billno)->get();
        $salesDetails = SalesDetails::with(['preListRelation','unitRelation'])->where('billno',$billno)->get();
        $billno = $billno;

        $customers = Account::select('id','name')->where('account_type_id',3)->orWhere('account_type_id',4)->get();
        $ownBanks = Account::select('id','name')->where('account_type_id',1)->orderBy('is_pre_select','DESC')->get();
        $currencies = Currency::select('id','name')->get();
        // return response()->json(['warehouseSales' => $warehouseSales,'salesDetails'=> $salesDetails]);
        return view('sales.edit',compact('warehouseSales','salesDetails','orgbios','todaysDate','customers','ownBanks','currencies','billno'));
    }

    /**
     * 
     */
    public function getSingleRecordForEdit(string $id)
    {
        $units = Unit::select('id','name')->get();
        $salesDetails = SalesDetails::with(['preListRelation','unitRelation'])->where('id', $id)->first();

        if (!$salesDetails) {
            return response()->json(['error' => 'Sales Details not found'], 404);
        }

        //  return response()->json(['boughtItemDetails' => $boughtItemDetails]);
        // return response()->json(['boughtItemDetails' => $boughtItemDetails, 'warehouseItems' => $warehouseItems]);
        return view('sales.editModalContent', compact('salesDetails', 'units'));
    }

    public function updateSalesAndWarehouseItems(Request $request)
    {
        $validated = $request->validate([
            'id'                => 'required|exists:sales_details,id',
            'pre_list_id'       => 'required|exists:bought_item_pre_lists,id',
            'warehouse_id'      => 'required|exists:warehouses,id',
            'amount'            => 'required|numeric|min:0',
            'old_amount'        => 'required|numeric|min:0',
            'billno'            => 'required|numeric|min:0',
            'unit_id'           => 'required|exists:units,id',
            'sell_up'           => 'required|numeric|min:1',
            'discount'          => 'nullable|numeric|min:0', // Added validation for discount
        ]);

        DB::beginTransaction();
        try {
            $salesDetails = SalesDetails::findOrFail($validated['id']);

            $new_total = $validated['amount'] * $validated['sell_up'];
            $profit = $new_total - ($validated['amount'] * $salesDetails->avg_up);

            $salesDetails->update([
                'amount'   => $validated['amount'],
                'sell_up'  => $validated['sell_up'],
                'discount' => $validated['discount'] ?? 0, // Handle nullable case
                'profit'   => $profit,
                'unit_id'  => $validated['unit_id'],
                'total'    => $new_total,
            ]);

            // Find Warehouse Item
            $warehouseItem = WarehouseItem::where('warehouse_id', $validated['warehouse_id'])
                                            ->where('buy_pre_id', $validated['pre_list_id'])
                                            ->where('unit_id', $validated['unit_id'])
                                            ->first();

            if (!$warehouseItem) {
                throw new \Exception('Warehouse item not found.');
            }

            if ($validated['old_amount'] > $validated['amount']) {
                $diff = $validated['old_amount'] - $validated['amount'];
                $warehouseItem->available_amount += $diff;
                $warehouseItem->in_amount += $diff;
            } elseif ($validated['amount'] > $validated['old_amount']) {
                $diff = $validated['amount'] - $validated['old_amount'];
                if ($warehouseItem->available_amount < $diff) {
                    throw new \Exception('Not enough stock available in warehouse.');
                }
                $warehouseItem->available_amount -= $diff;
                $warehouseItem->out_amount += $diff; // Adjusted to increase, not decrease
            }

            $warehouseItem->available_total = $warehouseItem->available_amount * $warehouseItem->avg_up;
            $warehouseItem->save();

            DB::commit();

            return redirect()->route('sales.edit', ['billno' => $validated['billno']])
                            ->with('notification', [
                                'message' => 'موفقانه ویرایش گردید',
                                'type'    => 'success',
                            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Error in updateSalesAndWarehouseItems', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'request' => $validated,
            ]);

            return redirect()->back()->withInput()
                            ->with('notification', [
                                'message' => 'ویرایش نگردید: ' . $e->getMessage(),
                                'type'    => 'danger',
                            ]);
        }
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'billno'              => 'required|integer|min:1',
            'customer_account_id' => 'required|exists:accounts,id',
            'currency_id'         => 'required|exists:currencies,id',
            'total_price'         => 'required|numeric|min:0',
            'total_discount'      => 'required|numeric|min:0',
            'from_account_id'     => 'required|exists:accounts,id',
            'payable'             => 'required|numeric|min:0',
            'cur_pay'             => 'required|numeric|min:0',
            'remained'            => 'required|numeric|min:0',
            'note'                => 'nullable|string|max:500',
        ]);
    
        DB::beginTransaction();
    
        try {
            // Find the warehouse sale record
            $warehouseSales = WarehouseSales::where('billno', $validated['billno'])->firstOrFail();
    
            $note = "Total Payable: " . ($validated['payable'] ?? 0) . ", Paid: " . ($validated['cur_pay'] ?? 0) . ", Remained: " . ($validated['remained'] ?? 0);
    
            // Update warehouse sale details
            $warehouseSales->update([
                'total_price'    => $validated['total_price'],
                'total_discount' => $validated['total_discount'],
                'payable'        => $validated['payable'],
                'cur_pay'        => $validated['cur_pay'],
                'remained'       => $validated['remained'],
                'note'           => $note,
            ]);
    
            // Retrieve old journal records
            $oldJournals = Journal::where('times', $request->times)->where('status', 4)->get();
    
            if ($oldJournals->isNotEmpty()) {
                // Clone request to avoid modifying original data
                $clonedRequest = clone $request;
                $clonedRequest->merge([
                    'code' => $oldJournals->first()->code, // Get 'code' from the first record
                ]);
    
                // Delete all journal records in a single query
                Journal::where('times', $request->times)->where('status', 4)->delete();
    
                // Handle new journal entry
                $checkJournal = $this->handleJournalEntry($clonedRequest);
    
                if (!$checkJournal) {
                    DB::rollBack();
                    return redirect()->route('sales.details', ['billno' => $request->billno])
                        ->with('notification', [
                            'message' => 'ویرایش نگردید',
                            'type'    => 'danger',
                        ]);
                }
            }
    
            // Commit transaction
            DB::commit();
            return redirect()->route('sales.details', ['billno' => $request->billno])
                ->with('notification', [
                    'message' => 'موفقانه ویرایش گردید',
                    'type'    => 'success',
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating WarehouseSales: ' . $e->getMessage());
    
            return redirect()->route('sales.details', ['billno' => $request->billno])
                ->with('notification', [
                    'message' => 'ویرایش نگردید',
                    'type'    => 'danger',
                ]);
        }
    }
    


    /**
     * Remove the specified resource from storage.
     * Param: sales_deatils_id
     */
    public function deleteSingleItem(string $id)
    {
        DB::beginTransaction();
        try {
            // Retrieve SalesDetails correctly
            $SalesDetails = SalesDetails::findOrFail($id);

            // Debug: Log the retrieved SalesDetails info
            \Log::info("Deleting SalesDetails ID: $id", [
                'warehouse_id' => $SalesDetails->warehouse_id,
                'buy_pre_id' => $SalesDetails->pre_list_id,
                'unit_id' => $SalesDetails->unit_id
            ]);

            // Find Warehouse Item
            $warehouseItem = WarehouseItem::where('warehouse_id', $SalesDetails->warehouse_id)
                                        ->where('buy_pre_id', $SalesDetails->pre_list_id)
                                        ->where('unit_id', $SalesDetails->unit_id)
                                        ->first();

            if (!$warehouseItem) {
                throw new \Exception('Warehouse item not found.');
            }

            // Update warehouse item
            $warehouseItem->available_amount += $SalesDetails->amount;
            $warehouseItem->in_amount += $SalesDetails->amount; 
            $warehouseItem->available_total = (($warehouseItem->available_amount + $SalesDetails->amount) * $warehouseItem->avg_up);
            $warehouseItem->save();

            // Delete SalesDetails **after** updating warehouse item
            $SalesDetails->delete();

            DB::commit();

            Session::flash('notification', [
                'message' => 'موفقانه حذف گردید',
                'type' => 'success',
            ]);

            return true; 
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Error deleting records: ' . $e->getMessage());

            Session::flash('notification', [
                'message' => ' حذف نگردید',
                'type' => 'danger',
            ]);

            return false;
        }
    }

    public function destroy(string $billno)
    {
        DB::beginTransaction();
        try {
            // Delete all related records directly
            WarehouseSales::where('billno', $billno)->delete();
           
            DB::commit();
    
            Session::flash('notification', [
                'message' => 'موفقانه حذف گردید',
                'type' => 'success',
            ]);
    
            return redirect()->route('sales.index'); 
        } catch (\Exception $e) {
            DB::rollBack();
    
            \Log::error('Error deleting records: ' . $e->getMessage());
    
            Session::flash('notification', [
                'message' => ' حذف نگردید',
                'type' => 'danger',
            ]);
    
            return back();
        }
    }
}

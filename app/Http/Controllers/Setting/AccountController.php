<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting\Account;
use App\Models\Setting\Currency;
use App\Models\Setting\Branch;
use App\Models\Setting\AccountType;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Transaction\Journal;
use Morilog\Jalali\Jalalian;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Buy\BoughtItem;
use App\Models\Buy\BoughtItemDetails;
use App\Models\Warehouse\WarehouseSales;

class AccountController extends Controller
{
    protected $branch_id, $isAdmin;
    public function __construct()
    {
        if (auth()->check()) {
            $this->branch_id = session('branch_id', auth()->user()->branch_id ?? 0);
            $this->isAdmin = session('isAdmin', auth()->user()->isAdmin == 1);
        } else {
            $this->branch_id = 0;
            $this->isAdmin = false;
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $currencyes = Account::latest()->paginate(10); 
        // $accounts = Account::with(['accountType', 'journals' => function($query) {
        //     $query->select('account_id', 'amount', 'transaction_type'); // Select specific columns from journals
        // }])
        // ->withSum('journals', 'amount')  // Sum the 'amount' field from related journals
        // ->orderBy('id', 'DESC')
        // ->get();

        // return response()->json($accounts);
        
        if ($request->ajax()) {

            // if(!$this->isAdmin)
            // {
                $accounts = Account::with(['accountType','branchRelation'])->select('id','branch_id', 'account_type_id', 'name', 'phone', 'address','percent', 'description')
                ->where('accounts.branch_id', $this->branch_id)
                ->orderBy('id', 'DESC');
            // } 
            // else 
            // {
            //     $accounts = Account::with(['accountType','branchRelation'])->select('id','branch_id', 'account_type_id', 'name', 'phone', 'address', 'description')
            //     ->orderBy('id', 'DESC');
            // }


            // return DataTables::eloquent($accounts)
            //     ->addIndexColumn()
            //     ->addColumn('branch_name', function ($account) {
            //         return $account->branchRelation ? $account->branchRelation->name : '-';
            //     })
            //     ->addColumn('account_type', function ($account) {
            //         return $account->accountType ? $account->accountType->name : '-';
            //     })
            //     ->addColumn('name', function ($account) {
            //         return $account->account_type_id == 5 ? $account->name . ' '. $account->percent . '%' : $account->name;
            //     })
            //     ->addColumn('view', function ($account) {
            //         return '<i class="fas fa-eye viewAccount" data-id="' . $account->id . '" style="font-size:20px;"></i>';
            //     })
            //     ->addColumn('edit', function ($account) {
            //         return '<i class="fas fa-pen-square editAccount" data-id="' . $account->id . '" style="font-size:20px;"></i>';
            //     })
            //     ->addColumn('delete', function ($account) {
            //         return $account->accountType->is_disabled == 0  ? '<i class="fas fa-trash-alt deleteAccount" data-id="' . $account->id . '" style="font-size:20px; color:red;"></i>' : '';
            //     })
            //     ->rawColumns(['view','edit', 'delete'])
            //     ->make(true);

            return DataTables::eloquent($accounts)
            ->addIndexColumn()
            ->addColumn('branch_name', function ($account) {
                return $account->branchRelation ? $account->branchRelation->name : '-';
            })
            ->addColumn('account_type', function ($account) {
                return $account->accountType ? $account->accountType->name : '-';
            })
            ->addColumn('name', function ($account) {
                return $account->account_type_id == 5 ? $account->name . ' ' . $account->percent . '%' : $account->name;
            })
            ->addColumn('view', function ($account) {
                return '<i class="fas fa-eye viewAccount" data-id="' . $account->id . '" style="font-size:20px;"></i>';
            })
            ->addColumn('edit', function ($account) {
                return '<i class="fas fa-pen-square editAccount" data-id="' . $account->id . '" style="font-size:20px;"></i>';
            })
            ->addColumn('delete', function ($account) {
                return $account->accountType->is_disabled == 0  ? '<i class="fas fa-trash-alt deleteAccount" data-id="' . $account->id . '" style="font-size:20px; color:red;"></i>' : '';
            })
            ->filterColumn('branch_name', function($query, $keyword) {
                $query->whereHas('branchRelation', function($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('account_type', function($query, $keyword) {
                $query->whereHas('accountType', function($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('name', function($query, $keyword) {
                $query->where(function($q) use ($keyword) {
                    // Search in the original name field
                    $q->where('name', 'like', "%{$keyword}%");
                    
                    // Also search in the combination of name and percent for account_type_id == 5
                    $q->orWhereRaw("CONCAT(name, ' ', percent, '%') like ?", ["%{$keyword}%"]);
                });
            })
            ->rawColumns(['view','edit', 'delete'])
            ->make(true);
        }

        return view('settings.accounts.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $accountTypes = AccountType::select('id','name')->where('is_disabled',0)->get();
        $currencies = Currency::all();
        $branchs = Branch::where('id',$this->branch_id)->get();
        return view('settings.account.addForm', compact('accountTypes','currencies','branchs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store2(Request $request)
    {
        $messages = [
            'account_type_id_required' => __('validate.account_type_id_required'),
            'name.required' => __('validate.pre_list_name_required'),
            'name.max' => __('validate.pre_list_name_max'),
            'name.min' => __('validate.pre_list_name_min'),
            'name.unique' => __('validate.pre_list_name_unique'),
            'branch_id.required' => __('validate.pre_list_branch_id_required'),
        ];


        $validated = $request->validate([
            'account_type_id' => 'required|exists:account_types,id',
            'name' => 'required|string|max:255|min:3|unique:accounts,name',
            'branch_id' => 'required|exists:branches,id',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:1000',
            'is_pre_select' => 'nullable|numeric',
            'percent' => 'nullable|numeric',
        ], $messages);

        DB::beginTransaction();
        try {
            // Create Account
            $account = Account::create($validated);

            // Handle Journal Entries (only if amount[] exists and is > 0)
            if (!empty($request->amount) && is_array($request->amount) && collect($request->amount)->sum() > 0) {
                $newJournalCode = Journal::where('branch_id', $this->branch_id)->max('code') + 1;

                $jalaliDate = Jalalian::now();
                $year = $jalaliDate->getYear();
                $month = $jalaliDate->getMonth();
                $day = $jalaliDate->getDay();
                $short_date = "$year-$month-$day";
                $times = time();

                $transactionMapping = [
                    1 => ['tType' => 1, 'pType' => 1, 'option_label' => 'افزایش نقده'], // افزایش پول نقد (Cash Received)
                    2 => ['tType' => 2, 'pType' => 2, 'option_label' => 'ثبت طلب'], // ثبت در بخش طلبات (Paid Loan)
                    3 => ['tType' => 1, 'pType' => 2, 'option_label' => 'ثبت قرضه'], // ثبت در بخش قرضه (Received Loan)
                ];

                $journalEntries = [];
                foreach ($request->amount as $key => $value) {
                    if ($value > 0) {
                        $types = $transactionMapping[$request->options[$key]] ?? ['tType' => 1, 'pType' => 1];

                        $journalEntries[] = [
                            'code' => $newJournalCode,
                            'account_id' => $account->id,
                            'branch_id' => $request->branch_id,
                            'amount' => $value,
                            'currency_id' => $request->currency_id[$key] ?? null,
                            'transaction_type' => $types['tType'],
                            'payment_type' => $types['pType'],
                            'options'      => $request->options[$key],
                            'option_label' =>  $types['option_label'],
                            'user' => auth()->user()->full_name ?? '',
                            'year' => $year,
                            'month' => $month,
                            'day' => $day,
                            'inserted_short_date' => $short_date,
                            'details' => 'رسید حساب سابقه',
                            'status' => 1,
                            'times' => $times,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }

                // Bulk insert
                Journal::insert($journalEntries);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'حساب موفقانه ثبت گردید']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'خطایی رخ داده است. لطفاً دوباره تلاش کنید.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function storeBkp(Request $request)
    {
        $messages = [
            'account_type_id.required' => 'انتخاب نوع حساب ضروری میباشد',
            'name.required' => 'نام حساب ضروری میباشد',
            'name.max' => 'حداکثر ۱۰۰ حرف مجاز میباشد',
            'name.min' => 'حداقل باید ۳ حرف باشد',
            'name.unique' => 'این نام قبلاً ثبت شده است',
            'branch_id.required' => 'انتخاب شعبه ضروری میباشد',
        ];

        $validated = $request->validate([
            'account_type_id' => 'required|exists:account_types,id',
            // 'name' => 'required|string|max:255|min:3|unique:accounts,name',
            'name' => 'required|string|max:255|min:3|unique:accounts,name,NULL,id,branch_id,' . $request->branch_id,
            'branch_id' => 'required|exists:branches,id',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:1000',
            'is_pre_select' => 'nullable|numeric',
            'percent' => 'nullable|numeric',
        ], $messages);

        DB::beginTransaction();
        try {
            // Create Account
            $account = Account::create($validated);

            // get default company_account_id
            $ownBanks = Account::select('id','name')->where('account_type_id',1)->orderBy('is_pre_select','DESC')->first();
            $from_account_id = $ownBanks->id ?? 0;

            if (!$from_account_id) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'هیچ حساب بانکی برای تراکنش‌ها یافت نشد.',
                ], 400);
            }

            // Handle Journal Entries (only if amount[] exists and is > 0)
            if (!empty($request->amount) && is_array($request->amount) && collect($request->amount)->sum() > 0 && isset($from_account_id)) {
                $newJournalCode = Journal::where('branch_id', $this->branch_id)->max('code') + 1;

                $jalaliDate = Jalalian::now();
                $year = $jalaliDate->getYear();
                $month = $jalaliDate->getMonth();
                $day = $jalaliDate->getDay();
                $short_date = "$year-$month-$day";
                $full_date =  $year.'-'.$month.'-'.$day.' '.Date('h:i:s A');
                $times = time();
               

                // $journalEntries = [];
                foreach (array_filter($request->amount) as $key => $value) 
                {
                    if ($value > 0) 
                    {

                        $amount = $value;
                        $currency_id = $request->currency_id[$key] ?? 0;
                        $details = 'رسید حساب سابقه';
            
                        $to_account_id = $account->id;
                        $branch_id = $request->branch_id ?? 0;


                        //  افزایش پول نقد
                        if(intval($request->options[$key]) === 1) 
                        {
                            //cacheRecieved = t1p1 = دریافت نقد
                            $optionLable = 'آورد نقد';
                            $options = 1;
                            $this->createJournalEntry($optionLable, $to_account_id, $amount,  $ttype = "1", $ptype="1", 
                                    $full_date, $short_date, $details, $newJournalCode, $times,$branch_id, $options, $currency_id);
                                    
                        } 

                        //  ثبت در بخش طلبات
                        else if(intval($request->options[$key]) === 2)
                        {
                            // ثبت طلب مشتری = Paid Loan = t2p2
                            $optionLable = 'ثبت طلب';
                            $options = 2;
                            $this->createJournalEntry($optionLable, $to_account_id, $amount,  $ttype = "2", $ptype="2", 
                                    $full_date, $short_date, $details, $newJournalCode, $times,$branch_id, $options, $currency_id);
                            
                            // ثبت قرض  خزانه = Recieved Loan = t1p2
                            $optionLable = 'ثبت قرض';
                            $options = 2;
                            $this->createJournalEntry($optionLable, $from_account_id, $amount,  $ttype = "1", $ptype="2", 
                                    $full_date, $short_date, $details, $newJournalCode, $times,$branch_id, $options, $currency_id);
                        }
                        // ثبت در بخش قرضه
                        else if(intval($request->options[$key]) === 3)
                        {
                            // ثبت طلب خزانه = Paid Loan = t2p2
                            $optionLable = 'ثبت طلب';
                            $options = 3;
                            $this->createJournalEntry($optionLable, $from_account_id, $amount,  $ttype = "2", $ptype="2", 
                                    $full_date, $short_date, $details, $newJournalCode, $times,$branch_id, $options, $currency_id);
                            
                            // ثبت قرض  مشتری = Recieved Loan = t1p2
                            $optionLable = 'ثبت قرض';
                            $options = 3;
                            $this->createJournalEntry($optionLable, $to_account_id, $amount,  $ttype = "1", $ptype="2", 
                                    $full_date, $short_date, $details, $newJournalCode, $times,$branch_id, $options, $currency_id);

                        }
                       
                    }
                }
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'حساب موفقانه ثبت گردید']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'خطایی رخ داده است. لطفاً دوباره تلاش کنید.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function store(Request $request)
    {
   
        $messages = [
            'account_type_id_required' => __('validate.account_type_id_required'),
            'name.required' => __('validate.pre_list_name_required'),
            'name.max' => __('validate.pre_list_name_max'),
            'name.min' => __('validate.pre_list_name_min'),
            'name.unique' => __('validate.pre_list_name_unique'),
            'branch_id.required' => __('validate.pre_list_branch_id_required'),
        ];


        $validated = $request->validate([
            'account_type_id' => 'required|exists:account_types,id',
            'name' => 'required|string|max:255|min:3|unique:accounts,name,NULL,id,branch_id,' . $request->branch_id,
            'branch_id' => 'required|exists:branches,id',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:1000',
            'is_pre_select' => 'nullable|numeric',
            'net_salary'    => 'nullable|numeric',
            'salary_currency' => 'nullable|numeric',
            'percent' => 'nullable|numeric',
        ], $messages);

        DB::beginTransaction();
        try {
            // Create Account
            $account = Account::create($validated);

            // Get default company_account_id
            $ownBanks = Account::where('account_type_id', 1)
                // ->where('branch_id', $this->branch_id)
                ->orderBy('is_pre_select', 'DESC')
                ->first();

            $from_account_id = $ownBanks->id ?? null;

            // Ensure there's a valid bank account
            if (!$from_account_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('common.not_found'),
                ], 400);
            }

            // Handle Journal Entries
            if (!empty($request->amount) && is_array($request->amount) && collect($request->amount)->sum() > 0) 
            {
                $newJournalCode = Journal::where('branch_id', $this->branch_id)->max('code') + 1;
                $jalaliDate = Jalalian::now();
                $full_date = $jalaliDate->format('Y-m-d h:i:s A');
                $short_date = $jalaliDate->format('Y-m-d');
                $times = time();

                // get participant name
                $pName = Account::select('name')->where('id', $account->id)
                ->where('branch_id', $this->branch_id)
                ->first();

                $participant_name = $pName->name ?? 'No Name';

                foreach (array_filter($request->amount) as $key => $value) {
                    $amount = $value;
                    $currency_id = $request->currency_id[$key] ?? 0;
                    $details = __('validate.add_old_journal');
                    $details2 = __('validate.added_by_participants') . ' '. $participant_name;
                    $to_account_id = $account->id;
                    $branch_id = $request->branch_id ?? 0;

                    switch (intval($request->options[$key])) {
                         /**
                          * افزایش پول نقد
                          *  cacheRecieved = t1p1 = دریافت نقد
                          */
                        case 1:
                            if($validated['account_type_id']==5) // اگر سهم داران پول نقد علاوه نماید برای خزانه باید علاوه شود و سهم دار صرف نام شان در کمنت گرفته شود. 
                            {
                                $this->createJournalEntry(__('validate.cache_in'), $from_account_id, $amount, "1", "1","1", 
                                $full_date, $short_date, $details2, $newJournalCode, $times, $branch_id, 1, $currency_id);
                            } else 
                            {
                                $this->createJournalEntry(__('validate.cache_in'), $to_account_id, $amount, "1", "1","1", 
                                    $full_date, $short_date, $details, $newJournalCode, $times, $branch_id, 1, $currency_id);
                            }
                            break;

                        case 2:
                             /**
                              * ثبت در بخش طلبات
                              */
                              // ثبت طلب مشتری = Paid Loan = t2p2
                            $this->createJournalEntry(__('validate.talab_save'), $to_account_id, $amount, "2", "2","0", 
                                $full_date, $short_date, $details, $newJournalCode, $times, $branch_id, 2, $currency_id);
                           // ثبت قرض  خزانه = Recieved Loan = t1p2
                            $this->createJournalEntry(__('validate.loan_save'), $from_account_id, $amount, "1", "2","0", 
                                $full_date, $short_date, $details, $newJournalCode, $times, $branch_id, 2, $currency_id);
                            break;
                        case 3:
                           // ثبت در بخش قرضه
                           // ثبت طلب خزانه = Paid Loan = t2p2
                            $this->createJournalEntry(__('validate.talab_save'), $from_account_id, $amount, "2", "2","0", 
                                $full_date, $short_date, $details, $newJournalCode, $times, $branch_id, 3, $currency_id);
                            
                            // ثبت قرض  مشتری = Recieved Loan = t1p2
                            $this->createJournalEntry(__('validate.loan_save'), $to_account_id, $amount, "1", "2","0", 
                                $full_date, $short_date, $details, $newJournalCode, $times, $branch_id, 3, $currency_id);
                            break;
                    }
                }
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => __('common.added_successfully')]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'خطایی رخ داده است. لطفاً دوباره تلاش کنید.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


  
    private function createJournalEntry($optionLable, $account_id, $amount, $ttype, $ptype, $belongsToMe, $full_date, $short_date, 
    $details, $newJournalCode, $times,$branch_id, $options, $currency_id)
    {
            $jalaliDate = Jalalian::now();
            $year = $jalaliDate->getYear();
            $month = $jalaliDate->getMonth();
            $day = $jalaliDate->getDay();

            $account_type_id = Account::where('id', $account_id)->value('account_type_id');

            // Create the Journal entry
            Journal::create([
                'bill_no' => 0,
                'code' => $newJournalCode,
                'account_type_id' => $account_type_id,
                'account_id' => $account_id,
                'branch_id' => $branch_id,
                'amount' => $amount,
                'currency_id' => $currency_id,
                'transaction_type' => $ttype,
                'payment_type' => $ptype,
                'options' => $options,
                'option_label' => $optionLable,
                'user' => auth()->user()->full_name ?? '',
                'year' => $year,
                'month' => $month,
                'day' => $day,
                'inserted_short_date' => $short_date,
                'inserted_full_date' => $full_date,
                'details' => $details,
                'status' => 1,  
                'times' => $times,
                'is_single_record' => 1,
                'belongsToMe' => $belongsToMe,
            ]);

            // Log::info('Journal entry created successfully.');
    }


    /**
     * Display the specified resource for edit
     */
    public function show($id)
    {
        $account = Account::find($id);

        if (!$account) {
            return response()->json(['status' => 'failed', 'message' => __('common.not_found')], 404);
        }

        $ownBanks = Account::where('account_type_id', 1)
            ->orderBy('is_pre_select', 'DESC')
            // ->where('branch_id',$this->branch_id)
            ->first();

        $default_account_id = $ownBanks->id ?? null;

        // Start the query
        $journals = Journal::with(['currencyRelation' => function($query) {
            $query->select('id', 'name'); // Ensure you also select the 'id' field as it's the foreign key
        }])
        ->select('amount', 'transaction_type', 'currency_id', 'times', 'code', 'branch_id', 'options') // Select fields from the Journal model
        ->where('account_id', $id);

        // Apply additional condition if default account matches
        if ($default_account_id == $id) {
            $journals->where('belongsToMe', 1);
        }

        $journals = $journals->where('status', 1)->get();

        return view('settings.account.viewForm', compact('account', 'journals'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $account = Account::find($id);
        $accountTypes = AccountType::select('id','name')->where('is_disabled',0)->get();
        $currencies = Currency::all();
        $branchs = Branch::where('id',$this->branch_id)->get();

        $ownBanks = Account::where('account_type_id', 1)
            ->orderBy('is_pre_select', 'DESC')
            // ->where('branch_id', $this->branch_id)
            ->first();

        $default_account_id = $ownBanks->id ?? null;

        $journals = Journal::with(['currencyRelation' => function($query) {
            $query->select('id', 'name'); // Ensure you also select the 'id' field as it's the foreign key
        }])->select('amount', 'transaction_type', 'currency_id','times','code','branch_id','options') // Select fields from the Journal model
          ->where('account_id', $id);


        // Apply additional condition if default account matches
        if ($default_account_id == $id) {
            $journals->where('belongsToMe', 1);
        }

        $journals = $journals->where('status', 1)->get();

        if (!$account) {
            return response()->json(['status' => 'failed','message' => __('common.not_found')], 404);
        }

        return view('settings.account.editForm', compact('account', 'accountTypes','currencies','journals','branchs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $messages = [
            'account_type_id_required' => __('validate.account_type_id_required'),
            'name.required' => __('validate.pre_list_name_required'),
            'name.max' => __('validate.pre_list_name_max'),
            'name.min' => __('validate.pre_list_name_min'),
            'name.unique' => __('validate.pre_list_name_unique'),
            'branch_id.required' => __('validate.pre_list_branch_id_required'),
        ];

        $validated = $request->validate([
            'account_type_id' => 'required|exists:account_types,id',
            'name' => 'required|string|max:255|min:3',
            'branch_id' => 'required|exists:branches,id',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:1000',
            'is_pre_select' => 'nullable|numeric',
            'net_salary'    => 'nullable|numeric',
            'salary_currency' => 'nullable|numeric',
            'percent' => 'nullable|numeric',
        ], $messages);

        DB::beginTransaction();
        try {
            // Find and update the account
            $account = Account::findOrFail($request->id);
            $account->update($validated);
            
            // Get default company_account_id
            $ownBanks = Account::where('account_type_id', 1)
                // ->where('branch_id', $this->branch_id)
                ->orderBy('is_pre_select', 'DESC')
                ->first();

            $from_account_id = $ownBanks->id ?? null;
            
            // Ensure there's a valid bank account
            if (!$from_account_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('validate.not_found'),
                ], 400);
            }
            
            // Handle Journal Entries
            if (!empty($request->amount) && is_array($request->amount) && collect($request->amount)->sum() > 0) 
            {

                // get participant name
                $pName = Account::select('name')->where('id', $account->id)
                ->where('branch_id', $this->branch_id)
                ->first();

                $participant_name = $pName->name ?? 'No Name';

                // delete old journal records
                Journal::where('times', $request->times)->delete();
                $newJournalCode = Journal::where('branch_id', $this->branch_id)->max('code') + 1;
                $jalaliDate = Jalalian::now();
                $full_date = $jalaliDate->format('Y-m-d h:i:s A');
                $short_date = $jalaliDate->format('Y-m-d');
                $times = time();

                foreach (array_filter($request->amount) as $key => $value) {
                    $amount = $value;
                    $currency_id = $request->currency_id[$key] ?? 0;
                    $details = __('validate.add_old_journal');
                    $details2 = __('validate.added_by_participants') . ' '. $participant_name;
                    $to_account_id = $account->id;
                    $branch_id = $request->branch_id ?? 0;

                    switch (intval($request->options[$key])) {
                            /**
                             * افزایش پول نقد
                             *  cacheRecieved = t1p1 = دریافت نقد
                             */
                        case 1:
                            if($validated['account_type_id']==5) // اگر سهم داران پول نقد علاوه نماید برای خزانه باید علاوه شود و سهم دار صرف نام شان در کمنت گرفته شود. 
                            {
                                $this->createJournalEntry(__('validate.cache_in'), $from_account_id, $amount, "1", "1","1", 
                                $full_date, $short_date, $details2, $newJournalCode, $times, $branch_id, 1, $currency_id);
                            } else 
                            {
                                $this->createJournalEntry(__('validate.cache_in'), $to_account_id, $amount, "1", "1","1", 
                                    $full_date, $short_date, $details, $newJournalCode, $times, $branch_id, 1, $currency_id);
                            }
                            break;

                        case 2:
                            // ثبت در بخش طلبات
                            // ثبت طلب مشتری = Paid Loan = t2p2
                            $this->createJournalEntry(__('validate.talab_save'), $to_account_id, $amount, "2", "2","0", 
                                $full_date, $short_date, $details, $newJournalCode, $times, $branch_id, 2, $currency_id);
                            // ثبت قرض  خزانه = Recieved Loan = t1p2
                            $this->createJournalEntry(__('validate.loan_save'), $from_account_id, $amount, "1", "2","0", 
                                $full_date, $short_date, $details, $newJournalCode, $times, $branch_id, 2, $currency_id);
                            break;
                        case 3:
                            // ثبت در بخش قرضه
                            // ثبت طلب خزانه = Paid Loan = t2p2
                            $this->createJournalEntry(__('validate.talab_save'), $from_account_id, $amount, "2", "2","0", 
                                $full_date, $short_date, $details, $newJournalCode, $times, $branch_id, 3, $currency_id);
                            
                            // ثبت قرض  مشتری = Recieved Loan = t1p2
                            $this->createJournalEntry(__('validate.loan_save'), $to_account_id, $amount, "1", "2","0", 
                                $full_date, $short_date, $details, $newJournalCode, $times, $branch_id, 3, $currency_id);
                            break;
                    }
                }
            } 
            else 
            {
                $journals = Journal::where('times', $request->times);
                // Check if any related journals exist and delete them
                if ($journals->exists()) {
                    $journals->delete(); // Delete all related journal records
                }
            }
            DB::commit();
            return response()->json(['status' => 'success', 'message' => __('validate.added_successfully')]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'خطایی رخ داده است. لطفاً دوباره تلاش کنید.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
    * Remove the specified resource from storage.
    */

    public function destroy($id)
    {
        DB::beginTransaction();
        try 
        {
            $account = Account::find($id);

            // Check if account exists before accessing properties
            if (!$account) {
                return response()->json([
                    'status' => 'failed', 
                    'message' => __('validate.not_found')
                ]);
            }

            // Log the deletion attempt with formatted date
            // Log::info('Deleting journal by created_at:', ['created_at' => $account->created_at->toDateTimeString()]);

            // Check if the account has related records
            $boughtItemExists = BoughtItem::where('account_id', $id)->orWhere('customer_account_id', $id)->exists();
            $boughtItemDetailsExists = BoughtItemDetails::where('customer_account_id', $id)->exists();
            $warehouseSalesExists = WarehouseSales::where('account_id', $id)->orWhere('customer_account_id', $id)->exists();

            // If any record exists, prevent deletion
            if ($boughtItemExists || $boughtItemDetailsExists || $warehouseSalesExists) {
                return response()->json([
                    'status' => 'failed', 
                    'message' => __('validate.has_records_in_tables')
                ]);
            }


            // Delete related journals by date (ignoring time precision issues)
            Journal::where('created_at', $account->created_at)->delete();

            // Delete the account
            $account->delete();

            DB::commit();
            return response()->json([
                'status' => 'success', 
                'message' => __('common.deleted_successfully'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => __('common.delete_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting\Account;
use App\Models\Setting\Currency;
use App\Models\Setting\Branch;
use App\Models\Setting\Car;
use App\Models\Setting\AccountType;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Transaction\Journal;
use Carbon\Carbon; 
use Yajra\DataTables\Facades\DataTables;
use App\Models\Buy\BoughtItem;
use App\Models\Buy\BoughtItemDetails;
use App\Models\Warehouse\WarehouseSales;

class AccountController extends Controller
{
    
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
    public function index(Request $request)
    {
        
        if ($request->ajax()) {

            $accounts = Account::with(['accountType'])->select('id', 'account_type_id', 'name', 'phone', 'address','percent', 'description','loan_limit','loan_limit_option')
            ->orderBy('id', 'DESC');

            return DataTables::eloquent($accounts)
            ->addIndexColumn()
            ->addColumn('account_type', function ($account) {
                return $account->accountType ? $account->accountType->name : '-';
            })
            ->addColumn('name', function ($account) {
                return $account->account_type_id == 5 ? $account->name . ' ' . $account->percent . '%' : $account->name;
            })
            
           ->addColumn('loan_limit', function ($account) {
                return ((int)$account->loan_limit > 0) 
                    ? ($account->loan_limit_option == 1 
                        ? '<i class="fas fa-check-circle text-success"></i> ' 
                        : '<i class="fas fa-times-circle text-danger"></i> ') . $account->loan_limit
                    : '';
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
            ->rawColumns(['view','edit', 'delete','loan_limit'])
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
        $cars = Car::all();
        return view('settings.account.addForm', compact('accountTypes','currencies','cars'));
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
   
        $messages = [
            'account_type_id_required' => __('validate.account_type_id_required'),
            'name.required' => __('validate.pre_list_name_required'),
            'name.max' => __('validate.pre_list_name_max'),
            'name.min' => __('validate.pre_list_name_min'),
            'name.unique' => __('validate.pre_list_name_unique'),
        ];


        $validated = $request->validate([
            'account_type_id' => 'required|exists:account_types,id',
            'name' => 'required|string|max:255|min:3|unique:accounts,name',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:1000',
            'is_pre_select' => 'nullable|numeric',
            'net_salary'    => 'nullable|numeric',
            'salary_currency' => 'nullable|numeric',
            'percent' => 'nullable|numeric',
            'loan_limit' => 'nullable|numeric',
            'loan_limit_option' => 'nullable|numeric',
            'emp_car_id' => 'nullable|numeric',
            'emp_start_date' => 'nullable',
        ], $messages);

        DB::beginTransaction();
        try 
        {
            $data = $request->all();
              // Only parse if value exists
            if ($request->filled('emp_start_date')) {
                $data['emp_start_date'] = Carbon::parse($request->emp_start_date)->format('Y-m-d');
            } else {
                $data['emp_start_date'] = null; 
            }
            // Create Account
            $account = Account::create($data);

            // Get default company_account_id
            $ownBanks = Account::where('account_type_id', 1)
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
                $newJournalCode = Journal::max('code') + 1;
                $miladiDate = Carbon::now();
                $full_date = $miladiDate->format('Y-m-d h:i:s A');
                $short_date = $miladiDate->format('Y-m-d');
                $times = time();

                // get participant name
                $pName = Account::select('name')->where('id', $account->id)->first();

                $participant_name = $pName->name ?? 'No Name';

                foreach (array_filter($request->amount) as $key => $value) {
                    $amount = $value;
                    $currency_id = $request->currency_id[$key] ?? 0;
                    $details = __('validate.add_old_journal');
                    $details2 = __('validate.added_by_participants') . ' '. $participant_name;
                    $to_account_id = $account->id;

                    switch (intval($request->options[$key])) {
                         /**
                          * افزایش پول نقد
                          *  cacheRecieved = t1p1 = دریافت نقد
                          */
                        case 1:
                            if($validated['account_type_id']==5) // اگر سهم داران پول نقد علاوه نماید برای خزانه باید علاوه شود و سهم دار صرف نام شان در کمنت گرفته شود. 
                            {
                                $this->createJournalEntry(__('validate.cache_in'), $from_account_id, $amount, "1", "1","1", 
                                $full_date, $short_date, $details2, $newJournalCode, $times, 1, $currency_id);
                            } else 
                            {
                                $this->createJournalEntry(__('validate.cache_in'), $to_account_id, $amount, "1", "1","1", 
                                    $full_date, $short_date, $details, $newJournalCode, $times, 1, $currency_id);
                            }
                            break;

                        case 2:
                             /**
                              * ثبت در بخش طلبات
                              */
                              // ثبت طلب مشتری = Paid Loan = t2p2
                            $this->createJournalEntry(__('validate.talab_save'), $to_account_id, $amount, "2", "2","0", 
                                $full_date, $short_date, $details, $newJournalCode, $times, 2, $currency_id);
                           // ثبت قرض  خزانه = Recieved Loan = t1p2
                            $this->createJournalEntry(__('validate.loan_save'), $from_account_id, $amount, "1", "2","0", 
                                $full_date, $short_date, $details, $newJournalCode, $times, 2, $currency_id);
                            break;
                        case 3:
                           // ثبت در بخش قرضه
                           // ثبت طلب خزانه = Paid Loan = t2p2
                            $this->createJournalEntry(__('validate.talab_save'), $from_account_id, $amount, "2", "2","0", 
                                $full_date, $short_date, $details, $newJournalCode, $times, 3, $currency_id);
                            
                            // ثبت قرض  مشتری = Recieved Loan = t1p2
                            $this->createJournalEntry(__('validate.loan_save'), $to_account_id, $amount, "1", "2","0", 
                                $full_date, $short_date, $details, $newJournalCode, $times, 3, $currency_id);
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
    $details, $newJournalCode, $times, $options, $currency_id)
    {
            $miladiDate = Carbon::now();
            $year = $miladiDate->year;
            $month = $miladiDate->month;
            $day = $miladiDate->day;

            $account_type_id = Account::where('id', $account_id)->value('account_type_id');

            // Create the Journal entry
            Journal::create([
                'bill_no' => 0,
                'code' => $newJournalCode,
                'account_type_id' => $account_type_id,
                'account_id' => $account_id,
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
                'idate' => $short_date,
                'details' => $details,
                'status' => 1,  
                'times' => $times,
                'is_single_record' => 1,
                'belongsToMe' => $belongsToMe
            ]);

            // Log::info('Journal entry created successfully.');
    }


    /**
     * Display the specified resource for edit
     */
    public function show($id)
    {
        $account = Account::with('car')->find($id);

        if (!$account) {
            return response()->json(['status' => 'failed', 'message' => __('common.not_found')], 404);
        }

        $ownBanks = Account::where('account_type_id', 1)
            ->orderBy('is_pre_select', 'DESC')
            ->first();

        $default_account_id = $ownBanks->id ?? null;

        // Start the query
        $journals = Journal::with(['currencyRelation' => function($query) {
            $query->select('id', 'name'); // Ensure you also select the 'id' field as it's the foreign key
        }])
        ->select('amount', 'transaction_type', 'currency_id', 'times', 'code', 'options') // Select fields from the Journal model
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
        if ($account->emp_start_date) {
          $account->emp_start_date = Carbon::parse($account->emp_start_date)->format('Y-m-d');
         }
        $accountTypes = AccountType::select('id','name')->where('is_disabled',0)->get();
        $currencies = Currency::all();

        $ownBanks = Account::where('account_type_id', 1)
            ->orderBy('is_pre_select', 'DESC')
            ->first();

        $default_account_id = $ownBanks->id ?? null;

        $journals = Journal::with(['currencyRelation' => function($query) {
            $query->select('id', 'name'); // Ensure you also select the 'id' field as it's the foreign key
        }])->select('amount', 'transaction_type', 'currency_id','times','code','options') // Select fields from the Journal model
          ->where('account_id', $id);


        // Apply additional condition if default account matches
        if ($default_account_id == $id) {
            $journals->where('belongsToMe', 1);
        }

        $journals = $journals->where('status', 1)->get();
        $cars = Car::all();
        if (!$account) {
            return response()->json(['status' => 'failed','message' => __('common.not_found')], 404);
        }

        return view('settings.account.editForm', compact('account', 'accountTypes','currencies','journals','cars'));
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
        ];

        $validated = $request->validate([
            'account_type_id' => 'required|exists:account_types,id',
            'name' => 'required|string|max:255|min:3',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:1000',
            'is_pre_select' => 'nullable|numeric',
            'net_salary'    => 'nullable|numeric',
            'salary_currency' => 'nullable|numeric',
            'percent' => 'nullable|numeric',
            'loan_limit' => 'nullable|numeric',
            'loan_limit_option' => 'nullable|numeric'
        ], $messages);

        DB::beginTransaction();
        try {
            // Find and update the account
            $account = Account::findOrFail($request->id);

             $data = $request->all();
              // Only parse if value exists
            if ($request->filled('emp_start_date')) {
                $data['emp_start_date'] = Carbon::parse($request->emp_start_date)->format('Y-m-d');
            } else {
                $data['emp_start_date'] = null; 
            }

            if (!$account) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('validate.not_found'),
                ], 400);
            }
            $account->update($data);
            
            // Get default company_account_id
            $ownBanks = Account::where('account_type_id', 1)
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
                $pName = Account::select('name')->where('id', $account->id)->first();

                $participant_name = $pName->name ?? 'No Name';

                // delete old journal records
                Journal::where('times', $request->times)->delete();
                $newJournalCode = Journal::max('code') + 1;
                $miladiDate = Carbon::now();
                $full_date = $miladiDate->format('Y-m-d h:i:s A');
                $short_date = $miladiDate->format('Y-m-d');
                $times = time();

                foreach (array_filter($request->amount) as $key => $value) {
                    $amount = $value;
                    $currency_id = $request->currency_id[$key] ?? 0;
                    $details = __('validate.add_old_journal');
                    $details2 = __('validate.added_by_participants') . ' '. $participant_name;
                    $to_account_id = $account->id;

                    switch (intval($request->options[$key])) {
                            /**
                             * افزایش پول نقد
                             *  cacheRecieved = t1p1 = دریافت نقد
                             */
                        case 1:
                            if($validated['account_type_id']==5) // اگر سهم داران پول نقد علاوه نماید برای خزانه باید علاوه شود و سهم دار صرف نام شان در کمنت گرفته شود. 
                            {
                                $this->createJournalEntry(__('validate.cache_in'), $from_account_id, $amount, "1", "1","1", 
                                $full_date, $short_date, $details2, $newJournalCode, $times, 1, $currency_id);
                            } else 
                            {
                                $this->createJournalEntry(__('validate.cache_in'), $to_account_id, $amount, "1", "1","1", 
                                    $full_date, $short_date, $details, $newJournalCode, $times, 1, $currency_id);
                            }
                            break;

                        case 2:
                            // ثبت در بخش طلبات
                            // ثبت طلب مشتری = Paid Loan = t2p2
                            $this->createJournalEntry(__('validate.talab_save'), $to_account_id, $amount, "2", "2","0", 
                                $full_date, $short_date, $details, $newJournalCode, $times, 2, $currency_id);
                            // ثبت قرض  خزانه = Recieved Loan = t1p2
                            $this->createJournalEntry(__('validate.loan_save'), $from_account_id, $amount, "1", "2","0", 
                                $full_date, $short_date, $details, $newJournalCode, $times, 2, $currency_id);
                            break;
                        case 3:
                            // ثبت در بخش قرضه
                            // ثبت طلب خزانه = Paid Loan = t2p2
                            $this->createJournalEntry(__('validate.talab_save'), $from_account_id, $amount, "2", "2","0", 
                                $full_date, $short_date, $details, $newJournalCode, $times, 3, $currency_id);
                            
                            // ثبت قرض  مشتری = Recieved Loan = t1p2
                            $this->createJournalEntry(__('validate.loan_save'), $to_account_id, $amount, "1", "2","0", 
                                $full_date, $short_date, $details, $newJournalCode, $times, 3, $currency_id);
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

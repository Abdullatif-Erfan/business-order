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
use App\Models\Journal\Journal;
use Morilog\Jalali\Jalalian;
use Yajra\DataTables\Facades\DataTables;

class AccountController extends Controller
{
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
            $accounts = Account::with('accountType')->select('id', 'account_type_id', 'name', 'phone', 'address', 'description')->orderBy('id', 'DESC');

            return DataTables::eloquent($accounts)
                ->addIndexColumn()
                ->addColumn('account_type', function ($account) {
                    return $account->accountType ? $account->accountType->name : '-';
                })
                ->addColumn('view', function ($account) {
                    return '<i class="fas fa-eye viewAccount" data-id="' . $account->id . '" style="font-size:20px;"></i>';
                })
                ->addColumn('edit', function ($account) {
                    return '<i class="fas fa-pen-square editAccount" data-id="' . $account->id . '" style="font-size:20px;"></i>';
                })
                ->addColumn('delete', function ($account) {
                    return '<i class="fas fa-trash-alt deleteAccount" data-id="' . $account->id . '" style="font-size:20px; color:red;"></i>';
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
        $accountTypes = AccountType::all();
        $currencies = Currency::all();
        $branchs = Branch::all();
        return view('settings.account.addForm', compact('accountTypes','currencies','branchs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
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
            'name' => 'required|string|max:255|min:3|unique:accounts,name',
            'branch_id' => 'required|exists:branches,id',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:1000',
        ],$messages);

        DB::beginTransaction();
        try {
            // Create Account
            $account = Account::create($validated);

            // Handle Journal Entries (only if amount[] exists and is > 0)
            if (!empty($request->amount) && is_array($request->amount) && $request->amount[0] > 0) {
                $journalCode = Journal::latest('code')->value('code');
                $newJournalCode = $journalCode ? $journalCode + 1 : 1;
                $jalaliDate = Jalalian::now();
                $year = $jalaliDate->getYear();
                $month = $jalaliDate->getMonth();
                $day = $jalaliDate->getDay();
                $short_date = $year.'-'.$month.'-'.$day;

                $times = time();

                foreach ($request->amount as $key => $value) {
                    if ($value > 0) {
                        Journal::create([
                            'code' => $newJournalCode,
                            'account_id' => $account->id,
                            'branch_id' => $request->branch_id,
                            'amount' => $value,
                            'currency_id' => $request->currency_id[$key] ?? null,
                            'transaction_type' => $request->transaction_type[$key] ?? null,
                            'payment_type' => 1,
                            'user_id' => Session::get('userId', 0),
                            'year' => $year,
                            'month' => $month,
                            'day' => $day,
                            'inserted_short_date' => $short_date,
                            'details' => 'رسید حساب سابقه',
                            'status' => 1,
                            'times' => $times,
                        ]);
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
    /**
     * Display the specified resource for edit
     */
    public function show($id)
    {
        $account = Account::find($id);
        // $accountTypes = AccountType::all();
        // $currencies = Currency::all();
        
        $journals = Journal::with(['currencyRelation' => function($query) {
            $query->select('id', 'name'); // Ensure you also select the 'id' field as it's the foreign key
          }])->select('amount', 'transaction_type', 'currency_id','times','code','branch_id') // Select fields from the Journal model
          ->where('account_id', $id)
          ->get();


        if (!$account) {
            return response()->json(['status' => 'failed','message' => 'حساب یافت نگردید'], 404);
        }

        return view('settings.account.viewForm', compact('account','journals'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $account = Account::find($id);
        $accountTypes = AccountType::all();
        $currencies = Currency::all();
        $branchs = Branch::all();

        $journals = Journal::with(['currencyRelation' => function($query) {
            $query->select('id', 'name'); // Ensure you also select the 'id' field as it's the foreign key
        }])->select('amount', 'transaction_type', 'currency_id','times','code','branch_id') // Select fields from the Journal model
          ->where('account_id', $id)
          ->get();


        if (!$account) {
            return response()->json(['status' => 'failed','message' => 'حساب یافت نگردید'], 404);
        }

        return view('settings.account.editForm', compact('account', 'accountTypes','currencies','journals','branchs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
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
            'name' => 'required|string|max:255|min:3|unique:accounts,name,' . $request->id,
            'branch_id' => 'required|exists:branches,id',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:1000',
        ],$messages);

        DB::beginTransaction();
        try {
            // Find and update the account
            $account = Account::findOrFail($request->id);
            $account->update($validated);

            // Handle Journal Entries (only if amount[] exists and is > 0)
            if (!empty($request->amount) && is_array($request->amount) && count($request->amount) > 0 && $request->amount[0] > 0) {

                
                // delete the old journal
                $jrnal = Journal::where('times', $request->times)->delete();
                
                    
                     $journalNewCode = Journal::latest('code')->value('code');
                     $newJournalCode = $journalNewCode ? $journalNewCode + 1 : 1;
                     $curTimes = time();

                     $journalCode = $request->code ?? $newJournalCode;
                     $jalaliDate = Jalalian::now();
                     $times = $request->times ?? $curTimes;

                    $year = $jalaliDate->getYear();
                    $month = $jalaliDate->getMonth();
                    $day = $jalaliDate->getDay();
                    $short_date = $year.'-'.$month.'-'.$day;


                    foreach ($request->amount as $key => $value) {
                        if ($value > 0) {
                           
                            Journal::create([
                                'code' => $journalCode,
                                'account_id' => $account->id,
                                'branch_id' => $request->branch_id,
                                'amount' => $value,
                                'currency_id' => $request->currency_id[$key] ?? null,
                                'transaction_type' => $request->transaction_type[$key] ?? null,
                                'payment_type' => 1,
                                'user_id' => Session::get('userId', 0),
                                'year' => $year,
                                'month' => $month,
                                'day' => $day,
                                'inserted_short_date' => $short_date,
                                'details' => 'رسید حساب سابقه',
                                'status' => 1,
                                'times' => $times,
                            ]);
                        }
                    }
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'حساب با موفقیت به روز رسانی شد']);
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
        $account = Account::find($id);

        if (!$account) {
            return response()->json(['status' => 'failed', 'message' => 'حساب یافت نگردید']);
        }

        $account->delete();

        return response()->json(['status' => 'success', 'message' => 'حساب موفقانه حذف گردید']);
    }
}

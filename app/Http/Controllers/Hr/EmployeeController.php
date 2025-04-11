<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting\Account;
use App\Models\Setting\Currency;
use App\Models\Setting\Branch;
use App\Models\Setting\AccountType;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
// use App\Models\Transaction\Journal;
use Morilog\Jalali\Jalalian;
use App\Models\Setting\OrgBio;
use Yajra\DataTables\Facades\DataTables;

class EmployeeController extends Controller
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
    
    public function index(Request $request)
    {
        // $currencyes = Account::latest()->paginate(10); 
        //   $accounts = Account::with('salaryCurrency')
        //     ->select('id', 'account_type_id', 'name', 'phone', 'address', 'description','salary_currency','net_salary')
        //     ->where('account_type_id',2)
        //     ->orderBy('id', 'DESC')
        // ->get();

        // return response()->json($accounts);
        $orgbios = OrgBio::all();
        $todaysDate = Jalalian::now()->format('Y-m-d');
        return view('hr.employee.list', compact('orgbios','todaysDate'));
    }

    public function getData(Request $request)
    {
            $employee_account_type_id = 2;
            $accounts = Account::with('salaryCurrency')
            ->select('id', 'account_type_id', 'name', 'phone', 'address', 'description','salary_currency','net_salary')
            ->where('account_type_id',$employee_account_type_id)
            ->where('branch_id', $this->branch_id)
            ->orderBy('id', 'DESC');

            return DataTables::eloquent($accounts)
                ->addIndexColumn()

                ->addColumn('salaryCurrency', function ($account) {
                    return $account->salaryCurrency ? $account->salaryCurrency->name : '';
                })

                ->addColumn('net_salary', function ($account) {
                    return $account->net_salary ? number_format($account->net_salary,2) : '';
                })

                ->addColumn('edit', function ($account) {
                    return '<a href="' . route("employee.edit", ["id" => $account->id]) . '">
                                <i class="fas fa-pen-square editAccount" data-id="' . $account->id . '" style="font-size:20px;"></i>
                            </a>';
                })                
                ->addColumn('delete', function ($account) {
                    return '<a href="' . route("employee.destroy", ["id" => $account->id]) . '" 
                                onclick="return doConfirm()">
                                <i class="fas fa-trash-alt deleteAccount" style="font-size:20px; color:red;"></i>
                            </a>';
                })
                ->rawColumns(['edit', 'delete'])
                ->make(true);
    }

    public function create()
    {
        $branchs = Branch::where('id',$this->branch_id)->get();
        $currencies = Currency::all();

        return view('hr.employee.create', compact('branchs','currencies'));
    }

    public function store(Request $request)
    {
        $messages = [
            'name.required' => 'نام حساب ضروری میباشد',
            'name.max' => 'حداکثر ۱۰۰ حرف مجاز میباشد',
            'name.min' => 'حداقل باید ۳ حرف باشد',
            'name.unique' => 'این نام قبلاً ثبت شده است',
            'branch_id.required' => 'انتخاب شعبه ضروری میباشد',
        ];

        $validated = $request->validate([
            'account_type_id' => 'nullable|integer',
            'name' => 'required|string|max:255|min:3|unique:accounts,name,NULL,id,branch_id,' . $request->branch_id,
            'branch_id' => 'required|exists:branches,id',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'net_salary'    => 'nullable|numeric',
            'salary_currency' => 'nullable|numeric',
        ], $messages);

        try {
             // Set default value for account_type_id if not provided
             $validated['account_type_id'] = $request->filled('account_type_id') ? $request->account_type_id : 2;

            // Create Account
            $account = Account::create($validated);

            Session::flash('notification', [
                'message' => 'موفقانه ثبت گردید ',
                'type' => 'success',
            ]);
            return redirect()->route('employee.index');
        } catch (\Exception $e) {
            Session::flash('notification', [
                'message' => 'ثبت نگردید',
                'type' => 'danger',
            ]);
            return redirect()->route('employee.index');
        }
    }

    public function edit($id)
    {
      
        $currencies = Currency::all();
        $branchs = Branch::where('id',$this->branch_id)->get();
        $account = Account::findOrFail($id);

        if (!$account) {
            return response()->json(['status' => 'failed','message' => 'حساب یافت نگردید'], 404);
        }

        return view('hr.employee.edit', compact('account','currencies','branchs'));
    }

    public function update(Request $request)
{
    // return ['data' => $request->all()];
    $messages = [
        'name.required' => 'نام حساب ضروری میباشد',
        'name.max' => 'حداکثر ۱۰۰ حرف مجاز میباشد',
        'name.min' => 'حداقل باید ۳ حرف باشد',
        'name.unique' => 'این نام قبلاً ثبت شده است',
        'branch_id.required' => 'انتخاب شعبه ضروری میباشد',
    ];

    $validated = $request->validate([
        'id' => 'required|exists:accounts,id',
        'name' => 'required|string|max:255|min:3|unique:accounts,name,' . $request->id . ',id,branch_id,' . $request->branch_id,
        'branch_id' => 'required|exists:branches,id',
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:500',
        'net_salary' => 'nullable|numeric',
        'salary_currency' => 'nullable|numeric',
    ], $messages);

    try {
        // Find and update the account
        $account = Account::findOrFail($request->id);
        $account->update($validated);

        session()->flash('notification', [
            'message' => 'موفقانه ویرایش شد',
            'type' => 'success',
        ]);
        return redirect()->route('employee.index');
    } catch (\Exception $e) {
        session()->flash('notification', [
            'message' => 'ویرایش نشد',
            'type' => 'danger',
        ]);
        return redirect()->route('employee.index');
    }
}


    /**
    * Remove the specified resource from storage.
    */
    public function destroy($id)
    {
        $account = Account::find($id);

        // Check if account exists before accessing properties
        if (!$account) {
            session()->flash('notification', [
                'message' => 'حذف نگردید',
                'type' => 'danger',
            ]);
            return redirect()->route('employee.index');
        }

        $account->delete();
        session()->flash('notification', [
            'message' => 'موفقانه حذف گردید',
            'type' => 'success',
        ]);
        return redirect()->route('employee.index');
    }



}

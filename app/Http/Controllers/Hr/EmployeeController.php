<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting\Account;
use App\Models\Setting\Currency;
use App\Models\Setting\AccountType;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
// use App\Models\Transaction\Journal;
use Carbon\Carbon;
use App\Models\Setting\OrgBio;
use App\Models\Setting\Car;
use Yajra\DataTables\Facades\DataTables;

class EmployeeController extends Controller
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
        // $currencyes = Account::latest()->paginate(10); 
        //   $accounts = Account::with('salaryCurrency')
        //     ->select('id', 'account_type_id', 'name', 'phone', 'address', 'description','salary_currency','net_salary')
        //     ->where('account_type_id',2)
        //     ->orderBy('id', 'DESC')
        // ->get();

        // return response()->json($accounts);
        $orgbios = OrgBio::all();
        $todaysDate = Carbon::now()->format('Y-m-d');
        return view('hr.employee.list', compact('orgbios','todaysDate'));
    }

    public function getData(Request $request)
    {
            $employee_account_type_id = 2;
            $accounts = Account::with(['salaryCurrency','car'])
            ->select('id', 'account_type_id', 'name', 'phone', 'address', 'description','salary_currency','net_salary','emp_car_id',
            'emp_start_date')
            ->where('account_type_id',$employee_account_type_id)
            ->orderBy('id', 'DESC');

            return DataTables::eloquent($accounts)
                ->addIndexColumn()

                ->addColumn('salaryCurrency', function ($account) {
                    return $account->salaryCurrency ? $account->salaryCurrency->name : '';
                })

                ->addColumn('net_salary', function ($account) {
                    return $account->net_salary ? number_format($account->net_salary,2) : '';
                })
                ->addColumn('emp_car_name', function ($account) {
                    return $account->car->name ? $account->car->name : '';
                })
                ->addColumn('emp_start_date', function ($account) {
                    return $account->emp_start_date ? $account->emp_start_date : '';
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
        $currencies = Currency::all();
        $cars = Car::all();
        return view('hr.employee.create', compact('currencies','cars'));
    }

    public function store(Request $request)
    {
        // return ['data' => $request->all()];
        $messages = [
            'name.required' => __('validate.pre_list_name_required'),
            'name.max' => __('validate.pre_list_name_max'),
            'name.min' => __('validate.pre_list_name_min'),
            'name.unique' => __('validate.pre_list_name_unique'),
        ];

        $validated = $request->validate([
            'account_type_id' => 'nullable|integer',
            'name' => 'required|string|max:255|min:3|unique:accounts,name,NULL,id',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'net_salary'    => 'nullable|numeric',
            'salary_currency' => 'nullable|numeric',
            'emp_car_id'    => 'nullable|numeric',
            'emp_start_date' => 'required',
        ], $messages);

        try {
             // Set default value for account_type_id if not provided
             $validated['account_type_id'] = $request->filled('account_type_id') ? $request->account_type_id : 2;

            // Create Account
            $account = Account::create($validated);

            Session::put('notification', [
                'message' => __('common.added_successfully'),
                'type' => 'success',
            ]);
            return redirect()->route('employee.index');
        } catch (\Exception $e) {
            Session::put('notification', [
                'message' => __('common.add_failed'),
                'type' => 'danger',
            ]);
            return redirect()->route('employee.index');
        }
    }

    public function edit($id)
    {
      
        $currencies = Currency::all();
        $account = Account::findOrFail($id);
        // $account = Account::with('car')->findOrFail($id);
        $cars = Car::all();
        if (!$account) {
            return response()->json(['status' => 'failed','message' => __('common.not_found')], 404);
        }

        return view('hr.employee.edit', compact('account','currencies','cars'));
    }

    public function update(Request $request)
{
    // return ['data' => $request->all()];
 
    $messages = [
        'name.required' => __('validate.pre_list_name_required'),
        'name.max' => __('validate.pre_list_name_max'),
        'name.min' => __('validate.pre_list_name_min'),
        'name.unique' => __('validate.pre_list_name_unique'),
    ];

    $validated = $request->validate([
        'id' => 'required|exists:accounts,id',
        'name' => 'required|string|max:255|min:3|unique:accounts,name,' . $request->id . ',id',
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:500',
        'net_salary' => 'nullable|numeric',
        'salary_currency' => 'nullable|numeric',
        'emp_car_id'    => 'nullable|numeric',
        'emp_start_date' => 'required',
    ], $messages);

    try {
        // Find and update the account
        $account = Account::findOrFail($request->id);
        $account->update($validated);

        session()->put('notification', [
            'message' => __('common.updated_successfully'),
            'type' => 'success',
        ]);
        return redirect()->route('employee.index');
    } catch (\Exception $e) {
        session()->put('notification', [
            'message' => __('common.update_failed'),
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
            session()->put('notification', [
                'message' => __('common.delete_failed'),
                'type' => 'danger',
            ]);
            return redirect()->route('employee.index');
        }

        $account->delete();
        session()->put('notification', [
            'message' => __('common.deleted_successfully'),
            'type' => 'success',
        ]);
        return redirect()->route('employee.index');
    }



}

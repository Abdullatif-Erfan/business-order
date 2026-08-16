<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting\Account;
use App\Models\Setting\Currency;
use App\Models\Setting\AccountType;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Setting\OrgBio;
use App\Models\Order\DraftOrder;
use App\Models\Warehouse\WarehouseSales;
use App\Models\Setting\Car;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    protected $isAdmin, $userId, $userName, $customerIds, $carIds;
    
    public function __construct()
    {
        if (auth()->check()) {
            $this->isAdmin = session('isAdmin', auth()->user()->isAdmin == 1);
            $this->userId = session('userId', auth()->user()->id);
            $this->userName = auth()->user()->full_name;
            $this->carIds = session('carIds', []);
            $this->customerIds = session('customerIds', []);
        } else {
            $this->isAdmin = false;
            $this->userId = 0;
            $this->userName = 'System';
            $this->customerIds = [];
            $this->carIds = [];
        }
    }
    
    public function index(Request $request)
    {
        $orgbios = OrgBio::all();
        $todaysDate = Carbon::now()->format('Y-m-d');
        return view('hr.customer.list', compact('orgbios', 'todaysDate'));
    }

    public function getData(Request $request)
    {
        // Get account type from request or default to 3 (customer)
        $accountTypeId = $request->get('account_type_id', 3);
        
        $accounts = Account::with(['salaryCurrency', 'car'])
            ->select('id', 'account_type_id', 'name', 'phone', 'address', 'loan_limit', 'loan_limit_option')
            ->where('account_type_id', $accountTypeId)
            ->orderBy('id', 'DESC');

        if (!$this->isAdmin) {
            $accounts->where('user_account_id', $this->userId);
        }

        return DataTables::eloquent($accounts)
            ->addIndexColumn()
            ->addColumn('name', function ($account) {
                return $account->name ?: '';
            })
            ->addColumn('phone', function ($account) {
                return $account->phone ?: '';
            })  
            ->addColumn('address', function ($account) {
                return $account->address ?: '';
            })
            ->addColumn('loan_limit', function ($account) {
                return ((int)$account->loan_limit > 0) 
                    ? ($account->loan_limit_option == 1 
                        ? '<i class="fas fa-check-circle text-success"></i> ' 
                        : '<i class="fas fa-times-circle text-danger"></i> ') . $account->loan_limit
                    : ($account->loan_limit_option == 1 ? '<i class="fas fa-check-circle text-success"></i> ' : '');
            })
            ->addColumn('edit', function ($account) {
                return '<a href="' . route("customer.edit", ["id" => $account->id]) . '">
                            <i class="fas fa-pen-square editCustomerAccount" data-id="' . $account->id . '" style="font-size:20px;"></i>
                        </a>';
            })                
            ->addColumn('delete', function ($account) {
                return '<a href="' . route("customer.destroy", ["id" => $account->id]) . '" 
                            onclick="return doConfirm()">
                            <i class="fas fa-trash-alt deleteCustomerAccount" style="font-size:20px; color:red;"></i>
                        </a>';
            })
            ->filterColumn('name', function ($query, $keyword) {
               $query->where('name', 'LIKE', "%{$keyword}%");
            })
            ->filterColumn('phone', function ($query, $keyword) {
                $query->where('phone', 'LIKE', "%{$keyword}%");
            })
            ->filterColumn('address', function ($query, $keyword) {
                $query->where('address', 'LIKE', "%{$keyword}%");
            })
            ->rawColumns(['loan_limit', 'edit', 'delete'])
            ->make(true);
    }

    public function create(Request $request)
    {
        $currencies = Currency::all();
        $cars = Car::all();
        $accountTypes = AccountType::whereIn('id', [3])->get(); // Only customer & supplier types
        
        // Get selected type from query param (for switching between customer/supplier)
        $selectedType = $request->get('type', 3); // Default: customer
        
        return view('hr.customer.create', compact('currencies', 'cars', 'accountTypes', 'selectedType'));
    }

    public function store(Request $request)
    {
        $messages = [
            'name.required' => __('validate.pre_list_name_required'),
            'name.max' => __('validate.pre_list_name_max'),
            'name.min' => __('validate.pre_list_name_min'),
            'name.unique' => __('validate.pre_list_name_unique'),
        ];

        $validated = $request->validate([
            'account_type_id' => 'required|integer|in:3', // Only customer or supplier
            'name' => 'required|string|max:255|min:3|unique:accounts,name,NULL,id',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'loan_limit' => 'nullable|numeric|min:0',
            'loan_limit_option' => 'nullable|boolean',
        ], $messages);

        try {
            // Create Account
            $account = Account::create($validated);

            Session::put('notification', [
                'message' => __('common.added_successfully'),
                'type' => 'success',
            ]);
            return redirect()->route('customer.index');
            
        } catch (\Exception $e) {
            Session::put('notification', [
                'message' => __('common.add_failed') . ': ' . $e->getMessage(),
                'type' => 'danger',
            ]);
            return redirect()->back()->withInput();
        }
    }

    public function edit($id)
    {
        $currencies = Currency::all();
        $cars = Car::all();
        $account = Account::findOrFail($id);
        $accountTypes = AccountType::whereIn('id', [3])->get();

        if (!$account) {
            return response()->json(['status' => 'failed', 'message' => __('common.not_found')], 404);
        }

        return view('hr.customer.edit', compact('account', 'currencies', 'cars', 'accountTypes'));
    }

    public function update(Request $request)
    {
        $messages = [
            'name.required' => __('validate.pre_list_name_required'),
            'name.max' => __('validate.pre_list_name_max'),
            'name.min' => __('validate.pre_list_name_min'),
            'name.unique' => __('validate.pre_list_name_unique'),
        ];

        $validated = $request->validate([
            'id' => 'required|exists:accounts,id',
            'account_type_id' => 'required|integer|in:3',
            'name' => 'required|string|max:255|min:3|unique:accounts,name,' . $request->id . ',id',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'loan_limit' => 'nullable|numeric|min:0',
            'loan_limit_option' => 'nullable|boolean',
        ], $messages);

        try {
            $account = Account::findOrFail($request->id);
            $account->update($validated);

            Session::put('notification', [
                'message' => __('common.updated_successfully'),
                'type' => 'success',
            ]);
            return redirect()->route('customer.index');
            
        } catch (\Exception $e) {
            Session::put('notification', [
                'message' => __('common.update_failed') . ': ' . $e->getMessage(),
                'type' => 'danger',
            ]);
            return redirect()->back()->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $account = Account::find($id);

            if (!$account) {
                Session::put('notification', [
                    'message' => __('common.delete_failed'),
                    'type' => 'danger',
                ]);
                return redirect()->route('customer.index');
            }

             // Check if the account has related records
            $draftOrder = DraftOrder::where('customer_id', $id)->exists();
            $warehouseSalesExists = WarehouseSales::where('customer_account_id', $id)->exists();

            // If any record exists, prevent deletion
            if ($draftOrder || $warehouseSalesExists) {
                return response()->json([
                    'status' => 'failed', 
                    'message' => __('validate.has_records_in_tables')
                ]);
            }

            // Check if account has related records before deleting
            // You can add checks here for related journals, transactions, etc.

            $account->delete();
            
            Session::put('notification', [
                'message' => __('common.deleted_successfully'),
                'type' => 'success',
            ]);
            
        } catch (\Exception $e) {
            Session::put('notification', [
                'message' => __('common.delete_failed') . ': ' . $e->getMessage(),
                'type' => 'danger',
            ]);
        }
        
        return redirect()->route('customer.index');
    }
}
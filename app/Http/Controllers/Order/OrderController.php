<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Order\Order;
use App\Models\Setting\Account;
use App\Models\Setting\Currency;
use App\Models\Transaction\Journal;
use App\Models\Setting\Category;
use App\Models\Setting\Branch;
use App\Models\Setting\OrgBio;
use App\Models\Buy\BuyPreList;
use App\Models\Setting\Unit;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
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
    public function index()
    {
        $orgbios = OrgBio::all();
        // $categories = Category::select('id', 'name')->orderBy('name')->get();
        // $employees = Account::select('id','name')->where('account_type_id',2)->get();
        // $suppliers = Account::select('id','name')->where('account_type_id',4)->get();
        $todaysDate = Carbon::now()->format('Y-m-d');

        // return view('order.list', compact('categories', 'employees', 'todaysDate', 'orgbios','suppliers'));
        return view('order.list', compact('todaysDate', 'orgbios'));
    }

    /**
     * Get Data for DataTable
     */
    public function getData(Request $request)
    {
        $orders = Order::with([
            'employeeRelation',
            'supplierRelation',
            'preListRelation',
            'unitRelation',
            'categoryRelation'
        ])
        ->orderBy('id', 'DESC');

        // Apply filters
        if ($request->ord_num) {
            $orders->where('ord_num', 'LIKE', "%{$request->ord_num}%");
        }

        if ($request->supplier_name) {
            $orders->whereHas('supplierRelation', function ($q) use ($request) {
                $q->where('name', 'LIKE', "%{$request->supplier_name}%");
            });
        }

        if ($request->employee_name) {
            $orders->whereHas('employeeRelation', function ($q) use ($request) {
                $q->where('name', 'LIKE', "%{$request->employee_name}%");
            });
        }

        if ($request->category_name) {
            $orders->whereHas('categoryRelation', function ($q) use ($request) {
                $q->where('name', 'LIKE', "%{$request->category_name}%");
            });
        }

        if ($request->state !== null && $request->state !== '') {
            $orders->where('state', $request->state);
        }

        if ($request->start_date && $request->end_date) {
            $orders->whereBetween('idate', [$request->start_date, $request->end_date]);
        } elseif ($request->start_date) {
            $orders->whereDate('idate', '>=', $request->start_date);
        } elseif ($request->end_date) {
            $orders->whereDate('idate', '<=', $request->end_date);
        }

        return DataTables::of($orders)
            ->addIndexColumn()
            ->addColumn('ord_num', function ($order) {
                return $order->ord_num ? $order->ord_num : '-';
            })
            ->addColumn('item_name', function ($order) {
                return $order->preListRelation ? $order->preListRelation->name : '-';
            })
            ->addColumn('supplier_name', function ($order) {
                return $order->supplierRelation ? $order->supplierRelation->name : '-';
            })
            ->addColumn('employee_name', function ($order) {
                return $order->employeeRelation ? $order->employeeRelation->name : '-';
            })
            ->addColumn('category_name', function ($order) {
                return $order->categoryRelation ? $order->categoryRelation->name : '-';
            })
            ->addColumn('unit_name', function ($order) {
                return $order->unitRelation ? $order->unitRelation->name : '-';
            })
            ->addColumn('amount', function ($order) {
                return $order->amount;
            })
            ->addColumn('state', function ($order) {
                if ($order->state == 1) {
                    return '<span class="badge badge-new">' . __('order.new') . '</span>';
                } elseif ($order->state == 2) {
                    return '<span class="badge badge-done">' . __('order.done') . '</span>';
                } elseif ($order->state == 3) {
                    return '<span class="badge badge-cancelled">' . __('order.cancelled') . '</span>';
                }
                return '<span class="badge badge-secondary">' . __('order.unknown') . '</span>';
            })
            ->addColumn('idate', function ($order) {
                return $order->idate ? \Carbon\Carbon::parse($order->idate)->format('Y/m/d') : '-';
            })
            ->addColumn('done_by', function ($order) {
                return $order->done_by ?? '-';
            })
            ->addColumn('action', function ($order) {
                return '
                    <div class="action-icons">
                        <i class="fas fa-eye viewOrder" data-id="' . $order->id . '" title="' . __('common.view') . '"></i>
                        <i class="fas fa-pen-square editOrder" data-id="' . $order->id . '" title="' . __('common.edit') . '"></i>
                        <i class="fas fa-trash-alt deleteOrder" data-id="' . $order->id . '" title="' . __('common.delete') . '"></i>
                    </div>
                ';
            })
            ->filterColumn('supplier_name', function ($query, $keyword) {
                $query->whereHas('supplierRelation', function ($q) use ($keyword) {
                    $q->where('name', 'LIKE', "%{$keyword}%");
                });
            })
            ->filterColumn('employee_name', function ($query, $keyword) {
                $query->whereHas('employeeRelation', function ($q) use ($keyword) {
                    $q->where('name', 'LIKE', "%{$keyword}%");
                });
            })
            ->filterColumn('category_name', function ($query, $keyword) {
                $query->whereHas('categoryRelation', function ($q) use ($keyword) {
                    $q->where('name', 'LIKE', "%{$keyword}%");
                });
            })
            ->rawColumns(['state', 'action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $preLists = BuyPreList::select('id', 'name','category_id')->orderBy('name')->get();
        $units = Unit::select('id', 'name')->orderBy('name')->get();
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $employees = Account::select('id','name')->where('account_type_id',2)->get();
        $suppliers = Account::select('id','name')->where('account_type_id',4)->get();
        $orderNumber = Order::max('ord_num') + 1;
        $miladiDate = Carbon::now();
        $todaysDate = $miladiDate->format('Y-m-d');
        $times = time();
        
        return view('order.create.form', compact('preLists', 'units', 'categories','todaysDate','employees','suppliers',
        'orderNumber','times'));
    }

    /**
     * Add order item (AJAX)
     */
    public function addItem(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'pre_list_id' => 'required|exists:buy_pre_lists,id',
                'amount' => 'required|numeric|min:0',
                'unit_id' => 'nullable|exists:units,id',
                'unit_price' => 'nullable|numeric|min:0',
            ]);

            // Store item in session or return HTML
            $html = view('order.item_row', ['item' => $validated])->render();
            
            return response()->json([
                'status' => 'success',
                'html' => $html,
                'message' => 'Item added successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed to add item: ' . $e->getMessage()
            ], 500);
        }
    }

        /**
         * Check order number duplication
         */
        public function checkDuplication(Request $request)
        {
            $exists = Order::where('ord_num', $request->ord_num)->exists();
            return response()->json(['exists' => $exists]);
        }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'buy_pre_list' => 'required|array|min:1',
            'buy_pre_list.*' => 'required|exists:bought_item_pre_lists,id',
            'amount' => 'required|array|min:1',
            'amount.*' => 'required|numeric|min:0.01',
            'unit_id' => 'required|array|min:1',
            'unit_id.*' => 'required|exists:units,id',
            'category_id' => 'nullable|array',
            // 'category_id.*' => 'nullable|exists:categories,id',
            'supplier_id' => 'required|exists:accounts,id',
            'employee_id' => 'required|exists:accounts,id',
            'ord_num' => 'required|string|max:50',
            'date' => 'required|date',
            'times' => 'required|integer',
        ]);

        DB::beginTransaction();
        try {
            // Combine the arrays into a single collection of items
            $items = collect($validated['buy_pre_list'])->map(function ($preListId, $index) use ($validated) {
                return [
                    'pre_list_id' => $preListId,
                    'amount' => $validated['amount'][$index] ?? 0,
                    'unit_id' => $validated['unit_id'][$index] ?? null,
                    'category_id' => $validated['category_id'][$index] ?? null,
                ];
            })->filter(function ($item) {
                return $item['pre_list_id'] && $item['amount'] > 0;
            });

            foreach ($items as $item) {
                Order::create([
                    'ord_num' => $validated['ord_num'],
                    'pre_list_id' => $item['pre_list_id'],
                    'category_id' => $item['category_id'],
                    'supplier_id' => $validated['supplier_id'],
                    'employee_id' => $validated['employee_id'],
                    'amount' => $item['amount'],
                    'unit_id' => $item['unit_id'],
                    'iby' => auth()->user()->full_name ?? '',
                    'idate' => $validated['date'],
                    'state' => 1, // 1: new, 2:done
                    'times' => $validated['times'],
                ]);
            }

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => __('common.added_successfully')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => __('common.error_occurred') . ': ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $order = Order::with(['preListRelation', 'unitRelation', 'categoryRelation'])
            ->find($id);

        if (!$order) {
            return response()->json([
                'status' => 'failed',
                'message' => __('common.not_found')
            ], 404);
        }

        return view('order.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $order = Order::find($id);
        
        if (!$order) {
            return response()->json([
                'status' => 'failed',
                'message' => __('common.not_found')
            ], 404);
        }

        $preLists = BuyPreList::select('id', 'name')->orderBy('name')->get();
        $units = Unit::select('id', 'name')->orderBy('name')->get();
        $categories = Category::select('id', 'name')->orderBy('name')->get();

        return view('order.edit', compact('order', 'preLists', 'units', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $messages = [
            'ord_num.required' => 'Order number is required',
            'pre_list_id.required' => 'Item is required',
            'amount.required' => 'Amount is required',
            'amount.numeric' => 'Amount must be a number',
            'idate.required' => 'Date is required',
        ];

        $validated = $request->validate([
            'ord_num' => 'required|string|max:50|unique:orders,ord_num,' . $id,
            'pre_list_id' => 'required|exists:buy_pre_lists,id',
            'category_id' => 'nullable|exists:categories,id',
            'amount' => 'required|numeric|min:0',
            'unit_id' => 'nullable|exists:units,id',
            'iby' => 'nullable|string|max:100',
            'idate' => 'required|date',
            'state' => 'nullable|integer|in:0,1,2,3',
            'done_by' => 'nullable|string|max:100',
        ]);

        DB::beginTransaction();
        try {
            $order = Order::findOrFail($id);
            $order->update($validated);

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => __('common.updated_successfully'),
                'data' => $order
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order Update Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update order. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $order = Order::find($id);
            
            if (!$order) {
                return response()->json([
                    'status' => 'failed',
                    'message' => __('common.not_found')
                ], 404);
            }

            $order->delete();

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => __('common.deleted_successfully')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order Delete Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete order. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'state' => 'required|integer|in:0,1,2,3',
            'done_by' => 'nullable|string|max:100'
        ]);

        DB::beginTransaction();
        try {
            $order = Order::findOrFail($id);
            
            $order->state = $validated['state'];
            if ($validated['state'] == 2) { // Completed
                $carbonDate = Carbon::now();
                $order->done_year = $carbonDate->year;
                $order->done_month = $carbonDate->month;
                $order->done_day = $carbonDate->day;
                $order->done_by = $validated['done_by'] ?? auth()->user()->full_name ?? 'System';
            }
            
            $order->save();

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Order status updated successfully',
                'data' => $order
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order Status Update Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update order status.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get order statistics
     */
    public function getStatistics()
    {
        try {
            $stats = [
                'total' => Order::count(),
                'pending' => Order::where('state', 0)->count(),
                'in_progress' => Order::where('state', 1)->count(),
                'completed' => Order::where('state', 2)->count(),
                'cancelled' => Order::where('state', 3)->count(),
                'total_amount' => Order::sum('amount'),
            ];

            return response()->json([
                'status' => 'success',
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Order Statistics Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get statistics.'
            ], 500);
        }
    }

    /**
     * Get orders for dashboard
     */
    public function getDashboardOrders()
    {
        try {
            $recentOrders = Order::with(['preListRelation', 'categoryRelation'])
                ->orderBy('id', 'DESC')
                ->limit(10)
                ->get()
                ->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'ord_num' => $order->ord_num,
                        'item_name' => $order->preListRelation ? $order->preListRelation->name : '-',
                        'amount' => number_format($order->amount, 2),
                        'state' => $order->state,
                        'state_label' => $this->getStateLabel($order->state),
                        'date' => Carbon::parse($order->idate)->format('Y/m/d')
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $recentOrders
            ]);
        } catch (\Exception $e) {
            Log::error('Dashboard Orders Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get dashboard orders.'
            ], 500);
        }
    }

    /**
     * Get state label
     */
    private function getStateLabel($state)
    {
        $labels = [
            0 => 'Pending',
            1 => 'In Progress',
            2 => 'Completed',
            3 => 'Cancelled'
        ];
        return $labels[$state] ?? 'Unknown';
    }
}
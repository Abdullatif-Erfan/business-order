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
use App\Models\Setting\OrgBio;
use App\Models\Buy\BuyPreList;
use App\Models\Setting\Unit;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
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

        // $orders = Order::select(
        //     'id',
        //     'ord_num',
        //     'supplier_id',
        //     'employee_id',
        //     'pre_list_id',
        //     'unit_id',
        //     'category_id',
        //     'amount',
        //     'idate',
        //     'state',
        //     'done_by',
        //     'created_at'
        // )
        // ->with([
        //     'employeeRelation:id,name',
        //     'supplierRelation:id,name',
        //     'preListRelation:id,name',
        //     'unitRelation:id,name',
        //     'categoryRelation:id,name'
        // ])
        // ->whereIn('id', function ($query) {
        //     $query->selectRaw('MAX(id)')
        //         ->from('orders')
        //         ->groupBy('ord_num');
        // })
        // ->orderBy('id', 'DESC')->get();
        // return $orders;

        // return view('order.list', compact('categories', 'employees', 'todaysDate', 'orgbios','suppliers'));
        return view('order.list', compact('todaysDate', 'orgbios'));
    }

    /**
     * Get Data for DataTable
     */
    public function getData(Request $request)
    {
        // Get one record per ord_num with necessary columns
        $orders = Order::select(
            'id',
            'ord_num',
            'supplier_id',
            'employee_id',
            'pre_list_id',
            'unit_id',
            'category_id',
            'amount',
            'idate',
            'state',
            'done_by',
            'created_at'
        )
        ->with([
            'employeeRelation:id,name',
            'supplierRelation:id,name',
            'preListRelation:id,name',
            'unitRelation:id,name',
            'categoryRelation:id,name'
        ])
        // ->whereIn('id', function ($query) {
        //     $query->selectRaw('MAX(id)')
        //         ->from('orders')
        //         ->groupBy('ord_num');
        // })
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
                if ($order->state == 0) {
                    return '<span class="badge badge-draft newState" data-ord-num="' . $order->ord_num . '" data-state="0" style="cursor:pointer;">' . __('order.draft') . '</span>';
                } elseif ($order->state == 1) {
                    return '<span class="badge badge-new newState" data-ord-num="' . $order->ord_num . '" data-state="1" style="cursor:pointer;">' . __('order.new') . '</span>';
                } elseif ($order->state == 2) {
                    return '<span class="badge badge-cancelled newState" data-ord-num="' . $order->ord_num . '" data-state="2" style="cursor:pointer;">' . __('order.cancelled') . '</span>';
                } elseif ($order->state == 3) {
                    return '<span class="badge badge-completed newState" data-ord-num="' . $order->ord_num . '" data-state="3" style="cursor:pointer;">' . __('order.completed') . '</span>';
                }
                return '<span class="badge badge-secondary">' . __('order.unknown') . '</span>';
            })
            ->addColumn('idate', function ($order) {
                return $order->idate ?? '-';
            })
            ->addColumn('done_by', function ($order) {
                return $order->done_by ?? '-';
            })
            ->addColumn('action', function ($order) {
                return '
                    <div class="action-icons">
                        <i class="fas fa-eye viewOrder" data-id="' . $order->ord_num . '" title="' . __('common.view') . '"></i>
                        <a href="/orders/edit/'.$order->ord_num.'">
                            <i class="fas fa-pen-square editOrder" style="font-size:20px;"></i>
                        </a>
                        <i class="fas fa-trash-alt deleteOrder" data-id="' . $order->ord_num . '" title="' . __('common.delete') . '"></i>
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

  
   public function getCounts(Request $request)
    {
        $query = Order::query();
        
        // Apply filters
        if ($request->ord_num) {
            $query->where('ord_num', 'LIKE', "%{$request->ord_num}%");
        }
        if ($request->supplier_name) {
            $query->whereHas('supplierRelation', function ($q) use ($request) {
                $q->where('name', 'LIKE', "%{$request->supplier_name}%");
            });
        }
        if ($request->employee_name) {
            $query->whereHas('employeeRelation', function ($q) use ($request) {
                $q->where('name', 'LIKE', "%{$request->employee_name}%");
            });
        }
        if ($request->category_name) {
            $query->whereHas('categoryRelation', function ($q) use ($request) {
                $q->where('name', 'LIKE', "%{$request->category_name}%");
            });
        }
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('idate', [$request->start_date, $request->end_date]);
        } elseif ($request->start_date) {
            $query->whereDate('idate', '>=', $request->start_date);
        } elseif ($request->end_date) {
            $query->whereDate('idate', '<=', $request->end_date);
        }
        
        // Get distinct ord_num counts by state
        $draftCount = (clone $query)->where('state', 0)->distinct('ord_num')->count('ord_num');
        $newCount = (clone $query)->where('state', 1)->distinct('ord_num')->count('ord_num');
        $cancelledCount = (clone $query)->where('state', 2)->distinct('ord_num')->count('ord_num');
        $completedCount = (clone $query)->where('state', 3)->distinct('ord_num')->count('ord_num');
        
        return response()->json([
            'draft_count' => $draftCount,
            'new_count' => $newCount,
            'cancelled_count' => $cancelledCount,
            'completed_count' => $completedCount
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $preLists = BuyPreList::select('id', 'name','category_id')->orderBy('name')->get();
        $units = Unit::select('id', 'name')->orderBy('name')->get();
        $employees = Account::select('id','name')->where('account_type_id',2)->get();
        $suppliers = Account::select('id','name')->where('account_type_id',4)->get();
        $orderNumber = Order::max('ord_num') + 1;
        $miladiDate = Carbon::now();
        $todaysDate = $miladiDate->format('Y-m-d');
        $times = time();
        
        return view('order.create.form', compact('preLists', 'units','todaysDate','employees','suppliers',
        'orderNumber','times'));
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
        // return ['formData' => $request->all()];

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
            'state' => 'required|numeric',
            'times' => 'required|integer',
        ]);

        DB::beginTransaction();
        try {
            // Combine the arrays into a single collection of items
            // $idate = isset($validated['idate']) && !empty($validated['idate']) 
            //     ? Carbon::parse($validated['idate'])->startOfDay() 
            //     : now()->startOfDay();

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
                    'idate' => $request->date,
                    'state' => $validated['state'], 
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
   public function show($ord_num)
    {
        // Get all orders with this ord_num (multiple items)
        $orders = Order::with([
            'preListRelation', 
            'unitRelation', 
            'categoryRelation',
            'employeeRelation',
            'supplierRelation'
        ])->where('ord_num', $ord_num)->get();
        
        if ($orders->isEmpty()) {
            return response()->json([
                'status' => 'failed',
                'message' => __('common.not_found')
            ], 404);
        }

        // Get the first order for header information
        $order = $orders->first();
        
        // Get all items for this order
        $orderItems = $orders;

        return view('order.show', compact('order', 'orderItems'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($ord_num)
    {
        // Get all orders with this ord_num (multiple items)
        $orders = Order::where('ord_num', $ord_num)->get();
        
        if ($orders->isEmpty()) {
            return response()->json([
                'status' => 'failed',
                'message' => __('common.not_found')
            ], 404);
        }

        // Get the first order for header information
        $order = $orders->first();
        
        // Get all items for this order
        $orderItems = $orders;
        
        $preLists = BuyPreList::select('id', 'name', 'category_id')->orderBy('name')->get();
        $units = Unit::select('id', 'name')->orderBy('name')->get();
        $employees = Account::select('id','name')->where('account_type_id',2)->get();
        $suppliers = Account::select('id','name')->where('account_type_id',4)->get();

        return view('order.edit.form', compact('order', 'orderItems', 'preLists', 'units', 'employees', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $ord_num)
    {
        //  return ['formData' => $request->all()];
        // Validate the request - same as store
        $validated = $request->validate([
            'buy_pre_list' => 'required|array|min:1',
            'buy_pre_list.*' => 'required|exists:bought_item_pre_lists,id',
            'amount' => 'required|array|min:1',
            'amount.*' => 'required|numeric|min:0.01',
            'unit_id' => 'required|array|min:1',
            'unit_id.*' => 'required|exists:units,id',
            'category_id' => 'nullable|array',
            'supplier_id' => 'required|exists:accounts,id',
            'employee_id' => 'required|exists:accounts,id',
            'ord_num' => 'required|string|max:50',
            'state' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $idate = isset($validated['date']) && !empty($validated['date']) 
            ? Carbon::parse($validated['date'])->startOfDay() 
            : now()->startOfDay();
            // Delete existing items for this order
            Order::where('ord_num', $ord_num)->delete();
            
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

            // Create new items
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
                    'idate' => $request->date,
                    'state' => $validated['state'] ?? 1, // Use state from form or default to 1
                    'times' => time(), // Generate new times
                ]);
            }

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => __('common.updated_successfully')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order Update Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => __('common.error_occurred') . ': ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($ord_num)
    {
        DB::beginTransaction();
        try {
            // Get all orders with this ord_num
            $orders = Order::where('ord_num', $ord_num)->get();
            
            // Check if any orders exist
            if ($orders->isEmpty()) {
                return response()->json([
                    'status' => 'failed',
                    'message' => __('common.not_found')
                ], 404);
            }

            // Delete all matching orders
            $deletedCount = Order::where('ord_num', $ord_num)->delete();

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => __('common.deleted_successfully') . ' (' . $deletedCount . ' ' . __('common.records') . ')'
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
    public function updateStatus(Request $request, $ord_num)
    {
        $validated = $request->validate([
            'state' => 'required|integer|in:0,1,2,3',
        ]);

        DB::beginTransaction();
        try {
            // Update all orders with this ord_num
            $orders = Order::where('ord_num', $ord_num)->get();
            
            if ($orders->isEmpty()) {
                return response()->json([
                    'status' => 'failed',
                    'message' => __('common.not_found')
                ], 404);
            }

            // Update status for all items
            foreach ($orders as $order) {
                $order->state = $validated['state'];
                
                	// 0:Draft, 1:new, 2:cancelled, 3: completed

                if ($validated['state'] == 3) { // Done/Completed
                    $carbonDate = Carbon::now();
                    $order->done_year = $carbonDate->year;
                    $order->done_month = $carbonDate->month;
                    $order->done_day = $carbonDate->day;
                    $order->done_by =  auth()->user()->full_name ?? 'System';
                } 
                else 
                {
                    // Reset done fields if not completed
                    $order->done_year = null;
                    $order->done_month = null;
                    $order->done_day = null;
                    $order->done_by = null;
                }
                
                $order->save();
            }

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => __('order.status_updated_successfully')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order Status Update Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => __('common.error_occurred') . ': ' . $e->getMessage()
            ], 500);
        }
    }
   

    

  
}
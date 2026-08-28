<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Order\Order;
use App\Models\Order\OrderItem;
use App\Models\Order\DraftOrder;
use App\Models\Setting\Account;
use App\Models\Setting\Currency;
use Illuminate\Support\Facades\Validator;
use App\Models\Setting\Category;
use App\Models\Setting\OrgBio;
use App\Models\Setting\Car;
use App\Models\Buy\BuyPreList;
use App\Models\Setting\Unit;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    protected $isAdmin, $customerIds, $carIds, $userId, $userName;
    
    public function __construct()
    {
        if (auth()->check()) {
            $this->isAdmin = session('isAdmin', auth()->user()->isAdmin == 1);
            $this->customerIds = session('customerIds', []);
            $this->carIds = session('carIds', []);
            $this->userId = session('userId', auth()->user()->id);
            $this->userName = auth()->user()->full_name;
        } else {
            $this->isAdmin = false;
            $this->customerIds = [];
            $this->carIds = [];
            $this->userId = 0;
            $this->userName ='';
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orgbios = OrgBio::all();
        $todaysDate = Carbon::now()->format('Y-m-d'); 
               
        // return view('order.list', compact('todaysDate', 'orgbios'));
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
            'category_id',
            'idate',
            'state',
            'user_name',
            'times'
        )
        ->with([
            'supplierRelation:id,name',
            'categoryRelation:id,name'
        ])
        // ->whereIn('id', function ($query) {
        //     $query->selectRaw('MAX(id)')
        //         ->from('orders')
        //         ->groupBy('ord_num');
        // })
        ->orderBy('id', 'DESC');

        if(!$this->isAdmin) {
            $orders->where('orders.user_id', $this->userId);
        }

        // Apply filters
        if ($request->ord_num) {
            $orders->where('ord_num', 'LIKE', "%{$request->ord_num}%");
        }

        if ($request->supplier_name) {
            $orders->whereHas('supplierRelation', function ($q) use ($request) {
                $q->where('name', 'LIKE', "%{$request->supplier_name}%");
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
            ->addColumn('supplier_name', function ($order) {
                return $order->supplierRelation ? $order->supplierRelation->name : '-';
            })
            ->addColumn('category_name', function ($order) {
                return $order->categoryRelation ? $order->categoryRelation->name : '-';
            })
            ->addColumn('state', function ($order) {
                 return $this->getStatusBadge($order->state);
            })  
            ->addColumn('idate', function ($order) {
                return $order->idate ?? '-';
            })
            ->addColumn('user_name', function ($order) {
                return $order->user_name ?? '-';
            })
            ->addColumn('action', function ($order) {

              // stop edit, move and delete when state is not 1:(new)   
                if($order->state == 1) 
                {
                        return '
                        <div class="action-icons">
                            <i class="fas fa-eye viewOrder" data-id="' . $order->id . '" title="' . __('common.view') . '"></i>
                            <i class="fas fa-retweet moveOrderItem" data-id="' . $order->id . '" title="' . __('common.move') . '"></i>
                            <i class="fas fa-pen-square editOrder" data-id="' . $order->id . '" title="' . __('common.edit') . '"></i>
                            <i class="fas fa-trash-alt deleteOrder" data-id="' . $order->id . '" title="' . __('common.delete') . '"></i>
                        </div>
                    ';
                } 
                else 
                {
                        return '
                        <div class="action-icons">
                            <i class="fas fa-eye viewOrder" data-id="' . $order->id . '" title="' . __('common.view') . '"></i>
                             <i class="fas fa-retweet" title="' . __('common.move') . '"></i>
                            <i class="fas fa-pen-square" style="color:#ddd"  title="' . __('common.edit') . '"></i>
                            <i class="fas fa-trash-alt" style="color:#ddd"  title="' . __('common.delete') . '"></i>
                        </div>
                    ';
                }
            })
            ->filterColumn('supplier_name', function ($query, $keyword) {
                $query->whereHas('supplierRelation', function ($q) use ($keyword) {
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

  
    private function getStatusBadge($state)
    {
        $badges = [
            1 => '<span class="badge badge-primary">' . __('order.new') . '</span>',
            2 => '<span class="badge badge-warning">' . __('order.pending') . '</span>',
            3 => '<span class="badge badge-success">' . __('order.completed') . '</span>',
            4 => '<span class="badge badge-danger">' . __('order.cancelled') . '</span>'
        ];

        return $badges[$state] ?? '<span class="badge badge-secondary">' . __('order.unknown') . '</span>';
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
        // $preLists = BuyPreList::select('id', 'name','category_id')->orderBy('name')->get();
        // $units = Unit::select('id', 'name')->orderBy('name')->get();
        // // $employees = Account::select('id','name')->where('account_type_id',2)->get();
        // // $suppliers = Account::select('id','name')->where('account_type_id',4)->get();
        // $customers = Account::select('id','name')->where('account_type_id',3)->get();
        // $orderNumber = Order::max('ord_num') + 1;
        // $orderNumber = Order::max('ord_num') + 1;
        // $todaysDate = Carbon::now()->format('Y-m-d');
        // $times = time();
        
        // return view('order.create.form', compact('preLists', 'units','todaysDate','customers','times'));
        //  $draftOrders = DraftOrder::select(
        //     'category_id',
        //     'pre_list_id',
        //     'unit_id',
        //     'amount',
        //     'idate',
        //     'iby',
        //     'user_name',
        //     'state',
        //     'times'
        // )
        // ->with([
        //     'preListRelation:id,name,category_id',
        //     'unitRelation:id,name',
        // ])
        // ->where('draft_orders.state', 1)
        // ->orderBy('category_id', 'DESC')
        // ->get();

        // // Group by category_id and format
        // $formattedData = $draftOrders->groupBy('category_id')->map(function ($items, $categoryId) {
        //     return [
        //         'category_id' => $categoryId,
        //         'total_items' => $items->count(),
        //         'total_amount' => $items->sum('amount'),
        //         'items' => $items->map(function ($item) {
        //             return [
        //                 'pre_list_id' => $item->pre_list_id,
        //                 'pre_list_name' => $item->preListRelation->name ?? null,
        //                 'unit_id' => $item->unit_id,
        //                 'unit_name' => $item->unitRelation->name ?? null,
        //                 'amount' => $item->amount,
        //                 'idate' => $item->idate,
        //                 'iby' => $item->iby,
        //                 'user_name' => $item->user_name,
        //                 'state' => $item->state,
        //                 'times' => $item->times,
        //             ];
        //         })
        //     ];
        // })->values();

        // return response()->json($formattedData);
        // ================================ 1 ===================================

        //     $preLists = BuyPreList::select('id', 'name', 'category_id')->orderBy('name')->get();
        //     $units = Unit::select('id', 'name')->orderBy('name')->get();
        //     $customers = Account::select('id', 'name')->where('account_type_id', 3)->get();
        //     $categories = Category::select('id', 'name')->orderBy('name')->get();
        //     $todaysDate = Carbon::now()->format('Y-m-d');
        //     $times = time();
            
        //     // Get grouped items (for initial empty state)
        //     $groupedItems = [];

        //     $draftOrders = DraftOrder::select(
        //     'category_id',
        //     'pre_list_id',
        //     'unit_id',
        //     'amount',
        //     'idate',
        //     'iby',
        //     'user_name',
        //     'state',
        //     'times'
        // )
        // ->with([
        //     'preListRelation:id,name,category_id',
        //     'unitRelation:id,name',
        //     'categoryRelation:id,name'
        // ])
        // ->where('draft_orders.state', 1)
        // ->orderBy('category_id', 'ASC')
        // ->get();

        // // Group by category_id, then by pre_list_id and unit_id
        // $formattedData = $draftOrders->groupBy('category_id')->map(function ($items, $categoryId) use ($categories) {

        //      $categoryName = $categories->where('id', $categoryId)->first()->name ?? null;
        //     // Group items by pre_list_id and unit_id combination
        //     $groupedItems = $items->groupBy(function ($item) {
        //         return $item->pre_list_id . '_' . $item->unit_id;
        //     })->map(function ($group) {
        //         $first = $group->first();
        //         return [
        //             'pre_list_id' => $first->pre_list_id,
        //             'pre_list_name' => $first->preListRelation->name ?? null,
        //             'unit_id' => $first->unit_id,
        //             'unit_name' => $first->unitRelation->name ?? null,
        //             'amount' => $group->sum('amount'), // Sum the amounts
        //             'idate' => $first->idate,
        //             'iby' => $first->iby,
        //             'user_name' => $first->user_name,
        //             'state' => $first->state,
        //             'times' => $first->times,
        //             'count' => $group->count() // Number of items grouped
        //         ];
        //     })->values(); // Reset keys
            
        //     return [
        //         'category_id' =>  $categoryId,
        //         'category_name' => $categoryName ?? null,
        //         'total_items' =>  $items->count(),
        //         'total_amount' => $items->sum('amount'),
        //         'items' => $groupedItems
        //     ];
        // })->values();

        // // return view('order.create.form', compact('preLists', 'units', 'customers','categories','todaysDate','times','groupedItems'));
        //     // return response()->json($formattedData);
        //     return response()->json($formattedData);
        // ============================= 2 ================================
    
        $preLists = BuyPreList::select('id', 'name', 'category_id','supplier_id','unit_id','unit_name')->orderBy('name')->get();
        $units = Unit::select('id', 'name')->orderBy('name')->get();
        $customers = Account::select('id', 'name')->where('account_type_id', 3)->get();
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $todaysDate = Carbon::now()->format('Y-m-d');
        $times = time();



        if($this->isAdmin) {
            $cars = Car::get();
            // Get draft orders with state = 1
            $draftOrders = DraftOrder::select(
                'category_id',
                'pre_list_id',
                'unit_id',
                'amount'
            )
            ->with([
                'preListRelation:id,name,category_id,supplier_id',
                'preListRelation.categoryRelation:id,name', // Load category through preListRelation
                'unitRelation:id,name',
            ])
            ->where('draft_orders.state', 1) // get just new draft orders
            ->get();
        } 
        else 
        {
            $cars = Car::whereIn('id', $this->carIds)->get();
            $draftOrders = DraftOrder::select(
                'category_id',
                'pre_list_id',
                'unit_id',
                'amount'
            )
            ->with([
                'preListRelation:id,name,category_id,supplier_id',
                'preListRelation.categoryRelation:id,name', // Load category through preListRelation
                'unitRelation:id,name',
            ])
            ->where('draft_orders.state', 1) // get just new draft orders
            ->whereIn('draft_orders.customer_id', $this->customerIds)
            ->get();
        }
   

        // Group by category_id, then by pre_list_id and unit_id
        $groupedItems = $draftOrders->groupBy('category_id')->map(function ($items, $categoryId) {
            // Get category name from the first item's preListRelation->categoryRelation
            $firstItem = $items->first();
            $categoryName = $firstItem->preListRelation->categoryRelation->name ?? 'Unknown';
            
            return $items->groupBy(function ($item) {
                return $item->pre_list_id . '_' . $item->unit_id;
            })->map(function ($group) use ($categoryId, $categoryName) {
                $first = $group->first();
                return [
                    'category_id' => $categoryId,
                    'category_name' => $categoryName,
                    'pre_list_id' => $first->pre_list_id,
                    'supplier_id' => $first->preListRelation->supplier_id ?? null,
                    'pre_list_name' => $first->preListRelation->name ?? null,
                    'unit_id' => $first->unit_id,
                    'unit_name' => $first->unitRelation->name ?? null,
                    'amount' => $group->sum('amount'),
                    'count' => $group->count()
                ];
            })->values();
        })->flatten(1)->values();

        return view('order.create.form', compact(
            'preLists', 
            'units', 
            'customers', 
            'categories', 
            'todaysDate', 
            'times',
            'groupedItems',
            'cars'
        ));

        // return response()->json($groupedItems);

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
        // return response()->json($request->all());
        // Get items directly from request (it's already an array)
        $items = $request->input('items');
        $times =  time();
        $user_id = auth()->user()->id ?? 0;
        $user_name = auth()->user()->full_name ?? 'System';

        if (!$items || !is_array($items) || count($items) === 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'No items provided'
            ], 422);
        }

        $validated = $request->validate([
            'date' => 'required|date',
            'state' => 'required|integer|in:0,1,2,3',
            'car_id' => 'required|exists:cars,id',
        ]);

        // Validate each item
        foreach ($items as $index => $item) {
            $validator = Validator::make($item, [
                'pre_list_id' => 'required|exists:bought_item_pre_lists,id',
                'category_id' => 'required|exists:categories,id',
                'supplier_id' => 'required|exists:accounts,id',
                'unit_id' => 'required|exists:units,id',
                'amount' => 'required|numeric|min:0.01',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors()
                ], 422);
            }
        }

        DB::beginTransaction();
        try {
            // Group items by category_id
            $groupedItems = [];
            foreach ($items as $item) {
                $categoryId = $item['category_id'];
                if (!isset($groupedItems[$categoryId])) {
                    $groupedItems[$categoryId] = [];
                }
                $groupedItems[$categoryId][] = $item;
            }

            // Create one order per category
            $createdOrders = [];
            foreach ($groupedItems as $categoryId => $categoryItems) {
                // Get supplier_id from first item in this category
                $supplierId = $categoryItems[0]['supplier_id'] ?? null;
                
                // Create order for this category
                $order = Order::create([
                    'supplier_id' => $supplierId,
                    'category_id' => $categoryId,
                    'idate' => $validated['date'],
                    'state' => $validated['state'],
                    'car_id' => $validated['car_id'],
                    'user_id' => $user_id,
                    'user_name' => $user_name,
                    'times' => $times,
                ]);

                // Create order items for this order
                foreach ($categoryItems as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'pre_list_id' => $item['pre_list_id'],
                        'category_id' => $item['category_id'],
                        'unit_id' => $item['unit_id'],
                        'amount' => $item['amount'],
                    ]);
                }

                $createdOrders[] = $order;
            }

            DB::commit();

            // Format response
            $orderData = [];
            foreach ($createdOrders as $order) {
                $orderData[] = [
                    'id' => $order->id,
                    'ord_num' => $order->ord_num,
                    'category_id' => $order->category_id,
                    'items_count' => $order->items()->count()
                ];
            }

            // update DraftOrder state to progress
            if($this->isAdmin) {
                DraftOrder::where('state', 1)->update(['state' => 2]);
            } else {
                DraftOrder::where('state', 1)->where('iby', $user_id)->update(['state' => 2]);
            }
            

            return response()->json([
                'status' => 'success',
                'message' => __('common.added_successfully') . ' (' . count($createdOrders) . ' ' . __('order.orders') . ')',
                'orders' => $orderData
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order Creation Error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => __('common.error_occurred') . ': ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($order_id)
    {
        $orgbios = OrgBio::all();
        $todaysDate = Carbon::now()->format('Y-m-d'); 
        // Get the main order (assuming you have an Order model)
        $order = Order::where('id', $order_id)->first();
        
        if (!$order) {
            return response()->json([
                'status' => 'failed',
                'message' => __('common.not_found')
            ], 404);
        }

        // Get all order items with this order_id
        $orderItems = OrderItem::select(
            'id',
            'category_id',
            'pre_list_id',
            'unit_id',
            'amount',
            'order_id'
        )
        ->with([
            'preList:id,name,category_id,supplier_id',
            'unit:id,name',
            'category:id,name',
        ])
        ->where('order_id', $order_id)
        ->get();
        
        // Group items by category
        $groupedItems = $orderItems->groupBy('category_id')->map(function ($items, $categoryId) {
        $firstItem = $items->first();
            
            // Get category name from the relationship
            $categoryName = $firstItem->category->name ?? 
                        $firstItem->preList->category->name ?? 
                        'Unknown';
            
            return [
                'category_id' => $categoryId,
                'category_name' => $categoryName,
                'items' => $items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'pre_list_id' => $item->pre_list_id,
                        'pre_list_name' => $item->preList->name ?? '-',
                        'unit_id' => $item->unit_id,
                        'unit_name' => $item->unit->name ?? '-',
                        'amount' => $item->amount,
                        'supplier_id' => $item->preList->supplier_id ?? null,
                    ];
                }),
                'total_items' => $items->count(),
                'total_amount' => $items->sum('amount')
            ];
        })->values();

        return view('order.show', compact('order', 'groupedItems', 'orderItems','orgbios','todaysDate'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($order_id)
    {
        // Get the main order (assuming you have an Order model)
        $order = Order::where('id', $order_id)->first();
        
        if (!$order) {
            return response()->json([
                'status' => 'failed',
                'message' => __('common.not_found')
            ], 404);
        }

        // $suppliers = Account::select('id', 'name')->where('account_type_id', 4)->get();
        // Get all order items with this order_id
        $orderItems = OrderItem::select(
            'id',
            'category_id',
            'pre_list_id',
            'unit_id',
            'amount',
            'order_id'
        )
        ->with([
            'preList:id,name,category_id,supplier_id',
            'unit:id,name',
            'category:id,name',
        ])
        ->where('order_id', $order_id)
        ->get();
        
        // Group items by category
        $groupedItems = $orderItems->groupBy('category_id')->map(function ($items, $categoryId) {
            $firstItem = $items->first();
            
            // Get category name from the relationship
            $categoryName = $firstItem->category->name ?? 
                        $firstItem->preList->category->name ?? 
                        'Unknown';
            
            return [
                'category_id' => $categoryId,
                'category_name' => $categoryName,
                'items' => $items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'pre_list_id' => $item->pre_list_id,
                        'pre_list_name' => $item->preList->name ?? '-',
                        'unit_id' => $item->unit_id,
                        'unit_name' => $item->unit->name ?? '-',
                        'amount' => $item->amount,
                        'supplier_id' => $item->preList->supplier_id ?? null,
                    ];
                }),
                'total_items' => $items->count(),
                'total_amount' => $items->sum('amount')
            ];
        })->values();

        return view('order.edit.edit', compact('order', 'groupedItems', 'orderItems'));
    }

    /**
     * Get Order Item to move from one supplier to another supplier 
     */
    public function getOrderItemForMove($order_id) 
    {

       // Get the main order (assuming you have an Order model)
        $order = Order::with('supplierRelation:id,name')->where('id', $order_id)->first();
        
        if (!$order) {
            return response()->json([
                'status' => 'failed',
                'message' => __('common.not_found')
            ], 404);
        }

        $suppliers = Account::select('id', 'name')->where('account_type_id', 4)->get();
        // Get all order items with this order_id
        $orderItems = OrderItem::select(
            'id',
            'category_id',
            'pre_list_id',
            'unit_id',
            'amount',
            'order_id'
        )
        ->with([
            'preList:id,name,category_id,supplier_id',
            'unit:id,name',
            'category:id,name',
        ])
        ->where('order_id', $order_id)
        ->get();
        return view('order.move', compact('suppliers', 'order', 'orderItems'));
    }

    /**
    * Move items from one supplier to another
    */
    /**
     * Move a single item to another supplier
     */
    public function moveItemOld(Request $request)
    {
        // return response()->json(['data' => $request->all()]);
        // item_id: "177"
        // move_amount: "2"
        // order_id: "161"
        // to_supplier_id: "98"

        try {
            $validated = $request->validate([
                'order_id' => 'required|exists:orders,id',
                'item_id' => 'required|exists:order_items,id',
                'move_amount' => 'required|numeric|min:0.01',
                'to_supplier_id' => 'required|exists:accounts,id',
            ]);

            $times = time();
            $idate = Carbon::now()->format('Y-m-d');

            DB::beginTransaction();

            // Get the source order and item
            $sourceOrder = Order::find($validated['order_id'])->where('user_id', $this->userId)->where('state', 1);
            $sourceItem = OrderItem::where('order_id', $validated['order_id'])->find($validated['item_id']);

            if (!$sourceOrder || !$sourceItem) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('common.not_found')
                ], 404);
            }

            // Check if move amount is valid
            if ($validated['move_amount'] > $sourceItem->amount) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('order.amount_cannot_exceed') . ': ' . $sourceItem->amount
                ], 422);
            }

            // Check if target supplier is different from current
            if ($validated['to_supplier_id'] == $sourceOrder->supplier_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'به عین تهیه کننده انتقال نمیکند'
                ], 422);
            }

            /**
             * Analyze
             * 1: جنس که میخواهد انتقال نماید باید چک شود که آیا همین جنس از همین کتگوری مربوط همین یوزر در حالت جدید موجود است ؟
             * 2: اگر موجود باشد باید مقدارش آپدیت شود و اگر نیست باید جدید ثبت شود 
             * 3: از مقدار سفارش به تعداد انتقال شده باید کم شود و اگر صفر مانده بود باید حذف شود
             */

            // Check if target supplier already has an order with same category and state
            $targetOrder = Order::where('supplier_id', $validated['to_supplier_id'])
                ->where('category_id', $sourceOrder->category_id)
                ->where('user_id', $this->userId)
                // ->where('car_id', $sourceOrder->car_id)
                ->where('state', 1) 
                ->first();

            if($targetOrder->count() > 0) // اگر قبلا نیز لیست خرید جدید داشته حالا باید آپدیت شود
            {
               // get prev order items to increase the amount 
               $oldTargetOrderItem = OrderItem::where('order_id', $targetOrder->id)
                ->where('category_id', $targetOrder->category_id)
                ->where('pre_list_id', $sourceItem->pre_list_id)
                ->where('unit_id', $sourceItem->unit_id)
                ->first();

                if($oldTargetOrderItem) // اگر از همان جنس در همان کتگوری در حالت جدید موجود باشد
                {
                    $oldTargetOrderItem->amount += $validated['move_amount'];
                    $oldTargetOrderItem->save();
                }
                else // اگر موجود نباشد باید جنس جدید ثبت کند در آردر آیتم 
                {
                     OrderItem::create([
                        'order_id' => $targetOrder->id,
                        'pre_list_id' => $sourceItem->pre_list_id,
                        'category_id' => $targetOrder->category_id,
                        'unit_id' => $sourceItem->unit_id,
                        'amount' => $validated['move_amount'],
                    ]);
                }

                
            }
            else  // لیست خرید نداشته حالا باید سفارش جدید با آیتم هایش ایجاد شود
            {
                /**
                 * ۱: ایجاد سفارش جدید در تیبل آردر
                 * ۲: ایجاد آیتم های آردر
                 */
                // Create order for this category
                $order = Order::create([
                    'supplier_id' => $validated['to_supplier_id'],
                    'category_id' => $sourceOrder->category_id,
                    'idate' => $idate,
                    'state' => 1,
                    'car_id' => $sourceOrder->car_id,
                    'user_id' => $this->userId,
                    'user_name' => $this->userName,
                    'times' => $times,
                ]);

                // Create order items for this order
                OrderItem::create([
                    'order_id' => $order->id,
                    'pre_list_id' => $sourceItem->pre_list_id,
                    'category_id' => $sourceItem->category_id,
                    'unit_id' => $sourceItem->unit_id,
                    'amount' => $validated['move_amount'],
                ]);
            }

             // Update source item amount
            $sourceItem->amount -= $validated['move_amount'];
            
            if ($sourceItem->amount == 0) {
                $sourceItem->delete();
                $remainingAmount = 0;
                $isFullyMoved = true;
            } else {
                $sourceItem->save();
                $remainingAmount = $sourceItem->amount;
                $isFullyMoved = false;
            }
            
            // Check if source order has no items left
            $remainingItems = OrderItem::where('order_id', $sourceOrder->id)->count();
            if ($remainingItems == 0) {
                $sourceOrder->delete();
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => __('order.item_moved_successfully'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Move Item Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => __('common.error_occurred') . ': ' . $e->getMessage()
            ], 500);
        }
    }

    public function moveItem(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Validate Request
        |--------------------------------------------------------------------------
        | One item is moved per request.
        |
        | If the user wants to move multiple items, this method can be called
        | separately for each item.
        */
        $validated = $request->validate([
            'order_id'       => 'required|exists:orders,id',
            'item_id'        => 'required|exists:order_items,id',
            'move_amount'    => 'required|numeric|min:0.01',
            'to_supplier_id' => 'required|exists:accounts,id',
        ]);

        DB::beginTransaction();

        try {

            $times = time();
            $idate = Carbon::now()->format('Y-m-d');


            /*
            |--------------------------------------------------------------------------
            | 2. Get and Verify Source Order
            |--------------------------------------------------------------------------
            |
            | The order must:
            | - belong to the current user
            | - be in active/new state
            */
            $sourceOrder = Order::where('id', $validated['order_id'])
                ->where('user_id', $this->userId)
                ->where('state', 1)
                ->first();


            /*
            |--------------------------------------------------------------------------
            | 3. Get and Verify Source Order Item
            |--------------------------------------------------------------------------
            |
            | The item must belong to the selected source order.
            |
            | This is important because validating item_id separately does not
            | guarantee that the item actually belongs to order_id.
            */
            $sourceItem = OrderItem::where('id', $validated['item_id'])
                ->where('order_id', $validated['order_id'])
                ->first();


            if (!$sourceOrder || !$sourceItem) {

                DB::rollBack();

                return response()->json([
                    'status'  => 'error',
                    'message' => __('common.not_found')
                ], 404);
            }


            /*
            |--------------------------------------------------------------------------
            | 4. Prevent Moving to the Same Supplier
            |--------------------------------------------------------------------------
            */
            if ((int) $validated['to_supplier_id'] === (int) $sourceOrder->supplier_id) {

                DB::rollBack();

                return response()->json([
                    'status'  => 'error',
                    'message' => 'انتقال به همان تهیه کننده امکان پذیر نیست.'
                ], 422);
            }


            /*
            |--------------------------------------------------------------------------
            | 5. Validate Move Amount
            |--------------------------------------------------------------------------
            |
            | The user cannot move more than the available amount.
            */
            if ($validated['move_amount'] > $sourceItem->amount) {

                DB::rollBack();

                return response()->json([
                    'status'  => 'error',
                    'message' => __('order.amount_cannot_exceed') . ': ' . $sourceItem->amount
                ], 422);
            }


            /*
            |--------------------------------------------------------------------------
            | 6. Find Existing Target Order
            |--------------------------------------------------------------------------
            |
            | Check whether the destination supplier already has an active order
            | for the same:
            |
            | - supplier
            | - category
            | - user
            | - state
            |
            | If it exists, we will add the item to that order.
            | Otherwise, a new order will be created.
            */
            $targetOrder = Order::where('supplier_id', $validated['to_supplier_id'])
                ->where('category_id', $sourceOrder->category_id)
                ->where('user_id', $this->userId)
                ->where('state', 1)
                ->first();


            /*
            |--------------------------------------------------------------------------
            | 7. Target Order Already Exists
            |--------------------------------------------------------------------------
            */
            if ($targetOrder) {

                /*
                | Check whether exactly the same item already exists in
                | the destination order item list.
                |
                | Same item means:
                | - same pre_list
                | - same category
                | - same unit
                */
                $targetOrderItem = OrderItem::where('order_id', $targetOrder->id)
                    ->where('pre_list_id', $sourceItem->pre_list_id)
                    ->where('category_id', $sourceItem->category_id)
                    ->where('unit_id', $sourceItem->unit_id)
                    ->first();


                /*
                | If the item already exists, increase its quantity.
                */
                if ($targetOrderItem) {

                    $targetOrderItem->amount += $validated['move_amount'];

                    $targetOrderItem->save();

                } else {

                    /*
                    | The target order exists, but this specific item does not.
                    | Create a new order item.
                    */
                    OrderItem::create([
                        'order_id'   => $targetOrder->id,
                        'pre_list_id'=> $sourceItem->pre_list_id,
                        'category_id'=> $sourceItem->category_id,
                        'unit_id'    => $sourceItem->unit_id,
                        'amount'     => $validated['move_amount'],
                    ]);
                }

            } else {

                /*
                |--------------------------------------------------------------------------
                | 8. Create New Target Order
                |--------------------------------------------------------------------------
                |
                | The destination supplier does not have an active order for
                | this category, so create a new order first.
                */
                $targetOrder = Order::create([
                    'supplier_id' => $validated['to_supplier_id'],
                    'category_id' => $sourceOrder->category_id,
                    'car_id'      => $sourceOrder->car_id,
                    'user_id'     => $this->userId,
                    'idate'       => $idate,
                    'state'       => 1,
                    'user_name'   => $this->userName,
                    'times'       => $times,
                ]);


                /*
                |--------------------------------------------------------------------------
                | 9. Create First Item for New Target Order
                |--------------------------------------------------------------------------
                */
                OrderItem::create([
                    'order_id'    => $targetOrder->id,
                    'pre_list_id' => $sourceItem->pre_list_id,
                    'category_id' => $sourceItem->category_id,
                    'unit_id'     => $sourceItem->unit_id,
                    'amount'      => $validated['move_amount'],
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | 10. Reduce Amount from Source Item
            |--------------------------------------------------------------------------
            */
            $sourceItem->amount -= $validated['move_amount'];


            /*
            |--------------------------------------------------------------------------
            | 11. Delete Source Item if Fully Moved
            |--------------------------------------------------------------------------
            |
            | If the entire amount was transferred, the item is no longer needed.
            | Otherwise, save the remaining amount.
            */
            if ($sourceItem->amount <= 0) {

                $sourceItem->delete();

                $remainingAmount = 0;
                $isFullyMoved = true;

            } else {

                $sourceItem->save();

                $remainingAmount = $sourceItem->amount;
                $isFullyMoved = false;
            }


            /*
            |--------------------------------------------------------------------------
            | 12. Delete Source Order if It Has No Remaining Items
            |--------------------------------------------------------------------------
            */
            $remainingItems = OrderItem::where(
                'order_id',
                $sourceOrder->id
            )->count();


            if ($remainingItems === 0) {

                $sourceOrder->delete();
            }


            /*
            |--------------------------------------------------------------------------
            | 13. Commit All Changes
            |--------------------------------------------------------------------------
            |
            | Everything is successful:
            | - target order updated/created
            | - target item updated/created
            | - source item reduced/deleted
            | - empty source order deleted
            */
            DB::commit();


            return response()->json([
                'status'          => 'success',
                'message'         => __('order.item_moved_successfully'),
                'remaining_amount'=> $remainingAmount,
                'is_fully_moved'  => $isFullyMoved,
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();


            Log::error('Move Order Item Error', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);


            return response()->json([
                'status'  => 'error',
                'message' => __('common.error_occurred')
            ], 500);
        }
    }

    // Update single item
    public function update(Request $request, $id)
    {
        try {
            $item = OrderItem::findOrFail($id);
            $item->amount = $request->amount;
            $item->save();
            
            return response()->json([
                'status' => 'success',
                'message' => __('common.updated_successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Update all items
    public function updateAll(Request $request)
    {
        try {
            $items = $request->items;
            
            foreach ($items as $itemData) {
                $item = OrderItem::find($itemData['id']);
                if ($item) {
                    $item->amount = $itemData['amount'];
                    $item->save();
                }
            }
            
            return response()->json([
                'status' => 'success',
                'message' => __('common.updated_successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

 

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($order_id)
    {
        DB::beginTransaction();
        try {
            // Find the order
            $order = Order::find($order_id);
            
            // Check if order exists
            if (!$order) {
                return response()->json([
                    'status' => 'failed',
                    'message' => __('common.not_found')
                ], 404);
            }

            // Delete related OrderItems first
            $deletedItems = OrderItem::where('order_id', $order_id)->delete();
            
            // Then delete the Order
            $order->delete();

            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => __('common.deleted_successfully'),
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
    public function updateStatus(Request $request, $times)
    {
        $validated = $request->validate([
            'state' => 'required|integer|in:0,1,2,3',
        ]);

        DB::beginTransaction();
        try {
            // Update all orders with this ord_num
            $orders = Order::where('times', $times)->get();
            
            if ($orders->isEmpty()) {
                return response()->json([
                    'status' => 'failed',
                    'message' => __('common.not_found')
                ], 404);
            }

            // Update status for all items
            foreach ($orders as $order) {
                $order->state = $validated['state'];
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
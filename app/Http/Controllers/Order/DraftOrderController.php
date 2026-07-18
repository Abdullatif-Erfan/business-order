<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Order\DraftOrder;
use App\Models\Setting\Account;
use App\Models\Setting\Currency;
use App\Models\Setting\Category;
use App\Models\Setting\OrgBio;
use App\Models\Buy\BuyPreList;
use App\Models\Setting\Unit;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class DraftOrderController extends Controller
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
        $todaysDate = Carbon::now()->format('Y-m-d');

        return view('order.draft.list', compact('todaysDate', 'orgbios'));
        // if ($tabIndex == 0) {
        // return view('order.draft.list', compact('todaysDate', 'orgbios'));
        // } else {
        //     return view('order.list', compact('todaysDate', 'orgbios'));
        // }

    }

    /**
     * Get Data for DataTable
     */
     public function getData(Request $request)
    {
        $draftOrders = DraftOrder::select(
            'id',
            'dord_num',
            'customer_id',
            'category_id',
            'pre_list_id',
            'unit_id',
            'amount',
            'idate',
            'iby',
            'user_name',
            'state',
            'times'
        )
        ->with([
            'customerRelation:id,name',
            'preListRelation:id,name,category_id',
            'unitRelation:id,name',
        ])
        ->orderBy('id', 'DESC');

        // Apply filters
        if ($request->customer_name) {
            $draftOrders->whereHas('customerRelation', function ($q) use ($request) {
                $q->where('name', 'LIKE', "%{$request->customer_name}%");
            });
        }

        if ($request->item_name) {
            $draftOrders->whereHas('preListRelation', function ($q) use ($request) {
                $q->where('name', 'LIKE', "%{$request->item_name}%");
            });
        }

        if ($request->state !== null && $request->state !== '') {
            $draftOrders->where('state', $request->state);
        }

        if ($request->start_date && $request->end_date) {
            $draftOrders->whereBetween('idate', [$request->start_date, $request->end_date]);
        } elseif ($request->start_date) {
            $draftOrders->whereDate('idate', '>=', $request->start_date);
        } elseif ($request->end_date) {
            $draftOrders->whereDate('idate', '<=', $request->end_date);
        }

        return DataTables::of($draftOrders)
            ->addIndexColumn()
            ->addColumn('dord_num_display', function ($order) {
                static $displayed = [];
                if (!in_array($order->dord_num, $displayed)) {
                    $displayed[] = $order->dord_num;
                    return '<strong>' . $order->dord_num . '</strong>';
                }
                return '';
            })
            ->addColumn('customer_name', function ($order) {
                static $displayedCustomers = [];
                if (!in_array($order->dord_num, $displayedCustomers)) {
                    $displayedCustomers[] = $order->dord_num;
                    return $order->customerRelation ? $order->customerRelation->name : '-';
                }
                return '';
            })
            ->addColumn('item_name', function ($order) {
                return $order->preListRelation ? $order->preListRelation->name : '-';
            })
            ->addColumn('unit_name', function ($order) {
                return $order->unitRelation ? $order->unitRelation->name : '-';
            })
            ->addColumn('amount', function ($order) {
                return number_format($order->amount, 2);
            })
            ->addColumn('state', function ($order) {
                return $this->getStatusBadge($order->state);
            })
            ->addColumn('idate', function ($order) {
                static $displayedDates = [];
                if (!in_array($order->dord_num, $displayedDates)) {
                    $displayedDates[] = $order->dord_num;
                    return $order->idate ?? '-';
                }
                return '';
            })
            ->addColumn('user_name', function ($order) {
                static $displayedUsers = [];
                if (!in_array($order->dord_num, $displayedUsers)) {
                    $displayedUsers[] = $order->dord_num;
                    return $order->user_name ?? '-';
                }
                return '';
            })
             ->addColumn('action', function ($order) {
                    static $displayedEditIcon = [];     
                    // stop edit and delete when state is not 1:(new)   
                    if($order->state == 1) {
                         $edit = '<i class="fas fa-edit" style="color: #ddd; margin: 0 5px;"></i>'; // Initialize
                        if (!in_array($order->dord_num, $displayedEditIcon)) {
                            $displayedEditIcon[] = $order->dord_num;
                            $edit = '<a href="' . route('draftOrders.edit', $order->dord_num) . '">
                                        <i class="fas fa-edit" style="color: #007bff; margin: 0 5px;"></i>
                                    </a>';
                        }
                        $delete = '<i class="fas fa-trash-alt deleteOrder" data-id="' . $order->id . '" title="' . __('common.delete') . '" style="cursor:pointer; color: #dc3545; margin: 0 5px;"></i>';
                    } 
                    else 
                    {
                         $edit = '<i class="fas fa-edit" style="color: #ddd; margin: 0 5px;"></i>'; // Initialize
                        if (!in_array($order->dord_num, $displayedEditIcon)) {
                            $displayedEditIcon[] = $order->dord_num;
                            $edit = '<i class="fas fa-edit" style="color: #ddd; margin: 0 5px;"></i>';
                        }
                        $delete = '<i class="fas fa-trash-alt" title="' . __('common.delete') . '" style="cursor:pointer; color: #ddd; margin: 0 5px;"></i>';
                    }
                   
                    return '<div class="action-icons">' . $edit . ' ' . $delete . '</div>';
            })
            ->filterColumn('item_name', function ($query, $keyword) {
                $query->whereHas('preListRelation', function ($q) use ($keyword) {
                    $q->where('name', 'LIKE', "%{$keyword}%");
                });
            })
            ->filterColumn('customer_name', function ($query, $keyword) {
                $query->whereHas('customerRelation', function ($q) use ($keyword) {
                    $q->where('name', 'LIKE', "%{$keyword}%");
                });
            })
            ->rawColumns(['dord_num_display', 'state', 'action'])
            ->make(true);
    }

    /**
     * Get status badge HTML
     */
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

    /**
     * Get counts for dashboard
     */
    public function getCounts(Request $request)
    {
        $query = DraftOrder::query();
        
        // Apply filters
        if ($request->customer_name) {
            $query->whereHas('customerRelation', function ($q) use ($request) {
                $q->where('name', 'LIKE', "%{$request->customer_name}%");
            });
        }

        if ($request->item_name) {
            $query->whereHas('preListRelation', function ($q) use ($request) {
                $q->where('name', 'LIKE', "%{$request->item_name}%");
            });
        }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('idate', [$request->start_date, $request->end_date]);
        } elseif ($request->start_date) {
            $query->whereDate('idate', '>=', $request->start_date);
        } elseif ($request->end_date) {
            $query->whereDate('idate', '<=', $request->end_date);
        }
        
        $draftCount = (clone $query)->where('state', 0)->count();
        $newCount = (clone $query)->where('state', 1)->count();
        $pendingCount = (clone $query)->where('state', 2)->count();
        $completedCount = (clone $query)->where('state', 3)->count();
        $cancelledCount = (clone $query)->where('state', 4)->count();
        
        return response()->json([
            'draft_count' => $draftCount,
            'new_count' => $newCount,
            'pending_count' => $pendingCount,
            'completed_count' => $completedCount,
            'cancelled_count' => $cancelledCount,
            'total_count' => $draftCount + $newCount + $pendingCount + $completedCount + $cancelledCount
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $autoNum = DraftOrder::max('dord_num') + 1;
        $preLists = BuyPreList::select('id', 'name', 'category_id')->orderBy('name')->get();
        $units = Unit::select('id', 'name')->orderBy('name')->get();
        $customers = Account::select('id', 'name')->where('account_type_id', 3)->get();
        $todaysDate = Carbon::now()->format('Y-m-d');
        $times = time();
        
        return view('order.draft.create_form', compact('preLists', 'units', 'customers','todaysDate', 'times','autoNum'));
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
            'unit_id' => 'required|array|min:1',
            'customer_id' => 'required|exists:accounts,id',
            'category_id' => 'nullable|array',
            'times' => 'required|integer',
            'dord_num' => 'required|integer',
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
                DraftOrder::create([
                    'dord_num'   => $validated['dord_num'],
                    'pre_list_id' => $item['pre_list_id'],
                    'category_id' => $item['category_id'],
                    'customer_id' => $validated['customer_id'],
                    'amount' => $item['amount'],
                    'unit_id' => $item['unit_id'],
                    'iby' => auth()->user()->id ?? '',
                    'user_name' => auth()->user()->full_name ?? '',
                    'idate' => $request->date,
                    'state' => 1, 
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
        $draftOrder = DraftOrder::with([
            'customerRelation',
            'preListRelation',
            'unitRelation',
            'categoryRelation'
        ])->find($id);
        
        if (!$draftOrder) {
            return response()->json([
                'status' => 'failed',
                'message' => __('common.not_found')
            ], 404);
        }

        return view('order.draft.show', compact('draftOrder'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Get all items with this dord_num
        $draftOrders = DraftOrder::where('dord_num', $id)->get();
        
        if ($draftOrders->isEmpty()) {
            return redirect()->route('draftOrders.index')
                ->with('notification', [
                    'type' => 'danger',
                    'message' => __('common.not_found')
                ]);
        }

        // Get the first order for header information
        $draftOrder = $draftOrders->first();
        $orderItems = $draftOrders; // All items for this order

        $preLists = BuyPreList::select('id', 'name', 'category_id')->orderBy('name')->get();
        $units = Unit::select('id', 'name')->orderBy('name')->get();
        $customers = Account::select('id', 'name')->where('account_type_id', 3)->get();
        $categories = Category::select('id', 'name')->orderBy('name')->get();

        return view('order.draft.edit_form', compact('draftOrder', 'orderItems', 'preLists', 'units', 'customers', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
   public function update(Request $request, $dord_num)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:accounts,id',
            'idate' => 'required',
            'state' => 'required|integer|in:0,1,2,3,4',
            'items' => 'required|array|min:1',
            'items.*.pre_list_id' => 'required|exists:bought_item_pre_lists,id',
            'items.*.category_id' => 'nullable|exists:categories,id',
            'items.*.amount' => 'required|numeric|min:0.01',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.id' => 'nullable|exists:draft_orders,id',
            'deleted_items' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Delete items marked for deletion
            if ($request->filled('deleted_items')) {
                $deletedIds = explode(',', $request->deleted_items);
                DraftOrder::whereIn('id', $deletedIds)->where('dord_num', $dord_num)->delete();
            }

            // Update or create items
            foreach ($validated['items'] as $itemData) {
                if (isset($itemData['id']) && $itemData['id']) {
                    // Update existing item
                    $draftOrder = DraftOrder::find($itemData['id']);
                    if ($draftOrder && $draftOrder->dord_num == $dord_num) {
                        $draftOrder->update([
                            'customer_id' => $validated['customer_id'],
                            'pre_list_id' => $itemData['pre_list_id'],
                            'category_id' => $itemData['category_id'] ?? null,
                            'amount' => $itemData['amount'],
                            'unit_id' => $itemData['unit_id'],
                            'idate' => Carbon::parse($validated['idate'])->format('Y-m-d'),
                            'state' => $validated['state'],
                            'iby' => auth()->user()->id ?? 0,
                            'user_name' => auth()->user()->full_name ?? 'System',
                        ]);
                    }
                } else {
                    // Create new item
                    DraftOrder::create([
                        'dord_num' => $dord_num,
                        'customer_id' => $validated['customer_id'],
                        'pre_list_id' => $itemData['pre_list_id'],
                        'category_id' => $itemData['category_id'] ?? null,
                        'amount' => $itemData['amount'],
                        'unit_id' => $itemData['unit_id'],
                        'idate' => Carbon::parse($validated['idate'])->format('Y-m-d'),
                        'state' => $validated['state'],
                        'iby' => auth()->user()->id ?? 0,
                        'user_name' => auth()->user()->full_name ?? 'System',
                        'times' => time()
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => __('common.updated_successfully')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Draft Order Update Error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => __('common.error_occurred') . ': ' . $e->getMessage()
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
            $draftOrder = DraftOrder::find($id);
            
            if (!$draftOrder) {
                return response()->json([
                    'status' => 'failed',
                    'message' => __('common.not_found')
                ], 404);
            }

            $draftOrder->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => __('common.deleted_successfully')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Draft Order Delete Error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => __('common.error_occurred') . ': ' . $e->getMessage()
            ], 500);
        }
    }

   

    /**
     * Bulk delete draft orders
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:draft_orders,id'
        ]);

        DB::beginTransaction();
        try {
            $deletedCount = DraftOrder::whereIn('id', $validated['ids'])->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => __('common.deleted_successfully') . ' (' . $deletedCount . ' ' . __('common.records') . ')'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk Delete Error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => __('common.error_occurred') . ': ' . $e->getMessage()
            ], 500);
        }
    }
}
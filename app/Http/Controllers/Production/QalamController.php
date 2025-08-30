<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Production\Qalam;
use App\Models\Production\Models;
use App\Models\Production\ModelDetails;
use App\Models\Warehouse\WarehouseItem;
use App\Models\Setting\Unit;
use App\Models\Setting\OrgBio;
use App\Models\Setting\Currency;
use Morilog\Jalali\Jalalian;
use App\Models\Setting\Warehouse;
use Illuminate\Support\Facades\Session;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class QalamController extends Controller
{
    protected $branch_id, $isAdmin, $packageId;
    public function __construct()
    {
        if (auth()->check()) {
            $this->branch_id = session('branch_id', auth()->user()->branch_id ?? 0);
            $this->isAdmin = session('isAdmin', auth()->user()->isAdmin == 1);
            $this->packageId = session('package_type') ? session('package_type') : 1;
        } else {
            $this->branch_id = 0;
            $this->isAdmin = false;
            $this->packageId = 1;
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $models = Models::orderBy('id','DESC')->get();
        // $units = Unit::orderBy('id','DESC')->get();
        $currencies = Currency::orderBy('id','ASC')->get();
        $orgbios = OrgBio::all();
        // return response()->json(['models' => $models, 'units' => $units, 'currency' => $currency]);
        $todaysDate = Jalalian::now()->format('Y-m-d');
        $branch_id = $this->branch_id;

        // $qalams = Qalam::with(['modelRelation','currencyRelation','unitRelation'])  
        // ->where('branch_id', $this->branch_id)
        // ->orderByDesc('id')
        // ->get();

        // return response()->json($qalams);
        return view('production.qalam.list', compact('branch_id','currencies','todaysDate','orgbios'));
    }
    
    
    /**
     * Show the journal data
     */
    public function getData(Request $request)
    {
        // 'branch_id','model_id','amount','unit_id','unit_price','total_price','currency_id','dates','user'
        $qalams = Qalam::with(['modelRelation','currencyRelation','unitRelation'])  
        ->where('branch_id', $this->branch_id)
        ->orderByDesc('id');

        if ($request->input('item_name')) {
            $qalams->whereHas('preListRelation', function ($query) use ($request) {
                $query->where('name', 'LIKE', "%{$request->input('item_name')}%");
            });
        }
        
        if ($request->input('currency_id')) {
            $qalams->where('currency_id', $request->input('currency_id'));
        }
    
        return DataTables::of($qalams)
            
            ->addIndexColumn()

            // ->addColumn('addItem', function ($qalams) {
            //     return '<a href="modelDetails/create/' . $qalams->id . '" class="hidden-print">
            //                 <i class="btn btn-sm btn-success" data-id="' . $qalams->id . '">'
            //                     . (($qalams->model_details_relation_count > 0) 
            //                         ? 'Item count: ' . $qalams->model_details_relation_count . ' / Edit' 
            //                         : 'Add Item') .
            //                 '</i>
            //             </a>';
            // })

            // ->addColumn('edit', function($models) {
            //     return '<i class="fas fa-pen-square editIcon" data-id="'.$models->id.'" style="font-size:20px;"></i>';
            // })
            ->addColumn('delete', function($models) {
                return '<i class="fas fa-trash-alt deleteIcon" data-id="'.$models->id.'" style="font-size:20px; color:red;"></i>';
            })
            // ->rawColumns(['addItem','edit','delete'])
            ->rawColumns(['addItem','delete'])
            ->make(true);
    }

    /**
    * Show the form for creating a new resource.
    */
    public function create()
    {
        $models = Models::orderBy('id','DESC')->get();
        $units = Unit::orderBy('id','DESC')->get();
        $currency = Currency::orderBy('id','DESC')->get();
        $branch_id = $this->branch_id;
      
        // return response()->json($preLists);
        return view('production.qalam.create',compact('branch_id','models','units','currency'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::info('Start Storing Qalam');
        DB::beginTransaction();
        try {
            // 1. Insert into qalam table
            $qalam = new Qalam();
            $qalam->branch_id   = $request->branch_id ?? $this->branch_id;
            $qalam->model_id    = $request->model_id;
            $qalam->amount      = $request->amount;
            $qalam->unit_id     = $request->unit_id;
            $qalam->unit_price  = $request->price;
            $qalam->total_price = $request->amount * $request->price;
            $qalam->currency_id = $request->currency_id;
            $qalam->dates       = Jalalian::now()->format('Y-m-d');
            $qalam->user        = auth()->user()->full_name ?? '';
            $qalam->save();

            // 2. Fetch model details (the "recipe")
            $modelDetails = ModelDetails::where('model_id', $request->model_id)
                ->where('branch_id', $this->branch_id)
                ->get();
                /**
                 * model name is cake
                 * model_details items amounts are (ard = 0.5 sir, sugar = 1 sir)
                 * if I make qalam with amount 2
                 * - save record in qalam
                 * - update the warehouse_items and decrease ard = 7 - (0.5 * 2) || sugar = 7 - (1 * 2)
                 * - warehouse item amount should be {ard = 6} and {sugar = 5}
                 */
               // 3. Loop through modelDetails and deduct from warehouse
               foreach ($modelDetails as $detail) 
               {
                    $dedactQty = $request->amount * $detail->amount; // 2 * 0.5   || 2 * 1
                    $warehouseItem = WarehouseItem::where('branch_id', $this->branch_id)
                    ->where('buy_pre_id', $detail->pre_list_id)
                    ->where('currency_id', $detail->currency_id)
                    ->where('unit_id', $detail->unit_id)
                    ->where('available_amount', '>', 0)
                    ->orderBy('created_at', 'asc')
                    ->first();
                    
                    if (!$warehouseItem) {
                        throw new \Exception("Item {$detail->pre_list_id} not found in warehouse.");
                    }
                    
                    if ($warehouseItem->available_amount < $dedactQty) {
                        throw new \Exception("Not enough stock for item {$detail->pre_list_id}. Needed {$deductQty}, available {$warehouseItem->available_amount}");
                    }
                    $remainingQty = $warehouseItem->available_amount - $dedactQty;
                    // Deduct
                    $warehouseItem->available_amount = $remainingQty;
                    $warehouseItem->available_total   = $remainingQty * $warehouseItem->avg_up;
                    // Optional: track how much taken out
                    $warehouseItem->out_amount = ($warehouseItem->out_amount ?? 0) + $dedactQty;
                    $warehouseItem->save();        
            }

            DB::commit();
            Log::info('Qalam stored successfully');

            Session::put('notification', [
                'message' => __('common.added_successfully'),
                'type'    => 'success',
            ]);
            return redirect()->route('warehousesList.create');
            // return redirect()->route('qalam.index');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error storing Qalam', ['error' => $e->getMessage()]);

            Session::put('notification', [
                'message' => __('common.add_failed'),
                'type'    => 'danger',
            ]);
            return redirect()->route('qalam.index')->withErrors($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function getprice(string $model_id)
    {
        $price = DB::table('model_details')
            ->join('models', 'models.id', '=', 'model_details.model_id')
            ->where('model_details.model_id', $model_id)
            ->where('models.branch_id', $this->branch_id)
            ->selectRaw('SUM(model_details.total_price) as model_details_total_price')
            ->first();

        return response()->json([
            'data' => number_format((float)$price->model_details_total_price,2) ?? 0
        ]);   
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $qalam = Qalam::findOrFail($id);
        if($qalam) {
            $qalam->delete();
            return response()->json([
                'status' => 'success',
                'message' => __('common.deleted_successfully')
            ]);
        }
        else
        {
            return response()->json([
                'status' => 'failed',
                'message' => __('common.delete_failed')
            ]);
        }
    }
}

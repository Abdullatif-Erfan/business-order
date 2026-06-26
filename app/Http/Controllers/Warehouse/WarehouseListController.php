<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
// use App\Models\Buy\BoughtItem;
use App\Models\Setting\Currency;
use Carbon\Carbon;
use App\Models\Setting\OrgBio;
use App\Models\Setting\Unit;
use App\Models\Buy\BuyPreList;
use App\Models\Setting\Warehouse;
use App\Models\Warehouse\WarehouseItem;



use Yajra\DataTables\Facades\DataTables;



class WarehouseListController extends Controller
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
    public function index(Request $request)
    {
        $id = $request->query('id');
        $currencies = Currency::all();
        // $branches = Branch::all();
        $orgbios = OrgBio::all();
        $todaysDate = Carbon::now()->format('Y-m-d');
        $warehouse = Warehouse::select('id','name')->where('id',$id)->first();
        

           
        // $WarehouseItems = WarehouseItem::with(['currencyRelation','unitRelation','preListRelation'])
        // ->where('warehouse_id', 1)
        // ->orderBy('id','DESC')
        // ->orderBy('buy_pre_id','DESC')->get();
            
        // return ['data', $WarehouseItems];
        
        return view('warehouseitem.list',compact('currencies','todaysDate','orgbios','warehouse'));
    }


    /**
     * Get paginated data
     */
   public function getData(Request $request)
{
    // \Log::info('Received Request:', $request->all());
    // Log::info('Received warehouse_id:', ['warehouse_id' => $request->input('warehouse_id')]);
    // return response()->json(['message' => 'Debugging getData', 'request' => $request->all()]);

    $warehouse_id = 1;
    $tax_activation = $request->input('tax_activation', 0); // Get from request or default 0
    
    $WarehouseItems = WarehouseItem::with(['currencyRelation','unitRelation','preListRelation'])
        ->where('warehouse_id', $warehouse_id)
        ->orderBy('id', 'DESC')
        ->orderBy('buy_pre_id', 'DESC');
        
    if ($request->input('item_name')) {
        $WarehouseItems->whereHas('preListRelation', function ($query) use ($request) {
            $query->where('name', 'LIKE', "%{$request->input('item_name')}%");
        });
    }
    
    if ($request->input('currency_id')) {
        $WarehouseItems->where('currency_id', $request->input('currency_id'));
    }
    
    return DataTables::of($WarehouseItems)
        ->addIndexColumn()
        ->addColumn('prelist', function ($WarehouseItem) {
            return optional($WarehouseItem->preListRelation)->name ?? '';
        })
        ->addColumn('unit', function ($WarehouseItem) {
            return optional($WarehouseItem->unitRelation)->name ?? '';
        })
        ->addColumn('buy_tax_per', function($WarehouseItem) {
            return $WarehouseItem->buy_tax_per ? "% " . $WarehouseItem->buy_tax_per : '';
        })
        ->addColumn('total', function ($WarehouseItem) use ($tax_activation) {
            // Use $tax_activation variable, not $this->$tax_activation
            return (int)$tax_activation === 1 
                ? number_format($WarehouseItem->total_vat ?? 0, 2) 
                : number_format($WarehouseItem->total ?? 0, 2);
        })
        ->addColumn('sell_up', function ($WarehouseItem) use ($tax_activation) {
            // Use $tax_activation variable, not $this->$tax_activation
            return (int)$tax_activation === 1 
                ? number_format($WarehouseItem->sell_up_vat ?? 0, 2) 
                : number_format($WarehouseItem->sell_up ?? 0, 2);
        })
        ->addColumn('view', function ($WarehouseItem) {
            return '<a href="warehousesList/details/'.$WarehouseItem->id.'" class="hidden-print">
                        <i class="fas fa-eye viewItems" data-id="' . $WarehouseItem->id . '" style="font-size:20px;"></i>
                    </a>';
        })
        ->rawColumns(['view'])
        ->make(true);
}



    /**
     * Show warehouse details by id
     */
    public function details(string $id)
    {   
        if (!$id) {
            throw new \Exception('Id not found');
        }

        $orgbios = OrgBio::all();
        $currencies = Currency::all();
        $units = Unit::all();
        $todaysDate = Jalalian::now()->format('Y-m-d');
        $warehouseItems = WarehouseItem::with(['currencyRelation','unitRelation','preListRelation'])
        ->where('id', $id)->get();
        $warehouse = Warehouse::select('name')->where('id',$warehouseItems->first()->warehouse_id)->first();

        //  return response()->json(['warehouseItems' => $WarehouseItems]);

         return view('warehouseitem.details',compact('todaysDate','orgbios','warehouseItems','warehouse','currencies','units'));
    }

    /**
     * Get Warehouse Item for transfer in the modal
     */
    public function getWarehouseItemForTransfer(string $id)
    {
        $warehouseItems = WarehouseItem::with(['unitRelation','preListRelation'])
        ->where('id', $id)->first();
        $warehouses = Warehouse::select('id','name')->get();
        $units = Unit::all();
        // return response()->json(['data' => $warehouseItems]);
        // return response()->json(['data' => $warehouse]);
        return view('warehouseitem.modalTransfer',compact('warehouseItems','warehouses','units'));

    }

    /**
     * Get Warehouse Item for Conversion in the modal
     */
    public function getWarehouseItemForConversion(string $id)
    {
        $warehouseItems = WarehouseItem::with(['unitRelation','preListRelation','currencyRelation'])
        ->where('id', $id)->first();

        $warehouses = Warehouse::select('id','name')->get();
        $default_currency = Currency::select('id','name','symbols')->where('is_base','=','yes')->first();
        $units = Unit::all();
        // return response()->json(['data' => $warehouseItems]);
        // return response()->json(['data' => $warehouse]);
        return view('warehouseitem.modalConversion',compact('warehouseItems','warehouses','units','default_currency'));

    }

    /**
     *  Transfer from Warehouse to Warehouse
     */
    public function updateTransfer(Request $request)
    { 
        $validated = $request->validate([
            'id' => 'required|exists:warehouse_items,id',
            'source_warehouse_id' => 'required|min:1',
            'distination_warehouse_id' => 'required|numeric|min:1',
            'amount' => 'nullable|numeric|min:1',
            'unit_id' => 'required'
        ]);

        DB::beginTransaction();
        try 
        {
            // **Source Warehouse**
            $sourceWareHouseItem = WarehouseItem::where('id', $validated['id'])->firstOrFail();
            
            // Check if amount is greater than available amount
            if ($validated['amount'] > $sourceWareHouseItem->available_amount) {
                throw new \Exception('Amount exceeds available stock.');
            }

            // Reduce stock from source warehouse
            $sourceWareHouseItem->available_amount -= $validated['amount'];
            $sourceWareHouseItem->out_amount += $validated['amount'];
            $sourceWareHouseItem->available_total -= ($validated['amount'] * $sourceWareHouseItem->avg_up);
            $sourceWareHouseItem->save();

            // **Destination Warehouse**
            $distWareHouseItem = WarehouseItem::where('buy_pre_id', $sourceWareHouseItem->buy_pre_id)
                ->where('warehouse_id', $validated['distination_warehouse_id'])
                
                ->where('unit_id', $validated['unit_id'])
                ->first();



            if (!$distWareHouseItem) 
            {
                // \Log::info('Create New Record in Warehouse during transfer');
                // Create new record in destination warehouse
                $distWareHouseItem = new WarehouseItem();
                $distWareHouseItem->name = $validated['item_name'] ?? '';
                $distWareHouseItem->warehouse_id = $validated['distination_warehouse_id'];
                $distWareHouseItem->buy_pre_id = $sourceWareHouseItem->buy_pre_id;
                $distWareHouseItem->name = $sourceWareHouseItem->name;
                $distWareHouseItem->unit_id = $sourceWareHouseItem->unit_id;
                $distWareHouseItem->bought_up = $sourceWareHouseItem->bought_up;
                $distWareHouseItem->available_amount = $validated['amount'];
                $distWareHouseItem->in_amount = $validated['amount'];
                $distWareHouseItem->out_amount = 0;
                $distWareHouseItem->wastage_amount = 0;
                $distWareHouseItem->wastage_total = 0;
                $distWareHouseItem->avg_up = $sourceWareHouseItem->avg_up;
                $distWareHouseItem->sell_up = $sourceWareHouseItem->sell_up;
                $distWareHouseItem->total = $validated['amount'] * $sourceWareHouseItem->bought_up;
                $distWareHouseItem->available_total = $validated['amount'] * $sourceWareHouseItem->avg_up;
                $distWareHouseItem->currency_id = $sourceWareHouseItem->currency_id;
                $distWareHouseItem->notification_amount = $sourceWareHouseItem->currency_id;
                $distWareHouseItem->inserted_by =  auth()->user()->full_name ?? '';
                $distWareHouseItem->expire_date =  $sourceWareHouseItem->expire_date;
                $distWareHouseItem->inserted_short_date =  $sourceWareHouseItem->inserted_short_date;
                $distWareHouseItem->year =  $sourceWareHouseItem->year;
                $distWareHouseItem->month =  $sourceWareHouseItem->month;
                $distWareHouseItem->day =  $sourceWareHouseItem->day;
                $distWareHouseItem->is_cleared = 0;
                $distWareHouseItem->save();

            } 
            else 
            {
                // \Log::info('Increase stock in destination warehouse');
                $total_available_amount = $distWareHouseItem->available_amount + $validated['amount'];
                $distWareHouseItem->available_amount = $total_available_amount;
                $distWareHouseItem->in_amount += $validated['amount'];
                $distWareHouseItem->available_total = ($total_available_amount * $distWareHouseItem->avg_up);
                $distWareHouseItem->save();
            }

            DB::commit();

            Session::put('notification', [
                'message' => __('common.moved_successfully'),
                'type' => 'success',
            ]);

            return redirect()->route('warehousesList.details', $validated['id']);

        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            \Log::error('Error in updateTransfer', ['error' => $e->getMessage()]);

            Session::put('notification', [
                'message' => __('common.move_failed') . $e->getMessage(),
                'type' => 'danger',
            ]);

            return redirect()->route('warehousesList.details', $validated['id']);
        }
    }

    
    /**
    *  Convert Item based on unit_id
    */
    public function updateConversion(Request $request)
    { 
        // return response()->json(['data' => $request->all()]);
        // die();

        $validated = $request->validate([
            'id' => 'required|exists:warehouse_items,id',
            'source_warehouse_id' => 'required|min:1',
            'convertable_amount' => 'required|numeric|min:1',
            'options' => 'required|numeric|min:1',
            'new_unit_id' => 'required|numeric|min:1',
            'converted_amount' => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();
        try 
        {
            /**
             * مقدار اجناس انتخاب شده باید از مقدار قبلی اش کم شود
             * مقدار اجناس با واحد جدید اگر قبلا وجود داشت آپدیت شود مقدارش و اگر نداشت جدیدا ثبت شود
             */
            // **Source Warehouse**
            $sourceWareHouseItem = WarehouseItem::where('id', $validated['id'])->firstOrFail();
            
            // Check if convertable_amount is greater than stock available_amount
            if ($validated['convertable_amount'] > $sourceWareHouseItem->available_amount) {
                throw new \Exception('Amount exceeds available stock.');
            }

            // Reduce stock from source warehouse
            $sourceWareHouseItem->available_amount -= $validated['convertable_amount'];
            $sourceWareHouseItem->out_amount += $validated['convertable_amount'];
            $sourceWareHouseItem->available_total -= ($validated['convertable_amount'] * $sourceWareHouseItem->avg_up);
            $sourceWareHouseItem->save();

            // **Destination unit_id**
            $distWareHouseItem = WarehouseItem::where('buy_pre_id', $sourceWareHouseItem->buy_pre_id)
                ->where('warehouse_id', $validated['source_warehouse_id'])
                ->where('unit_id', $validated['new_unit_id'])
                
                ->first();

            if (!$distWareHouseItem) // اگر ریکارد قبلی وجود نداشته باشد باید ثبت شود.
            {
                // \Log::info('Create New Record in Warehouse during conversion');
                // Create new record in destination warehouse
                // options:1 => to lower, 2: to greater;
                $avg_up = $request->avg_up;
                // if($validated['options'] == 1) // change from greater to lower
                // {
                //     $avg_up = (($validated['convertable_amount'] * $sourceWareHouseItem->avg_up) /  $validated['converted_amount']);
                // }
                // else if($validated['options'] == 2) // change from lower to greater
                // {
                //     $avg_up  =  (($validated['convertable_amount'] / $validated['converted_amount']) * $sourceWareHouseItem->avg_up);
                // }
                $distWareHouseItem = new WarehouseItem();
                $distWareHouseItem->name = $validated['item_name'] ?? '';
                $distWareHouseItem->warehouse_id = $validated['source_warehouse_id'];
                $distWareHouseItem->buy_pre_id = $sourceWareHouseItem->buy_pre_id;
                $distWareHouseItem->name = $sourceWareHouseItem->name;
                $distWareHouseItem->unit_id = $validated['new_unit_id'];
                // $distWareHouseItem->bought_up = $sourceWareHouseItem->bought_up * $validated['convertable_amount'];
                $distWareHouseItem->bought_up = $avg_up; // وقتیکه واحد تغیر کند نرخ آخر خرید نباید از واحد قبلی انتقال کند
                $distWareHouseItem->available_amount = $validated['converted_amount'];
                $distWareHouseItem->in_amount = $validated['converted_amount'];
                $distWareHouseItem->out_amount = 0;
                $distWareHouseItem->wastage_amount = 0;
                $distWareHouseItem->wastage_total = 0;
                $distWareHouseItem->avg_up = $avg_up; 
                $distWareHouseItem->sell_up = 0;
                $distWareHouseItem->total = ($sourceWareHouseItem->total + ($validated['converted_amount'] * $avg_up));
                $distWareHouseItem->available_total = $validated['converted_amount'] * $avg_up;
                $distWareHouseItem->currency_id = $sourceWareHouseItem->currency_id;
                $distWareHouseItem->notification_amount = $sourceWareHouseItem->currency_id;
                $distWareHouseItem->inserted_by =  auth()->user()->full_name ?? '';
                $distWareHouseItem->expire_date =  $sourceWareHouseItem->expire_date;
                $distWareHouseItem->inserted_short_date =  $sourceWareHouseItem->inserted_short_date;
                $distWareHouseItem->year =  $sourceWareHouseItem->year;
                $distWareHouseItem->month =  $sourceWareHouseItem->month;
                $distWareHouseItem->day =  $sourceWareHouseItem->day;
                $distWareHouseItem->is_cleared = 0;
                $distWareHouseItem->save();
            } 
            else // باید مقدارش آپدیت شود
            {
                // \Log::info('Increase stock in destination warehouse');
                // if($validated['options'] == 1) // change to lower
                // {
                //     // 1: 
                //     // 25 * 8 = ttl 200 , avg1 = 25
                //     // 2:
                //     // 30 * 8 = 6 = ttl 240, avg2 ? 
                //     // avg2 = 200 + 240 / 16 = 27.5

                //     // 8 unit, 200 ttl1, avg1 = 25
                //     // 8 unit, ? ttl2,   
                //     // avg2 = ttl1 + ttl2 / 16;

                //     // $recent_avg_up = (($validated['convertable_amount'] * $distWareHouseItem->avg_up) /  $validated['converted_amount']);
                //     // $recent_total = $validated['converted_amount'] * $recent_avg_up;
                //     // $avg_up = (($distWareHouseItem->available_total +  $recent_total) / ($distWareHouseItem->available_amount + $validated['converted_amount']));
                //     $avg_up  = $distWareHouseItem->avg_up;
                // }
                // else // change from lower to greater
                // {
                //     $avg_up  = $distWareHouseItem->avg_up;
                // }

                $avg_up = $request->avg_up;
                $total_available_amount = $distWareHouseItem->available_amount + $validated['converted_amount'];
                $distWareHouseItem->available_amount = $total_available_amount;
                $distWareHouseItem->in_amount += $validated['converted_amount'];
                $distWareHouseItem->available_total = ($total_available_amount * $avg_up);
                $distWareHouseItem->save();
            }

            DB::commit();

            Session::put('notification', [
                'message' => __('common.moved_successfully'),
                'type' => 'success',
            ]);
            
            return redirect()->route('warehousesList.details', $validated['id']);

        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            \Log::error('Error in updateConversion', ['error' => $e->getMessage()]);

            Session::put('notification', [
                'message' => __('common.move_failed') . $e->getMessage(),
                'type' => 'danger',
            ]);

            return redirect()->route('warehousesList.details', $validated['id']);
        }
    }


    /**
     * Show a form for inserting old items
     */
    public function create(Request $request)
    {
        // $boughtList = BoughtItemDetails::with(['boughtItemRelation','preListRelation'])->get();
       
        $currencies = Currency::select('id','name')->get();
        $warehouses = Warehouse::select('id','name')->get();
        $preLists = BuyPreList::select('id','name','code')->get();

        $todaysDate = Jalalian::now()->format('Y-m-d');
        $units = Unit::select('id','name')->get();

        $times = time();

        // return response()->json($preLists);
        return view('warehouseitem.create.oldItemForm',compact('currencies','todaysDate','preLists','units','warehouses','times'));
    }

    /**
     * Store old items in warehouse_items
     */
    public function store(Request $request)
    {
        // return response()->json(['data'=>$request->all()]);
        $this->validateRequest($request);
        DB::beginTransaction();
        try 
        {
          $this->createOrUpdateWarehouseItems($request);
          
          DB::commit();
         return response()->json(['status' => 'success'], 201); 

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error storing BoughtDetailsController', ['error' => $e]);
            
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500); // Use 500 for server errors

        } 
    }


    // +++++++++++++++++++++++++++++ STORE RELATED FUNCTIONS ++++++++++++++++++++++++++++++++++++++
     private function validateRequest($request)
    {
        $validated = $request->validate([
            'pre_list_id' => 'required|integer',
            'amount' => 'required|numeric|min:0.01',
            'unit_id' => 'required|integer',
            'bought_up' => 'required|numeric|min:0.01',
            'currency_id' => 'required|integer',
            'warehouse_id' => 'required|array',
            'warehouse_id.*' => 'exists:warehouses,id',
            'warehouse_amount' => 'required|array',
            'warehouse_amount.*' => 'numeric',
            'warehouse_sell_up' => 'required|array',
            'warehouse_sell_up.*' => 'numeric',
        ], [
                'wh_pre_list_id.required' => __('validate.wh_pre_list_id.required'),
            
                'wh_amount.required' => __('validate.wh_amount.required'),
                'wh_amount.numeric' => __('validate.wh_amount.numeric'),
            
                'wh_unit_id.required' => __('validate.wh_unit_id.required'),
                'wh_unit_id.integer' => __('validate.wh_unit_id.integer'),
            
                'wh_bought_up.required' => __('validate.wh_bought_up.required'),
                'wh_bought_up.numeric' => __('validate.wh_bought_up.numeric'),
                'wh_bought_up.min' => __('validate.wh_bought_up.min'),
            
                'wh_currency_id.required' => __('validate.wh_currency_id.required'),
                'wh_currency_id.integer' => __('validate.wh_currency_id.integer'),
            
                'wh_warehouse_id.required' => __('validate.wh_warehouse_id.required'),
                'wh_warehouse_id.array' => __('validate.wh_warehouse_id.array'),
                'wh_warehouse_id.*.exists' => __('validate.wh_warehouse_id.*.exists'),
            
                'wh_warehouse_amount.required' => __('validate.wh_warehouse_amount.required'),
                'wh_warehouse_amount.array' => __('validate.wh_warehouse_amount.array'),
                'wh_warehouse_amount.*.numeric' => __('validate.wh_warehouse_amount.*.numeric'),
            
                'wh_warehouse_sell_up.required' => __('validate.wh_warehouse_sell_up.required'),
                'wh_warehouse_sell_up.array' => __('validate.wh_warehouse_sell_up.array'),
                'wh_warehouse_sell_up.*.numeric' => __('validate.wh_warehouse_sell_up.*.numeric'),
        ]);
        
    }

    private function createOrUpdateWarehouseItems($request)
    {
        /**
         * 1: Check based on (buy_pre_id and warehouse_id) 
         * 2: If record exists, update it
         * 3: If it doesn't exist, insert a new item
         */
    
        $short_date = $request->todays_date ?? Jalalian::now()->format('Y-m-d');
        [$year, $month, $day] = explode('-', $short_date);
        $insertedBy = auth()->user()->full_name ?? '';
    
        // Prepare bulk insert/update data
        $warehouseItemsToInsert = [];
        $warehouseItemsToUpdate = [];
    
        foreach ($request->warehouse_id as $index => $warehouseId) 
        {
            $WarehouseItem = WarehouseItem::where('warehouse_id', $warehouseId)
                ->where('buy_pre_id', $request->pre_list_id)
                ->where('unit_id', $request->unit_id)
                
                ->first();
    
            $warehouseAmount = $request->warehouse_amount[$index];
            $new_total = $request->bought_up * $warehouseAmount; // Cost of new stock
    
            if ($WarehouseItem) 
            {
                $available_amounts = $WarehouseItem->available_amount + $warehouseAmount;
                $new_avg_up = ($available_amounts > 0) ? (($WarehouseItem->available_total + $new_total) / $available_amounts) : 0;
                $new_available_total = $available_amounts * $new_avg_up;

                // Update existing warehouse item
                $warehouseItemsToUpdate[] = [
                    'id' => $WarehouseItem->id,
                    'in_amount' => $WarehouseItem->in_amount + $warehouseAmount,
                    'available_amount' => $available_amounts,
                    'wastage_amount' => $WarehouseItem->wastage_amount,
                    'wastage_total' => $WarehouseItem->wastage_total,
                    'bought_up' => $request->bought_up,
                    'avg_up' => $new_avg_up,
                    'total' => $WarehouseItem->total + $new_total,
                    'available_total' => $new_available_total,
                    'sell_up' => $request->warehouse_sell_up[$index],
                    'notification_amount' => $request->notification_amount ?? 0,
                    'inserted_by' => $insertedBy,
                    'expire_date' => $request->expire_date ?? null,
                    'times' => $request->times,
                    'is_cleared' => 0,
                ];
            } 
            else 
            {
                // Insert new warehouse item
                $warehouseItemsToInsert[] = [
                    'warehouse_id' => $warehouseId,
                    'buy_pre_id' => $request->pre_list_id,
                    'name' => $request->item_name ?? 'Unknown Item',
                    'in_amount' => $warehouseAmount,
                    'out_amount' => 0.00,
                    'available_amount' => $warehouseAmount,
                    'wastage_amount' => $request->wastage_amount ?? 0,
                    'wastage_total' => $request->wastage_total ?? 0,
                    'unit_id' => $request->unit_id,
                    'bought_up' => $request->bought_up,
                    'avg_up' => $request->bought_up,
                    'sell_up' => $request->warehouse_sell_up[$index],
                    'total' => $new_total,
                    'available_total' => $new_total,
                    'currency_id' => $request->currency_id,
                    'notification_amount' => $request->notification_amount ?? 0,
                    'inserted_by' => $insertedBy,
                    'expire_date' => $request->expire_date ?? null,
                    'inserted_short_date' => $short_date,
                    'year' => $year,
                    'month' => $month,
                    'day' => $day,
                    'times' => $request->times,
                    'is_cleared' => 0,
                ];
            }
        }
    
        // Bulk update existing records
        if (!empty($warehouseItemsToUpdate)) {
            foreach ($warehouseItemsToUpdate as $updateData) {
                WarehouseItem::where('id', $updateData['id'])->update($updateData);
            }
        }
    
        // Bulk insert new records
        if (!empty($warehouseItemsToInsert)) {
            WarehouseItem::insert($warehouseItemsToInsert);
        }
    
        return true;
    }
    


    // +++++++++++++++++++++++++++++ / STORE RELATED FUNCTIONS ++++++++++++++++++++++++++++++++++++++


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function update(Request $request)
    {
            // return response()->json(['data' => $request->all()]);
            // Validate request data
             $validated = $request->validate([
                'updateId'    => 'required|numeric',
                'unit_id' => 'required|exists:units,id',
                'currency_id' => 'required|exists:currencies,id',
                'available_amount' => 'required|numeric|min:0',
                'in_amount' => 'nullable|numeric|min:0',
                'out_amount' => 'nullable|numeric|min:0',
                'bought_up' => 'nullable|numeric|min:0',
                'sell_up' => 'nullable|numeric|min:0',
                'avg_up' => 'nullable|numeric|min:0',
                'available_total' => 'nullable|numeric|min:0',
                'notification_amount' => 'nullable|numeric|min:0',
                'expire_date' => 'nullable',
                'wastage_amount' => 'nullable|numeric|min:0',
                'wastage_total' => 'nullable|numeric|min:0',
                'inserted_short_date' => 'nullable|date',
            ]);

            // Find the warehouse item
            $warehouseItem = WarehouseItem::findOrFail($validated['updateId']);

            // Update fields
            $warehouseItem->update([
                'unit_id' => $request->unit_id,
                'currency_id' => $request->currency_id,
                'available_amount' => $request->available_amount,
                'in_amount' => $request->in_amount,
                'out_amount' => $request->out_amount,
                'bought_up' => $request->bought_up,
                'sell_up' => $request->sell_up,
                'avg_up' => $request->avg_up,
                'available_total' => $request->available_total,
                'notification_amount' => $request->notification_amount,
                'expire_date' => $request->expire_date,
                'wastage_amount' => $request->wastage_amount,
                'wastage_total' => $request->wastage_total,
                'inserted_short_date' => $request->inserted_short_date,
                'inserted_by' => auth()->user()->full_name ?? ''
            ]);

        
            Session::put('notification', [
                'message' => __('common.updated_successfully'),
                'type' => 'success',
            ]);

            return redirect()->route('warehousesList.index', ['id' => $warehouseItem->warehouse_id]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        
        try {
            // \Log::info('Start deleting record in WarehouseListController: ');
            // Delete all related records directly
            WarehouseItem::where('id',$id)->delete();
            return response()->json(['status' => 'success', 'message' => __('common.deleted_successfully')]); 
        } catch (\Exception $e) {
    
            \Log::error('Error deleting record in WarehouseListController: ' . $e->getMessage());
    
            return response()->json(['status' => 'failed', 'message' => __('common.delete_failed')]); 
        }
    }

    // ======================== ALL List ========================================
    public function all()
    {
        $currencies = Currency::all();
        // $branches = Branch::all();
        $orgbios = OrgBio::all();
        $todaysDate = Jalalian::now()->format('Y-m-d');
        
        // $WarehouseItem = WarehouseItem::with(['currencyRelation','unitRelation','preListRelation'])->where('warehouse_id',$id)->get();
        // return response()->json(['data' => $warehouse]);

        return view('warehouseitem.all_list',compact('currencies','todaysDate','orgbios'));
    }


    /**
     * Get paginated data
     */
    public function allData(Request $request)
    {

        // \Log::info('Received Request:', $request->all()); // Log incoming request
        // Log::info('Received warehouse_id:', ['warehouse_id' => $request->input('warehouse_id')]); // Log warehouse_id properly
        // return response()->json(['message' => 'Debugging getData', 'request' => $request->all()]);

        $WarehouseItems = WarehouseItem::with(['warehouseRelation','currencyRelation','unitRelation','preListRelation'])
        
        ->orderBy('id','DESC')
        ->orderBy('buy_pre_id','DESC');
            
    
        if ($request->input('item_name')) {
            $WarehouseItems->whereHas('preListRelation', function ($query) use ($request) {
                $query->where('name', 'LIKE', "%{$request->input('item_name')}%");
            });
        }

        if ($request->input('wh_name')) {
            $WarehouseItems->whereHas('warehouseRelation', function ($query) use ($request) {
                $query->where('name', 'LIKE', "%{$request->input('wh_name')}%");
            });
        }
        
        
        if ($request->input('currency_id')) {
            $WarehouseItems->where('currency_id', $request->input('currency_id'));
        }
        
            return DataTables::of($WarehouseItems)
            
            ->addIndexColumn()

            ->addColumn('prelist', function ($WarehouseItem) {
                return optional($WarehouseItem->preListRelation)->name ?? '';
            })

            ->addColumn('wh_name', function ($WarehouseItem) {
                return optional($WarehouseItem->warehouseRelation)->name ?? '';
            })

            ->addColumn('currency', function ($WarehouseItem) {
                return optional($WarehouseItem->currencyRelation)->name ??  '';
            })

            ->addColumn('unit', function ($WarehouseItem) {
                return optional($WarehouseItem->unitRelation)->name ?? '';
            })

            ->addColumn('available_total', function ($WarehouseItem) {
                return $WarehouseItem->avg_up ? number_format($WarehouseItem->avg_up * $WarehouseItem->available_amount,2) : '';
            })
           
           
           
            ->addColumn('view', function ($WarehouseItem) {
                return '<a href="warehousesList/details/'.$WarehouseItem->id.'" class="hidden-print"><i class="fas fa-eye viewItems" 
                data-id="' . $WarehouseItem->id . '" style="font-size:20px;"></i></a>';
            })

            ->rawColumns(['view'])
            ->make(true);

    }
}

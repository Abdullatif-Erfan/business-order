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
// use App\Models\Setting\Branch;
use Morilog\Jalali\Jalalian;
use App\Models\Setting\OrgBio;
use App\Models\Setting\Unit;
use App\Models\Buy\BuyPreList;
use App\Models\Setting\Warehouse;
use App\Models\Warehouse\WarehouseItem;



use Yajra\DataTables\Facades\DataTables;



class WarehouseListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $id = $request->query('id');
        $currencies = Currency::all();
        // $branches = Branch::all();
        $orgbios = OrgBio::all();
        $todaysDate = Jalalian::now()->format('Y-m-d');
        $warehouse = Warehouse::select('id','name')->where('id',$id)->first();
        
        // $WarehouseItem = WarehouseItem::with(['currencyRelation','unitRelation','preListRelation'])->where('warehouse_id',$id)->get();
        // return response()->json(['data' => $warehouse]);

        return view('warehouseitem.list',compact('currencies','todaysDate','orgbios','warehouse'));
    }


    /**
     * Get paginated data
     */
    public function getData(Request $request)
    {

        // \Log::info('Received Request:', $request->all()); // Log incoming request
        // Log::info('Received warehouse_id:', ['warehouse_id' => $request->input('warehouse_id')]); // Log warehouse_id properly
        // return response()->json(['message' => 'Debugging getData', 'request' => $request->all()]);

        $warehouse_id = $request->input('warehouse_id');
        $WarehouseItems = WarehouseItem::with(['currencyRelation','unitRelation','preListRelation'])
        ->where('warehouse_id', $warehouse_id)
        ->orderBy('id','DESC');
            
    
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
                return $WarehouseItem->preListRelation->name ? $WarehouseItem->preListRelation->name : '';
            })

            ->addColumn('currency', function ($WarehouseItem) {
                return $WarehouseItem->currencyRelation->name ? $WarehouseItem->currencyRelation->name : '';
            })

            ->addColumn('unit', function ($WarehouseItem) {
                return $WarehouseItem->unitRelation->name ? $WarehouseItem->unitRelation->name : '';
            })

            ->addColumn('available_total', function ($WarehouseItem) {
                return $WarehouseItem->avg_up ? number_format($WarehouseItem->avg_up * $WarehouseItem->available_amount,2) : '';
            })
           
            ->addColumn('wastage_total', function ($WarehouseItem) {
                return $WarehouseItem->wastage_total ? number_format($WarehouseItem->wastage_total,2) : '';
            })
           
            ->addColumn('view', function ($WarehouseItem) {
                return '<a href="warehousesList/details/'.$WarehouseItem->id.'" class="hidden-print"><i class="fas fa-eye viewItems" 
                data-id="' . $WarehouseItem->id . '" style="font-size:20px;"></i></a>';
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

        //  return response()->json(['data' => $WarehouseItems]);

         return view('warehouseitem.details',compact('todaysDate','orgbios','warehouseItems','warehouse','currencies','units'));
    }

    /**
     * Get Warehouse Item for transfer in the modal
     */
    public function getWarehouseItemForTransfer(string $id)
    {
        $warehouseItems = WarehouseItem::with(['unitRelation','preListRelation'])
        ->where('id', $id)->get();
        $warehouses = Warehouse::select('id','name')->get();
        $units = Unit::all();
        // return response()->json(['data' => $warehouseItems]);
        // return response()->json(['data' => $warehouse]);
        return view('warehouseitem.modalTransfer',compact('warehouseItems','warehouses','units'));

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
                ->first();

            if (!$distWareHouseItem) 
            {
                \Log::info('Create New Record in Warehouse during transfer');
                // Create new record in destination warehouse
                $distWareHouseItem = new WarehouseItem();
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
                $distWareHouseItem->save();

            } 
            else 
            {
                \Log::info('Increase stock in destination warehouse');
                $total_available_amount = $distWareHouseItem->available_amount + $validated['amount'];
                $distWareHouseItem->available_amount = $total_available_amount;
                $distWareHouseItem->in_amount += $validated['amount'];
                $distWareHouseItem->available_total = ($total_available_amount * $distWareHouseItem->avg_up);
                $distWareHouseItem->save();
            }

            DB::commit();

            Session::flash('notification', [
                'message' => 'موفقانه انتقال گردید',
                'type' => 'success',
            ]);

            return redirect()->route('warehousesList.details', $validated['id']);

        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            \Log::error('Error in updateTransfer', ['error' => $e->getMessage()]);

            Session::flash('notification', [
                'message' => 'انتقال نگردید: ' . $e->getMessage(),
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
        $preLists = BuyPreList::select('id','name','branch_id')->get();

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
            'pre_list_id.required' => ' نام جنس از  فهرست الزامی است.',
        
            'amount.required' => 'تعداد جنس الزامی است.',
            'amount.numeric' => 'تعداد جنس باید عدد باشد.',
        
            'unit_id.required' => 'انتخاب واحد جنس الزامی است.',
            'unit_id.integer' => 'شناسه واحد باید عدد صحیح باشد.',
        
            'bought_up.required' => 'قیمت خرید الزامی است.',
            'bought_up.numeric' => 'قیمت خرید باید عدد باشد.',
            'bought_up.min' => 'قیمت خرید باید حداقل 0.01 باشد.',
        
            'currency_id.required' => 'انتخاب واحد پول الزامی است.',
            'currency_id.integer' => 'شناسه واحد پول باید عدد صحیح باشد.',
        
            'warehouse_id.required' => 'حداقل یک گدام را انتخاب کنید.',
            'warehouse_id.array' => 'فرمت گدام‌ها نادرست است.',
            'warehouse_id.*.exists' => 'انتخاب گدام الزامی است.',
        
            'warehouse_amount.required' => 'تعداد انتقال الزامی است.',
            'warehouse_amount.array' => 'فرمت تعداد انتقال نادرست است.',
            'warehouse_amount.*.numeric' => 'تعداد انتقال باید عدد باشد.',
        
            'warehouse_sell_up.required' => 'قیمت فروش الزامی است.',
            'warehouse_sell_up.array' => 'فرمت قیمت‌های فروش نادرست است.',
            'warehouse_sell_up.*.numeric' => 'قیمت فروش باید عدد باشد.',
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
                'expire_date' => 'nullable|date',
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

        
            Session::flash('notification', [
                'message' => 'موفقانه ویرایش گردید',
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
            // Delete all related records directly
            WarehouseItem::where('id',$id)->delete();
    
            return response()->json(['status' => 'success', 'message' => 'موفقانه حذف گردید']); 
        } catch (\Exception $e) {
    
            \Log::error('Error deleting record in WarehouseListController: ' . $e->getMessage());
    
            return response()->json(['status' => 'failed', 'message' => 'حذف نگردید']); 
        }
    }
}

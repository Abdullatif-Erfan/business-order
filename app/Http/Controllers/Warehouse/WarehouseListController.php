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
use App\Models\Setting\Car;
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
        $orgbios    = OrgBio::all();
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
        
        $WarehouseItems = WarehouseItem::with(['currencyRelation','unitRelation','preListRelation','carRelation'])
            ->where('warehouse_id', $warehouse_id)
            ->orderBy('id', 'DESC')
            ->orderBy('buy_pre_id', 'DESC');
            
        if ($request->input('item_name')) {
            $WarehouseItems->whereHas('preListRelation', function ($query) use ($request) {
                $query->where('name', 'LIKE', "%{$request->input('item_name')}%");
            });
        }

        if ($request->input('car_name')) {
            $WarehouseItems->whereHas('carRelation', function ($query) use ($request) {
                $query->where('name', 'LIKE', "%{$request->input('car_name')}%");
            });
        }
        
        if ($request->input('currency_id')) {
            $WarehouseItems->where('currency_id', $request->input('currency_id'));
        }

         if($request->input('availability_options') && $request->input('availability_options') == 1) {
            $WarehouseItems->where('available_amount','>', 0);
        }
        

        if ($request->start_date && $request->end_date) {
                $WarehouseItems->whereBetween('idate', [$request->start_date, $request->end_date]);
            } elseif ($request->start_date) {
                $WarehouseItems->whereDate('idate', '=', $request->start_date);
            } elseif ($request->end_date) {
                $WarehouseItems->whereDate('idate', '<=', $request->end_date);
            }
        
        return DataTables::of($WarehouseItems)
            ->addIndexColumn()
            ->addColumn('prelist', function ($WarehouseItem) {
                return optional($WarehouseItem->preListRelation)->name ?? '';
            })
            ->addColumn('carName', function ($WarehouseItem) {
                return optional($WarehouseItem->carRelation)->name ?? '';
            })
            ->addColumn('unit', function ($WarehouseItem) {
                return optional($WarehouseItem->unitRelation)->name ?? '';
            })
            ->addColumn('buy_tax_per', function($WarehouseItem) {
                return $WarehouseItem->buy_tax_per ? "% " . $WarehouseItem->buy_tax_per : '';
            })
            
            ->addColumn('buy_tax_price', function($WarehouseItem) {
                return $WarehouseItem->buy_tax_price ? $WarehouseItem->buy_tax_price: ''; 
            })
            ->addColumn('buy_up_vat', function($WarehouseItem) {
                return $WarehouseItem->buy_up_vat ? $WarehouseItem->buy_up_vat: ''; 
            })
            
            ->addColumn('available_total', function ($WarehouseItem) {
                // Use $tax_activation variable, not $this->$tax_activation
                return  number_format($WarehouseItem->available_total ?? 0, 2);
            })
            //  ->addColumn('buy_up', function ($WarehouseItem)  use ($tax_activation) {
            //     return (int)$tax_activation === 1 ? 
            //         number_format($WarehouseItem->buy_up ?? 0, 2) 
            //         : number_format($WarehouseItem->buy_up_vat ?? 0, 2);
            // })
            // ->addColumn('sell_up', function ($WarehouseItem) use ($tax_activation) {
            //     return (int)$WarehouseItem->buy_tax_per > 0 ? 
            //         number_format($WarehouseItem->sell_up_vat ?? 0, 2) 
            //         : number_format($WarehouseItem->sell_up ?? 0, 2);
            // })

            ->addColumn('buy_up', function($WarehouseItem) {
                // If tax is enabled, show price with VAT
                if ($WarehouseItem->buy_tax_per && $WarehouseItem->buy_tax_per > 0) {
                    return $WarehouseItem->buy_up_vat;
                }
                return $WarehouseItem->buy_up; 
            })


            ->addColumn('total', function($WarehouseItem) {
                return number_format($WarehouseItem->total,2); 
            })

            
            ->addColumn('sell_up', function($WarehouseItem) {
                  if ($WarehouseItem->sell_tax_per && $WarehouseItem->sell_tax_per > 0) {
                    return $WarehouseItem->sell_up_vat;
                }
                return $WarehouseItem->sell_up; 
            })

             ->addColumn('transfer', function ($WarehouseItem) {
                return '<i class="fas fa-exchange-alt transferItems" data-id="' . $WarehouseItem->id . '" style="font-size:20px;color:blue; cursor:pointer"></i>';
            })
            // ->addColumn('view', function ($WarehouseItem) {
            //     return '<a href="warehousesList/details/'.$WarehouseItem->id.'" class="hidden-print">
            //                 <i class="fas fa-eye viewItems" data-id="' . $WarehouseItem->id . '" style="font-size:20px;"></i>
            //             </a>';
            // })
            ->rawColumns(['view','buy_tax_per','transfer'])
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
        $todaysDate = Carbon::now()->format('Y-m-d');
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
        $warehouseItems = WarehouseItem::with(['unitRelation','preListRelation','carRelation'])
        ->where('id', $id)->first();
        $units = Unit::all();
        $cars = Car::all();
        // return response()->json(['data' => $warehouseItems]);
        // return response()->json(['data' => $warehouse]);
        return view('warehouseitem.modalTransfer',compact('warehouseItems','cars','units'));

    }

 
    /**
     *  Transfer from Warehouse to Warehouse
     */
    public function updateTransfer(Request $request)
    { 
        $validated = $request->validate([
            'id' => 'required|exists:warehouse_items,id',
            'source_warehouse_id' => 'required|numeric|min:1',
            'car_id' => 'required|numeric|min:1|exists:cars,id',
            'amount' => 'required|numeric|min:0.01',
            'unit_id' => 'required|exists:units,id',
            'item_name' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try 
        {
            // **Source Car (Current Item)**
            $sourceItem = WarehouseItem::where('id', $validated['id'])->firstOrFail();
            
            // Check if amount is greater than available amount
            if ($validated['amount'] > $sourceItem->available_amount) {
                throw new \Exception('Amount exceeds available stock. Available: ' . $sourceItem->available_amount);
            }

            // **Reduce stock from source item**
            $sourceItem->available_amount -= $validated['amount'];
            $sourceItem->in_amount -= $validated['amount'];
            // $sourceItem->out_amount += $validated['amount'];
            
            // Calculate available total based on buy_up
            $valuationPrice = $sourceItem->buy_up;
            if (intval($sourceItem->buy_tax_per) > 0) {
                $valuationPrice = $sourceItem->buy_up_vat ?? $sourceItem->buy_up;
            }
            $sourceItem->available_total = round($sourceItem->available_amount * $valuationPrice, 2);
            $sourceItem->total = round($sourceItem->in_amount * $valuationPrice, 2);
            $sourceItem->save();

            // **Destination Car**
            // Check if item already exists in destination car with same buy_pre_id and unit_id
            $destinationItem = WarehouseItem::where('buy_pre_id', $sourceItem->buy_pre_id)
                ->where('car_id', $validated['car_id'])
                ->where('unit_id', $validated['unit_id'])
                ->first();

            if (!$destinationItem) 
            {
                // Create new record in destination car
                $destinationItem = new WarehouseItem();
                $destinationItem->warehouse_id = $validated['source_warehouse_id'];
                $destinationItem->billno = $sourceItem->billno ?? null;
                $destinationItem->buy_pre_id = $sourceItem->buy_pre_id;
                $destinationItem->name = $sourceItem->name;
                $destinationItem->unit_id = $sourceItem->unit_id;
                $destinationItem->car_id = $validated['car_id'];
                $destinationItem->supplier_id = $sourceItem->supplier_id ?? null;
                $destinationItem->buy_up = $sourceItem->buy_up;
                $destinationItem->buy_tax_per = $sourceItem->buy_tax_per;
                $destinationItem->buy_tax_price = $sourceItem->buy_tax_price;
                $destinationItem->buy_up_vat = $sourceItem->buy_up_vat;
                $destinationItem->sell_up = $sourceItem->sell_up;
                $destinationItem->sell_tax_per = $sourceItem->sell_tax_per;
                $destinationItem->sell_tax_price = $sourceItem->sell_tax_price;
                $destinationItem->sell_up_vat = $sourceItem->sell_up_vat;
                $destinationItem->currency_id = $sourceItem->currency_id;
                $destinationItem->category_id = $sourceItem->category_id;
                $destinationItem->in_amount = $validated['amount'];
                $destinationItem->out_amount = 0;
                $destinationItem->available_amount = $validated['amount'];
                
                // Calculate totals
                $destinationItem->total = $validated['amount'] * $valuationPrice;
                $destinationItem->available_total = round($validated['amount'] * $valuationPrice, 2);
                
                $destinationItem->user_id = auth()->id();
                $destinationItem->idate = now()->format('Y-m-d');
                $destinationItem->year = now()->year;
                $destinationItem->month = now()->month;
                $destinationItem->day = now()->day;
                $destinationItem->is_cleared = 0;
                $destinationItem->save();
            } 
            else 
            {
                // Increase stock in destination car
                $destinationItem->available_amount += $validated['amount'];
                $destinationItem->in_amount += $validated['amount'];
                
                // Recalculate available_total
                $destValuationPrice = $destinationItem->buy_up;
                if (intval($destinationItem->buy_tax_per) > 0) {
                    $destValuationPrice = $destinationItem->buy_up_vat ?? $destinationItem->buy_up;
                }
                $destinationItem->available_total = round($destinationItem->available_amount * $destValuationPrice, 2);
                $destinationItem->total = round($destinationItem->in_amount * $valuationPrice, 2);
                $destinationItem->save();
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => __('common.moved_successfully'),
                'data' => [
                    'source_item_id' => $sourceItem->id,
                    'destination_item_id' => $destinationItem->id,
                ]
            ]);

        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            \Log::error('Error in updateTransfer', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => __('common.move_failed') . ': ' . $e->getMessage()
            ], 500);
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

        $todaysDate = Carbon::now()->format('Y-m-d');
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
    
        $short_date = $request->todays_date ?? Carbon::now()->format('Y-m-d');
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
        $todaysDate = Carbon::now()->format('Y-m-d');
        
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

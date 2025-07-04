<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Setting\Currency;
use Morilog\Jalali\Jalalian;
use App\Models\Setting\OrgBio;
use App\Models\Setting\Unit;
use App\Models\Buy\BuyPreList;
use App\Models\Setting\Warehouse;
use App\Models\Warehouse\WarehouseItem;
use App\Models\Warehouse\WarehouseWastage;




use Yajra\DataTables\Facades\DataTables;



class WarehouseWastageController extends Controller
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
    public function index(Request $request)
    {
        $currencies = Currency::all();
        $orgbios = OrgBio::all();
        $todaysDate = Jalalian::now()->format('Y-m-d');
        
        // $WarehouseWastage = WarehouseWastage::with(['currencyRelation','unitRelation','preListRelation'])->get();
        // return response()->json(['data' => $WarehouseWastage]);

        return view('warehouseitem.wastage.list',compact('currencies','todaysDate','orgbios'));
    }


    /**
     * Get paginated data
     */
    public function getData(Request $request)
    {
        $WarehouseWastage = WarehouseWastage::with(['currencyRelation','unitRelation','preListRelation'])
        ->where('branch_id', $this->branch_id)
        ->orderBy('id','DESC');            
    
        if ($request->input('item_name')) {
            $WarehouseWastage->whereHas('preListRelation', function ($query) use ($request) {
                $query->where('name', 'LIKE', "%{$request->input('item_name')}%");
            });
        }
        
        if ($request->input('currency_id')) {
            $WarehouseWastage->where('currency_id', $request->input('currency_id'));
        }
        
        if ($request->start_date && $request->end_date) {
            $WarehouseWastage->whereBetween('idate', [$request->start_date, $request->end_date]);
        } elseif ($request->start_date) {
            $WarehouseWastage->whereDate('idate', '=', $request->start_date);
        } elseif ($request->end_date) {
            $WarehouseWastage->whereDate('idate', '>=', $request->end_date); // Until today
        }

            return DataTables::of($WarehouseWastage)
            
            ->addIndexColumn()

            ->addColumn('prelist', function ($warehouseWastage) {
                return optional($warehouseWastage->preListRelation)->name ?? '';
            })

            ->addColumn('currency', function ($warehouseWastage) {
                return optional($warehouseWastage->currencyRelation)->name ??  '';
            })

            ->addColumn('unit', function ($warehouseWastage) {
                return optional($warehouseWastage->unitRelation)->name ?? '';
            })           
            ->make(true);

    }

    public function create()
    {
        // $warehouseItems = WarehouseItem::with(['currencyRelation','unitRelation','preListRelation','warehouseRelation'])
        // ->where('available_amount','>',0)
        // ->where('branch_id', $this->branch_id)
        // ->orderBy('id','DESC')
        // ->get();
        $warehouseItems = DB::table('warehouse_items')
        ->join('bought_item_pre_lists', 'bought_item_pre_lists.id', '=', 'warehouse_items.buy_pre_id')
        ->join('warehouses', 'warehouses.id', '=', 'warehouse_items.warehouse_id')
        ->join('units', 'units.id', '=', 'warehouse_items.unit_id')
        ->where('warehouse_items.available_amount', '>', 0)
        ->select('warehouse_items.id','warehouse_items.currency_id','warehouse_items.expire_date','warehouse_items.unit_id','avg_up','sell_up', 'warehouse_items.available_amount', 'units.name as unit_name','warehouses.id as warehouse_id', 'warehouses.name as warehouse_name', 'bought_item_pre_lists.name as item_name','bought_item_pre_lists.branch_id','bought_item_pre_lists.id as pre_list_id')
        ->where('warehouse_items.branch_id', $this->branch_id)
        ->get();

        $todaysDate = Jalalian::now()->format('Y-m-d');
        $units = Unit::select('id','name')->get();

        // return response()->json($warehouseItems);

        return view('warehouseitem.wastage.create',compact('todaysDate','units','warehouseItems'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // return response()->json(['data' => $request->all()]);
        $check = true;
        // Validate the request and return errors if validation fails
        $validator = Validator::make($request->all(), $this->validationRules(), $this->validationMessages());

        if ($validator->fails()) 
        {
            return redirect()->route('sales.wastage.create')
                ->withErrors($validator)
                ->withInput(); // Preserve old input
        }

        // Start the transaction
        DB::beginTransaction();

        try 
        {
            
            // create warehouse_wastage
            $check = $this->createWarehouseWastage($request);
            
            // decrease from warehouse_items
            $checkWarehouseItems = $this->decreaseWarehouseItemFromSoldAmount($request);

            if(!$check || !$checkWarehouseItems)
            {
                DB::rollBack();
                Session::put('notification', [
                    'message' => __('common.add_failed'),
                    'type' => 'danger',
                ]);
                return redirect()->route('warehousesList.wastage.create');
            }

            // Flash error message
            DB::commit();
            Session::put('notification', [
                'message' => __('common.added_successfully'),
                'type' => 'success',
            ]);
             return redirect()->route('warehousesList.wastage');
 
 
         } catch (\Exception $e) {
             // Rollback the transaction if an error occurs
             DB::rollBack();
             // Optionally, log the error for debugging
             \Log::error('Error storing WarehouseWastageController', ['error' => $e]);

            // Flash error message
            Session::put('notification', [
                'message' => __('common.add_failed'),
                'type' => 'danger',
            ]);
             return redirect()->route('warehousesList.wastage.create');
         }   
    }

    /**
     * Validation rules
     */
    private function validationRules()
    {
        return [
            'warehouse_id'  => 'required',
            'warehouseItemId.*' => 'required|integer|exists:warehouse_items,id',
            'amount' => 'required|array',
            'amount.*' => 'required|numeric|min:1',
            'unit_id' => 'required|array',
            'unit_id.*' => 'required|integer|exists:units,id',
            'avg_up' => 'required|array',
            'avg_up.*' => 'nullable|numeric|min:0',
            'total' => 'required|array',
            'total.*' => 'nullable|numeric|min:0',
        ];
    }

    /**
     * Custom validation messages
     */
    private function validationMessages()
    {
        return [
            'warehouseItemId.required' => 'انتخاب حداقل یک کالا الزامی است.',
            'warehouseItemId.array' => 'فرمت کالاها صحیح نیست.',
            'warehouseItemId.*.integer' => 'شناسه کالا باید عدد باشد.',
            'warehouseItemId.*.exists' => 'کالای انتخاب شده معتبر نیست.',
            'amount.required' => 'وارد کردن مقدار الزامی است.',
            'amount.array' => 'فرمت مقدار کالاها صحیح نیست.',
            'amount.*.numeric' => 'مقدار کالا باید عدد باشد.',
            'amount.*.min' => 'مقدار کالا نمی‌تواند کمتر از ۱ باشد.',
            'unit_id.required' => 'انتخاب واحد کالا الزامی است.',
            'unit_id.array' => 'فرمت واحدها صحیح نیست.',
            'unit_id.*.integer' => 'شناسه واحد باید عدد باشد.',
            'unit_id.*.exists' => 'واحد انتخاب شده معتبر نیست.',
            'total.required' => 'وارد کردن مجموع مبلغ الزامی است.',
            'total.array' => 'فرمت مجموع مبلغ صحیح نیست.',
            'total.*.numeric' => 'مجموع مبلغ باید عدد باشد.',
            'total.*.min' => 'مجموع مبلغ نمی‌تواند منفی باشد.',
        ];
    }

    private function createWarehouseWastage($request)
    {
        $todays_date =  Jalalian::now()->format('Y-m-d');
        $date = explode('-', $todays_date);
        $year = $date[0];
        $month = $date[1];
        $day  = $date[2];

        foreach($request->warehouseItemId as $index => $itemId)
        {
            WarehouseWastage::create([
                'warehouse_id' => $request->warehouse_id[$index],
                'warehouse_item_id' => $itemId, 
                'buy_pre_id' => $request->pre_list_id[$index], 
                'currency_id' => $request->currency_id[$index],
                'amount' => $request->amount[$index], 
                'bought_up' => $request->avg_up[$index], 
                'total' => $request->total[$index],  
                'unit_id' => $request->unit_id[$index], 
                'branch_id' => $request->branch_id[$index] ?? $this->branch_id,
                'year' => $year, 
                'month' => $month, 
                'day'   => $day,
                'idate' => $todays_date,
                'iby'   => auth()->user()->full_name ?? '',
                'expire_date' => $request->expire_date[$index]
            ]);
        }

        return true;
    }

    /**
     * Decrease the amount of items in stock by sold amount
     */
    private function decreaseWarehouseItemFromSoldAmount($request)
    {
        foreach ($request->warehouseItemId as $index => $itemId) {
            $warehouseItem = WarehouseItem::where('id', $itemId)->first(); 

            if ($warehouseItem) {
                $warehouseItem->out_amount += $request->amount[$index];
                $warehouseItem->available_amount -= $request->amount[$index];
                $warehouseItem->save();
            } else {
                return false;
            }
        }
        return true;
    }

}
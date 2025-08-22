<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Production\Qalam;
use App\Models\Production\Models;
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

            ->addColumn('edit', function($models) {
                return '<i class="fas fa-pen-square editIcon" data-id="'.$models->id.'" style="font-size:20px;"></i>';
            })
            ->addColumn('delete', function($models) {
                return '<i class="fas fa-trash-alt deleteIcon" data-id="'.$models->id.'" style="font-size:20px; color:red;"></i>';
            })
            ->rawColumns(['addItem','edit','delete'])
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
        // return response()->json(['data'=>$request->all()]);
        // die();
        // $validated = $request->validate([
        //     'name' => 'required|string|max:255|min:3|unique:models,name',
        //     'branch_id' => 'required|exists:branches,id',
        // ]);
        // ['branch_id','model_id','amount','unit_id','unit_price','total_price','currency_id','dates','user'];

        // $this->validateRequest($request);
         // 1. Insert into qalam table
        DB::beginTransaction();
        try 
        {
          // insert into qalam table
          $qalam = new Qalam();
          $qalam->branch_id = $request->branch_id ?? $this->branch_id;
          $qalam->model_id  =  $request->model_id;
          $qalam->amount    = $request->amount;
          $qalam->unit_id   = $request->unit_id;
          $qalam->unit_price = $request->price;
          $qalam->total_price = $request->amount * $request->price;
          $qalam->currency_id = $request->currency_id;
          $qalam->dates = Jalalian::now()->format('Y-m-d');
          $qalam->user = auth()->user()->full_name ?? '';
          $qalam->save();
         
          /**
           * fetch the modelDetails Items and multiply curr amount * modelDetails->amount => then decrease that amount from warehouse items
           * 2. Fetch model details for this model
           * 
           */
        $modelDetails = ModelDetails::where('model_id', $request->model_id)
        ->where('branch_id', $this->branch_id)
        ->get();

        // 3. Loop through modelDetails and calculate required qty
        foreach ($modelDetails as $detail) {
            // required quantity = entered amount * recipe amount
            $requiredQty = $request->amount * $detail->amount;

            // 4. Deduct from warehouse items
            $warehouseItem = WarehouseItem::where('branch_id', $this->branch_id)
                ->where('item_id', $detail->pre_list_id) // or proper FK column
                ->first();

            if ($warehouseItem) {
                if ($warehouseItem->stock < $requiredQty) {
                    throw new \Exception("Not enough stock for item {$detail->pre_list_id}");
                }

                $warehouseItem->stock -= $requiredQty;
                $warehouseItem->save();
            } else {
                throw new \Exception("Item {$detail->pre_list_id} not found in warehouse.");
            }
        }


          DB::commit();
          Log::info('qalam stored successfully');
          Session::put('notification', [
            'message' => __('common.added_successfully'),
            'type' => 'success',
           ]);
          return redirect()->route('warehousesList.create');


        } catch (\Exception $e) {
             DB::rollBack();
            \Log::error('Error storing Qalam', ['error' => $e]);
            
            Session::put('notification', [
                'message' => __('common.add_failed'),
                'type' => 'success',
            ]);
            return redirect()->route('qalam.index');
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
            'data' => $price->model_details_total_price ?? 0
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
        //
    }
}

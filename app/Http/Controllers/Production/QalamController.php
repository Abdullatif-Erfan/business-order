<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Production\Qalam;
use App\Models\Production\Models;
use App\Models\Setting\Unit;

use App\Models\Setting\Currency;
use Morilog\Jalali\Jalalian;
use App\Models\Setting\Warehouse;

use Illuminate\Support\Facades\DB;
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
        $models = Models::orderBy('id','DESC')->get();
        $units = Unit::orderBy('id','DESC')->get();
        $currency = Currency::orderBy('id','DESC')->get();

        // return response()->json(['models' => $models, 'units' => $units, 'currency' => $currency]);

        $branch_id = $this->branch_id;
        return view('production.qalam.list', compact('branch_id','models','units','currency'));
    }
    
    
    /**
     * Show the journal data
     */
    public function getData(Request $request)
    {
        // 'branch_id','model_id','amount','unit_id','unit_price','total_price','currency_id','dates','user'
        $qalams = Qalam::with(['modelDetailsRelation','currencyRelation','unitRelation'])  
        ->where('branch_id', $this->branch_id)
        ->orderByDesc('id');
    
        return DataTables::of($qalams)
            
            ->addIndexColumn()

            ->addColumn('addItem', function ($qalams) {
                return '<a href="modelDetails/create/' . $qalams->id . '" class="hidden-print">
                            <i class="btn btn-sm btn-success" data-id="' . $qalams->id . '">'
                                . (($qalams->model_details_relation_count > 0) 
                                    ? 'Item count: ' . $qalams->model_details_relation_count . ' / Edit' 
                                    : 'Add Item') .
                            '</i>
                        </a>';
            })

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
        // return response()->json(['data'=>$request->all()]);
        $validated = $request->validate([
            'name' => 'required|string|max:255|min:3|unique:models,name',
            'branch_id' => 'required|exists:branches,id',
        ]);
        ['branch_id','model_id','amount','unit_id','unit_price','total_price','currency_id','dates','user'];

        // $this->validateRequest($request);
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
         
          // fetch the modelDetails Items and multiply curr amount * modelDetails->amount => then decrease that amount from warehouse items
          DB::commit();
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

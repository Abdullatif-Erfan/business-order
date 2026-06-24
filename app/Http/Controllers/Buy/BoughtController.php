<?php

namespace App\Http\Controllers\Buy;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Buy\BoughtItem;
use App\Models\Setting\Account;
use App\Models\Setting\Currency;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction\Journal;
use App\Models\Buy\BoughtItemDetails; 
use App\Models\Warehouse\WarehouseItem;
use Yajra\DataTables\Facades\DataTables;

class BoughtController extends Controller
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
        // $boughtlists = BoughtItem::with(['account', 'currency'])->get(); // Eager loading the relations
        // return response()->json($boughtItems); // Or return view with data
        $currencies = Currency::all();
        $todaysDate = Carbon::now()->format('Y-m-d');

        return view('buy.bought.list',compact('currencies','todaysDate'));
    }

    /**
     * show paginated data 
     */
    public function getData(Request $request)
    {
        $boughtlists = BoughtItem::with(['account', 'currency'])->orderBy('id', 'DESC')->get();

        return DataTables::of($boughtlists)
            
            ->addIndexColumn()
            // ->addColumn('branch', function($buyPreList) {
            //     return $buyPreList->branchRelation->name;
            // })
            ->addColumn('edit', function($buyList) {
                return '<i class="fas fa-pen-square editIcon" data-id="'.$buyList->id.'" style="font-size:20px;"></i>';
            })
            ->addColumn('delete', function($buyList) {
                return '<i class="fas fa-trash-alt deleteIcon" data-id="'.$buyList->id.'" style="font-size:20px; color:red;"></i>';
            })
            ->rawColumns(['edit','delete'])
            ->make(true);
    }
    /**
    * Show the form for creating a new resource.
    */
    public function create()
    {
        $accounts = Account::get(); // Fetch all accounts
        $currencies = Currency::all(); // Fetch all currencies

        return response()->json(compact('accounts', 'currencies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'currency_id' => 'required|exists:currencies,id',
            'billno' => 'required|string',
            'total_price' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'payable' => 'nullable|numeric',
            // Other validation rules...
        ]);

        // Create a new BoughtItem
        $boughtItem = BoughtItem::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Bought item created successfully',
            'data' => $boughtItem
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $boughtItem = BoughtItem::with(['account', 'currency'])->findOrFail($id);

        return response()->json($boughtItem);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $boughtItem = BoughtItem::findOrFail($id);
        $accounts = Account::all();
        $currencies = Currency::all();

        return response()->json(compact('boughtItem', 'accounts', 'currencies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'currency_id' => 'required|exists:currencies,id',
            'billno' => 'required|string',
            'total_price' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'payable' => 'nullable|numeric',
            // Other validation rules...
        ]);

        $boughtItem = BoughtItem::findOrFail($id);
        $boughtItem->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Bought item updated successfully',
            'data' => $boughtItem
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $boughtItem = BoughtItem::findOrFail($id);
        $boughtItem->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Bought item deleted successfully'
        ]);
    }

    public function delete_uncompleted_buy(string $times)
    {
        /**
         * 1: list all warehouses
         * 2: delete if item exists in
         */

        DB::beginTransaction();
        try {
            // Delete all related records directly
            WarehouseItem::where('times', $times)->delete();
            BoughtItemDetails::where('times', $times)->delete();
            BoughtItem::where('times', $times)->delete();
            Journal::where('times', $times)->delete();

            DB::commit();
    
            Session::put('notification', [
                'message' => __('common.deleted_successfully'),
                'type' => 'success',
            ]);
    
            return redirect()->route('boughtList.index'); 
        } catch (\Exception $e) {
            DB::rollBack();
    
            \Log::error('Error deleting records: ' . $e->getMessage());
    
            Session::put('notification', [
                'message' => __('common.delete_failed'),
                'type' => 'danger',
            ]);
    
            return back();
        }
    }

}
<?php

namespace App\Http\Controllers\Buy;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Buy\BoughtItem;
use App\Models\Setting\Account;
use App\Models\Setting\Currency;
use App\Models\Setting\Branch;
use Morilog\Jalali\Jalalian;
use Yajra\DataTables\Facades\DataTables;


class BoughtController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $boughtlists = BoughtItem::with(['account', 'currency'])->get(); // Eager loading the relations
        // return response()->json($boughtItems); // Or return view with data
        $currencies = Currency::all();
        $branches = Branch::all();
        $todaysDate = Jalalian::now()->format('Y-m-d');

        return view('buy.bought.list',compact('currencies','branches','todaysDate'));
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
        $accounts = Account::all(); // Fetch all accounts
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
}
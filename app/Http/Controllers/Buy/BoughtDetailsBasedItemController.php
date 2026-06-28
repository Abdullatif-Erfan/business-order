<?php

namespace App\Http\Controllers\Buy;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use App\Models\Setting\OrgBio;
use App\Models\Setting\Unit;
use App\Models\Buy\BuyPreList;
use App\Models\Buy\BoughtItem;
use App\Models\Setting\Currency;
use App\Models\Buy\BoughtItemDetails; 
use App\Models\Transaction\Journal;
use App\Models\Setting\Warehouse;
use App\Models\Warehouse\WarehouseItem;

use App\Models\Setting\Account;
use Yajra\DataTables\Facades\DataTables;


class BoughtDetailsBasedItemController extends Controller
{
    protected  $isAdmin;
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
        // $boughtItemDetails = BoughtItemDetails::with(['boughtItemRelation','accountRelation','preListRelation','unitRelation'])->get();
        // return response()->json(['boughtItemDetails' => $boughtItemDetails]);

        $currencies = Currency::all();
        $orgbios = OrgBio::all();
        $todaysDate = Carbon::now()->format('Y-m-d');

        return view('buy.bought.item_list',compact('currencies','todaysDate','orgbios'));
    }

    public function getData(Request $request)
    {

            $tax_activation = $request->input('tax_activation', 0); // Get from request or default 0
            $boughtItems = BoughtItemDetails::with(['boughtItemRelation','accountRelation','preListRelation','unitRelation'])->orderBy('id', 'DESC');
            
              // Apply filters if provided
              if ($request->customer_name) {
                    $boughtItems->whereHas('accountRelation', function ($query) use ($request) {
                    $query->where('name', 'LIKE', "%{$request->customer_name}%");
                });
            }
            
            if ($request->item_name) {
                $boughtItems->whereHas('preListRelation', function ($query) use ($request) {
                $query->where('name', 'LIKE', "%{$request->item_name}%");
              });
           }

            if ($request->currency_id) {
                $boughtItems->whereHas('boughtItemRelation', function ($query) use ($request) {
                    $query->where('currency_id', $request->currency_id);
                });
            }
            
            if ($request->start_date && $request->end_date) 
            {
                $boughtItems->whereHas('boughtItemRelation', function ($query) use ($request) {
                    $query->whereBetween('idate', [$request->start_date, $request->end_date]);
                });
            } elseif ($request->start_date) {
                $boughtItems->whereHas('boughtItemRelation', function ($query) use ($request) {
                    $query->whereDate('idate', '=', $request->start_date);
                });
            } elseif ($request->end_date) {
                $boughtItems->whereHas('boughtItemRelation', function ($query) use ($request) {
                    $query->whereDate('idate', '<=', $request->start_date);
                });
            }
            
            if ($request->bill_number) {
                $boughtItems->where('billno', $request->bill_number);
            }
            
            return DataTables::of($boughtItems->get())
            

            ->addIndexColumn()
            // ->addColumn('branch', function($buyPreList) {
            //     return $buyPreList->branchRelation->name;
            // })

            ->addColumn('billno', function($boughtItem) {
                return $boughtItem->billno ? $boughtItem->billno : '';
            })

             ->addColumn('buy_up', function($boughtItem) {
                //  <!--   اگر در زمان ثبت این ریکارد مالیات فعال بوده است حتما باید ریکارد مالیات دار  نشان داده شود در هر حالت -->
                if ($boughtItem->buy_tax_per && $boughtItem->buy_tax_per > 0) {
                    return $boughtItem->buy_up_vat;
                }
                return $boughtItem->buy_up;
            })

            ->addColumn('buy_tax_per', function($boughtItem){
                return "%". " ".$boughtItem->buy_tax_per;
            })
         
            ->addColumn('total', function ($boughtItem) use ($tax_activation) {
            // Use $tax_activation variable, not $this->$tax_activation
               if ($boughtItem->buy_tax_per && $boughtItem->buy_tax_per > 0) {
                    return $boughtItem->total_vat;
                }
                return $boughtItem->total;
             })
            ->rawColumns(['billno'])
            ->make(true);

    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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

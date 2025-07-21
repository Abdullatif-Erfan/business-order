<?php

namespace App\Http\Controllers\Setting;

// use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting\Currency;

use App\Models\Buy\BoughtItem;
use App\Models\Buy\BoughtItemDetails;
use App\Models\Warehouse\WarehouseItem;
use App\Models\Warehouse\WarehouseSales;
use App\Models\Transaction\Journal;

use Yajra\DataTables\Facades\DataTables;


class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $currencyes = Currency::latest()->paginate(10); // Adjust pagination size as needed
        // return response()->json($currencyes);
        
        if($request->ajax())
        {
            $currency = Currency::query()->select('id','name','symbols','is_base','color')->orderBy('id', 'DESC');
            // Get the first record ID
            $firstRecordId = Currency::orderBy('id', 'ASC')->first()?->id; 

            return  DataTables::eloquent($currency)

            // Add Index Column
            ->addIndexColumn()
            ->addColumn('color', function($currency) {
                return '<div style="width:20px;height:20px;border-radius:50%;background-color:'.$currency->color.'"></div>';
            })
            ->addColumn('is_base', function($currency) {
                return $currency->is_base == 'yes' ? '<i class="fas fa-check-circle"></i>' : '';
            })
            ->addColumn('edit', function($currency) {
                return '<i class="fas fa-pen-square editCurrency" data-id="'.$currency->id.'" style="font-size:20px;"></i>';
            })
            ->addColumn('delete', function($currency) use ($firstRecordId) {
                // Hide delete icon if it's the first record
                if ($currency->id == $firstRecordId) {
                    return ''; // No delete icon for the first record
                }

                return '<i class="fas fa-trash-alt deleteCurrency" data-id="'.$currency->id.'" style="font-size:20px; color:red;"></i>';
            })
            ->rawColumns(['color','is_base','edit','delete'])
            ->make(true);
            // dd($currency); 
        }

    }

    public function create()
    {
        $currencys = Currency::all();
        return view('settings.currency.addForm',compact('currencys'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Define custom validation messages
        // return ['data' => $request->all()];
        $messages = [
            'name.required'   => __('validate.currency_name_required'),
            'name.string'     => __('validate.currency_name_string'),
            'name.max'        => __('validate.currency_name_max'),
            'name.min'        => __('validate.currency_name_min'),
            'name.unique'     => __('validate.currency_name_unique'),
            'symbols.required'=> __('validate.currency_symbols_required'),
            'symbols.string'  => __('validate.currency_symbols_string'),
            'color.max'       => __('validate.currency_color_max'),
        ];

        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255|min:3|unique:currencies,name',
            'symbols' => 'required|string|max:20',
            'color'   => 'nullable|max:20',
        ], $messages);

        // Create new currency
        Currency::create([
            'name' => $validated['name'],
            'symbols' => $validated['symbols'],
            'color' => $validated['color'],
        ]);

        // Return success response
        return response()->json(['status' => 'success','message' => __('common.added_successfully')]);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {   
        $currency = Currency::where('id',$id)->first(); 
        if($currency) {
             return view('settings.currency.editForm',compact('currency'));
         }
        return response()->json(['message' => __('common.not_found')],404);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $messages = [
            'name.required'   => __('validate.currency_name_required'),
            'name.string'     => __('validate.currency_name_string'),
            'name.max'        => __('validate.currency_name_max'),
            'name.min'        => __('validate.currency_name_min'),
            'name.unique'     => __('validate.currency_name_unique'),
            'symbols.required'=> __('validate.currency_symbols_required'),
            'symbols.string'  => __('validate.currency_symbols_string'),
            'color.max'       => __('validate.currency_color_max'),
        ];

        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255|min:3|unique:currencies,name,' . $request->id,
            'symbols' => 'required|string|max:20',
            'color'   => 'nullable|max:20',
        ], $messages);

         $currency = Currency::find($request->id);

         if(!$currency) {
            return response()->json(['message' => __('common.not_found')], 404);
         }
     
        // Update the currency's name
        $currency->name = $request->input('name');
        $currency->symbols = $request->input('symbols');
        $currency->color = $request->input('color');

        $currency->save();

        return response()->json(['status' => 'success','message' => __('common.updated_successfully')], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $currency = Currency::findOrFail($id);
        
        // Check if any related record exists
        $journalExists = Journal::where('currency_id', $id)->exists();
        $boughtItemExists = BoughtItem::where('currency_id', $id)->exists();
        // $boughtItemDetailsExists = BoughtItemDetails::where('currency_id', $id)->exists();
        $warehouseItemExists = WarehouseItem::where('currency_id', $id)->exists();
        $warehouseSalesExists = WarehouseSales::where('currency_id', $id)->exists();
    
        // If any record exists, prevent deletion
        if ($journalExists || $boughtItemExists  || $warehouseItemExists || $warehouseSalesExists) 
        {
            return response()->json(['status' => 'failed', 'message' => __('common.has_records_in_tables')]);
        }
    
        // If no related records exist, delete the currency
        $currency->delete();
        return response()->json(['status' => 'success', 'message' => __('common.deleted_successfully')]);
    }
    

}

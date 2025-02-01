<?php

namespace App\Http\Controllers\Setting;

// use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting\Currency;
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
            ->addColumn('delete', function($currency) {
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
        $messages = [
            'name.required' => 'نام ضروری میباشد',
            'name.string' => 'نام باید حروف باشد',
            'name.max' => 'حداکثر ۲۵۵ حرف مجاز میباشد',
            'name.min' => 'حداقل باید ۳ حرف باشد',
            'name.unique' => 'این نام قبلاً ثبت شده است',
            'symbols.required' => 'سمبول ضروری میباشد',
            'symbols.string' => 'سمبول باید حروف باشد',
            'is_base.required' => 'لطفاً مشخص کنید که آیا این ارز اصلی است یا خیر',
            'is_base.in' => 'انتخاب باید بلی یا نخیر باشد',
            'color.max' => 'رنگ باید حداکثر ۲۰ حرف باشد',
        ];

        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255|min:3|unique:currencies,name',
            'symbols' => 'required|string|max:20',
            'is_base' => 'required|in:yes,no',
            'color' => 'nullable|max:20',
        ], $messages);

        // Create new currency
        Currency::create([
            'name' => $validated['name'],
            'symbols' => $validated['symbols'],
            'is_base' => $validated['is_base'],
            'color' => $validated['color'],
        ]);

        // Return success response
        return response()->json(['status' => 'success','message' => 'موفقانه ثبت گردید']);
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
        return response()->json(['message' => 'یافت نگردید'],404);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $messages = [
            'name.required' => 'نام ضروری میباشد',
            'name.string' => 'نام باید حروف باشد',
            'name.max' => 'حداکثر ۲۵۵ حرف مجاز میباشد',
            'name.min' => 'حداقل باید ۳ حرف باشد',
            'name.unique' => 'این نام قبلاً ثبت شده است',
            'symbols.required' => 'سمبول ضروری میباشد',
            'symbols.string' => 'سمبول باید حروف باشد',
            'is_base.required' => 'لطفاً مشخص کنید که آیا این ارز اصلی است یا خیر',
            'is_base.in' => 'انتخاب باید بلی یا نخیر باشد',
            'color.max' => 'رنگ باید حداکثر ۲۰ حرف باشد',
        ];

        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255|min:3|unique:currencies,name,' . $request->id,
            'symbols' => 'required|string|max:20',
            'is_base' => 'required|in:yes,no',
            'color'   => 'nullable|max:20',
        ], $messages);

         $currency = Currency::find($request->id);

         if(!$currency) {
            return response()->json(['message' => 'ریکارد مورد نظر یافت نشد'], 404);
         }
     
        // Update the currency's name
        $currency->name = $request->input('name');
        $currency->symbols = $request->input('symbols');
        $currency->color = $request->input('color');
        $currency->is_base = $request->input('is_base');

        $currency->save();

        return response()->json(['status' => 'success','message' => 'ریکارد با موفقیت بروزرسانی شد'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $currency = Currency::findOrFail($id);
        if($currency) 
        {
            $currency->delete();
            return response()->json(['status' => 'success', 'message' => 'موفقانه حذف گردید']);
        }
        return response()->json(['status' => 'failed', 'message' => ' حذف نگردید']);
    }
}

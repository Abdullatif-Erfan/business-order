<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rate;
use App\Models\Setting\Currency;
use Illuminate\Support\Facades\Session;

class RateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $currencies = Currency::all();
        $rates = Rate::with(['fromCurrency', 'toCurrency'])->get(); // Eager loading relationships
        return view('rates.list', compact('rates','currencies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $currencies = Currency::all();
        return view('rates.create', compact('currencies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'from_currency_id' => 'required|exists:currencies,id',
            'to_currency_id' => 'required|exists:currencies,id',
            'to_currency_amount' => 'required|numeric|min:0.01',
            'reverse_amount' => 'required|numeric|min:0.0001',
        ]);

        $request['greater_account_id'] = $request->from_currency_id;
        Rate::create($request->all());

        Session::flash('notification', [
            'message' => 'موفقانه ثبت گردید',
            'type' => 'success',
        ]);

        return redirect()->route('rate.index');

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $rate = Rate::findOrFail($id);
        $currencies = Currency::all();
        return view('rates.edit', compact('rate', 'currencies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'from_currency_id' => 'required|exists:currencies,id',
            'to_currency_id' => 'required|exists:currencies,id',
            'to_currency_amount' => 'required|numeric|min:0.01',
            'reverse_amount' => 'required|numeric|min:0.0001',
        ]);

        $rate = Rate::findOrFail($request->id);
        $rate->update($request->all());

        Session::flash('notification', [
            'message' => 'موفقانه ویرایش گردید',
            'type' => 'success',
        ]);

        return redirect()->route('rate.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $rate = Rate::findOrFail($id);
        $rate->delete();

        Session::flash('notification', [
            'message' => 'موفقانه حذف گردید',
            'type' => 'success',
        ]);

        return redirect()->route('rate.index');
    }
}

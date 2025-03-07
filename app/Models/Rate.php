<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Setting\Currency;

class Rate extends Model
{
    protected $fillable = ['from_currency_id','from_currency_amount','to_currency_id',
    'to_currency_amount','reverse_amount','greater_account_id'];

    // Relationship with Currency for from_currency
    public function fromCurrency()
    {
        return $this->belongsTo(Currency::class, 'from_currency_id');
    }

    // Relationship with Currency for to_currency
    public function toCurrency()
    {
        return $this->belongsTo(Currency::class, 'to_currency_id');
    }
}

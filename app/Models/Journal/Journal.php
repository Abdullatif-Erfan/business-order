<?php

namespace App\Models\Journal;

use Illuminate\Database\Eloquent\Model;
use App\Models\Setting\Currency;

class Journal extends Model
{
    protected $fillable = ['code','account_id','amount','currency_id','transaction_type','payment_type','user_id','year','month','day','status','branch_id','times'];

    
    // A journal belongs to one currency
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }
}

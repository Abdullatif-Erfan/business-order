<?php

namespace App\Models\Journal;

use Illuminate\Database\Eloquent\Model;
use App\Models\Setting\Currency;
use App\Models\Setting\Account;

class Journal extends Model
{
    protected $fillable = ['code','account_id','amount','currency_id','transaction_type','payment_type','user_id','year','month','day','inserted_full_date','inserted_short_date','details','status','branch_id','times','rate','profit','is_cleared','cleared_round'];

    
    // A journal belongs to one currency
    public function currencyRelation()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    /**
     * This (journal) is belongs to account
     * an Account hasMany journal records
     */
    public function accountRelation()
    {
        return $this->belongsTo(Account::class,'account_id');
    }
}

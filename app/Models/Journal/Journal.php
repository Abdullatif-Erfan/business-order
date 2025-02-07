<?php

namespace App\Models\Journal;

use Illuminate\Database\Eloquent\Model;
use App\Models\Setting\Currency;
use App\Models\Setting\Account;
use App\Models\Setting\Branch;
use App\Models\User;

class Journal extends Model
{
    protected $fillable = ['code','account_id','amount','currency_id','transaction_type','payment_type','user_id','year','month','day','inserted_full_date','inserted_short_date','details','status','branch_id','times','is_single_record','rate','profit','is_cleared','cleared_round'];

    
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

      // A journal belongs to one user
      public function userRelation()
      {
          return $this->belongsTo(User::class, 'user_id', 'id');
      }

    //   this (journal) is belongsTo branch
      public function branchRelation()
      {
          return $this->belongsTo(Branch::class, 'branch_id', 'id');
      }
}

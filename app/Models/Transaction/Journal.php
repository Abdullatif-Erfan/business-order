<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Model;
use App\Models\Setting\Currency;
use App\Models\Setting\Account;
use App\Models\Setting\Branch;
use App\Models\Setting\IncomeType;
use App\Models\Setting\ExpenseType;

use App\Models\User;

class Journal extends Model
{
    protected $fillable = ['code','billno','account_id','amount','currency_id','transaction_type','payment_type','options','option_label','user','year','month','day','inserted_full_date','inserted_short_date','doc','details','status','branch_id','dynamic_type','times','is_single_record','rate','belongsToMe','profit','is_cleared','cleared_round'];
    
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
    //   public function userRelation()
    //   {
    //       return $this->belongsTo(User::class, 'user_id', 'id');
    //   }

    //   this (journal) is belongsTo branch
      public function branchRelation()
      {
          return $this->belongsTo(Branch::class, 'branch_id', 'id');
      }

      public function  incomeTypeRelation()
      {
          return $this->belongsTo(IncomeType::class, 'dynamic_type', 'id');
      }

      public function  expenseTypeRelation()
      {
          return $this->belongsTo(ExpenseType::class, 'dynamic_type', 'id');
      }
}

<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Model;
use App\Models\Journal\Journal;
use App\Models\Buy\BoughtItem;
use App\Models\Setting\Currency;


class Account extends Model
{
    //

    protected $fillable = ['account_type_id','branch_id','name','phone','address','description','is_pre_select','percent','salary_currency','net_salary'];

    // an account type has many accounts
    // account is belongs to accountType
    public function accountType()
    {
        return $this->belongsTo(AccountType::class,'account_type_id');
    }

    public function branchRelation()
    {
        return $this->belongsTo(Branch::class,'branch_id');
    }

    // an account has many journal
    // a journal is belongs to account
    public function journals()
    {
        return $this->hasMany(Journal::class,'account_id');
    }

    public function boughtItems()
    {
        return $this->hasMany(BoughtItem::class, 'account_id');
    }

    public function salaryCurrency()
    {
        return $this->belongsTo(Currency::class, 'salary_currency','id');
    }
}

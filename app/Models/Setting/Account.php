<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Model;
use App\Models\Journal\Journal;
use App\Models\Buy\BoughtItem;
use App\Models\Setting\Currency;
use App\Models\User;


class Account extends Model
{
    //

    protected $fillable = ['account_type_id','name','phone','address','description','is_pre_select','percent','salary_currency','net_salary','loan_limit_option','loan_limit','user_account_id'];

 

    // an account type has many accounts
    // account is belongs to accountType
    public function accountType()
    {
        return $this->belongsTo(AccountType::class,'account_type_id');
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

       //✅ Relationship with User (one-to-one)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_account_id');
    }

    // ✅ Scope to get accounts with users
    public function scopeWithUser($query)
    {
        return $query->with('user');
    }

    // ✅ Scope to get accounts without users
    public function scopeWithoutUser($query)
    {
        return $query->whereNull('user_account_id');
    }

    // ✅ Scope to get accounts assigned to specific user
    public function scopeAssignedToUser($query, $userId)
    {
        return $query->where('user_account_id', $userId);
    }

    // ✅ Check if account has assigned user
    public function hasUser()
    {
        return !is_null($this->user_account_id);
    }

    // ✅ Get user name if assigned
    public function getUserNameAttribute()
    {
        return $this->user ? $this->user->full_name : null;
    }
    
}

<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Model;
use App\Models\Journal\Journal;

class Account extends Model
{
    //
    protected $fillable = ['account_type_id','branch_id','name','phone','address','description'];

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
}

<?php

namespace App\Models\Buy;

use Illuminate\Database\Eloquent\Model;
use App\Models\Setting\Account;
use App\Models\Setting\currency;


class BoughtItem extends Model
{
    protected $table = 'bought_items';
    protected $fillable = ['billno','factor','branch_id', 'journal_code', 'total_price', 'discount', 'payable', 'cur_pay', 'remained', 'account_id','customer_account_id','currency_id', 'trans_spend', 'note', 'idate', 'year', 'month', 'day', 'iby', 'times'];

    // Define relationships
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function customer()
    {
        return $this->belongsTo(Account::class, 'customer_account_id','id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }
}

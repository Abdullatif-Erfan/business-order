<?php

namespace App\Models\Buy;

use Illuminate\Database\Eloquent\Model;
use App\Models\Setting\Account;
use App\Models\Setting\Currency;
use App\Models\Journal;

class BoughtBillPayment extends Model
{
    protected $table = 'bought_bill_payments';
    
    protected $fillable = [
        'bought_item_id',
        'billno',
        'supplier_account_id',
        'account_id',
        'currency_id',
        'cur_pay',
        'remained',
        'payment_date',
        'note',
        'journal_code',
        'user_id',
        'user_name',
        'times'
    ];

    // Relationships
    public function boughtItem()
    {
        return $this->belongsTo(BoughtItem::class, 'bought_item_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Account::class, 'supplier_account_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
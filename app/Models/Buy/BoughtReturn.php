<?php

namespace App\Models\Buy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Setting\Account;
use App\Models\Setting\Unit;
use App\Models\Setting\Currency;
use App\Models\Buy\BuyPreList;

class BoughtReturn extends Model
{
    use SoftDeletes;

    protected $table = 'bought_returns';

    protected $fillable = [
        'bought_item_id',
        'bought_item_detail_id',
        'billno',
        'return_number',
        'return_date',
        'supplier_account_id',
        'pre_list_id',
        'unit_id',
        'quantity',
        'unit_price',
        'total',
        'tax_percentage',
        'tax_amount',
        'currency_id',
        'reason',
        'user_id',
        'user_name'
    ];

    protected $casts = [
        'return_date' => 'date',
    ];

    // Relationships
    public function boughtItem()
    {
        return $this->belongsTo(BoughtItem::class, 'bought_item_id');
    }

    public function boughtItemDetail()
    {
        return $this->belongsTo(BoughtItemDetails::class, 'bought_item_detail_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Account::class, 'supplier_account_id');
    }

    public function preList()
    {
        return $this->belongsTo(BuyPreList::class, 'pre_list_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }
}
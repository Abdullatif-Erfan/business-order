<?php

namespace App\Models\Buy;

use Illuminate\Database\Eloquent\Model;
use App\Models\Setting\Unit;
use App\Models\Setting\Account;


class BoughtItemDetails extends Model
{
    protected $table = 'bought_item_details';

    protected $fillable = ['billno', 'bought_item_id','customer_account_id','pre_list_id', 'amount', 'bought_up', 'sell_up', 'unit_id','discount','transport','total', 'is_moved', 'expire_date', 'times'];

    public function boughtItemRelation()
    {
        return $this->belongsTo(BoughtItem::class,'bought_item_id');
    }

    public function preListRelation()
    {
        return $this->belongsTo(BuyPreList::class,'pre_list_id');
    }

    public function unitRelation()
    {
        return $this->belongsTo(Unit::class,'unit_id');
    }

    public function accountRelation()
    {
        return $this->belongsTo(Account::class,'customer_account_id','id');
    }
}

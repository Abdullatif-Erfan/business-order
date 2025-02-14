<?php

namespace App\Models\Buy;

use Illuminate\Database\Eloquent\Model;

class BoughtItemDetails extends Model
{
    protected $table = 'bought_item_details';

    protected $fillable = ['billno', 'bought_item_id', 'pre_list_id', 'amount', 'bought_up', 'sell_up', 'unit_id', 'total', 'is_moved', 'expire_date', 'times'];

    public function boughtItemRelation()
    {
        return $this->belongsTo(BoughtItem::class,'bought_item_id');
    }

    public function preListRelation()
    {
        return $this->belongsTo(BuyPreList::class,'pre_list_id');
    }
}

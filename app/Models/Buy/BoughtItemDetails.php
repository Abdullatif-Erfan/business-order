<?php

namespace App\Models\Buy;

use Illuminate\Database\Eloquent\Model;
use App\Models\Setting\Unit;
use App\Models\Setting\Account;


class BoughtItemDetails extends Model
{
    protected $table = 'bought_item_details';

    protected $fillable = ['billno', 'bought_item_id','category_id','supplier_account_id','pre_list_id', 'amount', 'unit_id', 
    'buy_up', 'buy_tax_per','buy_tax_price', 'buy_up_vat', 'total','expected_profit', 'total_vat', 'sell_up','sell_tax_per','sell_tax_price','sell_up_vat',
    'is_moved', 'times','user_id','user_name'];

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
        return $this->belongsTo(Account::class,'supplier_account_id','id');
    }
    
}

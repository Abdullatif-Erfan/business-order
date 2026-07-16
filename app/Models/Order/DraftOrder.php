<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;
use App\Models\Buy\BuyPreList;
use App\Models\Setting\Unit;
use App\Models\Setting\Category;
use App\Models\Setting\Account;

class DraftOrder extends Model
{
    protected $fillable = [
        'dord_num',
        'customer_id',
        'pre_list_id',
        'category_id',
        'amount',
        'unit_id',
        'iby',
        'idate',
        'user_name',
        'state',
        'times'
    ];

    public function preListRelation()
    {
        return $this->belongsTo(BuyPreList::class, 'pre_list_id');
    }

     public function categoryRelation()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function unitRelation()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
    public function customerRelation()
    {
        return $this->belongsTo(Account::class, 'customer_id','id');
    }
}

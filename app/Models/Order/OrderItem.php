<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;
use App\Models\Buy\BuyPreList;
use App\Models\Setting\Category;
use App\Models\Setting\Unit;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'pre_list_id',
        'category_id',
        'unit_id',
        'amount'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class,'order_id');
    }

    public function preList()
    {
        return $this->belongsTo(BuyPreList::class, 'pre_list_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}
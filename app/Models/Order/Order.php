<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;
use App\Models\Setting\Category;
use App\Models\Setting\Account;

class Order extends Model
{
    protected $fillable = [
        "ord_num",
        'supplier_id',
        'category_id',
        'iby',
        'idate',
        'state',
        'user_name',
        'times'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->ord_num = self::generateOrderNumber();
        });
    }

    public static function generateOrderNumber()
    {
        $prefix = 'ORD-' . date('Y-m-');
        $last = self::where('ord_num', 'LIKE', $prefix . '%')
                    ->orderBy('ord_num', 'desc')
                    ->first();
        $lastNum = $last ? intval(substr($last->ord_num, -4)) : 0;
        return $prefix . str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
    }
  
    // Relationships
   
    public function categoryRelation()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function supplierRelation()
    {
        return $this->belongsTo(Account::class, 'supplier_id','id');
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
}
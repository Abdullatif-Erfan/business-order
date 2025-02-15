<?php

namespace App\Models\Warehouse;

use Illuminate\Database\Eloquent\Model;

class WarehouseItem extends Model
{
    protected $table = "warehouse_items";
    protected $fillable = ['warehouse_id', 'buy_pre_id', 'name', 'in_amount','out_amount','wastage_amount','unit_id', 'bought_up', 'sell_up', 'total', 'currency_id', 'notification_amount', 'inserted_by', 'expire_date', 'inserted_short_date', 'year', 'month', 'day'];
}

<?php

namespace App\Models\Warehouse;

use Illuminate\Database\Eloquent\Model;

class WarehouseSales extends Model
{
    //
    protected $table = 'warehouse_sales';
    protected $fillable = ['billno', 'factor', 'warehouse_item_id', 'account_id', 'branch_id', 'customer_account_id', 'item_name', 'unit_id', 'amount', 'sell_up', 'discount', 'profit', 'total', 'general_discount', 'payable', 'cur_pay', 'remained', 'currency_id', 'total_price', 'note', 'ifull_date', 'iby', 'uby', 'year', 'month', 'day'];
}

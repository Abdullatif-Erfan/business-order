<?php

namespace App\Models\Warehouse;

use Illuminate\Database\Eloquent\Model;
use App\Models\Buy\BuyPreList;
use App\Models\Setting\Unit;


class SalesDetails extends Model
{
    protected $table = "sales_details";
    protected $fillable = [
        'billno',
        'branch_id',
        'warehouse_id',
        'warehouse_sales_id',
        'pre_list_id',
        'unit_id',
        'amount',
        'avg_up',
        'sell_up',
        'discount',
        'profit',
        'total',
        'is_returned',
        'todays_date',
    ];

    function preListRelation()
    {
        return $this->belongsTo(BuyPreList::class,'pre_list_id','id');
    }
    function unitRelation()
    {
        return $this->belongsTo(Unit::class,'unit_id','id');
    }

    public function warehouseSale()
    {
        return $this->belongsTo(WarehouseSales::class, 'warehouse_sales_id');
    }
    public function accountRelation()
    {
        return $this->belongsTo(Account::class,'customer_account_id','id');
    }
}

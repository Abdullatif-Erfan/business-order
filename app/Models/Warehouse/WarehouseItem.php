<?php

namespace App\Models\Warehouse;

use Illuminate\Database\Eloquent\Model;
use App\Models\Setting\Warehouse;
use App\Models\Setting\Currency;
use App\Models\Setting\Unit;
use App\Models\Setting\Branch;
use App\Models\Buy\BuyPreList;

class WarehouseItem extends Model
{
    protected $table = "warehouse_items";
    protected $fillable = ['warehouse_id', 'buy_pre_id', 'name', 'in_amount','out_amount','available_amount','wastage_amount','wastage_total','unit_id', 'bought_up','avg_up', 'sell_up', 'total','available_total', 'currency_id','branch_id', 'notification_amount', 'inserted_by', 'expire_date', 'inserted_short_date', 'year', 'month', 'day','is_cleared'];

   public function warehouseRelation()
   {
       return $this->belongsTo(Warehouse::class,'warehouse_id','id');
   }

   public function currencyRelation()
   {
       return $this->belongsTo(Currency::class, 'currency_id','id');
   }

   public function unitRelation()
   {
       return $this->belongsTo(Unit::class, 'unit_id','id');
   }

   public function branchRelation()
   {
       return $this->belongsTo(Branch::class, 'branch_id', 'id');
   }

   public function preListRelation()
   {
       return $this->belongsTo(BuyPreList::class,'buy_pre_id','id');
   }
}

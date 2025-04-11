<?php

namespace App\Models\Warehouse;

use Illuminate\Database\Eloquent\Model;
use App\Models\Setting\Warehouse;
use App\Models\Setting\Currency;
use App\Models\Setting\Unit;
use App\Models\Setting\Branch;
use App\Models\Buy\BuyPreList;

class WarehouseWastage extends Model
{   
    protected $table='warehouse_wastage';
    protected $fillable = ['warehouse_id','warehouse_item_id','buy_pre_id','amount','bought_up','total','unit_id','currency_id','branch_id','year','month','day','idate','iby','expired_date'];

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

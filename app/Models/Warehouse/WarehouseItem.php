<?php

namespace App\Models\Warehouse;

use Illuminate\Database\Eloquent\Model;
use App\Models\Setting\Warehouse;
use App\Models\Setting\Currency;
use App\Models\Setting\Unit;
use App\Models\Setting\Branch;
use App\Models\Setting\Account;
use App\Models\Buy\BuyPreList;

class WarehouseItem extends Model
{
    protected $table = "warehouse_items";
    protected $fillable = ['warehouse_id', 'buy_pre_id', 'name', 'in_amount','out_amount','available_amount','unit_id',
    'buy_up', 'buy_tax_per','buy_tax_price','buy_up_vat', 'total', 'sell_up','sell_tax_per','sell_tax_price','sell_up_vat',
    'currency_id', 'category_id', 'user_id', 'idate', 'year', 'month', 'day','is_cleared'];

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

   public function accountRelation()
   {
       return $this->belongsTo(Account::class,'customer_account_id','id');
   }

}

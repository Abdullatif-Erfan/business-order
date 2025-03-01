<?php

namespace App\Models\Warehouse;

use Illuminate\Database\Eloquent\Model;
use App\Models\Setting\Currency;
use App\Models\Setting\Account;


class WarehouseSales extends Model
{
    protected $table = 'warehouse_sales';
    protected $fillable = ['billno', 'factor', 'account_id', 'branch_id', 'customer_account_id', 'total_price', 'total_discount', 'payable', 'cur_pay', 'remained', 'currency_id',  'note','short_date','ifull_date', 'iby', 'uby', 'year', 'month', 'day','times','is_cleared']; 

    public function currencyRelation()
    {
       return $this->belongsTo(Currency::class, 'currency_id','id');
    }

    public function accountRelation()
    {
       return $this->belongsTo(Account::class, 'customer_account_id','id');
    }

}

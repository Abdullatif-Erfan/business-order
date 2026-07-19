<?php

namespace App\Models\Warehouse;

use Illuminate\Database\Eloquent\Model;
use App\Models\Setting\Currency;
use App\Models\Setting\Account;
use App\Models\Setting\Car;


class WarehouseSales extends Model
{
    protected $table = 'warehouse_sales';
    protected $fillable = ['billno', 'factor', 'account_id','customer_account_id', 'total','cur_pay', 'remained', 
    'currency_id', 'car_id', 'tax_activation',  'note','idate', 'user_id', 'user_name', 'year', 'month', 'day','times','has_invoice','invoice_id','is_cleared']; 

    public function currencyRelation()
    {
       return $this->belongsTo(Currency::class, 'currency_id','id');
    }

    public function accountRelation()
    {
       return $this->belongsTo(Account::class, 'customer_account_id','id');
    }
     public function carRelation()
    {
        return $this->belongsTo(Car::class, 'car_id');
    }

}

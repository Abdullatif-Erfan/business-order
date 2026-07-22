<?php

namespace App\Models\Warehouse;

use Illuminate\Database\Eloquent\Model;
use App\Models\Setting\Account;
use App\Models\Setting\Currency;
use App\Models\Journal;

class SalesBillPayment extends Model
{
    protected $table = 'sales_bill_payments';
    
    protected $fillable = [
        'warehouse_sales_id',
        'billno',
        'customer_account_id',
        'account_id',
        'currency_id',
        'amount',
        'remaining_after_payment',
        'payment_date',
        'note',
        'journal_code',
        'user_id',
        'user_name',
        'times'
    ];

    // Relationships
    public function warehouseSales()
    {
        return $this->belongsTo(WarehouseSales::class, 'warehouse_sales_id');
    }

    public function customer()
    {
        return $this->belongsTo(Account::class, 'customer_account_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
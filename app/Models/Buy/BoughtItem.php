<?php

namespace App\Models\Buy;

use Illuminate\Database\Eloquent\Model;
use App\Models\Setting\Account;
use App\Models\Setting\Currency;


class BoughtItem extends Model
{
    protected $table = 'bought_items';
    protected $fillable = ['billno','factor','journal_code', 'total', 'cur_pay', 'remained', 'account_id','tax_activation','supplier_account_id','currency_id', 'note', 'idate', 'year', 'month', 'day','times', 'has_invoice', 'invoice_id',
    'user_id','user_name'];

    // Define relationships
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function customerRelation()
    {
        return $this->belongsTo(Account::class, 'supplier_account_id','id');
    }

    public function currencyRelation()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }
    // Add invoice relation
    public function invoice()
    {
        return $this->belongsTo(BuyInvoice::class, 'invoice_id');
    }
}

<?php

namespace App\Models\Buy;

use Illuminate\Database\Eloquent\Model;
use App\Models\Setting\Account;

class BuyInvoicePayment extends Model
{
    protected $table = 'buy_invoice_payments';

    protected $fillable = [
        'invoice_id',
        'payment_date',
        'amount',
        'payment_method', // 1: cash, 2: bank, 3: loan
        'account_id',
        'reference_number',
        'notes',
        'created_by',
        'times'
    ];

    public function invoice()
    {
        return $this->belongsTo(BuyInvoice::class, 'invoice_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}
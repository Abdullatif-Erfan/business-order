<?php

namespace App\Models\Buy;

use Illuminate\Database\Eloquent\Model;
use App\Models\Setting\Account;
use App\Models\Setting\Currency;

class BuyInvoice extends Model
{
    protected $table = 'buy_invoices';

    protected $fillable = [
        'invoice_number',
        'supplier_id',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'currency_id',
        'status', // 0: draft, 1: pending, 2: partial, 3: paid
        'invoice_date',
        'due_date',
        'notes',
        'created_by',
        'times'
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
    ];

    // Relationships
    public function supplier()
    {
        return $this->belongsTo(Account::class, 'supplier_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function items()
    {
        return $this->hasMany(BuyInvoiceItem::class, 'invoice_id');
    }

    public function payments()
    {
        return $this->hasMany(BuyInvoicePayment::class, 'invoice_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
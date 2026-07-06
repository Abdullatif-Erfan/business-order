<?php

namespace App\Models\SalesInvoice;

use Illuminate\Database\Eloquent\Model;
use App\Models\Setting\Account;
use App\Models\Setting\Currency;
use App\Models\User;


class SalesInvoice extends Model
{
    protected $table = 'sales_invoices';

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'total',
        'paid_amount',
        'remaining',
        'currency_id',
        'status', // 0: draft, 1: pending, 2: partial, 3: paid
        'invoice_date',
        'tax_activation',
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
    public function customer()
    {
        return $this->belongsTo(Account::class, 'customer_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function items()
    {
        return $this->hasMany(SalesInvoiceItem::class, 'invoice_id');
    }

    public function payments()
    {
        return $this->hasMany(SalesInvoicePayment::class, 'invoice_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
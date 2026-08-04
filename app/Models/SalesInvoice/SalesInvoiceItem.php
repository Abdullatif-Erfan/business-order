<?php

namespace App\Models\SalesInvoice;

use Illuminate\Database\Eloquent\Model;
use App\Models\Setting\Unit;
use App\Models\Buy\BuyPreList;

class SalesInvoiceItem extends Model
{
    protected $table = 'sales_invoice_items';

    protected $fillable = [
        'invoice_id',
        'billno',
        'total',
        'cur_pay',
        'remained',
        'times',
        'invoice_date',
        'user_name',
    ];

    public function invoice()
    {
        return $this->belongsTo(SalesInvoice::class, 'invoice_id');
    }
}
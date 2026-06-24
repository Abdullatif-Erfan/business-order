<?php

namespace App\Models\Buy;

use Illuminate\Database\Eloquent\Model;

class BuyInvoiceItem extends Model
{
    protected $table = 'buy_invoice_items';

    protected $fillable = [
        'invoice_id',
        'bought_item_detail_id',
        'bought_item_id',
        'pre_list_id',
        'amount',
        'unit_price',
        'tax_percentage',
        'tax_amount',
        'total',
        'times'
    ];

    public function invoice()
    {
        return $this->belongsTo(BuyInvoice::class, 'invoice_id');
    }

    public function preList()
    {
        return $this->belongsTo(BuyPreList::class, 'pre_list_id');
    }
}
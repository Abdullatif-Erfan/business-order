<?php

namespace App\Models\Buy;

use Illuminate\Database\Eloquent\Model;
use App\Models\Setting\Unit;

class BuyInvoiceItem extends Model
{
    protected $table = 'buy_invoice_items';

    protected $fillable = [
        'invoice_id',
        'bought_item_detail_id',
        'bought_item_id',
        'pre_list_id',
        'amount',
        'unit_id',
        'unit_price',
        'unit_price_vat',
        'tax_percentage',
        'tax_amount',
        'buy_up_vat',
        'total',
        'total_vat',
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

    public function unit()
    {
        return $this->belongsTo(Unit::class,'unit_id');
    }
}
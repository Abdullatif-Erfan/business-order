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
        'sales_details_id',
        'warehouse_sales_id',
        'pre_list_id',
        'amount',
        'unit_id',
        'unit_price',
        'tax_percentage',
        'tax_amount',
        'sell_up_vat',
        'total',
        'total_vat',
        'times'
    ];

    public function invoice()
    {
        return $this->belongsTo(SalesInvoice::class, 'invoice_id');
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
<?php

namespace App\Models\Production;

use Illuminate\Database\Eloquent\Model;
use App\Models\Buy\BuyPreList;
use App\Models\Setting\Unit;


class ModelDetails extends Model
{
    protected $fillable = ['branch_id', 'model_id', 'pre_list_id', 'amount', 'unit_id', 'price','total_price','currency_id'];

    function preListRelation()
    {
        return $this->belongsTo(BuyPreList::class,'pre_list_id','id');
    }
    function unitRelation()
    {
        return $this->belongsTo(Unit::class,'unit_id','id');
    }
    function modelRelation()
    {
        return $this->belongsTo(Models::class,'model_id','id');
    }
}

<?php

namespace App\Models\Production;
use App\Models\Setting\Unit;
use App\Models\Setting\Currency;


use Illuminate\Database\Eloquent\Model;

class Qalam extends Model
{
    protected $fillable = ['branch_id','model_id','amount','unit_id','unit_price','total_price','currency_id','dates','user'];

    function modelRelation()
    {
        return $this->belongsTo(Models::class,'model_id','id');
    }

    function unitRelation()
    {
        return $this->belongsTo(Unit::class,'unit_id','id');
    }
    function currencyRelation()
    {
        return $this->belongsTo(Currency::class,'currency_id','id');
    }
}

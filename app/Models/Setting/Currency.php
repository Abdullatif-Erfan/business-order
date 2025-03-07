<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Model;
use App\Models\Buy\BoughtItem;

class Currency extends Model
{
    //
    protected $fillable = ['name','symbols','color'];

    public function boughtItems()
    {
        return $this->hasMany(BoughtItem::class, 'currency_id');
    }
}

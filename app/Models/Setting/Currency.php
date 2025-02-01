<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    //
    protected $fillable = ['name','symbols','is_base'];
}

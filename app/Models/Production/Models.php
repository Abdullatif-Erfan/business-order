<?php

namespace App\Models\Production;

use Illuminate\Database\Eloquent\Model;
use App\Models\Setting\Currency;

class Models extends Model
{
    protected $fillable = ['branch_id','name'];

    function modelDetailsRelation()
    {
        return $this->hasMany(ModelDetails::class, 'model_id', 'id');
    }
}

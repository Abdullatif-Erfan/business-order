<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    //
    protected $fillable = ['name','responsible','phone','email','address'];
    
    public function warehouses()
    {
        return $this->hasMany(Warehouse::class);
    }
}

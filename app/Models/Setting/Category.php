<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Model;
use App\Models\Setting\Account;

class Category extends Model
{
    protected $fillable = ['name','supplier_id'];

     public function supplier()
    {
        return $this->belongsTo(Account::class, 'supplier_id','id');
    }
}

<?php

namespace App\Models\Buy;

use Illuminate\Database\Eloquent\Model;
use App\Models\Setting\Branch;

class BuyPreList extends Model
{
    protected $table = 'bought_item_pre_lists';
    protected $fillable=['code','name','branch_id','is_prev_barcode','times','image_path','barcode_path'];

    //   this (journal) is belongsTo branch
    public function branchRelation()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}

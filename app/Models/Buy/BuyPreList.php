<?php

namespace App\Models\Buy;

use Illuminate\Database\Eloquent\Model;
use App\Models\Setting\Branch;

class BuyPreList extends Model
{
    protected $table = 'bought_item_pre_lists';
    protected $fillable=['name','branch_id'];

    //   this (journal) is belongsTo branch
    public function branchRelation()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}

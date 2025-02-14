<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $fillable = ['name', 'branch_id', 'responsible', 'address'];

    /**
     * Parent: Branch
     * Chiled: Warehouse
     * one branch has Many Warehouses
     * a Warehouse is belongs to a branch
     */
    public function branch()
    {
        // this (warehouse) is belongs to Branch
        return $this->belongsTo(Branch::class,'branch_id','id');
    }
}

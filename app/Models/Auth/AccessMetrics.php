<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Model;

class AccessMetrics extends Model
{
    //
    protected $table = "access_metrics";
    protected $fillable = ['access','roleId','isDeleted','createdBy','createdDtm','updatedBy','updatedDtm'];
}

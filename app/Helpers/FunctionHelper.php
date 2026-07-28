<?php

namespace App\Helpers;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;

class FunctionHelper
{ 

    public static function showWhere($column, $table, $where = [])
    {
        $query = DB::table($table)->select($column);
        if (!empty($where)) {
            $query->where($where);
        }
        $result = $query->first();
        return $result ? $result->$column : false;
    }

 
    
}
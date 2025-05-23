<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Backup extends Model
{
    
    use HasFactory;
    protected $fillable = ['label','file_name','file_path','times','dates','created_by'];
}

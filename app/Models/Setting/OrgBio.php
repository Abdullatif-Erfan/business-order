<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Model;

class OrgBio extends Model
{
    protected $table = "org_bios";
    protected $fillable = ['name', 'address', 'phone', 'header', 'logos','expired_after_days','is_active','note_for_print'];
}

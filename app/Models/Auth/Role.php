<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Role extends Model
{
    // You can define reverse relationships if needed, e.g., the users that belong to a role.
    protected $primaryKey = 'roleId'; // Explicitly define primary key
    protected $fillable = ['role','status','isDeleted','createdBy'];

    public function users()
    {
        return $this->hasMany(User::class, 'roleId', 'roleId');
    }
}

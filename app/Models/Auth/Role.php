<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Role extends Model
{
    // You can define reverse relationships if needed, e.g., the users that belong to a role.
    public function users()
    {
        return $this->hasMany(User::class, 'roleId', 'roleId');
    }
}

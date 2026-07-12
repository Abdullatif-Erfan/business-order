<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Session;
use Illuminate\Notifications\Notifiable;
use App\Models\Auth\Role;
use App\Models\Setting\Account;
use App\Models\Setting\Branch;


class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */ 
    protected $fillable = [
        'account_id',
        'full_name',
        'user_name',
        'email',
        'password',
        'roleId',
        'isAdmin',
        'isDeleted',
        'isHidden',
        'photo',
        'createdBy',
    ];

    protected $casts = [
        'access_metrics' => 'array',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

     // Check if user has account assigned
    public function hasAccount()
    {
        return !is_null($this->account);
    }

    // Get account details if assigned
    public function getAccountNameAttribute()
    {
        return $this->account ? $this->account->name : null;
    }

      //  Relationship with Account (one-to-one)
    public function account()
    {
        return $this->hasOne(Account::class, 'user_account_id', 'id');
        // OR if using account_id in users table:
        // return $this->belongsTo(Account::class, 'account_id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Define the relationship with the Role model (assuming 'roleId' is the foreign key)
    public function roleRelationName()
    {
        return $this->belongsTo(Role::class, 'roleId', 'roleId');  // 'roleId' is the foreign key
    }


    public function hasAccess($module, $option)
    {
        $accessInfo = Session::get('accessInfo', []);
        $isAdmin = Session::get('isAdmin', false);

        // Admins have access to everything
        if ($isAdmin) {
            return true;
        }

        // Check if module and option exist in accessInfo
        if (isset($accessInfo[$module])) {
            if (!empty($accessInfo[$module][$option]) &&  (int) $accessInfo[$module][$option] === 1) {
                return true;
            }
            if (!empty($accessInfo[$module]['total_access']) && (int) $accessInfo[$module]['total_access'] === 1) {
                return true;
            }
        }

        return false;
    }

}

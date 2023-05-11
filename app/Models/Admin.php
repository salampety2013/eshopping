<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     
    protected $hidden = [
        'password',
        'remember_token',
    ];*/

     protected $table ="admins";
    protected $guarded=[];
    public $timestamps = true;
	
	 public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function hasAbility($permissions)    //products  //mahoud -> admin can't see brands
    {
        $manager_role = $this->role;

        if (!$manager_role) {
            return false;
        }

        foreach ($manager_role->permissions as $manager_permission) {
            if (is_array($permissions) && in_array($manager_permission, $permissions)) {
                return true;
            } else if (is_string($permissions) && strcmp($permissions, $manager_permission) == 0) {
                return true;
            }
        }
        return false;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 use App\Models\User;
  use App\Models\Country;
   use App\Models\City;
use Auth;
 class Role extends Model

{
    use HasFactory;
     //protected $table ="roles";
    protected $guarded = [];
   
   // protected $fillable = ['name','user_id', 'pincode',]; 
	
	 
   public $timestamps = false;
    protected $fillable = [
        'name', 'permissions'   // json field
    ];

    public function users()
    {
        $this->hasMany(User::class);
    }

    public function getPermissionsAttribute($permissions)
    {
        return json_decode($permissions, true);
    }
 	
}

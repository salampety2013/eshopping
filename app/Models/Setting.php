<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 use App\Models\User;
  use App\Models\Country;
   use App\Models\City;
use Auth;
 class Setting extends Model

{
    use HasFactory;
     //protected $table ="Setting";
    protected $guarded = [];
   
   // protected $fillable = ['name','user_id', 'pincode',]; 
	
	 
   /* public static function  getDeliveryAddress()
{
 $user_id=Auth::user()->id;
 
    	$deliveryAddress=Address::where('user_id',$user_id)->get();
	return $deliveryAddress;
     	
}*/
 
         
 
 
 	
}

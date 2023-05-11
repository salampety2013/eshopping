<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 use App\Models\User;
  use App\Models\Country;
   use App\Models\City;
use Auth;
 class Address extends Model

{
    use HasFactory;
     //protected $table ="sub_categories";
    protected $guarded = [];
   
   // protected $fillable = ['name','user_id', 'pincode',]; 
	
	 
    public static function  getDeliveryAddress()
{
 $user_id=Auth::user()->id;
 
    	$deliveryAddress=Address::where('user_id',$user_id)->get();
	return $deliveryAddress;
     	
}
 
         
 public function user()
{
 return $this->belongsTo(User::class);
    	

     	
}
    
 public function country()
{
	 return $this->belongsTo(Country::class,'country_id');
      	
} 
 
 public function city()
{
	 return $this->belongsTo(City::class,'city_id');
      	
} 
 
 	
}

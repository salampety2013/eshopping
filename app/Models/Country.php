<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 use App\Models\City;
 use App\Models\Address;
 class Country extends Model

{
    use HasFactory;
    //protected $table ="sub_categories";
   
  //  protected $fillable = ['name_ar','name_en']; 
	
	   protected $guarded = [];
     
         
 public function cities()
{
 return $this->hasMany(City::class,'country_id');
    	

     	
}
   
   /*public function address()
{
 return $this->belongsTo(Address::class);
    	

     	
} */

/* public function orders()
{
 return $this->hasMany(Country::class,'country','id');
      	
}
	 */
   
}

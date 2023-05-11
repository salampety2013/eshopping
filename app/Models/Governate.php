<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 use App\Models\Country;
 use App\Models\City;

 class Governate extends Model

{
    use HasFactory;
    //protected $table ="sub_categories";
   
  //  protected $fillable = ['name_ar','name_en']; 
	
	   protected $guarded = [];
     
         
  
 public function city()
{
 return $this->belongsTo(City::class,'city_id');
    	

     	
}   
   


	
}

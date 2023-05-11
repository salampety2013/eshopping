<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 use App\Models\Country;
 use App\Models\Governate;

 class City extends Model

{
    use HasFactory;
    //protected $table ="sub_categories";
   
  //  protected $fillable = ['name_ar','name_en']; 
	
	   protected $guarded = [];
     
         
 public function governates()
{
 return $this->hasMany(Governate::class,'country_id');
      	
}
 public function country()
{
 return $this->belongsTo(Country::class,'country_id');
 
}   
   
/* public function orders()
{
 return $this->hasMany(Ciy::class,'city','id');
      	
} */
 
	
}

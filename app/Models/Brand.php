<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 
  use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;

 class Brand extends Model

{
    use HasFactory;
     //protected $table ="brands";
    protected $guarded = [];
   
   // protected $fillable = ['name','user_id', 'pincode',]; 
	//protected $casts = [ 'name' => 'array'];
   
   
    
 public function products()
{
	 return $this->HasMany(Product::class,'brand_id','id');
      	
} 
 // get attributes not bieng afield in database
 public function getNamesAttribute()
{
    if(\App::getLocale() == 'ar')
        return $this->attributes['name_ar'];
    else
        return $this->attributes['name_en'];
}	

 public function getNameAttribute()
{
//$name_lang= json_decode( $this -> name ) ;  
if(isset($this->attributes['name'])){
		$name_lang= json_decode($this->attributes['name']) ;  
		  foreach (  $name_lang  as $key => $name_language)
			{
				if($key==app()->getLocale())
					{
					return  $name_language;  
					}
			}
    }                              
}
 // return json_decode($value);



 public function scopeFilter(Builder $builder,$filter_key_words )
 {
  //dd($filter_key_words);
// this function filter search 
 
/* if($filter_key_words['search'] ?? false){
$builder->where('name_en', 'LIKE', "%{$filter_key_words['search']}%");
}
 //use pervious or next to filter data  */
$builder->when($filter_key_words['search'] ?? false,function($builder,$value){
  $builder->where('name_en', 'LIKE', "%{$value}%");
});

 }
  	
}

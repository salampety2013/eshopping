<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 use App\Models\Product;

 class Coupon   extends Model

{
    use HasFactory;
    //protected $table ="sub_categories";
   
   // protected $fillable = ['name_ar','name_en', 'code',]; 
	 protected $guarded = [];
     
         
 public function products()
{
 return $this->belongsTo(Product::class);
    	

     	
}
   
  


	
}

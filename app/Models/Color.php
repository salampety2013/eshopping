<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 use App\Models\Product;

 class Color extends Model

{
    use HasFactory;
    //protected $table ="sub_categories";
   
    protected $fillable = ['name_ar','name_en', 'code',]; 
	
	 
     
         
 public function products()
{
 return $this->belongsToMany(Product::class,'color_product_size','color_id', 'product_id')->withPivot('quantity')->withTimestamps();
    	

     	
}
   
   public function sizes()
    { 
	return $this->belongsToMany(Size::class,'color_product_size', 'color_id','size_id')->withPivot('quantity')->withTimestamps();
       // return $this->belongsToMany(Size::class, 'products');
    }


	
}

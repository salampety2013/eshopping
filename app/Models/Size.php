<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
  use App\Models\Product;
  use App\Models\Color; 
  use App\Models\ColorProductSize;
  


 class Size extends Model

{
    use HasFactory;
    //protected $table ="sub_categories";
   
    protected $fillable = ['name_ar','name_en', 'type']; 
	
	 
    public function products()
{
 return $this->belongsToMany(Product::class,'color_product_size','size_id', 'product_id')->withPivot('quantity')->withTimestamps();
    	

     	
}
   
 public function colors()
    {
       // return $this->belongsToMany(Color::class, 'color_product_size');
	  
  return $this->belongsToMany(Color::class,'color_product_size', 'size_id','color_id')->withPivot('quantity')->withTimestamps();
    }
    public function sizeName()
    { 
	return $this->belongsTo(ColorProductSize::class,'size_id','id');
	
	 
    }

	/* public function products()
{
 return $this->belongsToMany(Product::class);
    	

     	
}
   
 public function colors()
    {
       // return $this->belongsToMany(Color::class, 'color_product_size');
	  
  return $this->belongsToMany(Color::class);
    }*/
    
}

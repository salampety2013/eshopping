<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\Size;
use App\Models\Color;

 class color_product_size  extends Model

{
    use HasFactory;
     protected $table ="color_product_size";
    protected $guarded = [];
    
	
	  
   
	  
	 
		/* public function colors()
    {
       // return $this->belongsToMany(Color::class, 'color_product_size');
	  
  return $this->belongsToMany(Color::class,'color_product_size', 'product_id','color_id')->withPivot('quantity')->withTimestamps();
    }
    public function sizes()
    { 
	return $this->belongsToMany(Size::class,'color_product_size','size_id', 'product_id')->withPivot('quantity')->withTimestamps();
	
	
       // return $this->belongsToMany(Size::class, 'products');
    }
		 
		 
		  public function products()
{
 return $this->belongsToMany(Product::class,'color_product_size','size_id', 'product_id')->withPivot('quantity')->withTimestamps();
    	

     	
}*/
}

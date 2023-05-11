<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\Order;

 
 class OrdersProduct extends Model

{
    use HasFactory;
    //protected $table ="orders_products";
    protected $guarded = [];
  //  protected $fillable = ['cat_id','sub_id','name_ar','name_en', 'slug_ar','slug_en', 'is_active','img']; 
	
	 
public function product()
{
 return $this->belongsTo(Product::class,'product_id');
 } 	
	  
		
/*public function product()
{
 return $this->belongsTo(Product::class,'product_id');
 } 	
*/		
	
		
		
		
		
		
		
		
		
		
		 
	
	 
		
}

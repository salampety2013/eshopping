<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrdersProduct;
use App\Models\Product;
use App\Models\City;
use App\Models\Country; 
 class Order extends Model

{
    use HasFactory;
    //protected $table ="orders";
    protected $guarded = [];
  //  protected $fillable = ['cat_id','sub_id','name_ar','name_en', 'slug_ar','slug_en', 'is_active','img']; 
	
	 
	public function product()
{
 return $this->belongsTo(Product::class,'product_id');
    	

     	
} 
	  
	
	
 	  public function orders_products()
{
 return $this->hasMany(OrdersProduct::class,'order_id');
      	
}	 
	 
public function cityall()
{
 return $this->belongsTo(City::class,'city');
    	
} 	

public function countryall()
{
 return $this->belongsTo(Country::class,'country');
    	
} 	
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 
  use App\Models\Product;
  
 
 class Rating extends Model

{
    use HasFactory;
     //protected $table ="ratings";
    protected $guarded = [];
   
   // protected $fillable = ['name','user_id', 'pincode',]; 
	
   
    
 public function product()
{
	 return $this->belongsTo(Product::class,'product_id');
      	
} 
 
 public function user()
{
	 return $this->belongsTo(User::class,'user_id');
      	
} 
 	
}

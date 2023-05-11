<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
  use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;

 class Option extends Model

{
    use HasFactory;
     //protected $table ="options";
    protected $guarded = [];
   
    
    
 public function advertisement()
{
	 return $this->belongsTo(Advertisement::class,'option_id','id');
      	
} 
 
public function category()
{
	 return $this->belongsTo(Category::class,'cat_id','id');
      	
} 
 
  


  
}

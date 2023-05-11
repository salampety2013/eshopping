<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 
  use App\Models\Product;
  
 
 class Currency extends Model

{
    use HasFactory;
     //protected $table ="Currencies";
    protected $guarded = [];
	
   
   // protected $fillable = ['name','user_id', 'pincode',]; 
	 protected $casts = [
         'status' => 'boolean',
		 
    ];
	
	  public function getActive(){
       return  $this -> status  == 0 ?  'غير مفعل'   : 'مفعل' ;
    }
   
    
 public function products()
{
	 return $this->HasMany(Product::class,'brand_id','id');
      	
} 
 
 
 	
}

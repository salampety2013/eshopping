<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
  class Slider extends Model

{
    use HasFactory;
     //protected $table ="sliders";
    protected $guarded = [];
   
   // protected $fillable = ['name','user_id', 'pincode',]; 
	
	  protected $casts = [
         'is_active' => 'boolean',
		 
    ];
	
	  public function getActive(){
       return  $this -> is_active  == 0 ?  'غير مفعل'   : 'مفعل' ;
    }
    
         
 
 	
}

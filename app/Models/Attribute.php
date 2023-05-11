<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 

 class Attribute extends Model

{
    use HasFactory;
    //protected $table ="sub_categories";
   
    protected $fillable = ['name_ar','name_en', 'is_active']; 
	
	 protected $casts = [
         'is_active' => 'boolean',
		 
    ];
	
	  public function getActive(){
       return  $this -> is_active  == 0 ?  'غير مفعل'   : 'مفعل' ;
    }
     
   


	
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 
   
 
 class Page extends Model

{
    use HasFactory;
     //protected $table ="Pages";
    protected $guarded = [];
   
   // protected $fillable = []; 
	
   
    	 protected $casts = [
         'status' => 'boolean',
		 
    ];
	
	  public function getActive(){
       return  $this -> status  == 0 ?  'غير مفعل'   : 'مفعل' ;
    }

  
 
 	
}

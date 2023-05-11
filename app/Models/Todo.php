<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 

 class Todo extends Model

{
    use HasFactory;
    //protected $table ="sub_categories";
   
    protected $fillable = ['title','description']; 
	
	 
     
   


	
}

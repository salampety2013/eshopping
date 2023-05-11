<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SubCategory;
use App\Models\Product;
class Category extends Model
{
    use HasFactory;
    protected $fillable = ['name_ar','name_en', 'slug_ar','slug_en', 'is_active','img']; 
	
	 protected $casts = [
         'is_active' => 'boolean',
		 
    ];
	
	  public function getActive(){
       return  $this -> is_active  == 0 ?  'غير مفعل'   : 'مفعل' ;
    }
	
	
	//public function getImgAttribute($img){
	public function getImgPath($img){
        $actual_link = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
        return ($img == null ? '' : $actual_link . 'images/category/'.$img);
    }
	
	
	
	
	
	  public function subcategories(){
    	//return $this->hasMany(SubCategory::class,'id','cat_id'); 
		return $this->hasMany(SubCategory::class, 'cat_id');
		
    }
	
	public function products(){
    	 
		return $this->hasMany(Product::class ,'cat_id','id');
		
    }
	public function options(){
    	 
		return $this->hasMany(Option::class ,'cat_id','id');
		
    }
	
	
}

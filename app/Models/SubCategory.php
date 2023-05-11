<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\Product;


 class SubCategory extends Model

{
    use HasFactory;
    //protected $table ="sub_categories";
   
    protected $fillable = ['cat_id','name_ar','name_en', 'slug_ar','slug_en', 'is_active','img']; 
	
	 protected $casts = [
         'is_active' => 'boolean',
		 
    ];
	
	  public function getActive(){
       return  $this -> is_active  == 0 ?  'غير مفعل'   : 'مفعل' ;
    }
    public function category(){
    	return $this->belongsTo(Category::class,'cat_id','id');
    }
   
public function products(){
    	 
		return $this->hasMany(Product::class,'sub_cat_id','id' );
		
    }
/////////////get counts of products in this subcategory and put it in new field 
//write it as ---   get {column name} Attribute 
// that equalto name = products_count  /////////////////
//write that in query =>   SubCategory::find($id)->products_count;
//anther way to get count without  that function 
//2- return SubCategory::find($id)->products()->count(); or  SubCategory::find($id)->withCount('products')->get();
	public function getProductsCountAttribute(){
    	 
		return $this->products()->count();
		
    }
 
}


  


	   
	   

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\Size;
use App\Models\Color;
use App\Models\Brand;
use App\Models\OrdersProduct;


 class Product extends Model

{
    use HasFactory;
    //protected $table ="sub_categories";
    protected $guarded = [];
  //  protected $fillable = ['cat_id','sub_id','name_ar','name_en', 'slug_ar','slug_en', 'is_active','img'];

	 protected $casts = [
         'is_active' => 'boolean',

    ];

	  public function getActive(){
       return  $this -> is_active  == 0 ?  'غير مفعل'   : 'مفعل' ;
    }
    public function category(){
    	return $this->belongsTo(Category::class,'cat_id','id');
    }


   public function subcategory(){
    	return $this->belongsTo(SubCategory::class,'sub_cat_id','id');
    }
   public function brand(){
    	return $this->belongsTo(Brand::class,'brand_id','id');
    }
	 public function images()
    {
        return $this->hasMany(ProPic::class, 'pro_id');
    }
	public function pics(){
		return $this->hasMany(ProPic::class,'pro_id');

		}




		public function ratings(){
		return $this->hasMany(Rating::class,'product_id');

		}


		public function orderDetails(){
		return $this->hasMany( OrdersProduct::class,'product_id');

		}








		/* public function colors()
    {


  return $this->belongsToMany(Color::class );
    }
    public function sizes()
    {
	return $this->belongsToMany(Size::class );

    }*/


	 public function colors()
    {
       // return $this->belongsToMany(Color::class, 'color_product_size');

  return $this->belongsToMany(Color::class,'color_product_size', 'product_id','color_id')->withPivot('quantity')->withTimestamps();
    }
    public function sizes()
    {
	return $this->belongsToMany(Size::class,'color_product_size', 'product_id','size_id')->withPivot('quantity')->withTimestamps();

    }
		/*******************************************************
		 public function cat_info(){
        return $this->hasOne('App\Models\Category','id','cat_id');
    }
    public function sub_cat_info(){
        return $this->hasOne('App\Models\Category','id','child_cat_id');
    }
    public static function getAllProduct(){
        return Product::with(['cat_info','sub_cat_info'])->orderBy('id','desc')->paginate(10);
    }
    public function rel_prods(){
        return $this->hasMany('App\Models\Product','cat_id','cat_id')->where('status','active')->orderBy('id','DESC')->limit(8);
    }
    public function getReview(){
        return $this->hasMany('App\Models\ProductReview','product_id','id')->with('user_info')->where('status','active')->orderBy('id','DESC');
    }
    public static function getProductBySlug($slug){
        return Product::with(['cat_info','rel_prods','getReview'])->where('slug',$slug)->first();
    }
    public static function countActiveProduct(){
        $data=Product::where('status','active')->count();
        if($data){
            return $data;
        }
        return 0;
    }

    public function carts(){
        return $this->hasMany(Cart::class)->whereNotNull('order_id');
    }

    public function wishlists(){
        return $this->hasMany(Wishlist::class)->whereNotNull('cart_id');
    }
*//////////////////////////////////////////

}

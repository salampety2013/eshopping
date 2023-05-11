<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\Size;

use App\Models\Color;

use Auth;
use Session;
 class Cart extends Model

{
    use HasFactory;
    //protected $table ="sub_categories";
   
  //  protected $fillable = ['name_ar','name_en', 'code',]; 
	
	  protected $guarded = [];
     
         
 public function product()
{
 return $this->belongsTo(Product::class,'product_id');
    	

     	
} 
  public static function UserCartItems()
  {
	  if(Auth::check()){
		  //////update user cart with user_id
				//return "===".Session::get('session_id');
				/*if(!empty(Session::get('session_id'))){
					
					$user_id=Auth::user()->id;
					$session_id=Session::get('session_id');
					Cart::where('session_id',$session_id)->update(['user_id'=>$user_id]);
					}*/
				
             
		  
		  
	$userCartItems=Cart::with('product')->where('user_id', Auth::user()->id)->orderBy('id','desc')->get();

       	 // $userCartItems=Cart::with(['product' =>  function ($q) {

          //  return $q->select('id','name_ar'); }])->where('user_id', Auth::user()->id)->get();

		  }else{
		 $userCartItems=Cart::with('product') ->where('session_id',Session::get('session_id') )->orderBy('id','desc')->get();	

       
		  }
	  
	 return $userCartItems;
	 
	  
	  } 
  
	 public static function getSize($id)
  {
	 
   $product_size=Size::select('name_ar','name_en')->where('id',$id)->first();	

     // return $product_size->name_ar;
		 
	  
	 return $product_size;
	 
	  
	  } 
	  
	  
	  public static function getColor($id)
  {
	  // $product_id=2; 
  $product_color=Color::select('name_ar','name_en')->where('id',$id)->first();	

     // return $product_size->name_ar;
		 
	  
	 return $product_color;
	 
	  
	  } 
}

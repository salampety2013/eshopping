<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\Size;

use App\Models\Color;
use App\Models\Setting;

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
		  //////////////////////get tax put in sesstion///////////////////////////////////
		   $tottal_price=0;
			foreach($userCartItems as $CartItem){
				// $product_final_price=($CartItem->product->discount_price ?? $CartItem->product->discount_price);
				 $product_final_price=($CartItem->product->discount_price ?? $CartItem->product->price);
				$tottal_price+=$product_final_price * $CartItem->quantity;
			}
			 //return $tottal_price;
			$setting=Setting::first();
			 $tax=(isset($setting->tax) ?  $setting->tax : 0);
			 
			$tax_amount=($tottal_price * $tax)/100;
	  		Session::put('tax',$tax);
			 Session::put('tax_amount',$tax_amount);
	 return $userCartItems;
	 
	  
	  } 
	  
	 //----------------------------------------------------
	 
	  public static function UserCartItemsApi()
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
		// $userCartItems=Cart::with('product') ->where('cart_key', $cart_key )->orderBy('id','desc')->get();	

       
		  }
		  //////////////////////get tax put in sesstion///////////////////////////////////
		   $total_price=0;
			foreach($userCartItems as $CartItem){
				// $product_final_price=($CartItem->product->discount_price ?? $CartItem->product->discount_price);
				 $product_final_price=($CartItem->product->discount_price ?? $CartItem->product->price);
				$total_price+=$product_final_price * $CartItem->quantity;
			}
			 //return $total_price;
			$setting=Setting::first();
			 $tax=(isset($setting->tax) ?  $setting->tax : 0);
			 
			$tax_amount=($total_price * $tax)/100;
	  		 
			 $items=array();
			 $items=[
			 'userCartItems'=>$userCartItems,
			 'total_price'=>$total_price,
			 
			 'tax'=> $tax,
			 'tax_amount'=>$tax_amount,
			 ];
			 
	 return $items;
	 
	  
	  } 
	 
	 	
	 //--------------End -------------------------------- 
	 
	 
	 
	 
	 
	  public static function CartCalculation()
  {
	  if(Auth::check()){
 	//$userCartItems=Cart::with('product')->where('user_id', Auth::user()->id)->orderBy('id','desc')->get();
         	  $userCartItems=Cart::with(['product' =>  function ($q) {
            return $q->select('id','price','discount_price'); }])->where('user_id', Auth::user()->id)->get();
			
				 
		  }else{
		  $userCartItems=Cart::with(['product' =>  function ($q) {

           return $q->select('id','price','discount_price'); }])->where('session_id',Session::get('session_id') )->orderBy('id','desc')->get();	

       
		  }
		  
		  $tottal_price=0;
			foreach($userCartItems as $CartItem){
				 $product_final_price=($CartItem->product->discount_price ?? $CartItem->product->discount_price);
				$tottal_price+=$product_final_price * $CartItem->quantity;
			}
			 //return $tottal_price;
			$setting=Setting::first();
			 $tax=(isset($setting->tax) ?  $setting->tax : 0);
			$tax_amount=($tottal_price * $tax)/100;
			//$cart_operation=[];
			//$cart_operation
	 return $vate;
	 
	  
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

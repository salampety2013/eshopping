<?php
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Category;
use App\Models\SubCategory;
 
  
use App\Models\Currency;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrdersProduct;
use App\Models\Setting;
 
 
///////////////////////print sql////////////////
 use Illuminate\Database\Query\Builder;
use Illuminate\Support\Str;
//////////////////////////////
 
use Illuminate\Support\Facades\Session;
 use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
 //use Auth;
 define('PAGINATION_COUNT', 5);

 	//----------Begin  print sql with binging ? -------------------------------	

 function ddb($builder)
{
      $addSlashes = str_replace('?', "'?'", $builder->toSql());
	
  
        $quer= vsprintf(str_replace('?', '%s', $addSlashes), $builder->getBindings());
  dd($quer );
 }

	//------End-----------------------------------


  //---------------------------------------------
 //          Get  Folder CSS      Ar or En
 //-------------------------------------------------

function getFolder()
{

    return app()->getLocale() == 'ar' ? 'css-rtl' : 'css';
}
function uploadImage($folder,$image){
    
   //  $filename = $image->hashName();
   $filename = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
    $image->move($folder, $filename);
   // dd($filename);
    return  $filename;
 }

/*function uploadImage($folder,$image){
    $image->store('/', $folder);
    $filename = $image->hashName();
    return  $filename;
 }*/
 
 
 
 
 
  //---------------------------------------------
 //          Get  Cat And SubCat
 //-------------------------------------------------
   function getCatAndSubCat()
    {
 					
         $data = [];
 		// return   $data['categories'] = Category::select('id', 'name_ar')->with('subcategories')->get(); 
             
     
  return  $data['categories_menu'] = Category::select('id','name_ar','slug_ar')->with(['subcategories' =>function($q) {
		 $q->select('id','name_ar','slug_ar','cat_id')-> where('is_active',"1"); }])-> where('is_active',"1")->get();
       
        /* $data['categories'] = Category::parent()->select('id', 'slug')->with(['childrens' => function ($q) {
            $q->select('id', 'parent_id', 'slug');
            $q->with(['childrens' => function ($qq) {
                $qq->select('id', 'parent_id', 'slug');
            }]);
        }])->get();*/
		
 
     } 
 
 
 //---------------------------------------------
 //          save order in DB
 //-------------------------------------------------
 function storeOrder($pay_type){
	   
	 $user_id=Auth::user()->id;
		
		 
		 $settings=Setting::first();
		 
		   $cart_count=Cart::where('user_id',$user_id)->sum('quantity');
		 if($cart_count <= $settings->product_shipping_count){
			 $shipping_charges= $settings->product_shipping_price;
			 }else{
				 $shipping_charges=0;
				 }
				 
	 ////////////////////////////////////
		 
			// return $request;
			// try {

            //    DB::beginTransaction();
				
				$deliveryAddress=Address::where('id', $request->address_id)->first();
 			 // save order deatails
 				$order_id=Order::insertGetId([
			 
				'user_id'=> $user_id,
				'full_name'=>$deliveryAddress->name,
				'country'=>$deliveryAddress->country_id,
			 	'city' => $deliveryAddress->city_id,
			 	'pincode' => $deliveryAddress->pincode,
			 	'address' => $deliveryAddress->address,
			 	'shipping_charges' => $shipping_charges,
			 	'pay_type' => $request->pay_type,
			  	'pay_gateway' => $request->pay_type,
			  	'order_status' => "New",
			  	'coupon_code' => Session::get('coupon_code'),
			  	'coupon_amount' => Session::get('coupon_amount'),
					'tax' => Session::get('tax'), 
					'tax_amount' => Session::get('tax_amount'), 
					'sub_total' => Session::get('subTotal'), 
					
					 'created_at' => Carbon::now(), 
				'grand_total' => Session::get('grand_total')
 				]);
			
			///////////get cart item and save it in orders_products table///////////////
			 $cart_items=Cart::where('user_id',$user_id)->get();
			 // save product in orders_products
			foreach($cart_items as $item){
				
			 if($item->color_id!=null){
				$product_color=Color::select('name_ar','name_en')->where('id',$item->color_id)->first();
				$color_name=$product_color->name_en;
			}else{
					$color_name="";
				}
			if( $item->size_id!=null){
			   $product_size=Size::select('name_ar','name_en')->where('id',$item->size_id)->first();
			  $size_name=$product_size->name_en;
			}else{
					$size_name="";
				}
				OrdersProduct::insert([
					'user_id'=> $item->user_id,
					'order_id'=> $order_id,
					'product_id'=>$item->product_id,
					'color'=>$color_name, 
					'size'=>$size_name,
					 'quantity'=>$item->quantity,
					 'price'=>$item->price, 
					  'name'=>$item->product->name_en ,
					  'img'=>$item->product->img 
					  
					]);
					
			}
			
			 //////////////////delete all cart item: empty cart//////////////////////
			// Cart::where('user_id',$user_id)->delete();
			 
			 //////////////////////////////////////////
			 
			 // Session::put('order_id',$order_id);
			// DB::commit();
			// $pay_type=$request->pay_type; 
			 	 
			// } catch (\Exception $ex) {
          //  DB::rollback();
            //return $ex;
         
			// }
			return $order_id;
	 
 }
 
  
   //---------------------------------------------
 //          Get Total Cart Items
 //-------------------------------------------------

function totalCartItems(){
	
	if(Auth::check()){
		$totalCart=Cart::where('user_id',Auth::user()->id)->sum('quantity');
		
		}else{
			return	$totalCart=Cart::where('session_id',Session::get('session_id'))->sum('quantity');
		
			}
			return	$totalCart;
	}
	
	
	
	function totalCartAmount(){
	
	if(Auth::check()){
		$totalCart=Cart::where('user_id',Auth::user()->id)->get();
		
		
		}else{
			 	$totalCart=Cart::where('session_id',Session::get('session_id'))->get();
		
			}
			
			foreach($totalCart as $item){
					$discount_price=$item->product->discount_price;
					$price=$item->product->price;
   					$final_price=isset($discount_price) ? $discount_price : $price;  
					$totalAmount=$totalAmount+($final_price* $item->quantity );
				}
			
			return	$totalAmount;
	}
	
  //---------------------------------------------
 //          Get  Coupon  Aamount
 //-------------------------------------------------

	function getCouponAamount($coupon_code,$total)
	{
		
		//---------------------------------------------------------
		 if(Session::has('currency_code')){ 
          $currency_code=Session::get('currency_code');
        }else { 
          $currency_code='KWD';
		}
          
          if(Session::has('currency_symbol')){ 
          $curr_symbol=Session::get('currency_symbol');
         
        }else { 
          $curr_symbol='';
		}
          
          
          
          if(Session::has('exchange_rate')){ 
          $exchange_rate=Session::get('exchange_rate');
         }else {  
          $exchange_rate=1;
		 }
	  $coupon_code=Coupon::where(['coupon_code'=>$coupon_code,'status'=>"1"])->first();
	 
	 $amount_type= $coupon_code->amount_type;
	 $amount= $coupon_code->amount;
				
   //  $coupon_amount=($amount_type=="Fixed") ? $amount : (($total*$amount)/100);
       $coupon_amount=($amount_type=="Fixed") ? round($amount/$exchange_rate,2) : (($total_amount* round($amount/$exchange_rate,2) /100));

 //dd($coupon_amount);
 
 		return	$coupon_amount;
		
		}
   //---------------------------------------------
 //          Get  currency  Aamount
 //-------------------------------------------------

	
 function getAllCurrency()
	{
	  $currency=Currency::where(['status'=>"1"])->get();
	 
	// $exchange_rate= $currency->exchange_rate;
	//	 $tax_vate= $currency->tax_vate;

	//	 $code= $currency->code;
 
				
  
 //dd($coupon_amount);
 
 		return	$currency;
		
		}
		
		 
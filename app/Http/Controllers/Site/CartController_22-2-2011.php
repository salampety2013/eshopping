<?php

namespace App\Http\Controllers\Site;

use App\Http\Requests;
use App\Basket\Basket;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ColorProductSize;
use App\Models\Size;
use App\Models\Color;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Coupon;
use App\Models\Country;
use App\Models\City;
use App\Models\Governate;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrdersProduct;
use App\Models\Setting;
 
use Illuminate\Support\Facades\Session;
//use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
 use Illuminate\Support\Carbon;
 use App\Exceptions\QuantityExceededException;
 
 use Exception;
// use Session;
use Auth;
use View;

class CartController extends Controller
{
    
	

    
     

    //##########################
     // Show all items in the Basket.
    //##########################


    public function getIndex()
    {
             $basket=Cart::UserCartItems(); 
        return view('front.cart.index',compact('basket'));
    }
 

     //##########################
     // Add items to the Basket.
     //##########################
	 
	 
      
    public function AddToCart(Request $request ,$id)
    {
		 
		 ////////check quantity is available or not ////////////////
				 	

		/////// if there are sizes only or sizes with its colors  ///////////////////
         if($request->all_sizes!=""){
           $stocks=ColorProductSize::where('id',$request->all_sizes)->first();
			 
			 }  
			  ////////end check///////////
			 else if($request->color_id!= "" && $request->all_sizes==""){
				 // dd ($request->stock_id);
				  
     $stocks=ColorProductSize::where(['id'=>$request->stock_id,'product_id'=>$id])->first(); 
  // $stocks=ColorProductSize::where(['id'=>$request->stock_id,'product_id'=>$id])->first()->toArray() ; 
    // when using toArray() use $stocks['quantity'] equal to $stocks->quantity 
				  
					  
					  
					  } else if($request->color_id == "" && $request->all_sizes==""){
				 
				 //     $stocks=ColorProductSize::where('id',$request->stock_id)->first();
      $stocks=ColorProductSize::where(['product_id'=>$id])->first(); 
   
				  } 
 	
       /*if (!stocks){ ///  redirect to previous page with message
		 return  " invalid id " ;
              } */
			 
			/* if($request->qty ==""  || ($stocks->quantity < $request->qty)  ) {
				  return redirect()->back()->with(['error' => 'quantity not available']);
				 
			//return  "quantity not available".$stocks->quantity ;  
		 } */
		 
		 
		  $size_id=$stocks->size_id;
		 
		$color_id= $request->color_id;
		 /// Generate session_id if is not exist///////////////////////// 
		 
		   $session_id=Session::get('session_id');   //get if session  exist get it else 
		 
		 if(empty($session_id)){
			 	 $session_id=Session::getId(); /// generate some thing like token 
				Session::put('session_id',$session_id) ;
			 }
			 
			   $user_id= (!empty(Auth::user()->id))  ? Auth::user()->id : '';
			 // check if user is login or not 
			 if(Auth::check()){
				 
				 //user logged in
				    
				
				 $update_qty=Cart::where(['user_id'=>Auth::user()->id,'product_id'=>$id,'size_id'=>$size_id,'color_id'=>$color_id])->first();
				 }else{
					 
					 //user not logged in then use sesion_id
					 $update_qty=Cart::where(['session_id'=>$session_id,'product_id'=>$id,'size_id'=>$size_id,'color_id'=>$color_id])->first();
					 }
			
			
			
			// check if the product added before then if is exist update quanitity with one or send message that is exists
		 	// $update_qty=Cart::where(['session_id'=>$session_id,'product_id'=>$id,'size_id'=>$size_id,'color_id'=>$color_id])->count();
			
			 
			if($update_qty ){
				$check_qty=$request->qty+$update_qty->quantity;
				 //return redirect()->back()->with(['error' => 'product already exist']);
				 if(($stocks->quantity < $check_qty) ||$request->qty ==""  || ($stocks->quantity < $request->qty)  ) {
				 	 return redirect()->back()->with(['error' => 'quantity not available']);
				 
					//return  "quantity not available".$stocks->quantity ;  
		 		} 
		 
		 
				$update_qty->quantity=$request->qty+$update_qty->quantity;
				$update_qty->save();
				}else{
			
			// save product in cart if not exists
			
			Cart::insert([
			'session_id'=> $session_id,
			'user_id'=> $user_id,
			'product_id'=>$id,
			'color_id'=>$color_id,
			'size_id'=>$size_id,
			 'quantity'=>$request->qty,
			  'price'=>$request->price_now,
			 
			 'stock_id'=>$stocks->id
			]);
		 }
		  return redirect()->back()->with(['success' => 'Product added Successfully']);
			  
	}
	
	
	
	
 //##########################
     // update quantity in the Basket.
     //##########################
	 
     
    public function updateCart(Request $request)
    {
		 //$request->stock_id;
		 $data=$request->all();
		  $cart=Cart::where('id',$data['cart_id'])->first();
		   
	////////////check if the quantity is available in stock or not //////////
		 $available_qty=ColorProductSize::where('id',$cart->stock_id)->first();
		// echo "<pre>"; print_r($available_qty); die; 
		 //dd($available_qty);
		  if(($available_qty->quantity) >= $data['quantity'])
		 {
			
			 $cart->update(['quantity'=>$data['quantity']]);
			 $status= true ;
		  // return response()->json($cart);
		   }else{
			   		$status= false ;
			   }
		$basket=Cart::UserCartItems();
		$html = view('front.cart.cart_items',compact('basket'))->render();
		  //$html =View::make('front.cart.cart_items',compact('basket')) ;
       // return response()->json(compact('html'));
	 $totalCart=  totalCartItems();
		return response()->json([
		'status'=>$status,
		'html'=>$html, 
		'totalCartCount'=>$totalCart
		
		]);
		 // $view = View::make('front.cart.cart_items')->with('basket', $basket);
		//(string)View::make('front.cart.cart_items')->with(compact('basket') )
         // return respone()->json(['view'=>$view]);

			
		
		
	}
	
	##############################################################################
	//##############################################################################
     // 			deleteCart from the Basket.
     //##############################################################################
	 
	 
 	 public function deleteCart( Request $request)
    {

       // try { 
            // $cart=Cart::where('id',$id);
			$cart=Cart::where('id', $request->cart_id )->first();
             
           // if (!$cart)
             //   return redirect()->route('site.cart.delete')->with(['error' => 'هذا القسم غير موجود ']);
		 $cart->delete();
		$basket=Cart::UserCartItems();
		$html = view('front.cart.cart_items',compact('basket'))->render();
  //$html =View::make('front.cart.cart_items',compact('basket')) ;
        // return response()->json(compact('html'));
		  $totalCart=  totalCartItems();
		 //echo "<pre>"; echo($totalCart); die;
		return response()->json([
		 
		'html'=>$html, 
		'totalCartCount'=>$totalCart
		
		]);

           

           // return redirect()->route('admin.sizes')->with(['success' => 'تم  الحذف بنجاح']);
       // } catch (\Exception $ex) {
            //return $ex;
          //  return redirect()->route('admin.sizes')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        //}
    }
	
	
	
	
	
	
	//##############################################################################
     // 					Apply Coupon in the Basket.
     //##############################################################################
	 
	 public function ApplyCoupon( Request $request)
    {
       // try { 
	   $basket=Cart::UserCartItems(); 
		$html = view('front.cart.cart_items',compact('basket'))->render();
 		$totalCart=  totalCartItems();
	 
	 //get total amount
	 $total_amount=0;
				 foreach($basket as $key=> $item){
 					 
					$discount_price=$item->product->discount_price;
					$price=$item->product->price;
					 // dd($pro_price);
  $final_price=isset($discount_price) ? $discount_price : $price;  
					   //dd($final_price); 
				$total_amount=$total_amount+($final_price* $item->quantity );
				 }
				  //dd($total_amount);
		
	  /////unset coupon code and  coupon_amount in session #########
	 // Session::forget(['coupon_code', 'coupon_amount']);
	 if(Session::has('coupon_amount')){
		 
 			 Session::forget('coupon_code');
			  Session::forget('coupon_amount');
			 
			// dd(Session::get('coupon_amount'));
			 }
			#########################################################
 		 
              //$coupon_code=Coupon::where('coupon_code',$request->coupon_code)->count();
			//  $coupon_code=Coupon::where(['coupon_code'=>$request->coupon_code,'status'=>"1"])->count();
			 $coupon_code=Coupon::where(['coupon_code'=>$request->coupon_code,'status'=>"1"])->first();
 			 if(!$coupon_code){
					 
					 $message="Invalid Coupon Code";
					 
					 return response()->json([
									 'totalCartCount'=>$totalCart,
									'html'=>$html, 
									'status'=>false,
									'coupon_amount'=>"0$",
									'grand_total'=>$total_amount,
									'message'=>$message
									
									]);
				 }else{
				 	 
				
				 //################## check   coupon expiry_date
				 $expiry_date=$coupon_code->expiry_date;
				 $current_date=date('Y-m-d');
				  // dd($expiry_date."==".$current_date);
				 if( $expiry_date < $current_date){
					  //$status= false ;
					 $message="this Coupon is expire";
					 
					 return response()->json([
									 'totalCartCount'=>$totalCart,
									'html'=>$html, 
									'coupon_amount'=>"0$",
									'grand_total'=>$total_amount,
									'status'=>false,
									'message'=>$message
									
									]);
					 }
				 //################## check if coupon is from selected category 
				 if($coupon_code->categories_ids!="")
				 {
					  $cat_ids=explode(',',$coupon_code->categories_ids);
					//$cat_IDs = array_map('intval',$cat_ids );
				
				   // dd($cat_IDs);
					 //##### check if any item belong to categories from cart table
 					 foreach($basket as  $item){
 					// $sub_cat_id= Product::select('sub_cat_id')->where('id',$item->product_id)->first();
					//  $sub_cat_id= $sub_cat_id->sub_cat_id;
					   // dd($cat_ids);
					 $sub_cat_id= $item->product->sub_cat_id;
					    $sub_cat_id=(string)$sub_cat_id;
					      // dd($sub_cat_id);
					  if(!in_array($sub_cat_id,$cat_ids)){
					 
						   $message="this Coupon is  not apply for one of selected product";
						   return response()->json([
									 'totalCartCount'=>$totalCart,
									'html'=>$html,
									'coupon_amount'=>"0$",
									'grand_total'=>$total_amount, 
									'status'=>false,
									'message'=>$message
									
									]);
						  }
					 }
					 }
					 
					 
					 //################## check if coupon is apply to that user email
				  if($coupon_code->users_ids!="")
				 {
					 $users_emails=explode(',',$coupon_code->users_ids);
				 	 $login_user_email=Auth::user()->email;
				 	// dd($login_user_email);
				 	// dd($users_emails);
				 //##### check if login user belong to users that have coupon  
 					if(!in_array($login_user_email,$users_emails)){
						   $message="this Coupon is  not apply for you";
						   return response()->json([
									 'totalCartCount'=>$totalCart,
									'html'=>$html, 
									'coupon_amount'=>"0$",
									'grand_total'=>$total_amount,
									'status'=>false,
									'message'=>$message
									
									]);
						  } 
						  
				}
					  
				//################## check if coupon is one time or multiple user email
				  if($coupon_code->coupon_type=="Single Times")
				 {
					 $coupon_applay_count=Order::where(['user_id'=>Auth::user()->id,'coupon_code'=>$coupon_code->coupon_code])->count();
				 	 
				 	  //dd($coupon_applay_count);
				 	 
				 //##### check if login user belong to users that have coupon  
 					if($coupon_applay_count>=1){
						   $message="this Coupon is count is out";
						   return response()->json([
									 'totalCartCount'=>$totalCart,
									'html'=>$html, 
									'coupon_amount'=>"0$",
									'grand_total'=>$total_amount,
									'status'=>false,
									'message'=>$message
									
									]);
						  } 
						  
				}
					  	 
	 
            // dd($coupon_code);
            
			
			
			
			if(!isset($message)){
				
				  
 //////////////  check if coupon amount is fixed or percentage############# 
 				$amount_type= $coupon_code->amount_type;
				$amount= $coupon_code->amount;
				
   $coupon_amount=($amount_type=="Fixed") ? $amount : (($total_amount*$amount)/100);
  
 //dd($coupon_amount);
 
 $total_amount=$total_amount-$coupon_amount;
 			/////add coupon code and  coupon_amount in session #########
 				Session::put('coupon_code',$coupon_code->coupon_code);
				Session::put('coupon_amount',$coupon_amount);
			#########################################################
			
				 $message="Coupon Successfully Redeemed";
				  return response()->json([
									 'totalCartCount'=>$totalCart,
									'html'=>$html, 
									'status'=>true,
									'coupon_amount'=>$coupon_amount,
									'grand_total'=>$total_amount,
									
									'message'=>$message
									  
									]);
				}
			}
			
		
		  
		 //echo "<pre>"; echo($totalCart); die;
		

           

           // return redirect()->route('admin.sizes')->with(['success' => 'تم  الحذف بنجاح']);
       // } catch (\Exception $ex) {
            //return $ex;
          //  return redirect()->route('admin.sizes')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        //}
    }
	
	
	
	
	
	
	
	//##############################################################################
     // 				checkout and	Apply  insert data into oder table
     //##############################################################################
	 
	
	public function  Checkout(Request $request){
		$user_id=Auth::user()->id;
		
		 
		 $settings=Setting::first();
		 
		   $cart_count=Cart::where('user_id',$user_id)->sum('quantity');
		 if($cart_count <= $settings->product_shipping_count){
			 $shipping_charges= $settings->product_shipping_price;
			 }else{
				 $shipping_charges=0;
				 }
				 
	 ////////////////////////////////////
		 if($request->isMethod("post")){
			// return $request;
			 try {

                DB::beginTransaction();
				
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
			 Cart::where('user_id',$user_id)->delete();
			 
			 //////////////////////////////////////////
			 
			 Session::put('order_id',$order_id);
			 DB::commit();
			 $pay_type=$request->pay_type; 
			 	 if($request->pay_type=="cash"){
				 $pay_type="cash";
					return redirect()->route('site.order_complete',['pay_type' => "cash"]);
					//->with(['success' => 'تم ارسال طلبك بنجاح فى انتظار الموافقه من المسئول ','pay_type'=>$pay_type]);
					 }else{
						
			
return redirect()->route('site.order_complete',['pay_type' => "Visa"]);	
	}
			 } catch (\Exception $ex) {
            DB::rollback();
            //return $ex;
            return redirect()->route('site.checkout')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
			// return redirect()->route('admin.products')->with(['msg' => 'حدث خطا ما برجاء المحاوله لاحقا',  'alert-type' => 'danger'] );
        }
			} 
			 ///////////////////////////////
		$deliveryAddress=Address::getDeliveryAddress();

		 $basket=Cart::UserCartItems(); 
       return view('front.cart.checkout',compact('basket','deliveryAddress','shipping_charges'));
		 // return view('front.cart.payments',compact('basket'));
		 
		}
		 
    
	
	
	
	
	/////////////////order_complete//////////////////////
	
		public function  orderComplete(Request $request){
			
			 //////////////////delete all cart item: empty cart//////////////////////
			 Cart::where('user_id',Auth::user()->id)->delete();
			 
			 //////////////////////////////////////////
			 return view('front.cart.orders_complete');
		}
		
		
		
	
 }

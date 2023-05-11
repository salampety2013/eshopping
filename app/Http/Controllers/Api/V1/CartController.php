<?php

namespace App\Http\Controllers\Api\V1;

use Validator;
use App\Models\Cart;
use App\Models\Size;
use App\Models\Color;
use App\Basket\Basket;
use App\Http\Requests;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Currency;
use App\Models\Address;

use App\Traits\GeneralTrait;


use Illuminate\Http\Request;

use App\Models\ColorProductSize;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\V1\CartResource;

use App\Http\Resources\V1\CartCollection;
use App\Http\Resources\V1\CurrencyResource;
 use Illuminate\Support\Carbon;
 use App\Exceptions\QuantityExceededException;

 use Exception;


class CartController extends Controller
{
	    use GeneralTrait;


    //##########################
     // Show all items in the Basket.
    //##########################


    public function index(Request $request )
    {
		 try {
			  $rules = [
              'cart_api_key' => ['required',  'exists:carts,cart_api_key'],
              'cart_api_key' => ['required',  'exists:carts,cart_api_key'],



        ];

        $validator = Validator::make($request->all(), $rules);
         if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);
            // return $this->returnValidationErrorAll('422',$validator);

        }
		$cart_api_key=$request->cart_api_key;
 		 //return $basket=Cart::UserCartItemsApi();

		 //------------------------------------
 if(Auth::check()){
 	$userCartItems=Cart::with('product')->where('user_id', Auth::user()->id)->orderBy('id','desc')->get();

       	 // $userCartItems=Cart::with(['product' =>  function ($q) {
          //  return $q->select('id','name_ar'); }])->where('user_id', Auth::user()->id)->get();

		  }else{
		 $userCartItems=Cart::with('product') ->where('cart_api_key', $cart_api_key )->orderBy('id','desc')->get();

		  }
		  //////////////////////get tax   in  ///////////////////////////////////
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


            //-------------------------------------
//----------------get currency value -----------------------


$currency_id = $request->currency_id ?? 1;
$currency=Currency::where('id',$currency_id)->where('status',1)->first();
if(!$currency){
   $exchange_rate =1;
   //$currency  == null ? $currency : [];

} else{
   $exchange_rate = (double)$currency->exchange_rate ?? 1 ;
}

$currency_con=new CurrencyResource($currency) ;
//---------------------------------------------------------

			  $data = [
                'cart'=>CartResource::collection($userCartItems),

                 'currency'=>$currency_con,
				'total_price'=>$total_price,
 			 'tax'=> $tax,
			 'tax_amount'=>$tax_amount,

         ];



        //-------------------------------
     return $this->returnData('data',$data   );

           return $this->returnData('data',new CartCollection($userCartItems));
         // return $this->returnData('data',new CartResource($product));

      } catch (\Exception $e) {
        return $this->returnErrors(201, $e->getMessage());
      }
     }


     //##########################
     // Show all items in the Basket.
    //##########################


    public function applaycouponCart(Request $request )
    {
		 try {
			  $rules = [
              'cart_api_key' => ['required',  'exists:carts,cart_api_key'],
              'coupon_code' => ['nullable',  'exists:coupons,coupon_code'],



        ];

        $validator = Validator::make($request->all(), $rules);
         if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);
            // return $this->returnValidationErrorAll('422',$validator);

        }
		$cart_api_key=$request->cart_api_key;
 		 //return $basket=Cart::UserCartItemsApi();
          $grand_total = 0;
		 //------------------------------------
 if(Auth::check()){
 	$userCartItems=Cart::with('product')->where('user_id', Auth::user()->id)->orderBy('id','desc')->get();

       	 // $userCartItems=Cart::with(['product' =>  function ($q) {
          //  return $q->select('id','name_ar'); }])->where('user_id', Auth::user()->id)->get();

		  }else{
		 $userCartItems=Cart::with('product') ->where('cart_api_key', $cart_api_key )->orderBy('id','desc')->get();

		  }
		  //////////////////////get tax   in  ///////////////////////////////////
		   $total_price=0;
			foreach($userCartItems as $CartItem){
				// $product_final_price=($CartItem->product->discount_price ?? $CartItem->product->discount_price);
				 $product_final_price=($CartItem->product->discount_price>0) ? $CartItem->product->discount_price: $CartItem->product->price ;
				$total_price+=$product_final_price * $CartItem->quantity;


			}
//----------------get currency value -----------------------


$currency_id = $request->currency_id ?? 1;
$currency=Currency::where('id',$currency_id)->where('status',1)->first();
if(!$currency){
   $exchange_rate =1;
   //$currency  == null ? $currency : [];

} else{
   $exchange_rate = (double)$currency->exchange_rate ?? 1 ;
}

$currency_con=new CurrencyResource($currency) ;
//----------------------get Tax -----------------------------------
$setting=Setting::first();
			 $tax=(isset($setting->tax) ?  $setting->tax : 0);

			$tax_amount=($total_price * $tax)/100;


            //-------------------------------------

             //$grand_total = $total_price+$tax_amount;


            //-----------------get coupon if exist------------------------------------
if ($request->coupon_code) {


      #########################################################

    //$coupon_code=Coupon::where('coupon_code',$request->coupon_code)->count();
    //  $coupon_code=Coupon::where(['coupon_code'=>$request->coupon_code,'status'=>"1"])->count();
    $coupon_code=Coupon::where(['coupon_code'=>$request->coupon_code,'status'=>"1"])->first();
    if (!$coupon_code) {
        $message="Invalid Coupon Code";
        $data = [
                'cart'=>CartResource::collection($userCartItems),

                 'currency'=>$currency_con,
                'total_price'=>$total_price,
             'tax'=> $tax,
             'tax_amount'=>$tax_amount,
             'coupon_amount'=>"0",
              'grand_total'=>$grand_total,

         ];
        //return $this->returnData('data',$data   );
        return $this->returnErrors('202', __('Invalid Coupon Code'));
    } else {


           //################## check   coupon expiry_date
        $expiry_date=$coupon_code->expiry_date;
        $current_date=date('Y-m-d');
        // dd($expiry_date."==".$current_date);
        if ($expiry_date < $current_date) {
            //$status= false ;
            $message="this Coupon is expire";
            return $this->returnErrors('202', __('this Coupon is expire'));
        }
        //################## check if coupon is from selected category
        if ($coupon_code->categories_ids!="") {
            $cat_ids=explode(',', $coupon_code->categories_ids);
            //$cat_IDs = array_map('intval',$cat_ids );

            // dd($cat_IDs);
            //##### check if any item belong to categories from cart table
            foreach ($userCartItems as  $item) {
                // $sub_cat_id= Product::select('sub_cat_id')->where('id',$item->product_id)->first();
                //  $sub_cat_id= $sub_cat_id->sub_cat_id;
                // dd($cat_ids);
                $sub_cat_id= $item->product->sub_cat_id;
                $sub_cat_id=(string)$sub_cat_id;
                // dd($sub_cat_id);
                if (!in_array($sub_cat_id, $cat_ids)) {
                    $message="this Coupon is  not apply for one of selected product";
                    return $this->returnErrors('202', __('this Coupon is  not apply for one of selected product'));
                }
            }
        }


        //################## check if coupon is apply to that user email
        if ($coupon_code->users_ids!="") {
            $users_emails=explode(',', $coupon_code->users_ids);
            $login_user_email=Auth::user()->email;
            // dd($login_user_email);
            // dd($users_emails);
            //##### check if login user belong to users that have coupon
            if (!in_array($login_user_email, $users_emails)) {
                $message="this Coupon is  not apply for you";
                return $this->returnErrors('202', __('this Coupon is  not apply for you'));
            }
        }

        //################## check if coupon is one time or multiple user email
        if ($coupon_code->coupon_type=="Single Times") {
            $coupon_applay_count=Order::where(['user_id'=>Auth::user()->id,'coupon_code'=>$coupon_code->coupon_code])->count();

            //dd($coupon_applay_count);

            //##### check if login user belong to users that have coupon
            if ($coupon_applay_count>=1) {
                $message="this Coupon is count is out";
                return $this->returnErrors('202', __('this Coupon is count is out'));
            }
        }


        //////////////  check if coupon amount is fixed or percentage#############
        $amount_type= $coupon_code->amount_type;
        $amount= $coupon_code->amount;

        // $coupon_amount=($amount_type=="Fixed") ? $amount : (($total_amount*$amount)/100);
        $coupon_amount=($amount_type=="Fixed") ? round($amount/$exchange_rate, 2) : (($total_price* round($amount/$exchange_rate, 2) /100));

        //dd($coupon_amount);

        $limit_total=$total_price-$coupon_amount;

        //################## check if coupon is  is less than grand total to applay it
        if ($limit_total <=10) {

      // dd($coupon_code);
            $message="grand total of products less than buying limit value";
            return $this->returnErrors('202', __('grand total of products less than buying limit value'));
        }


        if (!isset($message)) {


       /////add coupon code and  coupon_amount  #########


            $message="Coupon Successfully Redeemed" ?? '' ;
        }
    }


    //-----------------end coupon-------------------------
}
			 //return $total_price;
             $coupon_amount=isset($coupon_amount) ? $coupon_amount : 0;
             $tax_amount=isset($tax_amount) ? $tax_amount : 0;

             $grand_total=($total_price-$coupon_amount)+$tax_amount ;


			  $data = [
                'cart'=>CartResource::collection($userCartItems),


				'total_price'=>$total_price ,
 			 'tax'=> $tax,
			 'tax_amount'=>$tax_amount,
             'coupon_code'=>$request->coupon_code ?? '',
             'coupon_amount'=>$coupon_amount?? 0,
             'grand_total'=>$grand_total,
            'currency'=>$currency_con,
         ];
         $message= $message ?? '';


        //-------------------------------
     return $this->returnData('data',$data ,$message );

         //  return $this->returnData('data',new CartCollection($userCartItems));
         // return $this->returnData('data',new CartResource($product));

      } catch (\Exception $e) {
        return $this->returnErrors(201, $e->getMessage());
      }
     }


     //##########################
     // Add items to the Basket.
     //##########################



    public function AddToCart(Request $request )
    {


		try {
			  $rules = [
              'product_id' => ['required',  'exists:products,id'],
              'quantity' => ['required',  'numeric'],
              'size_id' => ['nullable',  'numeric','exists:sizes,id'],
              'color_id' => ['nullable',  'numeric','exists:colors,id'],


           // 'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],

        ];

        $validator = Validator::make($request->all(), $rules);
         if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);


        }

 		//$basket=Cart::UserCartItems();

         /*  if (!$basket)


 */

		 ////////check quantity is available or not ////////////////
      /*    Search Comma Separated Values in Laravel
         $search_id = 1;
         $data = \DB::table("posts")
             ->select("posts.*")
             ->whereRaw("find_in_set('".$search_id."',posts.tag_id)")
             ->get();
 */
		/////// if there are sizes only or sizes with its colors  ///////////////////
        if($request->size_id!="" && $request->color_id== ""){
             $stocks=ColorProductSize::where('product_id',$request->product_id)->where('size_id',$request->size_id)->first();

			if (empty($stocks))
				  return $this->returnErrors('202', __('invalid id' ));
             $available_qty=$stocks->quantity;
              }
               ////////end check///////////

               else  if($request->size_id!="" && $request->color_id!= ""){
                  $stocks=ColorProductSize::where('product_id',$request->product_id)->where('size_id',$request->size_id)->whereRaw("find_in_set('".$request->color_id."',color_id)")->first();
				   if ($stocks === null)
				  return $this->returnErrors('202', __('invalid id' ));

				   $available_qty=$stocks->quantity;
                  }
                   ////////end check///////////
			 else if($request->color_id!= "" && $request->size_id==""){
				 // dd ($request->stock_id);

         $stocks=ColorProductSize::where('product_id',$request->product_id)->whereRaw("find_in_set('".$request->color_id."',color_id)")  ->first();
		 if ($stocks === null)
		 return $this->returnErrors('202', __('invalid id' ));

		 $available_qty=$stocks->quantity;


					  } else if($request->color_id == "" && $request->size_id==""){

      //  $stocks=ColorProductSize::where(['product_id'=> $product_id])->first();
           $stocks=Product::where( 'id',$request->product_id)->first();
		if ($stocks === null )
		return $this->returnErrors('202', __('invalid id' ));
		if ($stocks->has_Variants == 1 )
		return $this->returnErrors('202', __('this product should have color or size or both' ));
          $available_qty=$stocks->total_quantity;
				  }

				  if ( $stocks=="")
				  return $this->returnErrors('202', __('invalid id' ));

				  //--------------------------------
				 /*  if(($request->quantity =="" || ($available_qty < $request->quantity  ) )  ) {
					//return redirect()->back()->with(['error' => 'quantity not available']);
					return $this->returnErrors('202', __('quantity not available' ));

			   } */

//--------------------------------------------------

	 $product_id=$request ->product_id ?? '';
		  $size_id=$request ->size_id ?? '';


		$color_id= $request->color_id ?? '';
		 /// Generate cart_api_key if is not exist/////////////////////////
		 $cart_key= md5(uniqid(rand(), true));
		 $cart_api_key= (!empty($request ->cart_api_key))  ? $request ->cart_api_key : $cart_key;
		    //get if session  exist get it else
 /// generate some thing like token


			   $user_id= (!empty(Auth::user()->id))  ? Auth::user()->id : '';
			 // check if user is login or not
			 if(Auth::check()){

				 //user logged in


				 $update_qty=Cart::where(['user_id'=>Auth::user()->id,'product_id'=> $product_id,'size_id'=>$size_id,'color_id'=>$color_id])->first();
				 }else{

					 //user not logged in then use cart_api_key
					 $update_qty=Cart::where(['cart_api_key'=>$cart_api_key,'product_id'=> $product_id,'size_id'=>$size_id,'color_id'=>$color_id])->first();
					 }



			// check if the product added before then if is exist update quanitity with one or send message that is exists
		 	// $update_qty=Cart::where(['session_id'=>$session_id,'product_id'=> $product_id,'size_id'=>$size_id,'color_id'=>$color_id])->count();


			if($update_qty ){
				$check_qty=$request->quantity+$update_qty->quantity;
				 //return redirect()->back()->with(['error' => 'product already exist']);
				 if(($request->quantity =="" || ($available_qty < $request->quantity || $available_qty< $check_qty) )  ) {
				 	 //return redirect()->back()->with(['error' => 'quantity not available']);
					  return $this->returnErrors('202', __('quantity not available' ));

		 		}


				$update_qty->quantity=$request->quantity+$update_qty->quantity;
				$update_qty->save();

			$data=[
				'cart_api_key'=>$update_qty->cart_api_key,
				//'cart_type'=>$update_qty->cart_type,

				'cart_id'=>$update_qty->id,
				'user_id'=>$update_qty->user_id,
				'stock_id'=>$update_qty->stock_id,
				'product_id'=>$update_qty->product_id,
				'size_id'=>$update_qty->size_id,
				'color_id'=>$update_qty->color_id,
				'quantity'=>$update_qty->quantity,

	];
				return $this->returnData('data', $data );

				}else{





//$user_id= (!empty(Auth::user()->id))  ? Auth::user()->id : '';

		//----------------------------- save product in cart if not exists----------------
			$cart=Cart::CREATE([

            'cart_api_key'=>$cart_api_key,
            'cart_type'=>"api",
			'user_id'=> isset($user_id) ? $user_id : null,
			'product_id'=> $product_id,
			'color_id'=>$color_id,
			'size_id'=>$size_id,
			 'quantity'=>$request->quantity,
			  //'price'=>$request->price_now,


			]);

			$data=[
				'cart_api_key'=>$cart->cart_api_key,
				//'cart_type'=>$cart->cart_type,

				'cart_id'=>$cart->id,
				'user_id'=>$cart->user_id,
				'stock_id'=>$cart->stock_id,
				'product_id'=>$cart->product_id,
				'size_id'=>$cart->size_id,
				'color_id'=>$cart->color_id,
				'quantity'=>$cart->quantity,

	];
				return $this->returnData('data', $data );

			return $this->returnData('data', $cart );
			// return $this->returnData('data',Cart::collection($countries));
		   // return $this->returnData('data',new CartResource($product));
		  }





     } catch (\Exception $e) {
        return $this->returnErrors(201, $e->getMessage());
      }




	}




 //##########################
     // update quantity in the Basket.
     //##########################


    public function updateCart(Request $request)
    {
		try {
		 $rules = [
              'cart_id' => ['required',  'exists:carts,id'],
           // 'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],

        ];

        $validator = Validator::make($request->all(), $rules);
         if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);


        }


		 //$request->stock_id;
		 $data=$request->all();
		   $cart=Cart::where('id',$data['cart_id'])->first();
             if (!$cart)
              return $this->returnErrors('202', __('general.not found'));


         $stocks=Product::where( 'id',$cart->product_id)->first();
		if ($stocks->has_Variants == 0 ){
          $available_qty=$stocks->total_quantity;
}else{
    ////////////check if the quantity is available in stock or not //////////


                    //user not logged in then use cart_api_key
                 $available_qtys=ColorProductSize::where(['product_id'=>$cart->product_id,'size_id'=>$cart->size_id])->whereRaw("find_in_set('".$cart->color_id."',color_id)") ->first();

            $available_qty=$available_qtys->quantity;
    //----------------------- end check -quantity----------------------------
}
		// echo "<pre>"; print_r($available_qty); die;
		 //dd($available_qty);
		  if( $available_qty  < $data['quantity'])
		  return $this->returnErrors('202', __('quantity.not available'));

			 $cart->update(['quantity'=>$data['quantity']]);

             return $this->returnSuccessMessages('quantity updated successfully');
      } catch (\Exception $e) {
        return $this->returnErrors(201, $e->getMessage());
      }


	}

	##############################################################################
	//##############################################################################
     // 			delete Items from the Basket.
     //##############################################################################


 	 public function deleteCart( Request $request)
    {
try {
       $rules = [
              'cart_id' => ['required',  'exists:carts,id'],
           // 'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],

        ];

        $validator = Validator::make($request->all(), $rules);
         if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);


        }

 		//$basket=Cart::UserCartItems();


			$cart=Cart::where('id', $request->cart_id )->first();

             if (!$cart)
			  return $this->returnErrors('202', __('general.not found'));
 		 $cart->delete();




		  return $this->returnSuccessMessages('deleted successfully');
          //return $this->returnData('data',[]);

      } catch (\Exception $e) {
        return $this->returnErrors(201, $e->getMessage());
      }
    }






	//##############################################################################
     // 					Apply Coupon in the Basket.
     //##############################################################################

	 public function ApplyCoupon( Request $request)
    {

		try {
            $rules = [
                'cart_api_key' => ['required',  'exists:carts,cart_api_key'],



          ];

          $validator = Validator::make($request->all(), $rules);
           if ($validator->fails()) {
              $code = $this->returnCodeAccordingToInput($validator);
              return $this->returnValidationError($code, $validator);
              // return $this->returnValidationErrorAll('422',$validator);

          }
          $cart_api_key=$request->cart_api_key;

 		//$basket=Cart::UserCartItems();

          if (!$basket)

              return $this->returnErrors('202', __('general.not found'));


       // try {
	   $basket=Cart::UserCartItems();
		$html = view('front.cart.cart_items',compact('basket'))->render();
 		$totalCart=  totalCartItems();

	 //get total amount
	 $total_amount=0;
				 foreach($basket as $key=> $item){

					$discount_price=round($item->product->discount_price/$exchange_rate,2);
					$price=round($item->product->price/$exchange_rate,2);
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
									'coupon_amount'=>"0",
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
									'coupon_amount'=>"0",
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
									'coupon_amount'=>"0",
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
									'coupon_amount'=>"0",
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
									'coupon_amount'=>"0",
									'grand_total'=>$total_amount,
									'status'=>false,
									'message'=>$message

									]);
						  }

				}


			 //////////////  check if coupon amount is fixed or percentage#############
 				$amount_type= $coupon_code->amount_type;
				$amount= $coupon_code->amount;

  // $coupon_amount=($amount_type=="Fixed") ? $amount : (($total_amount*$amount)/100);
     $coupon_amount=($amount_type=="Fixed") ? round($amount/$exchange_rate,2) : (($total_amount* round($amount/$exchange_rate,2) /100));

  //dd($coupon_amount);

 $total_amount=$total_amount-$coupon_amount;

	  //################## check if coupon is  is less than grand total to applay it
				  if($total_amount <=10 )
				 {

            // dd($coupon_code);
             $message="grand total of products less than buying limit value";
						   return response()->json([
									 'totalCartCount'=>$totalCart,
									'html'=>$html,
									'coupon_amount'=>"0",
									'grand_total'=>$total_amount,
									'status'=>false,
									'message'=>$message

									]);
				 }


			if(!isset($message)){


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


//-----------------end coupon-------------------------
		 //echo "<pre>"; echo($totalCart); die;




           // return $this->returnData('data',Cart::collection($countries));
          return $this->returnData('data',new CartResource($product));

      } catch (\Exception $e) {
        return $this->returnErrors(201, $e->getMessage());
      }
    }







	//##############################################################################
     // 				checkout and	Apply  insert data into oder table
     //##############################################################################




    	public function  Checkout(Request $request){



		  //--------------------------------------------------------

			// return $request;
			 try {
                $rules = [
                    'cart_api_key' => ['required',  'exists:carts,cart_api_key'],
                    'coupon_code' => ['nullable',  'exists:coupons,coupon_code'],



              ];

              $validator = Validator::make($request->all(), $rules);
               if ($validator->fails()) {
                  $code = $this->returnCodeAccordingToInput($validator);
                  return $this->returnValidationError($code, $validator);
                  // return $this->returnValidationErrorAll('422',$validator);

              }
//--------------------------------------------------
DB::beginTransaction();
$user_id=Auth::user()->id ?? '';


		 $settings=Setting::first();

		   $cart_count=Cart::where('user_id',$user_id)->sum('quantity');
		 if($cart_count <= $settings->product_shipping_count){
			 $shipping_charges= $settings->product_shipping_price;
			 }else{
				 $shipping_charges=0;
				 }



              $cart_api_key=$request->cart_api_key;
                //return $basket=Cart::UserCartItemsApi();
                $grand_total = 0;
               //------------------------------------
       if(Auth::check()){
           $userCartItems=Cart::with('product')->where('user_id', Auth::user()->id)->orderBy('id','desc')->get();

                  // $userCartItems=Cart::with(['product' =>  function ($q) {
                //  return $q->select('id','name_ar'); }])->where('user_id', Auth::user()->id)->get();

                }else{
               $userCartItems=Cart::with('product') ->where('cart_api_key', $cart_api_key )->orderBy('id','desc')->get();

                }
                //////////////////////get tax   in  ///////////////////////////////////
                 $total_price=0;
                  foreach($userCartItems as $CartItem){
                      // $product_final_price=($CartItem->product->discount_price ?? $CartItem->product->discount_price);
                       $product_final_price=($CartItem->product->discount_price>0) ? $CartItem->product->discount_price: $CartItem->product->price ;
                      $total_price+=$product_final_price * $CartItem->quantity;


                  }
      //----------------get currency value -----------------------


      $currency_id = $request->currency_id ?? 1;
      $currency=Currency::where('id',$currency_id)->where('status',1)->first();
      if(!$currency){
         $exchange_rate =1;
         //$currency  == null ? $currency : [];

      } else{
         $exchange_rate = (double)$currency->exchange_rate ?? 1 ;
      }

      $currency_con=new CurrencyResource($currency) ;
      //----------------------get Tax -----------------------------------
                 $setting=Setting::first();
                   $tax=(isset($setting->tax) ?  $setting->tax : 0);

                  $tax_amount=($total_price * $tax)/100;

                  //-----------------get coupon if exist------------------------------------
if ($request->coupon_code) {


    #########################################################


  $coupon_code=Coupon::where(['coupon_code'=>$request->coupon_code,'status'=>"1"])->first();
  //////////////  check if coupon amount is fixed or percentage #############
  $amount_type= $coupon_code->amount_type;
  $amount= $coupon_code->amount;

  // $coupon_amount=($amount_type=="Fixed") ? $amount : (($total_amount*$amount)/100);
  $coupon_amount=($amount_type=="Fixed") ? round($amount/$exchange_rate, 2) : (($total_price* round($amount/$exchange_rate, 2) /100));

  //dd($coupon_amount);


//-----------------end coupon-------------------------
}
       //return $total_price;
       $coupon_amount=isset($coupon_amount) ? $coupon_amount : 0;
       $tax_amount=isset($tax_amount) ? $tax_amount : 0;

       $grand_total=($total_price-$coupon_amount)+$tax_amount ;




               if ($request-> default_address=="true") {

                    $deliveryAddress=Address::where(['user_id'=>$user_id,'default_address'=>'true'])->first();

                }else{
                    $deliveryAddress=Address::where('id', $request->address_id)->first();
            }
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
			  	'coupon_code' => $coupon_code,
			  	'coupon_amount' => $coupon_amount,
					'tax' => $tax,
					'tax_amount' =>$tax_amount,
					'sub_total' =>$total_price,
					'currency_code' =>  $currency->currency_code,
					'currency_symbol' =>  $currency->curr_symbol,

					 'created_at' => Carbon::now(),
				'grand_total' => $grand_total
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


			   $pay_type=$request->pay_type;
			 	 if($request->pay_type=="Cash"){

 					  //////////////////delete all cart item: empty cart//////////////////////

                       if(Auth::check()){
                        Cart::where('user_id',$user_id)->delete();

                             }else{
                             Cart::where('cart_api_key',$cart_api_key)->delete();
                             }


			 //////////////////////////////////////////
		 DB::commit();
         	 $pay_type="cash";
				  return view('front.payment.orders_complete');
					//return redirect()->route('site.order_complete',['pay_type' => "cash"]);
 					 }else{

					 return redirect()->route('site.ShowpayPal');
					}
			 } catch (\Exception $ex) {
            DB::rollback();
            //return $ex;
            return redirect()->route('site.checkout')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
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
			 //Cart::where('user_id',Auth::user()->id)->delete();

			 //////////////////////////////////////////
			 return view('front.payment.orders_complete');
		}













 }

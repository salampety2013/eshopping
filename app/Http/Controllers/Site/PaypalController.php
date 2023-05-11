<?php

namespace App\Http\Controllers\Site;

use App\Http\Requests;
 use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Events\NewOrderNotification;

use App\Models\User;
use App\Models\Cart;
use App\Models\Country;
use App\Models\City;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrdersProduct;
use Illuminate\Support\Facades\Session;
 use Illuminate\Support\Facades\DB;
 use Illuminate\Support\Carbon;
 use App\Exceptions\QuantityExceededException;

 use Exception;
 use Auth;
use View;
class PaypalController extends Controller
{
 	

    
     

    //##########################
     // Show all my orders 
    //##########################

 	 
		 
		
		
    public function ShowpayPal()
    {
		  $data=[];
    //////////////////delete all cart item: empty cart//////////////////////
			// Cart::where('user_id',Auth::user()->id)->delete();
			$data["order_detail"]= Order::where('id',Session::get('order_id'))->first();
			$data["user"]=User::find($data["order_detail"]->user_id);
			$data["city"]=	City::find($data["order_detail"]->city);
			$data["country"]=Country::find($data["order_detail"]->country);
	//////////////////////////////////////////
	//return $data;
			  return view('front.payment.payPal',$data);
    }
 

     //##########################
     // if payment is sucess .
     //##########################
	  public function sucessPayment()
    {
		if(Session::has('order_id')){
		  $data=[
 		  'order_id'=>Session::get('order_id') ,
		  'grand_total'=>Session::get('grand_total') ,
		  'user_name'=>Auth::user()->name ,
		  'msg'=>'تم الدفع بنجاح',
  		  ];
		  
		// event(new NewOrderNotification($data)); 
    //////////////////delete all cart item: empty cart//////////////////////
			 Cart::where('user_id',Auth::user()->id)->delete();
			  Order::find($order_id)->update(['payment_status'=>'Success']); 
	//////////////////////////////////////////
	//return $data;
		return view('front.payment.sucesspay');
    }else{
		return redirect()->route('site.site.cart.index');
		}
 
	} 
      //##########################
     // if payment is fail .
     //##########################
	  public function failPayment()
    {
		if(Session::has('order_id')){
		//	dd(Session::get('grand_total'));
		$order_id=Session::get('order_id');
		    $data=[
 		  'order_id'=>Session::get('order_id') ,
		  'grand_total'=>Session::get('grand_total') ,
		  'user_name'=>Auth::user()->name ,
		  'msg'=>'تم الدفع بنجاح',
  		  ];
		  //dd($data);
		 //event(new NewOrderNotification($data)); 
		// return "yes";
		 
    //////////////////delete all cart item: empty cart//////////////////////
			 Order::find($order_id)->update(['payment_status'=>'Failed']); 
			 
			
	//////////////////////////////////////////
	//return $data;
		return view('front.payment.failPay');
    }else{
		return redirect()->route('site.site.cart.index');
		}
 
	} 
      
   
 
     
     

}

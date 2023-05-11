<?php

namespace App\Http\Controllers\Site;

use App\Http\Requests;
 use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
class OrdersController extends Controller
{
    
	

    
     

    //##########################
     // Show all my orders 
    //##########################

 	 
		 
		
		
    public function index()
    {
		//
            	  $orders= Order::with('orders_products')->where('user_id',Auth::user()->id)->orderBy('id','Desc')->get();
			 
			 //////////////////////////////////////////
			 return view('front.orders.index',compact('orders'));
    }
 

     //##########################
     // Add items to the Basket.
     //##########################
	 
	 
       public function viewDetails($id)
    {
		 
             	  $order_details= Order::with('orders_products')->where('id',$id)->orderBy('id','Desc')->first();
			 
			 //////////////////////////////////////////
			  return view('front.orders.order_details',compact('order_details'));
    }
 

   
 //##########################
     // update quantity in the Basket.
     //##########################
	 
     
     

}

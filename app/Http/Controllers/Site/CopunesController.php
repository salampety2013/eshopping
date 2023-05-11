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
use View;

use Illuminate\Support\Facades\Session;
//use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
 use Illuminate\Support\Carbon;
 use App\Exceptions\QuantityExceededException;

 use Exception;
// use Session;
use Auth;

class CopunesController extends Controller
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
		 
			
		
		
	}
	
	
	
	 public function deleteCart( Request $request)
    {

       // try { 
            // $cart=Cart::where('id',$id);
			$cart=Cart::where('id', $request->cart_id );
             
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

}

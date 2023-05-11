<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
 use Illuminate\Support\Carbon;
 use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendInvoice;

use Illuminate\Support\Facades\Route;
 use Maatwebsite\Excel\Facades\Excel;
 
 use App\Exports\OrdersExport;
 use App\Models\Country;
 use App\Models\City;
//use App\Exports\OrderExport;

 
use App\Models\Order;
use App\Models\OrdersProduct;
use App\Models\User;

use PDF;

class OrdersController extends Controller
{
    public function index($order_status=null)
    {
		if($order_status!=null){
   $orders= Order::with('orders_products')->where('order_status',$order_status)->orderBy('created_at','desc')->get();
		}else{
			   $orders= Order::with('orders_products')->where('order_status','!=','New')->orderBy('id','ASC')->get();

			}
      // $orders =  Orders::latest()->get();
         return view('dashboard.orders.index_orders', compact('orders'));
        

    }
    public function ViewOrderDetails($id,$type=null)
    {
           $order_details= Order::with('orders_products')->where('id',$id)->orderBy('id','Desc')->first();
			 $user_details=User::where('id',$order_details->user_id)->first();
			if($type=="print"){
				$type="print";
				}else{
			$type="";}
			 //////////////////////////////////////////
			  return view('dashboard.orders.view_order_details',compact('order_details','user_details','type'));
       
    }
		public function PDFOrderDetails($id)
   			 {
           $order_details= Order::with('orders_products')->where('id',$id)->orderBy('id','Desc')->first();
			$user_details=User::where('id',$order_details->user_id)->first();
			 
				 
			 ///////////////////pdf package ///////////////////////
			 // reference the Dompdf namespace
 
				////////////////////////////////////////////////
				
				
				  $data = [
				 'id'=> $order_details->id,
				 'user_name'=> $user_details->name,
				 'mobile'=> $user_details->mobile,
				 'email'=> $user_details->email,
				 'date'=> Carbon::now()->format('d-m-Y'),
				 //,$order_details->created_at),
				 'pincode'=>$order_details->pincode,
				 'country'=> $order_details->country,
				 'city'=> $order_details->city,
				 'address'=> $order_details->address,
				 'coupon_amount'=>$order_details->coupon_amount,
				 'shipping_charges'=> $order_details->shipping_charges,
				 'tax'=> $order_details->tax,
				 
				 'grand_total'=> $order_details->grand_total,
 				];
				$items=[];
				 foreach($order_details->orders_products as $pro_detail){
                        $name= $pro_detail->name ; 
						 $price= $pro_detail->price ; 
						$quantity= $pro_detail->quantity;
                        $sub_total=($pro_detail->price)*($pro_detail->quantity);
                       // $total+=$sub_total;
						$size= $pro_detail->size ;  
                        $color=  $pro_detail->color; 
						  
					 $items[]=[
					 'name'=> $name ,
					 'price'=> $price ,
					 'quantity'=> $quantity ,
					 'sub_total'=> $sub_total ,
					 'size'=> $size ,
 					 'color'=> $color ,
 					];	
						
				 }
				  $data['items']=$items;
				 
		
				//$pdf = PDF::loadView('dashboard.orders', $data);
		
				//return $pdf->stream('document.pdf');
				 
				$pdf = PDF::loadView('dashboard.orders.PDF_invoice', $data);
				///////////////////////save pdf and semd email with attach invoice pdf//invoices_pdf///////////////////
				   if(Route::currentRouteName()=='admin.orders.PDFOrderDetails')
				   {
				   return $pdf->stream('invoice-'.$order_details->id.'.pdf');
				   }else{
					   
						 //  $pdf->save(realpath('assets/images/invoices_pdf/invoice-'.$order_details->id.'.pdf')); // online
						   $pdf->save('assets/images/invoices_pdf/invoice-'.$order_details->id.'.pdf'); //in local 
						   return 'invoice-'.$order_details->id.'.pdf';
					   }
						///////////////////////////////////////
						
				 
			  return view('dashboard.orders.PDF_invoice',compact('order_details','user_details'));
       
    }
   
//##########################send email 

public function sendEmail($id)
    {
      // $order =  Order::findOrFail($id);
           $order_details= Order::with('orders_products')->where('id',$id)->orderBy('id','Desc')->first();
		$user_details=User::where('id',$order_details->user_id)->first();

        if (!$order_details)
            return redirect()->route('admin.orders')->with(['error' => 'هذا القسم غير موجود']);
 
 			$this->PDFOrderDetails($id); 
  //Mail::to($user_details->email)->locale(config('app.locale'))->send(new SendInvoice($order_details));
   			
		/////////send email with out make new email class//////////////////////////////////	
				// $id=$request->id;
					 $messageData=[
 					'name'=>$user_details->name,
					'mobile'=>$user_details->mobile,
					'email'=>$user_details->email,
					
					'grand_total'=>$order_details->grand_total,
				 
					
					];
 $order_details= Order::with('orders_products')->where('id',$id)->orderBy('id','Desc')->first()->toArray();
					Mail::send('emails.SendInvoiceEmail',$order_details,function($message) use($messageData,$id)
						{
						$message->to($messageData["email"])->subject('invoice shipping code in Ecommerce Site');
						// $message->attach(realpath('assets/images/invoices_pdf/invoice-'.$id.'.pdf'));
						// $message->attach(realpath('assets/images/invoices_pdf/invoice-'.$id.'.pdf'));
						
						});
			
			
			//////////////////////////////////////////////////
			
			
			$notification = array(
						'msg' => 'Send Successfully',
						'alert-type' => 'success'
					);
		$success= 'تم الحفظ بنجاح';
 
					 return redirect()->route('admin.orders')->with($notification)->with(['success' => 'تم ارسال البريد بنجاح']);
			
			 // return view('dashboard.orders.PDF_invoice',compact('order_details','user_details'));

    }



 public function edit_shipping($id)
    {
       $order_shipping =  Order::find($id);
        if (!$order_shipping)
            return redirect()->route('admin.orders')->with(['error' => 'هذا القسم غير موجود']);
       // $category = Category::findOrFail($id);
        return view('dashboard.orders.add_edit_shipping', compact('order_shipping'));
    }


    public function AddEditShipping(Request $request )
    {
        
       
		
			// try {
            //return $request->all();
            
		 	$order_shipping =  Order::find($request->id);
					$user=User::where('id',$order_shipping->user_id)->first();

       /* if (!$order_shipping)
            return redirect()->route('admin.orders')->with(['error' => 'هذا القسم غير موجود']);
		*/	
		// return $request->type ;
            if($request->id){
					//$request->courier_name!="" &&  $request->tracking_number!=""
					$order_shipping ->update([
						'courier_name' => $request->courier_name,
						 'tracking_number' => $request->tracking_number 
						
						  
					]);
					 $notification = array(
						'msg' => 'Updated Successfully',
						'alert-type' => 'success'
					);
		$success= 'تم الحفظ بنجاح';
	/*	
		///4-  send shipping code email to multiple user with multiple attach
 		 
        $data["email"] = "aatmaninfotech@gmail.com";
        $data["title"] = "From ItSolutionStuff.com";
        $data["body"] = "This is Demo";
 
        $files = [
            realpath('files/160031367318.pdf'),
            realpath('files/1599882252.png'),
        ];
  
        Mail::send('emails.myTestMail', $data, function($message)use($data, $files) {
            $message->to($data["email"], $data["email"])
                    ->subject($data["title"]);
 
            foreach ($files as $file){
                $message->attach($file);
            }
            
        }); */
 
        
				
						
						 
						 
				   // return redirect()->route('admin.orders')->with(['success' => 'تم الحفظ بنجاح']);
					 //   return redirect()->to(url('/orderDetails/?id='.$request->id."&type=print")) ;

					 return redirect()->route('admin.orders')->with($notification)->with(['success' => 'تم الحفظ بنجاح']);
			}
			
			
            
			 // $orders =  Orders::findOrFail($id);
       // return view('dashboard.orders.add_edit_shipping', compact('order_shipping'));
			
       /* } catch (\Exception $ex) {
		//return  $ex;
           // return redirect()->route('admin.orders')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
                   return redirect()->route('admin.orders')->with(['msg' => 'حدث خطا ما برجاء المحاوله لاحقا','alert-type' => 'danger']);

	   
	    }*/
			
			
       
    }




    public function updateStatus(Request $request)
    {
        try {
            //return $request->all();
            
             
		if ($request->ids == "")
                return redirect()->route('admin.orders')->with(['error' => 'من فضلك قم بالاختيار ليتم التعديل']);

            $ids = $request->ids;
             Order::whereIn('id', $ids) ->update([
                'order_status' => $request->order_status,
                
                 'updated_at' => Carbon::now()
            ]);
             $notification = array(
                'msg' => 'Updated Successfully',
                'alert-type' => 'success'
            );

           // return redirect()->route('admin.orders')->with(['success' => 'تم الحفظ بنجاح']);

             return redirect()->route('admin.orders')->with($notification)->with(['success' => 'تم الحفظ بنجاح']);

        } catch (\Exception $ex) {
		//return  $ex;
           // return redirect()->route('admin.orders')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
                   return redirect()->route('admin.orders')->with(['msg' => 'حدث خطا ما برجاء المحاوله لاحقا','alert-type' => 'danger']);

	   
	    }
    }

 
    
    public function destroy($id)
    {

        try {
           
            $orders = Order::find($id);
            if (!$orders)
                return redirect()->route('admin.orders')->with(['error' => 'هذا القسم غير موجود ']);
 
            $orders->delete();

            return redirect()->route('admin.orders')->with(['success' => 'تم  الحذف بنجاح']);
        } catch (\Exception $ex) {
           // return $ex;
            return redirect()->route('admin.orders')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }

    public function delAll(Request $request)
    {
        try {
            if ($request->ids == "")
                return redirect()->route('admin.orders')->with(['error' => 'من فضلك قم بالاختيار ليتم الحذف']);

            $ids = $request->ids;
             Orders::whereIn('id', $ids)->delete();

            return redirect()->route('admin.orders')->with(['success' => 'تم  الحذف بنجاح']);
        } catch (\Exception $ex) {
            return $ex;
            return redirect()->route('admin.orders')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }
 


//------------------------------------------------------------------
// import- Export  Excel 
//-------------------------------------------------------------------
 public function fileExport($type=null)
    {
     
	   try {
		
		
       /*        $orderData= Order::with(['cityall' => function ($q) {

            return $q->select('id','name_ar','name_en');

        }, 'countryall' => function ($q) {

            return $q->select('id','name_ar','name_en');

        } ])->select('id','user_id','full_name','address','country','city','email','order_status','payment_status','grand_total')
		->orderBy('id','DESC')->get();
 $orderData= Order::with(['cityall','countryall'])->select('id','user_id','full_name','address','email','order_status','payment_status','grand_total')
		->orderBy('id','DESC')->get();*/
       
	   //  return Order::all();
	  /*  foreach($orderData as  $key=>$value){
		return $orderData[$key]['city']=$value-> cityall->name_ar;
		return $orderData[$key]['country'] =$value->countryall->name_ar;
		 
		 
		 // $cc=$orderData[$key]->city;
		dd($cc);
		echo"country=".$orderData['country_n']=$orderData->country->name_en;	
		  
	   }   */
   		//Excel::import(new ProductsImport, realpath('excel_products/'.$excel->name));
 			  return Excel::download(new OrdersExport, 'orders.'.$type);
         } catch (\Exception $ex) {
            return $ex;
            return redirect()->route('admin.orders')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }
 

}
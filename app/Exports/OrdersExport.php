<?php

namespace App\Exports;
 
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
 use App\Models\Order;
use App\Models\OrdersProduct;
use App\Models\City;
use App\Models\Country;
class OrdersExport implements withHeadings,FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
		$orderData= Order::with(['cityall' => function ($q) {

            return $q->select('id','name_ar','name_en');

        }, 'countryall' => function ($q) {

            return $q->select('id','name_ar','name_en');

        } ])->select('id','user_id','full_name','address','country','city','email','order_status','payment_status','grand_total')
		->orderBy('id','DESC')->get();

         /* $orderData= Order::select('id','user_id','full_name','address','country','city','email','order_status','payment_status','grand_total')
		->orderBy('id','DESC')->get(); */
       
	   //  return Order::all();
	  // foreach($orderData as $key=>$value){
		foreach($orderData as  $key=>$value){
			


		   $orderItems=OrdersProduct::select('name','quantity','product_code','price','size','color')
		   ->where('order_id',$value['id'])->get();
		   //$orderItems=json_decode(json_encode($orderItems));
		    // dd($orderItems);
			$product_names="";
			$prices="";
			$sizes="";
			$colors="";
			$quantity="";
			
			foreach($orderItems as $item){
		    
			  $product_names.=$item['name'].",";
			  $prices.=$item['price'].",";
			  $sizes.=$item['size'].",";
			  $colors.=$item['color'].",";
			  $quantity.=$item['quantity'].",";
			  
			  
			 // dd($product_codes);
			}
			//return $product_codes;
			$orderData[$key]['product_names']=$product_names;
			$orderData[$key]['price']=$prices;
			$orderData[$key]['sizes']=$sizes;
			$orderData[$key]['colors']=$colors;
			$orderData[$key]['quantity']=$quantity;
			
			  $orderData[$key]['cityA']=$value-> cityall->name_ar;
			  $orderData[$key]['countryA'] =$value->countryall->name_ar;
		   }
		   
		    return $orderData;
    } 
	
	public function headings():array{
        return[
            'Id',
            'user Id',
             
			'full name',
			'address',
			
			'email',
			'order_status',
			'payment_status',
			'grand total',
			
			'product_names',
			'price',
			
			'sizes',
			'colors',
			'quantity',
           'cityA', 'countryA',
			
        ];
    } 
	
	
	  
	
}

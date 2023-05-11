<?php

namespace App\Http\Resources\V1;
use App\Models\Product;
use App\Models\Currency;


use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
       // return parent::toArray($request);
	    $lang=app()->getLocale();
		$name=(string)'name_'.$lang;



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

		//------------------------
        $price = (double) number_format(((double) $this->product->price * $exchange_rate), 2, '.', '')  ;
				   $discount_price  = (double) number_format(((double) $this->product->discount_price * $exchange_rate), 2, '.', '')  ;
				   $final_price =  ($discount_price>0) ? $discount_price:$price;
                   $total_price  = (double) number_format(((double) ($final_price * $this->quantity ) * $exchange_rate), 2, '.', '') ;

		//dd($name);
	 return [
	    	'cart_id'=>$this->id,
             'cart_api_key'=> $this->cart_api_key ?? '' ,
       	 'user_id' => $this->user_id  ?? '',
  			 'product_id' => $this->product_id  ?? '',
  			 'color_id' => $this->color_id  ?? '',
  			 'size_id' => $this->size_id  ?? '',

				 'quantity' =>(int) $this->quantity,
				 'stock_id' => $this->stock_id,
				  //'price' =>(double)  $this->product->price * $exchange_rate    ?? '',
				   'price' =>(double) number_format(((double) $this->product->price * $exchange_rate), 2, '.', '')  ?? '',
				   'discount_price' =>(double) number_format(((double) $this->product->discount_price * $exchange_rate), 2, '.', '')  ?? '',

                  // 'final_price' =>  $final_price  ?? '',
                   'sub_total_price' =>  $total_price  ?? '',
				 // 'product' => [
					'name' => $this->product->$name,
					'img'=>  asset('assets/images/products/'.$this->product->img) ?? '',
				//],


					// 'currency' => new CurrencyResource($currency) ?? [],
					 // 'currency' => $currency_con!=null ? $currency_con:'' ,





 			];
    }
}

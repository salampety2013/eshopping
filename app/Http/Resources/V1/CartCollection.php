<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Models\Currency;
use App\Models\Setting;
use App\Models\Cart;
use Auth;

class CartCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {



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





       return [
       //  'data' => $this->collection,
        'cart' => CartResource::collection($this->collection),
        'currency' => $currency_con!=null ? $currency_con:[],
        'items_count' => $this->collection->count() ,




    ];
    }



      /**
     * Get additional data that should be returned with the resource array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

}

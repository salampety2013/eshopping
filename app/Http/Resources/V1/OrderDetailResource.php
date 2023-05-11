<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\OrdersProduct;

class OrderDetailResource extends JsonResource
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

		//dd($name);


$order_id = $this->id;
		//-------------------------get rating and reviews--------------"

		$orders_products = OrdersProduct:: select('product_id', 'name', 'img','price','size','color','quantity')
	 ->where('order_id', $order_id)->orderBy('created_at', 'Asc')->get();
		// return  $ratings->count();
		//if(($ratings->count())> 0){
        if (!empty($orders_products)) {

            //------------------------------

            //------------------------------
            foreach ($orders_products as $product) {
                $item = array();
                //$item['rating_value'] =12;
                $item['price'] = (float)$product->price ?? 0;
                $item['name'] = $product->name ?? '';
                $item['size'] = $product->size ?? '';
                $item['color'] = $product->color ?? '';

                $item['quantity'] = $product->quantity ?? 0;
                if ($product->img != null && $product->img) {
                    $item['img'] = asset('assets/images/products/'. $product->img) ?? '';
                } else {
                    $item['img'] = asset('images/noimage.png');
                }
                $products[] = $item;
                // }
            }
        }
	 return [
        'id'=>$this->id ?? '',
        'name'=>  $this ->name ?? '',
        'pay_type'=>  $this->pay_type ?? '',
        'sub_total'=>  (float)$this->sub_total ?? 0,
        'grand_total'=> (float) $this->grand_total ?? 0,
        'date'=>  $this->created_at ?? '',
        'order_status'=>  $this->order_status ?? '',
             'currency_code'=>  $this->currency_code ?? '',
             'currency_symbol'=>  $this->currency_symbol ?? '',
             'shipping_charges'=>  $this->shipping_charges ?? '',
             'coupon_code'=>  $this->coupon_code ?? '',
             'coupon_amount'=>(float)$this->coupon_amount ?? 0,
           //  'Vat'=>  $this->tax ?? '',
             'Vat_amount'=>  (float)$this->tax_amount ?? 0,
             'address'=>  ($this->country  ?? '') .",". ($this->city   ?? '').",".($this->pincode   ?? '').",". ($this->address   ?? '') ?? '',


             'order_products'=>$products ?? [],

			];

    }
}

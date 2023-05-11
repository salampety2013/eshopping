<?php

namespace App\Http\Resources\V1;
use App\Models\Currency;


use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
		$description=(string)'details_'.$lang;
		$slug=(string)'slug'.$lang;

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
		//dd($name);
	 return [
	    	'id'=>$this->id,
             'name'=> $this->$name ?? '' ,
             'description'=> $this->$description ?? '',
             'name'=> $this->$name ?? '',

            "slug"=>  $slug  ?? '',
            'img'=>  asset('assets/images/products/'.$this->img) ?? '',
  			 'cat_id' => $this->cat_id  ?? '',
				 'sub_cat_id' => $this->sub_cat_id  ?? '',
				 'brand_id' => $this->brand_id  ?? '',
				 'total_quantity' =>(int) $this->total_quantity,
				 'code' => $this->code,
				  'price' =>(double) number_format(((double) $this->price * $exchange_rate), 2, '.', '')  ?? '',
				   'discount_price' =>(double) number_format(((double) $this->discount_price * $exchange_rate), 2, '.', '')  ?? '',
				   //'discount_price' =>round((double) $this->discount_price * $exchange_rate) ?? '',
					 'new_trends' => $this->new_trends ?? '',
					// 'text' => $this->text !== null ? $this->text : '',
 					//'text' => $this->text ?? ''
					 'new_arrival' => $this->new_arrival ?? '' ,
					 'flash_sale' => $this->flash_sale ?? '',
					// 'exchange_rate' => $exchange_rate ?? '' ,
					// 'currency' => new CurrencyResource($currency) ?? [],
					// 'currency' => $currency_con!=null ? $currency_con:'' ,



					/*  'links' => [
                       // 'products' => route('api.products.category', $data->id),
                        'product_details' => url('api/v1/get-product-details/?id='.$this->id)
                    ] */

 			];
    }
}
//You can solved with your's database structure ;
//https://stackoverflow.com/questions/50088688/laravel-how-to-set-my-resource-to-give-empty-string-instead-of-null
//$table->string('person')->default('');


/* public function toArray($request)
{

   $product = $this->product;
   $brand = $product->brand;
    $category = $product->category;
    $favorite = 0;
    if(Auth::check()){
        $favorite = favorite::where('product_id', $product->id)->where('user_id', Auth::user()->id)->count();
    }

    $media = $this->products_media;
    $discount = $product->discount;

    return [

        'id'         => $product->id,

        'name'       => request()->lang == 'ar'? $product->name_ar : $product->name_en,

        'brand'      => !empty($brand) > 0? $brand->name_en: '',

        'category'   => !empty($category) > 0? $category->name_en: '',

        'price'      => (number_format($this->price ?? 0,3)) ?? '--',

        'status'     => $this->status ?? "",

        'stockStatus'=> $product->product_option->sum('quantity') > 0 ? 1 : 0,

        'isFavorite' => $favorite ?? 0,

        'discount'   => $discount->count() > 0 ? $discount->last()->discount : 0,

        'image'      => isset($media->media_path)? url('/') . $media->first()->media_path : '' ?? '',


    ];
}
 */

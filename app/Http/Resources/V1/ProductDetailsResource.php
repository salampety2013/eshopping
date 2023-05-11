<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Models\Rating;
// use App\Models\ColorProductSize;
use App\Models\Size;
use App\Models\Color;
use App\Models\Cart;
use App\Models\ProPic;
use App\Models\Currency;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;
use Session;
use Auth;




class ProductDetailsResource extends JsonResource
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
		$lang = app()->getLocale();
		$name = (string)'name_' . $lang;
		$description = (string)'details_' . $lang;
		$slug = (string)'slug' . $lang;
		//dd($name);

		$product_id = $this->id;

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
		//-------------------------get rating and reviews--------------"

		$ratings = Rating::with(['user' => function ($q) {
			$q->select('id', 'name', 'img')->where('status', 1);
		}])->where('status', 1)->select('id', 'user_id', 'rating_value', 'review', 'created_at')->where('product_id', $product_id)->orderBy('created_at', 'Asc')->get();
		// return  $ratings->count();
		//if(($ratings->count())> 0){
		if (!empty($ratings)) {

			//------------------------------
			$ratings_average = collect($ratings)->average("rating_value");
			//------------------------------
			foreach ($ratings as $rate) {

				$item = array();
				//$item['rating_value'] =12;
				$item['rating_value'] = (float)$rate->rating_value ?? '';
				$item['review'] = $rate->review ?? '';
				$item['created_at'] = $rate->created_at ?? '';

				$item['user_name'] = $rate->user->name ?? '';
				if ($rate->user->img != null && $rate->user->img) {
					$item['user_photo'] = asset('assets/images/members/' . $rate->user->img) ?? '';
				} else {
					$item['user_photo'] = asset('images/noimage.png');
				}
				$reviews[] = $item;
				// }
			}
		}/*else{    // end if

			 $reviews=[];
			} */

		//-----------------------------end rating--------------------

		//-------------------------get images of that product--------------"

		$photos = ProPic::where('pro_id', $product_id)->orderBy('created_at', 'Asc')->get('img');



		foreach ($photos as $image) {
			// if($rate->user->img != null && $rate->user->img){
			$items = array();


			if ($image->img != null && $image->img)

				$items['img'] = $image->img  ??  asset('images/noimage.png');
				// $items['img'] = asset('assets/images/products/' . $image->img) ??  asset('images/noimage.png');

			$images[] = $items;
			// }
		}
		//-----------------------------end images--------------------


		//-------------------------get   varient sizes and color and quantity   --------------"

		$product_sizes = DB::table('color_product_size')->where('product_id', $product_id)
			->leftJoin('products', 'products.id', '=', 'color_product_size.product_id')
			->leftJoin('sizes', 'sizes.id', '=', 'color_product_size.size_id')

			->leftJoin('colors', 'colors.id', '=', 'color_product_size.color_id')
			->select('color_product_size.id', 'color_product_size.product_id', 'color_product_size.quantity', 'color_product_size.size_id', 'color_product_size.color_id', 'products.name_ar as product_name', 'sizes.name_ar as size_name', 'colors.name_en as color_name', 'colors.code as color_code')->get();


		foreach ($product_sizes as $value) {
			// if($rate->user->img != null && $rate->user->img){
			$item = array();
			$item['size_id'] = $value->size_id ?? '';
			$item['size_name'] = $value->size_name ?? '';
			$item['quantity'] = $value->quantity ?? '';
			$colors_ids = $value->color_id;

			//--------get colors of every size of product-----------------------------
			//if ($colors_ids != "") {
				$colors_ids = explode(",", $colors_ids);

				$colors = array();
				foreach ($colors_ids as $product_color) {
					// echo "<br>".$product_color;

					if ($product_color != "") {
						$color_name =  Color::where('id', $product_color)->select('code', 'id', 'name_en')->first();

						$colors[] = array(
							'id' => $color_name->id,
							'name' => $color_name->name_en,
							'code' => $color_name->code

						);
					}
				}

				$item['colors'] = $colors ?? [];
			//}
			//  $item['color_name'] = $value->color_name ?? '';


			$units_colors[] = $item;
			// }
		}
		//-----------------------------end rating--------------------




		return [
			'id' => $this->id,
			'name' => $this->$name ?? '',
			'description' => $this->$description ?? '',
			// 'name'=> $this->$name ?? '',

			"slug" =>  $slug  ?? '',
			'main_image' =>  asset('assets/images/products/' . $this->img) ?? '',
			"images" =>  $images  ?? [],

			'cat_id' => $this->cat_id  ?? '',
			'sub_cat_id' => $this->subcategory_id  ?? '',
			'brand_id' => $this->brand_id  ?? '',
			'total_quantity' => (int) $this->total_quantity,
			'units_colors' => $units_colors  ?? [],

			'code' => $this->code,
			'price' =>(double) number_format(((double) $this->price * $exchange_rate), 2, '.', '')  ?? '', 
			'discount_price' =>(double) number_format(((double) $this->discount_price * $exchange_rate), 2, '.', '')  ?? '',
			//'discount_price' =>round((double) $this->discount_price * $exchange_rate) ?? '',
			 
			'new_trends' => $this->new_trends ?? '',
			// 'text' => $this->text !== null ? $this->text : '',
			//'text' => $this->text ?? ''
			'new_arrival' => $this->new_arrival ?? '',
			'flash_sale' => $this->flash_sale ?? '',

			'total_rating' => (float)$ratings_average,

			'reviews' => $reviews ?? [],
			'currency' => $currency_con!=null ? $currency_con:[],

		];
	}
}

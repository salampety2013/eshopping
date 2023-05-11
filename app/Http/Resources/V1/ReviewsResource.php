<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Models\Rating;
use App\Models\User;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;
use Session;
use Auth;




class ReviewsResource extends JsonResource
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


		$product_id = $this->product_id;
		//-------------------------get rating and reviews--------------"


		// return  $ratings->count();
		//if(($ratings->count())> 0){


			//------------------------------
		//	$ratings_average = collect($ratings)->average("rating_value");
			//------------------------------

				//$item['rating_value'] =12;
				   //$user=User::select('id', 'name', 'img')->where('id',$this->user_id)->get();
				$user_name  = $this->user->name ?? '';
				if ($this->user->img != null && $this->user->img) {
					 $user_photo  = asset('assets/images/members/'. $this->user->img) ?? '';
				} else {
					$user_photo = asset('images/noimage.png');
				}



		//-----------------------------end rating--------------------





		return [
            'rating_value'  =>(float)$this->rating_value ?? '',
           'review'  =>$this->review ?? '',
           'created_at'  =>$this->created_at ?? '',
			//'total_rating' => (float)$ratings_average,
			'user_name' => $user_name,
			'user_photo' => $user_photo,



		];
	}
}

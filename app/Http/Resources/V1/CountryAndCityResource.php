<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class CountryAndCityResource extends JsonResource
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
	 return [
	    	'id'=>$this->id,
             'name'=> $this->$name,
 			// 'SubCategories'=>SubCategoriesResource::collection($this->subcategories)
			 'cities'=>cityResource::collection($this->whenLoaded('cities')),
			 /* 'links' => [
                       // 'products' => route('api.products.category', $data->id),
                        'sub_categories' => url('api/v1/get-sub-categories/?cat_id='.$this->id)
                    ] */
 			];
    }
}

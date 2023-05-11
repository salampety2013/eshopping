<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoriesResource extends JsonResource
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
		$cat_id=$this->id;
		//dd($name);
	 return [
	    	'id'=>$this->id,
             'name'=> $this->$name,
           
           // "slug_ar"=>  $this->slug_ar,,
          //  "slug_en"=> $this->slug_en,,
            'img'=>  asset('assets/images/category/'.$this->img),
			//'created_at' => (string) $this->created_at,
       // 'updated_at' => (string) $this->updated_at,
		
		'links' => [
                       // 'products' => route('api.products.category', $data->id),
                        'sub_categories' => url('api/v1/get-sub-categories/?cat_id='.$cat_id)
                    ]
 			];
    }
}

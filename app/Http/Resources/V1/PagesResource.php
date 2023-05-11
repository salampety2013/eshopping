<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class PagesResource extends JsonResource
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
       $title=(string)'title_'.$lang;
       $details=(string)'description_'.$lang;




		//dd($name);
	 return [
	    	'id'=>$this->id,
            'name'=> $this->$title ?? '' ,
             // 'details'=>$this-> $details ?? '',
            'details'=>nl2br($this-> $details) ?? '',
           // "slug_ar"=>  $this->slug_ar,,
          //  "slug_en"=> $this->slug_en,,

			];

    }
}

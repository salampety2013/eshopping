<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
       
	 return [
	    	'id'=>$this->id,
             'name'=> $this->name,
             'country_id'=>(int) $this->country_id ?? '',
             'city_id'=> (int)$this->city_id ?? '', 
             'mobile'=> $this->mobile ?? '',
             'pincode'=> (int)$this->pincode ?? '',
             'address'=> $this->address ?? '',
             'notes'=> $this->notes ?? '',
             'user_id'=>(int) $this->user_id ?? '',
             'default_address'=> $this->default_address ?? '',
              
            
			];

    }
}

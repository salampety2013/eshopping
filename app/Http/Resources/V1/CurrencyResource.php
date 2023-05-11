<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class CurrencyResource extends JsonResource
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

	    	'id'=>$this->id ?? '',
             'name'=> $this->$name ?? '',

            'currency_code'=>  $this->code  ?? '',
            'currency_symbol'=>  $this->symbol ?? '',
            'exchange_rate'=> (double) $this->exchange_rate ?? 1,
           // 'tax_value'=> (double)  $this->tax_value  ?? 0,

           'img'=>  asset('assets/images/currencies/'.$this->img)  ??  asset('images/noimage.png')
			];

    }
}

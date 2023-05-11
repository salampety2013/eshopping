<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\V1\OrderResource;

class OrderCollection  extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {



   return [
        'orders' =>  OrderResource::collection($this->collection),
        /* 'pagination' => [
            "current_page" => $this->currentPage(),
            "first_page_url" =>  $this->getOptions()['path'].'?'.$this->getOptions()['pageName'].'=1',
            "prev_page_url" =>  $this->previousPageUrl(),
            "next_page_url" =>  $this->nextPageUrl(),
            "last_page_url" =>  $this->getOptions()['path'].'?'.$this->getOptions()['pageName'].'='.$this->lastPage(),
            "last_page" =>  $this->lastPage(),
            "per_page" =>  $this->perPage(),
            "total" =>  $this->total(),
            "path" =>  $this->getOptions()['path'],
        ], */
    ];
    }



      /**
     * Get additional data that should be returned with the resource array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

}

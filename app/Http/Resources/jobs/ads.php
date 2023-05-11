<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ads extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'url'=>$this->url,
            'image'=>$this->image
        ];
    }
}

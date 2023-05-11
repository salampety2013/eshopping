<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class company extends JsonResource
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
            'apiToken'=>$this->apiToken,
            'name'=>$this->name,
            'email'=>$this->email,
            'logo'=>$this->logo,
            'jobs'=>Job::collection($this->whenLoaded('jobs'))
        ];
    }
}

<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
       // return parent::toArray($request);
       return [
        // 'data' => $this->collection,
        'data' => ProductResource::collection($this->collection),
       // 'meta' => ['products_count' => $this->collection->count()],
    ];
    }



      /**
     * Get additional data that should be returned with the resource array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function with($request)
    {
        /* return [
            'meta' => [
                'key' => 'value',
            ],
        ]; */

        return [
            'status' => true,
            'errNum' => "S000",
            'msg' => ' ',
           
        ];

    }
}

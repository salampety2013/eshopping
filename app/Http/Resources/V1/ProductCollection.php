<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Models\Currency;


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

       // $pagination = new PaginationResource($this);
       // return parent::toArray($request);

//----------------get currency value -----------------------

	 
$currency_id = $request->currency_id ?? 1;
$currency=Currency::where('id',$currency_id)->where('status',1)->first();
if(!$currency){
   $exchange_rate =1;
   //$currency  == null ? $currency : [];
    
} else{
   $exchange_rate = (double)$currency->exchange_rate ?? 1 ;
}

$currency_con=new CurrencyResource($currency) ;
  
//------------------------




       return [
       //  'data' => $this->collection,
        'products' => ProductResource::collection($this->collection),
        'currency' => $currency_con!=null ? $currency_con:[],
       // 'meta' => ['products_count' => $this->collection->count()],
       'links'  => [
        "current_page" => $this->currentPage(),
       // "first_page_url" =>  $this->getOptions()['path'].'?'.$this->getOptions()['pageName'].'=1',
       // "prev_page_url" =>  $this->previousPageUrl(),
      //  "next_page_url" =>  $this->nextPageUrl(),
      //  "last_page_url" =>  $this->getOptions()['path'].'?'.$this->getOptions()['pageName'].'='.$this->lastPage(),
        "last_page" =>  $this->lastPage(),
        "per_page" =>  $this->perPage(),
        "total" =>  $this->total(),
        "path" =>  $this->getOptions()['path'],
       ],
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

<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Helpers\ResourcePaginationHelper;
class CategoriesCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $lang=app()->getLocale();
		$name=(string)'name_'.$lang;
		 

        
         
        return [
            'categories'=> $this->collection->map(function ($data,$name ){

                return[
                    'id'         => $data->id,
                    'name'       => $name,
                    'img'=>  asset('assets/images/category/'.$data->img),
               
                ];
            }),

           
           // 'links' => ResourcePaginationHelper::generateLinks($this, 'getMainCategories'),
           'links'  => [
            "current_page" => $this->currentPage(),
            "first_page_url" =>  $this->getOptions()['path'].'?'.$this->getOptions()['pageName'].'=1',
            "prev_page_url" =>  $this->previousPageUrl(),
            "next_page_url" =>  $this->nextPageUrl(),
            "last_page_url" =>  $this->getOptions()['path'].'?'.$this->getOptions()['pageName'].'='.$this->lastPage(),
            "last_page" =>  $this->lastPage(),
            "per_page" =>  $this->perPage(),
            "total" =>  $this->total(),
            "path" =>  $this->getOptions()['path'],
            
         ],
        ];


    }
   
       public function with($request)
       {
           return [
               'success' => true,
               'status' => 200
           ];
       }
	   
    
}

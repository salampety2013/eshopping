<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ResourcePaginationHelper  
{
    public static function generateLinks (ResourceCollection $resouceCollection, string $routeName)
    {
        $totalPages = $resouceCollection->total();
        $nextPrevPages = self::getNextPrevPages();

        $paginationData = [
            'total' => $totalPages,
            'links' => [],
            'next' => route($routeName, 'page='.$nextPrevPages['next']),
            'prev' => $nextPrevPages['prev'] != null ? route($routeName, 'page='.$nextPrevPages['prev']): null
        ];


        for($i = 1; $i <= $totalPages; $i++)
        {
            $paginationData['links']["$i"] = route($routeName, 'page='.$i);


            if($i == 1)
                $paginationData['first_page'] =  route($routeName, 'page='.$i);

            if($i == $totalPages)
                $paginationData['last_page'] =  route($routeName, 'page='.$i);
        }

        return $paginationData;
    }


    public static function getNextPrevPages()
    {
        $nextPrevPages = ['prev' => null, 'next' => 1];

        if(isset($_GET['page']))
        {
            $nextPrevPages['next'] = $_GET['page'] + 1;
            $nextPrevPages['prev'] = $_GET['page'] == 1 ? null : $_GET['page'] - 1;
        }

        return $nextPrevPages ;
    }
     
}

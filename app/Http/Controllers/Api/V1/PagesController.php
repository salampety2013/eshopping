<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Models\Page;

use App\Traits\GeneralTrait;
use Illuminate\Http\Request;


//use App\Http\Resources\V1\CategoriesCollection;
use App\Http\Resources\V1\CategoriesResource;
use App\Http\Resources\V1\PagesResource;

class PagesController extends Controller
{
    use GeneralTrait;



    public function getPages(Request $request)
    {
  try {
    $page =  Page::where('id',$request->id)->where('status',1)->first();
       if (!$page)
           //  return $this->returnError('001', 'هذا القسم غير موجود');
           return $this->returnError('202', __('general.not found'));

        return $this->returnData('data',new PagesResource($page));

		} catch (\Exception $e) {
            // return $this->returnError(201, $e->getMessage());
           return $this->returnError(201, 'something went wrong');
        }

    }




}

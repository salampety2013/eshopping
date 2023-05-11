<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;


use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Validator;


use App\Http\Resources\V1\BrandCollection;
use App\Http\Resources\V1\BrandsResource;
use App\Http\Resources\V1\ProductResource;
use App\Http\Resources\V1\ProductDetailsResource;



use App\Models\Brand;
use App\Models\SubCategory;
use App\Models\Product;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;
use Session;
use Auth;


class BrandsController extends Controller
{
  use GeneralTrait;

  public function index(Request $request)
  {

    try {

       // $brands = Brand::where('is_active', 1)->inRandomOrder()->get() ;
        $brands = Brand::where('is_active', 1)->inRandomOrder()->paginate(2) ;
        if (!$brands)
            //  return $this->returnError('001', 'هذا القسم غير موجود');
            return $this->returnError('202', __('general.not found'));
      //if (!$category)
      //  return $this->returnError('001', 'هذا القسم غير موجود');
    //  return response()->json([ 'data' => new BrandCollection( Brand::where('is_active', 1)->inRandomOrder()->paginate(2) ), 'success' => true ], 200 );
   //  return new BrandCollection($brands);
    // return $this->returnData('data',  BrandsResource::collection($brands));
      
      return $this->returnData('data',  new BrandCollection($brands));
      // return $this->returnData('data',new ProductResource($product));

    } catch (\Exception $e) {
      return $this->returnError(201, $e->getMessage());
    }
  }


  public function getBrandProducts(Request $request)
  {

    try {
      $id=$request->id;
      $currency_id=$request->currency_id;
              
        $rules = [

                'id' => 'required|numeric|exists:brands,id',
                'currency_id' => 'required|numeric|exists:currencies,id',
               ];

        $validator = Validator::make($request->all(), $rules);
//return $id;
        if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);
            // return $this->returnValidationErrorAll('422',$validator);

        }

        $brand = Brand::where('id',$request->id)->where('is_active', 1)->first() ;
        if (!$brand)
            //  return $this->returnError('001', 'هذا القسم غير موجود');
            return $this->returnError('202', __('general.not found'));
      

      $products = Product::where('brand_id',$request->id)->where('is_active', 1)->inRandomOrder()->paginate(2); //improve select only required fields



      return $this->returnData('data',  ProductResource::collection($products));
      // return $this->returnData('data',new ProductResource($product));

    } catch (\Exception $e) {
      return $this->returnError(201, $e->getMessage());
    }
  }










}

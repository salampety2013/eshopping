<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Product;

use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Validator;

//use App\Http\Resources\V1\CategoriesCollection;
use App\Http\Resources\V1\CategoriesResource;
use App\Http\Resources\V1\SubCategoriesResource;
use App\Http\Resources\V1\ProductResource;
use App\Http\Resources\V1\ProductCollection;

class SubCategoriesController extends Controller
{
    use GeneralTrait;

    public function index(Request $request)
    {
    // $lang= $request->lang;
  //  dd($lang);
	   try {
		   // $categories = SubCategory::paginate(2);
			 $subcategories = SubCategory::where('is_active',true)->get();
		//$subcategories = SubCategory::select('id','name_'.app()->getLocale().' as name','img')->get();




			//return response()->json($subcategories);

      //  return $this -> returnData('data',$subcategories);

 	 return $this -> returnData('data', SubCategoriesResource::collection($subcategories));

           // return $this->returnData('data', $managers);
        } catch (\Exception $e) {
            return $this->returnError(201, $e->getMessage());
        }


    }




 public function getSubCategoriesofcats(Request $request)
 {
 // $lang= $request->lang;
//  dd($lang);
    try {
        // $categories = SubCategory::paginate(2);
          $subcategories = SubCategory::where('is_active',true)->where('cat_id',$request->cat_id)->get();
     //$subcategories = SubCategory::select('id','name_'.app()->getLocale().' as name','img')->get();




         //return response()->json($subcategories);

   //  return $this -> returnData('data',$subcategories);
  // return BookResource::collection(Book::with('ratings')->paginate(25));
 //     return AlbumResource::collection(Album::where('user_id', $request->user()->id)->paginate());
   return $this -> returnData('data', SubCategoriesResource::collection($subcategories));

        // return $this->returnData('data', $managers);
     } catch (\Exception $e) {
         return $this->returnError(201, $e->getMessage());
     }


 }

 /* public function getSubCategoryById(Request $request)
 {
try {
     $subcategory = SubCategory::find($request->id);
    if (!$subcategory)
        //  return $this->returnError('001', 'هذا القسم غير موجود');
        return $this->returnError('202', __('general.not found'));

     return $this->returnData('data',new SubCategoriesResource($subcategory));

     } catch (\Exception $e) {
         // return $this->returnError(201, $e->getMessage());
        return $this->returnError(201, 'something went wrong');
     }

 }
 */

//------------------------------------------------------------------
 public function getsubCategeoryProducts(Request $request)
  {

    try {
      $currency_id=$request->currency_id ?? 1;
              
      $rules = [

              'id' => 'required|numeric|exists:sub_categories,id',
              'currency_id' => 'required|numeric|exists:currencies,id',
             ];

      $validator = Validator::make($request->all(), $rules);
//return $id;
      if ($validator->fails()) {
          $code = $this->returnCodeAccordingToInput($validator);
          return $this->returnValidationError($code, $validator);
          // return $this->returnValidationErrorAll('422',$validator);

      }

      $sub_category = subCategory::where('id', $request->id)->where('is_active', 1)->first();
      if (!$sub_category)
        //  return $this->returnError('001', 'هذا القسم غير موجود');
        return $this->returnError('202', __('general.not found'));
      //if (!$category)
      //  return $this->returnError('001', 'هذا القسم غير موجود');

      $products = Product::where('sub_cat_id', $request->id)->where('is_active', 1)->latest()->paginate(2); //improve select only required fields



      //  return $this->returnData('data',  ProductResource::collection($products));
     return $this->returnData('data',new ProductCollection($products));

    } catch (\Exception $e) {
      return $this->returnError(201, $e->getMessage());
    }
  }



}

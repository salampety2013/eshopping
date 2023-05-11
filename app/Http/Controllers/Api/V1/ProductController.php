<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;


use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Validator;

use App\Http\Resources\V1\SubCategoriesResource;
use App\Http\Resources\V1\CategoriesResource;

use App\Http\Resources\V1\BrandsResource;
use App\Http\Resources\V1\ProductCollection;
use App\Http\Resources\V1\ProductResource;
use App\Http\Resources\V1\ProductDetailsResource;
use App\Http\Resources\V1\CurrencyResource;


use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Currency;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;
use Session;
use Auth;
use App\Http\Requests\RatingRequest;

class ProductController extends Controller
{
  use GeneralTrait;

//----------------------- شرح كل api resources and collection with paginaton-- ------- ----
//http://semantic-portal.net/concept:686

//--------------------------------------------------------------------------------
public function custom_pagination_search(Request $request)
    {
        $query = Product::query();

        if ($s = $request->input('s')) {
            $query->whereRaw("name_ar LIKE '%" . $s . "%'")
                ->orWhereRaw("description_ar LIKE '%" . $s . "%'");
        }

        if ($sort = $request->input('sort')) {
            $query->orderBy('price', $sort);
        }

        $perPage = 9;
        $page = $request->input('page', 1);
        $total = $query->count();

        $result = $query->offset(($page - 1) * $perPage)->limit($perPage)->get();

        return [
            'data' => $result,
            'total' => $total,
            'perPage' => $perPage,
             
            'page' => $page,
            'last_page' => ceil($total / $perPage)
        ];
    }




//---------------------get products in brands or categories or subcategories-------- --------
public function getAllProducts(Request $request)
  {

    try {
      $currency_id=$request->currency_id ?? 1;
              
      $rules = [

             // 'id' => 'required|numeric|exists:sub_categories,id',
              'currency_id' => 'required|numeric|exists:currencies,id',
             ];

      $validator = Validator::make($request->all(), $rules);
//return $id;
      if ($validator->fails()) {
          $code = $this->returnCodeAccordingToInput($validator);
          return $this->returnValidationError($code, $validator);
          // return $this->returnValidationErrorAll('422',$validator);

      }
      //-----------------------------------------
      $per_page=($request->per_page ? $request->per_page : 10 );

     $type= $request->type;
     if($type=="categories"){ 


    $category = Category::where('id', $request->id)->where('is_active', 1)->first();
    if (!$category)
            return $this->returnError('202', __('general.not found'));

            $products = Product::where('cat_id',$request->id)->where('is_active', 1)->inRandomOrder()->paginate($per_page); //improve select only required fields
     
}else if($type=="subcategories"){

     $subcategory = subCategory::where('id', $request->id)->where('is_active', 1)->first();
     $products = Product::where('sub_cat_id',$request->id)->where('is_active', 1)->inRandomOrder()->paginate($per_page); //improve select only required fields

     if (!$subcategory)
        return $this->returnError('202', __('general.not found'));
   
}   if($type=="brands"){ 
 
        $brand = Brand::where('id',$request->id)->where('is_active', 1)->first() ;

        if (!$brand)
            //  return $this->returnError('001', 'هذا القسم غير موجود');
            return $this->returnError('202', __('general.not found'));  
                 $products = Product::where('brand_id',$request->id)->where('is_active', 1)->inRandomOrder()->paginate($per_page); //improve select only required fields

   }    
  // return  new ProductCollection($products) ;


      return $this->returnData('data', new ProductCollection($products));
     // return $this->returnData('data',  ProductResource::collection($products));
 
    } catch (\Exception $e) {
      return $this->returnError(201, $e->getMessage());
    }
  }




  //---------------------------getProductDetails-----------------------------------------
   //--------------------------------------------------------------------
  public function getProductDetails(Request $request)
  {
    // $lang= $request->lang;
    //  dd($lang);
    try {


      $currency_id=$request->currency_id ?? 1;
              
      $rules = [

              'id' => 'required|numeric|exists:products,id',
              'currency_id' => 'required|numeric|exists:currencies,id',
             ];

      $validator = Validator::make($request->all(), $rules);
//return $id;
      if ($validator->fails()) {
          $code = $this->returnCodeAccordingToInput($validator);
          return $this->returnValidationError($code, $validator);
          // return $this->returnValidationErrorAll('422',$validator);

      }

      $product = Product::where('id', $request->id)->where('is_active', 1)->first();  //improve select only required fields
      if (!$product)
        return $this->returnError('202', __('general.not found'));  ///  redirect to previous page with message



      return $this->returnData('data', new ProductDetailsResource($product));
      // return $this->returnData('data',new ProductResource($product));

    } catch (\Exception $e) {
      return $this->returnError(201, $e->getMessage());
    }
  }



//---------------------------getRelatedProducts-----------------------------------------
   //--------------------------------------------------------------------
  public function getRelatedProducts(Request $request)
  {
    // $lang= $request->lang;
    //  dd($lang);
    try {

      $currency_id=$request->currency_id ?? 1;
              
      $rules = [

               
              'id' => 'required|numeric|exists:products,id',
              'currency_id' => 'required|numeric|exists:currencies,id',
             ];

      $validator = Validator::make($request->all(), $rules);
//return $id;
      if ($validator->fails()) {
          $code = $this->returnCodeAccordingToInput($validator);
          return $this->returnValidationError($code, $validator);
          // return $this->returnValidationErrorAll('422',$validator);

      }
//---------------------------------------------

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

      $product = Product::where('id', $request->id)->where('is_active', 1)->first();  //improve select only required fields
      if (!$product)
        return $this->returnError('202', __('general.not found'));  ///  redirect to previous page with message


      $product_categories_ids = $product->sub_cat_id;

      $related_products  = Product::whereHas('subCategory', function ($cat) use ($product_categories_ids) {
        $cat->where('id', $product_categories_ids);
      })->limit(20)->latest()->get();


      //  $related_products  = Product:: where('sub_cat_id', $product_categories_ids) ->limit(20)->latest()->get();
      $data = [
                'products'=>ProductResource::collection($related_products),
                'currency'=>$currency_con,

         ];
      
      return $this->returnData('data',$data   );
      //return $this->returnData('data',  ProductResource::collection($related_products) );
    } catch (\Exception $e) {
      return $this->returnError(201, $e->getMessage());
    }
  }



//---------------------------search  Products-----------------------------------------
   //--------------------------------------------------------------------
  //--------------------------------------------------------------------

  public function search(Request $request)
  {

    try {
      //return $request->all();
      $currency_id=$request->currency_id ?? 1;
              
      $rules = [

               'brand_id' => 'nullable|exists:brands,id',
               'sub_cat_id' => 'nullable|exists:sub_categories,id',
              'currency_id' => 'required|numeric|exists:currencies,id',
             ];

      $validator = Validator::make($request->all(), $rules);
//return $id;
      if ($validator->fails()) {
          $code = $this->returnCodeAccordingToInput($validator);
          return $this->returnValidationError($code, $validator);
          // return $this->returnValidationErrorAll('422',$validator);

      }
//---------------------------------------------


    $per_page=($request->per_page ? $request->per_page : 10 );
	 $query = Product::query();  //instantiate a query and then build up conditions based on request variables.
     // $query = Product::where('is_active', "1");
      /*if($request->key_words==""){
        }*/


      if (!empty($request->key_words)) {


         $search = $request->key_words;

        /*$query =$query->where('name_ar','LIKE','%'.$search.'%')
           ->orWhere('name_en','LIKE','%'.$search.'%')
           ->orWhere('details_ar','LIKE','%'.$search.'%')
           ->orWhere('details_en','LIKE','%'.$search.'%')
           ->orWhere('price','LIKE','%'.$search.'%')
           ->orWhere('code','LIKE','%'.$search.'%');
         */


        //---------------------------
        $searchFields = ['name_ar', 'name_en', 'details_ar', 'details_en'];

        $query = $query->where(function ($query) use ($request, $searchFields) {

          $searchWildcard = '%' . $request->key_words . '%';

          foreach ($searchFields as $field) {
            //if(!empty($request->$field))
            //{
            $query->orWhere($field, 'LIKE', $searchWildcard);
            //}
          }
        });
      }
      //------------category------------------
      if (!empty($request->sub_cat_id)) {
          $sub_cats = $request->sub_cat_id;
        
          $query = $query->whereIn('sub_cat_id', $sub_cats);
      }
      /*$query2 = Product::where('color', 'blue')
->whereIn('value', ['Bomann', 'PHILIPS'])
->orWhere(function ($query) {
   $query->whereIn('value', ['Bomann', 'PHILIPS']);
})
->get();
Output:

select * from `products` where `color` = 'blue' and `value` in (Bomann, PHILIPS) OR (`value` in (red,white))
*/

      //--------brands-----------------
      if (!empty($request->brand_id)) {
          $brand_ids = $request->brand_id;
        /*$query= $query->orWhere(function ($q)use($brand_ids) {
   $q->whereIn('brand_id',$brand_ids);
});*/
        $query = $query->whereIn('brand_id', $brand_ids);
      }
      if (!empty($request->min_price)) {
        $min_price = $request->min_price;

        $query = $query->where('price', '>=', $min_price);
      }
      if (!empty($request->max_price)) {
        $max_price = $request->max_price;
        $query = $query->where(function ($q) use ($max_price) {
          $q->orwhere('price', '<=', $max_price)->orwhere('discount_price', '<=', $max_price);
        });
      }

      //------------------------------------
	   if ( empty($request->key_words)  && empty($request->sub_cat_id) && empty($request->brand_id) && empty($request->min_price)  && empty($request->max_price) )
return $this->returnSuccessMessages('No Result Found');

 
	$query=$query->where('is_active', "1");
           $products = $query->paginate($per_page);
     // $products = $query->get();
     
      /*$searchTerm ='milad zamir Abc';
$reservedSymbols = ['-', '+', '<', '>', '@', '(', ')', '~'];
$searchTerm = str_replace($reservedSymbols, ' ', $searchTerm);

$searchValues = preg_split('/\s+/', $searchTerm, -1, PREG_SPLIT_NO_EMPTY);

$res = User::where(function ($q) use ($searchValues) {
foreach ($searchValues as $value) {
$q->orWhere('name', 'like', "%{$value}%");
$q->orWhere('family_name', 'like', "%{$value}%");
}
})->get();*/

 
 
//return ProductResource::collection($products);
  // return new  ProductCollection($products);
/* return response()->json([
  'status' => true,
  'errNum' => "S000",
  'msg' => ' ',
  'data' => new  ProductCollection($products)
]); */

   //    return $this->returnData('products',  ProductResource::collection($products));
      return $this->returnData('data', new  ProductCollection($products));
     

    } catch (\Exception $e) {
      return $this->returnError(201, $e->getMessage());
    }
  }
}

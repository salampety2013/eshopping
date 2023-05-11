<?php

namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
 use Illuminate\Support\Carbon;
 
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;


use App\Http\Resources\V1\SubCategoriesResource;
use App\Http\Resources\V1\CategoriesResource;
use App\Http\Resources\V1\CatAndSubResource;
use App\Http\Resources\V1\BrandsResource;
use App\Http\Resources\V1\ProductResource;
 
 
 
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Currency;
use App\Models\Slider;
use Auth;
use Session;
use App\Models\Cart;
class HomeController extends Controller
{
 use GeneralTrait;
    public function home()
    {
		/*//return	$update_cart_session
		if(!empty(Session::get('session_id'))){
					 $user_id=Auth::user()->id;
					  $session_id=Session::get('session_id');
					 $update_cart_session=Cart::where('session_id',$session_id)->update(['user_id'=>$user_id]); 
				
					}*/
					
					////////////////end update/////////////////
        $data = [];
          $data['sliders'] = Slider::where('is_active',"1")->get(['img']);
		 ///$data['categories'] = Category::get();
		// return   $data['categories'] = Category::select('id', 'name_ar')->with('subcategories')->get(); 
             
     
    $data['categories'] = Category::select('id','name_ar','slug_ar')->with(['subcategories' =>function($q) {
		 $q->select('id','name_ar','slug_ar','cat_id')-> where('is_active',"1"); }])-> where('is_active',"1")->get();
           
             
      
        /* $data['categories'] = Category::parent()->select('id', 'slug')->with(['childrens' => function ($q) {
            $q->select('id', 'parent_id', 'slug');
            $q->with(['childrens' => function ($qq) {
                $qq->select('id', 'parent_id', 'slug');
            }]);
        }])->get();*/
		
		// $data['new_arrivals'] = product::where(['is_active'=>"1" ,'new_arrival'=>"new_arrival"] )->limit(12)-> latest()->get();
		  $new_arrivals_chunk = product::with('pics')->where(['is_active'=>"1" ,'new_arrival'=>"new_arrival"] )->limit(12)-> latest()->get()->toArray();
		  $data['new_arrivals_chunk']=array_chunk($new_arrivals_chunk,2);
		 $data['new_trends'] = product::where(['is_active'=>"1" ,'new_trends'=>"new_trends"] )->limit(12)-> latest()->get();

		     $data['flash_sale'] = product::where(['is_active'=>"1" ,'flash_sale'=>"flash_sale"] )
			->where('start_date', '<=',Carbon::now()) ->where('end_date', '>=', Carbon::now())->limit(10)-> latest()->get();
   
			 
/*Reservation::whereBetween('reservation_from', [$from1, $to1])
  ->orWhereBetween('reservation_to', [$from2, $to2])
  ->whereNotBetween('reservation_to', [$from3, $to3])
  ->get();
  
  $products = Sale::with('products')
    ->whereBetween('date',[$from_date, $to_date])
    ->where(function ($query) use ($search) {
    $query->where('employee_id', 'like', '%'.$search.'%')
          ->orWhere('employee_name', 'like', '%'.$search.'%');
    })
    ->get();
return response()->json($products);
  
  */
  
/*// Retrieve posts with at least one comment containing words like code%...
$posts = Post::whereHas('comments', function (Builder $query) {
    $query->where('content', 'like', 'code%');
})->get();
 
// Retrieve posts with at least ten comments containing words like code%...
$posts = Post::whereHas('comments', function (Builder $query) {
    $query->where('content', 'like', 'code%');
}, '>=', 10)->get();    */
     // $data['sub_categories'] = SubCategory:: with(['products' =>function($q) {
		 // $q -> where('is_active',"1")-> inRandomOrder()->limit(2); }])-> where('is_active',"1")-> inRandomOrder()->limit(3)->get();
 
//get 3 subcategory that has at least >= 2 products inrondom order
$data['sub_categories'] = SubCategory::whereHas('products', function ($query) {
      $query-> where('is_active',"1")-> inRandomOrder()->limit(2); }, '>=', 2)->where('is_active',"1")-> inRandomOrder()->limit(3)->get();
   


 
     // $data['sub_categories'] = SubCategory::select('id as subct_id','name_ar as subct_name_ar','name_en as subct_name_en','slug_ar as subct_slug_ar')->with(['products' =>function($q) {
		//  $q->select('id' ,'name_ar','name_en' ,'cat_id','sub_cat_id','price','discount_price')-> where('is_active',"1"); }])-> where('is_active',"1")->latest()->limit(3)->get();


  // $data['categories'] = Category::with(['subcategories' =>function($q) {
		// $q-> where('is_active',"1"); }])-> where('is_active',"1")->limit(3)->get();
     
	    $data['categories'] = Category::whereHas('subcategories',function($q) {
		 $q-> where('is_active',"1"); })-> where('is_active',"1")->get();

	  
      $data['brands']=Brand::where('is_active',"1")->latest()->inRandomOrder()->get();
     // $data['brands']=Brand:: whereHas ('products')->where('is_active',"1")->latest()->inRandomOrder()->get();
        return view('front.home', $data);
    }
	
	
	
	
public function homeCats(Request $request)
    {
    // $lang= $request->lang;
  //  dd($lang);
	   try { 
	   $sliders= Slider::where('is_active',"1")->get(['img']);
			 $categories =Category::select('id','name_ar','name_en','slug_ar')->with(['subcategories' =>function($q) {
		 $q->select('id','name_ar','name_en','slug_ar','cat_id')-> where('is_active',"1"); }])-> where('is_active',"1")->get();
           

     /*    $data = [];
          $data['sliders'] = Slider::get(['img']);
			 $data['categories'] = Category::where('is_active',true)->get();

 $data = [
'sliders'=>$sliders,
 'categories'=>$categories,

 ];
*/
			 $subcategories = SubCategory::where('is_active',true)->where('cat_id',$request->cat_id)->get();
			       $brands=Brand::where('is_active',"1")->latest()->inRandomOrder()->get();
     // $data['brands']=Brand:: whereHas ('products')->where('is_active',"1")->latest()->inRandomOrder()->get();
 $category_menu = ['categories'=>CatAndSubResource::collection($categories),

 ];
 
 
 
 // $new_arrivals_chunk = product::with('pics')->where(['is_active'=>"1" ,'new_arrival'=>"new_arrival"] )->limit(12)-> latest()->get()->toArray();
		 // $data['new_arrivals_chunk']=array_chunk($new_arrivals_chunk,2);
		$new_trends = product::where(['is_active'=>"1" ,'new_trends'=>"new_trends"] )->limit(12)-> latest()->get();

		     $flash_sale = product::where(['is_active'=>"1" ,'flash_sale'=>"flash_sale"] )
			->where('start_date', '<=',Carbon::now()) ->where('end_date', '>=', Carbon::now())->limit(10)-> latest()->get();
   
 
 
 

 $data = [
'sliders'=>$sliders,
 'menu'=>$category_menu,
 'brands'=>BrandsResource::collection($brands),
 'new_trends'=>ProductResource::collection($new_trends),
 'flash_sale'=>ProductResource::collection($flash_sale),

 ];

       return $this -> returnData('data',$data);
	 // return BookResource::collection(Book::with('ratings')->paginate(25));
    //     return AlbumResource::collection(Album::where('user_id', $request->user()->id)->paginate());
 	 // return $this -> returnData('data', CatAndSubResource::collection($data));
	
           // return $this->returnData('data', $managers);
        } catch (\Exception $e) {
            return $this->returnError(201, $e->getMessage());
        }
	   
        
    }

 
	
	
	
	
}

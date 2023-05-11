<?php

namespace App\Http\Controllers\Site;
 use Illuminate\Support\Carbon;

use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Product;
use App\Models\Advertisment;
use App\Models\Brand;
use App\Models\Currency;


use App\Models\Slider;
use App\Http\Controllers\Controller;
use Auth;
use Session;
use App\Models\Cart;
class HomeController extends Controller
{

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
          $data['sliders'] = Slider::get(['img']);
		 ///$data['categories'] = Category::get();
		// return   $data['categories'] = Category::select('id', 'name_ar')->with('subcategories')->get();

        $data['categories'] = Category::select('id','name_ar','name_ar','slug_ar','slug_en','img') -> where('is_active',"1")->get();




        /* $data['categories'] = Category::parent()->select('id', 'slug')->with(['childrens' => function ($q) {
            $q->select('id', 'parent_id', 'slug');
            $q->with(['childrens' => function ($qq) {
                $qq->select('id', 'parent_id', 'slug');
            }]);
        }])->get();*/

		// $data['new_arrivals'] = product::where(['is_active'=>"1" ,'new_arrival'=>"new_arrival"] )->limit(12)-> latest()->get();
		  $new_arrivals_chunk = Advertisment::with('pics')->where(['is_active'=>"1" ,'new_arrival'=>"new_arrival"] )->limit(12)-> latest()->get()->toArray();
		  $data['new_arrivals_chunk']=array_chunk($new_arrivals_chunk,2);
	 	 $data['new_trends'] = Advertisment::where(['is_active'=>"1"] )->limit(12)-> latest()->get();

		 //    $data['flash_sale'] = Advertisment::where(['is_active'=>"1" ,'flash_sale'=>"flash_sale"] )
		//	->where('start_date', '<=',Carbon::now()) ->where('end_date', '>=', Carbon::now())->limit(10)-> latest()->get();


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




     // $data['sub_categories'] = SubCategory::select('id as subct_id','name_ar as subct_name_ar','name_en as subct_name_en','slug_ar as subct_slug_ar')->with(['products' =>function($q) {
		//  $q->select('id' ,'name_ar','name_en' ,'cat_id','sub_cat_id','price','discount_price')-> where('is_active',"1"); }])-> where('is_active',"1")->latest()->limit(3)->get();


  // $data['categories'] = Category::with(['subcategories' =>function($q) {
		// $q-> where('is_active',"1"); }])-> where('is_active',"1")->limit(3)->get();



         return view('front.home', $data);
    }






     public function home2()
    {
		/*//return	$update_cart_session
		if(!empty(Session::get('session_id'))){
					 $user_id=Auth::user()->id;
					  $session_id=Session::get('session_id');
					 $update_cart_session=Cart::where('session_id',$session_id)->update(['user_id'=>$user_id]);

					}*/

					////////////////end update/////////////////
        $data = [];
          $data['sliders'] = Slider::get(['img']);
		 ///$data['categories'] = Category::get();
		// return   $data['categories'] = Category::select('id', 'name_ar')->with('subcategories')->get();


    $data['categories'] = Category::select('id','name_ar','slug_ar')->with(['subcategories' =>function($q) {
		 $q->select('id','name_ar','slug_ar','cat_id')-> where('is_active',"1"); }])-> where('is_active',"1")->get();



       /*  $data['categories'] = Category::parent()->select('id', 'slug')->with(['childrens' => function ($q) {
            $q->select('id', 'parent_id', 'slug');
            $q->with(['childrens' => function ($qq) {
                $qq->select('id', 'parent_id', 'slug');
            }]);
        }])->get(); */

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
	/*public function index(){
    	$blogpost = BlogPost::latest()->get();
    	$products = Product::where('status',1)->orderBy('id','DESC')->limit(6)->get();
    	$sliders = Slider::where('status',1)->orderBy('id','DESC')->limit(3)->get();
    	$categories = Category::orderBy('category_name_en','ASC')->get();

    	$featured = Product::where('featured',1)->orderBy('id','DESC')->limit(6)->get();
    	$hot_deals = Product::where('hot_deals',1)->where('discount_price','!=',NULL)->orderBy('id','DESC')->limit(3)->get();

    	$special_offer = Product::where('special_offer',1)->orderBy('id','DESC')->limit(6)->get();

    	$special_deals = Product::where('special_deals',1)->orderBy('id','DESC')->limit(3)->get();

    	$skip_category_0 = Category::skip(0)->first();
    	$skip_product_0 = Product::where('status',1)->where('category_id',$skip_category_0->id)->orderBy('id','DESC')->get();

    	$skip_category_1 = Category::skip(1)->first();
    	$skip_product_1 = Product::where('status',1)->where('category_id',$skip_category_1->id)->orderBy('id','DESC')->get();

    	$skip_brand_1 = Brand::skip(1)->first();
    	$skip_brand_product_1 = Product::where('status',1)->where('brand_id',$skip_brand_1->id)->orderBy('id','DESC')->get();


    	// return $skip_category->id;
    	// die();

    	return view('frontend.index',compact('categories','sliders','products','featured','hot_deals','special_offer','special_deals','skip_category_0','skip_product_0','skip_category_1','skip_product_1','skip_brand_1','skip_brand_product_1','blogpost'));

    }*/

 //---------------------------------------------
 //          Get  currency  Aamount
 //-------------------------------------------------
 public function get_currency($id)
    {


   $currency=Currency::where(['id'=>$id,'status'=>"1"])->first();
	  Session::put('currency_code', $currency->code);
	   Session::put('currency_symbol', $currency->symbol);
	// dd ($currency->symbol);
	 Session::put('exchange_rate', $currency->exchange_rate);
	 Session::put('tax_value', $currency->tax_value);

	// $exchange_rate= $currency->exchange_rate;
	//	 $tax_vate= $currency->tax_vate;
	  //dd(Session::get('currency_symbol')) ;

	//	 $code= $currency->code;
  //dd($coupon_amount);
  		  return redirect()->back()->with(['currency' =>$currency]);
  		 //   return redirect()->route('home')->with(compact('currency'));

 		}




  public function logout(){
    	Auth::logout();
    	return Redirect()->route('login');
    }








}

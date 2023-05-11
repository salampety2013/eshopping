<?php

namespace App\Http\Controllers\Site;
use Illuminate\Http\Request;
//use App\Models\Attribute;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Option;
 use App\Models\Product;
 use App\Models\Advertisment;
use App\Models\Rating;
use App\Models\Brand;
use App\Models\Coloradvertismentsize;
 use App\Models\Size;
  use App\Models\Color;
   use App\Models\Cart;
 use Illuminate\Support\Facades\DB;
 use Illuminate\Support\Carbon;
 use Exception;
use Session;
use Auth;
use View;

class AdvertismentsController extends Controller
{

	public function advertismentsBySlug($type=null,$slug)
    {
        $data = [];
		if($type=='category'){
           $data['category'] = Category::where('is_active', "1")->where('slug_ar', $slug)->first();

        if (!$data['category'])
			return redirect()->route('home');
            // $data['advertisments'] = $data['category']->advertisments;
			// return  advertisment::find(10)->images;
			//return  advertisment::find(10)->images[0];
			//return  advertisment::find(10)->images[0]->img;
			 $data['advertisments'] = Advertisment::with(['options'=>function ($q) {  return $q->select('id','name_ar','name_en')->where('is_active',1);}] )
             ->where('is_active', "1")->where('cat_id', $data['category']->id )->paginate(2);

		}

        return view('front.advertisments.advertisments', $data);
    }





    public function advertismentsDetailBySlug($slug)
    {
        $data=[];


         $lang=app()->getLocale();

    //     with(['options' =>function($q) { $q->select('id','name_ar','name_en','img'); }])->

           $data['advertisment'] = Advertisment:: where('is_active',"1") ->where('slug_'.$lang,$slug)->first();  //improve select only required fields
        if (!$data['advertisment']){ ///  redirect to previous page with message
              }

        $advertisment_id = $data['advertisment'] -> id ;
         $advertisment_categories_ids =  $data['advertisment'] ->category->id  ;
		  // [1,26,7] get all categories that advertisment on it
	 	$data['ratings']=Rating:: with(['user' =>function($q) {
		 $q->select('id','name')-> where('status',1); }])->where('status',1)->where('advertisment_id',$advertisment_id)->orderBy('created_at','Asc')->get();
      $data['ratings_average']= collect($data['ratings'])->average("rating_value");

	  /* $data['advertisment_attributes'] =  Attribute::whereHas('options' , function ($q) use($advertisment_id){
            $q -> whereHas('advertisment',function ($qq) use($advertisment_id){
                $qq -> where('advertisment_id',$advertisment_id);
            });
        })->get();*/

 //return  $data['related_advertisments'] = advertisment::where ('cat_id',$advertisment_categories_ids)->get();
          $data['related_advertisments'] = Advertisment::whereHas('category',function ($cat) use($advertisment_categories_ids){
           $cat-> where ('id',$advertisment_categories_ids);
       }) -> limit(20) -> latest() -> get();

    /*$data['advertisment_sizes'] = Coloradvertismentsize::with(['sizes' => function ($q) {

            return $q->select('id', 'name_ar');

        }  ])->where ('advertisment_id',$advertisment_id)->get();
		*/



        return view('front.advertisments.advertisments-details', $data);

    }





//-------------------------------------------------------------




 //---------------search -with reservation ----------------------------------------
 public function searchReservations(Request $request)
    {
		//return $request->all();
        $data = [];
		// $query=Advertisment::where('is_active', "1")->where('city_id', 1);


	//	 https://techvblogs.com/blog/get-data-between-two-dates-laravel#:~:text=Get%20data%20between%20two%20dates%20with%20MySQL%20Raw%20Query&text=%24startDate%20%3D%20'2022%2D06,%2C%20%24endDate%5D)%2D%3Eget()%3B
//whereIn, whereNotIn, whereNull, whereNotNull, whereDate, whereMonth, whereDay, whereYear, whereTime, whereColumn , whereExists, whereRaw.



//$start_date = date ("Y-m-d", strtotime($request->start_date));
//$end_date = date ("Y-m-d", strtotime($request->end_date));
$start_date= date('2023-05-11');
$end_date = date('2023-05-11');
 return  $query = Advertisment::doesntHave('orders','or',function($q) use($start_date,$end_date){
    $q->whereBetween('start_date', [$start_date,$end_date])
  ->whereBetween('end_date', [$start_date,$end_date]);

})->where('is_active', "1")->get();
//dd($query);

/* $query = Advertisment::doesntHave('orders', 'or', function($q){
    $q->WhereNotBetween('start_date', [$start_date,$end_date])
  ->whereNotBetween('end_date', [$start_date,$end_date]);

})->where('is_active', "1")->get(); */






/*
foreach ($period as $date) {
  // Insert Dates into listOfDates Array
  $listOfDates[] = $date-&gt;format('Y-m-d');
}
$videos = Video::whereDoesntHave('comments', function ($q) {
    $q->where('content', 'like', 'code%');
})->get();
$query = Advertisment::doesntHave('categories', 'or', function($q){
    $q->where('active', false);

})->doesntHave('countries', 'or')->get();
$posts = App\Post::whereDoesntHave('comments', function ($query) {
    $query->where('content', 'like', 'foo%');
})->get(); Delivered
$query= $query->whereBetween('reservation_from', [$from1, $to1])
  ->orWhereBetween('reservation_to', [$from2, $to2])
  ->whereNotBetween('reservation_to', [$from3, $to3])
  ->get();
  */

		//--------price-----------------

		if(!empty($request->minPrice)){
		   $min_price = $request->minPrice;

       $query= $query->where('price','>=',$min_price);

}
			if(!empty($request->maxPrice)){
		     $max_price = $request->maxPrice;
		 $query= $query-> where(function($q) use($max_price){
       $q->orwhere('price','<=',$max_price) ->orwhere('discount_price','<=',$max_price);
		 });

}

			 //------------------------------------

			//$data['advertisments']=$query->paginate(2);
			$data['advertisments']=$query->get();
        $data['brands']=Brand::where('is_active',"1")->latest()->get();
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

        return view('front.advertisments.advertisments_search', $data);
    }

 //---------------End search -----------------------------------------


 //---------------Advanced  search -----------------------------------------



 //---------------search -----------------------------------------
 public function Searchadvertisments(Request $request)
    {
		//return $request->all();
        $data = [];
		 $query=Advertisment::where('is_active', "1");
		 /*if($request->search_query==""){
 			}*/


			 if(!empty($request->search_query)){


			$search=$request->search_query;

			/*$query =$query->where('name_ar','LIKE','%'.$search.'%')
                ->orWhere('name_en','LIKE','%'.$search.'%')
                ->orWhere('details_ar','LIKE','%'.$search.'%')
                ->orWhere('details_en','LIKE','%'.$search.'%')
                ->orWhere('price','LIKE','%'.$search.'%')
                ->orWhere('code','LIKE','%'.$search.'%');
              */


			  //---------------------------
		 $searchFields = ['name_ar','name_en','details_ar','details_en'];

  		$query=$query->where(function($query) use($request, $searchFields){

    		$searchWildcard = '%' .$request->search_query. '%';

    		foreach($searchFields as $field){
				//if(!empty($request->$field))
					//{
      					$query->orWhere($field, 'LIKE', $searchWildcard);
   			 		//}
				}
 		 });
			}
			//------------category------------------
			 if(!empty($request->sub_cat)){
		   $sub_cats = $request->sub_cat;

       $query= $query->whereIn('sub_cat_id',$sub_cats);

}



		/*$query2 = Advertisment::where('color', 'blue')
    ->whereIn('value', ['Bomann', 'PHILIPS'])
    ->orWhere(function ($query) {
        $query->whereIn('value', ['Bomann', 'PHILIPS']);
    })
    ->get();
Output:

select * from `advertisments` where `color` = 'blue' and `value` in (Bomann, PHILIPS) OR (`value` in (red,white))
	 */

		//--------brands-----------------
    if(!empty($request->brand_id)){
		   $brand_ids = $request->brand_id;
		 /*$query= $query->orWhere(function ($q)use($brand_ids) {
        $q->whereIn('brand_id',$brand_ids);
    });*/
       $query= $query->whereIn('brand_id',$brand_ids);

}
		if(!empty($request->minPrice)){
		   $min_price = $request->minPrice;

       $query= $query->where('price','>=',$min_price);

}
			if(!empty($request->maxPrice)){
		     $max_price = $request->maxPrice;
		 $query= $query-> where(function($q) use($max_price){
       $q->orwhere('price','<=',$max_price) ->orwhere('discount_price','<=',$max_price);
		 });

}

			 //------------------------------------

			//$data['advertisments']=$query->paginate(2);
			$data['advertisments']=$query->get();
        $data['brands']=Brand::where('is_active',"1")->latest()->get();
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

        return view('front.advertisments.advertisments_search', $data);
    }

 //---------------End search -----------------------------------------


 //---------------Advanced  search -----------------------------------------




	 public function AdvancedSearch(Request $request)
    {

 			 if(!empty($request->search_query)){
 			 $search=$request->search_query;
			  $advertisments_auto_complete =Advertisment::where('is_active', "1")->where('name_ar','LIKE','%'.$search.'%')
			->orWhere('name_en','LIKE','%'.$search.'%')->select('name_ar','name_en','img','slug_ar','slug_en')->limit(10)->get();
			 // return response()->json($query);

			 $html = view('front.advertisments.advertisments_search_auto_complete',compact('advertisments_auto_complete'))->render();
		  //$html =View::make('front.cart.cart_items',compact('basket')) ;
       // return response()->json(compact('html'));

		return response()->json(['html'=>$html]);


			 // return view('front.advertisments.advertisments_search_auto_complete',compact('advertisments_auto_complete'));

			 }
	}





}

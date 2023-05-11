<?php

namespace App\Http\Controllers\Site;
use Illuminate\Http\Request;
//use App\Models\Attribute;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
 use App\Models\Product;
use App\Models\Rating;
use App\Models\Brand;
use App\Models\ColorProductSize;
 use App\Models\Size;
  use App\Models\Color;
   use App\Models\Cart;
 use Illuminate\Support\Facades\DB;
 use Illuminate\Support\Carbon;
 use Exception;
use Session;
use Auth;
use View;
 
class ProductController extends Controller
{
	
	public function productsBySlug($type=null,$slug)
    { 
        $data = [];
		if($type=='category'){
           $data['category'] = Category::where('is_active', "1")->where('slug_ar', $slug)->first();

        if (!$data['category'])
			return redirect()->route('home');
            // $data['products'] = $data['category']->products;
			// return  Product::find(10)->images;
			//return  Product::find(10)->images[0];
			//return  Product::find(10)->images[0]->img;
			 $data['products'] = Product::where('is_active', "1")->where('cat_id', $data['category']->id )->paginate(2);

		}
		elseif($type=='subcategory'){
           $data['subcategory'] = SubCategory::where('is_active', "1")->where('slug_ar', $slug)->first();
        if (!$data['subcategory'])
			return redirect()->route('home');
			  //$data['products'] = $data['subcategory']->products;
   $data['products'] = Product::where('is_active', "1")->where('sub_cat_id', $data['subcategory']->id )->paginate(2);
           
 			}
		elseif($type=='brands'){
           $data['brands'] = Brand::where('is_active', "1")->where('slug_ar', $slug)->first();

        if (!$data['brands'])
			return redirect()->route('home');
           //  $data['products'] = $data['brands']->products;
		      $data['products'] = Product::where('is_active', "1")->where('brand_id', $data['brands']->id )->paginate(2);

 			}
        return view('front.products.products', $data);
    }
	
	
	
	
	
    public function productsDetailBySlug($slug)
    {
        $data=[];
		//with(['ratings'=>function ($q) {  return $q->where('status',1);}] )

          

       
           $data['product'] = Product::where('slug_ar',$slug) -> where('is_active',1) ->first();  //improve select only required fields
        if (!$data['product']){ ///  redirect to previous page with message
              }

        $product_id = $data['product'] -> id ;
         $product_categories_ids =  $data['product'] ->category->id  ;
		  // [1,26,7] get all categories that product on it
	 	$data['ratings']=Rating:: with(['user' =>function($q) {
		 $q->select('id','name')-> where('status',1); }])->where('status',1)->where('product_id',$product_id)->orderBy('created_at','Asc')->get();
      $data['ratings_average']= collect($data['ratings'])->average("rating_value"); 
	 
	  /* $data['product_attributes'] =  Attribute::whereHas('options' , function ($q) use($product_id){
            $q -> whereHas('product',function ($qq) use($product_id){
                $qq -> where('product_id',$product_id);
            });
        })->get();*/
		
 //return  $data['related_products'] = Product::where ('cat_id',$product_categories_ids)->get();
          $data['related_products'] = Product::whereHas('category',function ($cat) use($product_categories_ids){
           $cat-> where ('id',$product_categories_ids);
       }) -> limit(20) -> latest() -> get();
	   
    /*$data['product_sizes'] = ColorProductSize::with(['sizes' => function ($q) {

            return $q->select('id', 'name_ar');

        }  ])->where ('product_id',$product_id)->get();
		*/
		
   $data['product_sizes_count'] = DB::table('color_product_size')->where('product_id',$product_id)->where('size_id', '!=',"")->get();
	
           
		
		   $data['product_sizes'] = DB::table('color_product_size')->where('product_id',$product_id) 
	  -> leftJoin('products', 'products.id', '=', 'color_product_size.product_id')
              ->leftJoin('sizes','sizes.id', '=', 'color_product_size.size_id')

			 ->leftJoin('colors','colors.id','=','color_product_size.color_id')
            -> select('color_product_size.id','color_product_size.product_id','color_product_size.quantity','color_product_size.size_id','color_product_size.color_id','products.name_ar as product_name','sizes.name_ar as size_name','colors.name_en as color_name','colors.code as color_code')->get();
			 

        return view('front.products.products-details', $data);
   
    }





//-------------------------------------------------------------	
	 public function getColorsAjax($stock_id)
    {
       
        $stocks=ColorProductSize::where('id',$stock_id)->first();
	
         

           $stock_colors=$stocks->color_id;
	  	 
		   if( $stock_colors !="") {
			     $colors= explode("," ,$stock_colors);
				  $html = '';
  $html = '<p>color:</p> <div class="custom-radios">';
  $i=1;
$data = array();
        foreach($colors as $product_color){ 
		// echo "<br>".$product_color;
		
			if($product_color!="")
			{
			 $color_name =  Color::where('id', $product_color) -> select('code','id','name_en')->first();
			 $html .= ' 
  <div><input type="radio" id="color-'.$i.'" name="color_id" value='.$color_name->id . '   required >
    <label for="color-'.$i.'">
      <span  style="background-color:'. $color_name->code.'">
        <img src= "'.asset("images/button/check-icn.svg").'"  alt="Checked Icon" />
      </span> </label></div>';
            /*$html .= '<p style="width:30px;height:30px;background-color:'. $color_name->code.'"><input type="radio" border="10" name="color_id" value='.$color_name->id . '   required > '. $color_name->name_en.'</p> ';
			*/
			
			$data[]=array(
			'id'=>$color_name->id,
			'name'=>$color_name->name_en,
			'code'=>$color_name->code
			
			);
			$i++;
			}
        }					
			 $html .='</div>';  
			  // echo $data= json_encode($html);
			 //return $data= json_encode($colors);
			 // return response()->json($colors);
			   return response()->json($html);
			   
			  
		 
	}  
	
	 } 
                                                             
    
       
 //---------------search -----------------------------------------
 //---------------search -----------------------------------------
 public function SearchProducts(Request $request)
    { 
		//return $request->all();
        $data = [];
		 $query=Product::where('is_active', "1");
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
			 
			//$data['products']=$query->paginate(2);
			$data['products']=$query->get();
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
	 
        return view('front.products.products_search', $data);
    }
	
 //---------------End search -----------------------------------------
 
                                
 //---------------Advanced  search -----------------------------------------
									
		 
		  
		 
	 public function AdvancedSearch(Request $request)
    { 
 			 
 			 if(!empty($request->search_query)){
 			 $search=$request->search_query;
			  $products_auto_complete =Product::where('is_active', "1")->where('name_ar','LIKE','%'.$search.'%') 
			->orWhere('name_en','LIKE','%'.$search.'%')->select('name_ar','name_en','img','slug_ar','slug_en')->limit(10)->get(); 
			 // return response()->json($query);
			 
			 $html = view('front.products.products_search_auto_complete',compact('products_auto_complete'))->render();
		  //$html =View::make('front.cart.cart_items',compact('basket')) ;
       // return response()->json(compact('html'));
	 
		return response()->json(['html'=>$html]);
 		
 		
			 // return view('front.products.products_search_auto_complete',compact('products_auto_complete'));
			 
			 }
	}
	
	
	

	
}

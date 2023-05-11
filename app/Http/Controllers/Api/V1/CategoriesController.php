<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Slider;


use App\Traits\GeneralTrait;
use Illuminate\Http\Request;


use App\Http\Resources\V1\SubCategoriesResource;
use App\Http\Resources\V1\CategoriesResource;
use App\Http\Resources\V1\CatAndSubResource;
use App\Http\Resources\V1\CategoriesCollection;

class CategoriesController extends Controller
{
    use GeneralTrait;

    public function getMainCategories(Request $request)
    {
    // $lang= $request->lang;
  //  dd($lang);
	   try {
		     $categories = Category::paginate(1);
			//$categories = Category::where('is_active',true)->get();
		//$categories = Category::select('id','name_'.app()->getLocale().' as name','img')->get();
		

	 
     // return  new CategoriesCollection($categories);
			//return response()->json($categories);

      //  return $this -> returnData('data',$categories);
	 // return BookResource::collection(Book::with('ratings')->paginate(25));
    //     return AlbumResource::collection(Album::where('user_id', $request->user()->id)->paginate());
	// return $this -> returnData('data',  CategoriesResource::collection($categories));
	 return $this -> returnData('data', new CategoriesCollection($categories));
	
           // return $this->returnData('data', $managers);
        } catch (\Exception $e) {
            return $this->returnError(201, $e->getMessage());
        }
	   
        
    }

    public function getCategoryById(Request $request)
    {
  try {
        $category = Category::find($request->id);
       if (!$category)
           //  return $this->returnError('001', 'هذا القسم غير موجود');
           return $this->returnError('202', __('general.not found'));

        return $this->returnData('data',new CategoriesResource($category));
		
		} catch (\Exception $e) {
            // return $this->returnError(201, $e->getMessage());
           return $this->returnError(201, 'something went wrong');
        }
		
    }

    public function changeStatus(Request $request)
    {
       //validation
        Category::where('id',$request -> id) -> update(['active' =>$request ->  active]);

        return $this -> returnSuccessMessage('تم تغيير الحاله بنجاح');

    }











//----------------------------------------------------
//---------------------ctegories and sub ctegories-----------------------------
//----------------------------------------------------

 public function getCatAndSubCat(Request $request)
    {
    // $lang= $request->lang;
  //  dd($lang);
	   try {
		   // $categories = SubCategory::paginate(2);
			
		//$subcategories = SubCategory::select('id','name_'.app()->getLocale().' as name','img')->get();
		

	 
        
      $categories  = Category::select('id','name_ar','name_en','img')
	  ->with(['subcategories' =>function($q) {
		 $q->select('id','name_ar','slug_ar','name_en','cat_id','img')-> where('is_active',"1"); }])
		 -> where('is_active',"1")->get();

      //  return $this -> returnData('data',$data);
	 // return BookResource::collection(Book::with('ratings')->paginate(25));
    //     return AlbumResource::collection(Album::where('user_id', $request->user()->id)->paginate());
 	 return $this -> returnData('data', CatAndSubResource::collection($categories));
	
           // return $this->returnData('data', $managers);
        } catch (\Exception $e) {
            return $this->returnError(201, $e->getMessage());
        }
	   
        
    }

 
public function homeCats(Request $request)
    {
    // $lang= $request->lang;
  //  dd($lang);
	   try { 
	   $sliders= Slider::get(['img']);
			 $categories = Category::where('is_active',true)->get();

     /*    $data = [];
          $data['sliders'] = Slider::get(['img']);
			 $data['categories'] = Category::where('is_active',true)->get();

 $data = [
'sliders'=>$sliders,
 'categories'=>$categories,

 ];
*/
			 $subcategories = SubCategory::where('is_active',true)->where('cat_id',$request->cat_id)->get();

 $data = [
'sliders'=>$sliders,
 'categories'=>CatAndSubResource::collection($categories),

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

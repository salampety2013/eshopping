<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProPic;
use App\Models\Size;
use App\Models\Color;
 use App\Models\ColorProductSize;


use App\Http\Requests\ProductImagesRequest;

use Illuminate\Support\Carbon;
//use App\Http\Requests\ProductRequest;
use Exception;
use Illuminate\Support\Facades\DB;

class PoductsController extends Controller
{
    public function index()
    {


       // $products = Product::latest()->get();
		// $subcategories = SubCategory::latest()->get();
       // $categories = Category::orderBy('name_ar', 'ASC')->get();


		$products = Product::with(['category' => function ($q) {

            return $q->select('id', 'name_ar','name_en');

        }, 'subcategory' => function ($q) {

            return $q->where('is_active', '1')->select('id', 'name_ar','name_en');

        } ])->latest()->get();


        //return view('dashboard.products.index_pro', compact('products', 'categories','subcategories'));
              return view('dashboard.products.index_pro', compact('products'));

			  /*      $products = Product::with(['category' => function ($q) {

            return $q->where('status', '=', '1')->select('id', 'title');

        }, 'subcategory' => function ($q) {

            return $q->where('status', '1')->select('id', 'title');

        }, 'childcat' => function ($q) {

            return $q->where('status', '=', '1')->select('id', 'title');

        }, 'subvariants' => function ($q) {

            return $q->where('def', '=', '1');

        }, 'subvariants.variantimages' => function ($q) {

            return $q->select('var_id', 'main_image');

        }, 'brand' => function ($q) {

            return $q->select('id', 'name');

        }])->with(['vender' =>  function ($q) {

            return $q->select('id', 'name');

        }])->whereHas('vender', function ($query) {

            return $query->where('status', '=', '1')->where('is_verified', '1');

        })->with(['store' =>  function ($q) {

            $q->select('id', 'name','status');

        }])->whereHas('store')->get();

	  */


    }
    //------------------------------------------------------------
     public function create($type=null, Request $request)
    {
         $categories = Category:: whereHas('subcategories', function ($query) {
	   $query-> where('is_active',"1"); })
	  ->where('is_active',"1")->orderBy('name_ar', 'ASC')->get();
        //$products = Product::latest()->get();

      $brands = Brand::where('is_active',"1")->orderBy('name_ar', 'ASC')->get();


	 if(isset( $request->type)) {

		  $type=$request->type	;
		 }else{
			 $type="";
			 }
       //  return view('dashboard.products.create_pro', compact('categories','brands'));
//
	//$type="flash_sale";

	 return view('dashboard.products.create_pro', compact('categories','brands','type'));

			//}
    }



////////////////////////////////////////////////////////

 //---------------------------------------------------------------
  public function GetProduct($cat_id){
			// dd("ff");
			  $subcat = Product::where('cat_id',$cat_id)->orderBy('name_ar','ASC')->get();
        return json_encode($subcat);
  // $subcat =Product::where('cat_id',$cat_id)->get();
    //  return  $subcat = Product::where('cat_id',$cat_id)->orderBy('name_en','ASC')->get();
       // return json_encode($subcat);
    }
//////////////////////////////////////////////////////////////////////
 public function GetSubCategory($cat_id){

			  $subcat = SubCategory::where('cat_id',$cat_id)->orderBy('name_en','ASC')->get();
        return json_encode($subcat);
     }

	//////////////////////////////////////////////////////////

	 public function addImages($product_id){
        return view('dashboard.products.images.create')->withId($product_id);
    }



	//////////////////////////////////////////////////////
  public function saveProductImages(Request $request ){


            $filePath = "";
            if ($request->has('dzfile')) {
                //dd($request->img);

                $filePath = uploadImage('assets/images/products/', $request->dzfile);

            /////////////////////////////////////////////////////////////////////////////////////

        $file = $request->file('dzfile');
       // $filename = uploadImage('products', $file);

        return response()->json([
            'name' => $filePath,
            'original_name' => $file->getClientOriginalName(),
        ]);
 }
    }
////////////////////////////////////////////////////////////////
    public function saveProductImagesDB(ProductImagesRequest $request){

        try {

            // save dropzone images
            if ($request->has('document') && count($request->document) > 0) {
                foreach ($request->document as $image) {
                    ProPic::create([
                        'pro_id' => $request->product_id,
                        'img' => $image,
                    ]);
                }
            }

            return redirect()->route('admin.products')->with(['success' => 'تم الحفظ بنجاح']);

        }catch(\Exception $ex){

        }
    }

       /////////////////////////////////////////////////////////////

 public function delDropzoneImages(Request $request)
    {

             $img =$request->filename;

            $img_path = 'assets/images/products/'.$img;

            if (file_exists($img_path)) {
                unlink($img_path);
				//dd($img_path);
			}
			return response()->json(['name'=>$img]);

    }




 public function store(Request $request)
    {

        try {

                DB::beginTransaction();

            //validation

           // return $request;

            ////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////

            $filePath = "";
            if ($request->has('img')) {
               // dd($request->img);
                // $filePath = uploadImage('products', $request->img);
                $filePath = uploadImage('assets/images/products/', $request->img);
            }
            /////////////////////////////////////////////////////////////////////////////////////

            if (!$request->has('is_active'))
                $flag = 0;
            else
                $flag = 1;
///////////////////////
            if (!$request->has('has_Variants'))
            $has_Variants = 0;
            else
            $has_Variants = 1;
///////////////////////
			if ($request->has('start_date')&& $request->has('end_date') ) {
              ///   dd($request->start_date);
                // $filePath = uploadImage('products', $request->img);
                $start_date = $request->start_date;
				$end_date = $request->end_date;
            }else{

                $start_date = $request->start_date;
				$end_date = $request->end_date;
				}
            $pro_id =Product::insertGetId([
                'name_ar' => $request->name_ar,
                'name_en' => $request->name_en,
                'cat_id' => $request->cat_id,
				 'sub_cat_id' => $request->subcategory_id,
				 'brand_id' => $request->brand_id,
                'slug_en' => strtolower(str_replace(' ', '-', $request->name_en)),
                'slug_ar' => str_replace(' ', '-', $request->name_ar),
				 'total_quantity' => $request->total_quantity,
				 'code' => $request->code,

				  'price' => $request->price,
				   'discount_price' => $request->discount_price,
				    'details_ar' => $request->details_ar,
					 'details_en' => $request->details_en,
					 'new_trends' => $request->new_trends,
					 'new_arrival' => $request->new_arrival,
					 'flash_sale' => $request->flash_sale,
					 'start_date' => $start_date,
					 'end_date' => $end_date,


                //'img' => $last_img,
                'img' => $filePath,
                'is_active' => $flag,
                'has_Variants' => $has_Variants,
                'created_at' => Carbon::now()
            ]);

			/* ColorProductSize::create([
                        'product_id' => $pro_id,
                        'quantity' => $request->total_quantity,
                       // 'varient' => 'false'

                    ]); */


            $notification = array(
                'msg' => ' Added Successfully',
                'alert-type' => 'success'
            );

///////////////////////insert multiple images to product /////////////////////////////////////




			 if ($request->has('document') && count($request->document) > 0) {
                foreach ($request->document as $image) {
                    ProPic::create([
                        'pro_id' => $pro_id,
                        'img' => $image,
                    ]);
                }
			 }
 DB::commit();
            // return redirect()->route('admin.products')->with(['success' => 'تم الاضافة بنجاح']);;
            return redirect()->route('admin.products')->with($notification);

        } catch (\Exception $ex) {
            DB::rollback();
            return $ex;
            //return redirect()->route('admin.products')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
			 return redirect()->route('admin.products')->with(['msg' => 'حدث خطا ما برجاء المحاوله لاحقا',  'alert-type' => 'danger'] );
        }
    }


     public function edit($id)
    {
         $categories = Category:: whereHas('subcategories', function ($query) {
	   $query-> where('is_active',"1"); })


	  ->where('is_active',"1")->orderBy('name_ar', 'ASC')->get();
	     $subcategories = SubCategory::where('is_active',"1")->orderBy('name_ar', 'ASC')->get();

        //$products = Product::latest()->get();
      $brands = Brand::where('is_active',"1")->orderBy('name_ar', 'ASC')->get();

        $product = Product::with('images')->find($id);

        if (!$product)
            return redirect()->route('admin.products')->with(['error' => 'هذا القسم غير موجود']);
        // $Product = Product::findOrFail($id);
        return view('dashboard.products.edit_pro', compact('product', 'categories','subcategories','brands'));
    }




    public function update($id, Request $request)
    {
        try {
             //return $request->all();
			   DB::beginTransaction();

            $Product = Product::find($request->id);
            if (!$Product)
                return redirect()->route('admin.products')->with(['error' => 'هذا المنتج غير موجود']);
            /////////////upload image/////////////////////
            $old_img = $Product->img;

            $old_img_path = 'assets/images/products/' . $old_img;
            $filePath = "";
			// dd($old_img_path);
            if ($request->has('img')) {
                //  dd($request->img);
                  //$filePath = uploadImage('products', $request->img);
                if($old_img!=null){

					if (file_exists($old_img_path)) {
						unlink($old_img_path);
                }
				}

                $filePath = uploadImage('assets/images/products/', $request->img);
            } else {

                $filePath = $old_img;
            }
			// dd($filePath);
            /////////////////////////////////////////////////////////////////////////////////////

            if (!$request->has('is_active'))
                $flag = 0;
            else
                $flag = 1;
            ///////////////////////
            if (!$request->has('has_Variants'))
            $has_Variants = 0;
            else
            $has_Variants = 1;
        ////////////////////////////////////
            $Product->update([
               'name_ar' => $request->name_ar,
                'name_en' => $request->name_en,
                'cat_id' => $request->cat_id,
				 'sub_cat_id' => $request->subcategory_id,
				 'brand_id' => $request->brand_id,
                'slug_en' => strtolower(str_replace(' ', '-', $request->name_en)),
                'slug_ar' => str_replace(' ', '-', $request->name_ar),
 			  'total_quantity' => $request->total_quantity,
				'code' => $request->code,
				  'price' => $request->price,
				   'discount_price' => $request->discount_price,
				    'details_ar' => $request->details_ar,
					 'details_en' => $request->details_en,
					 'new_trends' => $request->new_trends,
					 'new_arrival' => $request->new_arrival,
					 'flash_sale' => $request->flash_sale,


                //'img' => $last_img,
                'img' => $filePath,
                'has_Variants' => $has_Variants,
                'is_active' => $flag,
                'updated_at' => Carbon::now()
            ]);


            //  $Product->name_en = $request->name_en;
            // $Product->save();
            //$Product->update($request->all());
            //$product = Product::get();
            // DB::enableQueryLog();
            //$query = DB::getQueryLog();
            //$query = end($query);
            //dd($query);


            $notification = array(
                'msg' => 'Updated Successfully',
                'alert-type' => 'success'
            );


///////////////////////insert multiple images to product /////////////////////////////////////




			 if ($request->has('document') && count($request->document) > 0) {
                foreach ($request->document as $image) {
                    ProPic::create([
                        'pro_id' => $id,
                        'img' => $image,
                    ]);
                }
			 }
 DB::commit();
            // return redirect()->route('admin.products')->with(['success' => 'تم الاضافة بنجاح']);;
            return redirect()->back()->with($notification);
        } catch (\Exception $ex) {
 		DB::rollback();
           // return redirect()->route('admin.products')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
		    return redirect()->route('admin.products')->with(['msg' => 'حدث خطا ما برجاء المحاوله لاحقا',  'alert-type' => 'danger'] );
        }
    }





    public function deactivate($id)
    {

        try {

            //  $category = Product::orderBy('id', 'DESC')->find($id);
            $Product = Product::find($id);
            if (!$Product)
                return redirect()->route('admin.products')->with(['error' => 'هذا القسم غير موجود ']);

            $Product->is_active = 0;
            $Product->save();
			 $notification = array(
                'msg' => ' updated Successfully',
                'alert-type' => 'info'
            );
			return redirect()->route('admin.products')->with($notification);
         //   return redirect()->route('admin.products')->with(['success' => 'تم الحفظ بنجاح']);
        } catch (\Exception $ex) {
            //return $ex;
           // return redirect()->route('admin.products')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
		    return redirect()->route('admin.products')->with(['msg' => 'حدث خطا ما برجاء المحاوله لاحقا',  'alert-type' => 'danger'] );
        }
    }


//-----------------------------------------------
//delete images of product
//--------------------------------------

 public function deleteImage($id)
    {

        try {
             $image = ProPic::find($id);
            if (!$image)
                return redirect()->route('admin.products')->with(['error' => 'هذا القسم غير موجود ']);



            $image_name = $image->img;
            $img_path = 'assets/images/products/' . $image_name;

            if (file_exists($img_path)) {
                unlink($img_path);
            }

            $image->delete();
 $notification = array(
                'msg' => ' deleted Successfully',
                'alert-type' => 'success'
            );
			return redirect()->back()->with($notification);
           // return redirect()->route('admin.products')->with(['success' => 'تم  الحذف بنجاح']);
        } catch (\Exception $ex) {
            //return $ex;
           // return redirect()->route('admin.products')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
		    return redirect()->back()->with(['msg' => 'حدث خطا ما برجاء المحاوله لاحقا',  'alert-type' => 'danger'] );

        }
    }
//--------------------------------------------------------

    public function activate($id)
    {

        try {
            //get specific categories and its translations
            //  $category = Product::orderBy('id', 'DESC')->find($id);
            $Product = Product::find($id);
            if (!$Product)
                return redirect()->route('admin.products')->with(['error' => 'هذا القسم غير موجود ']);

            $Product->is_active = 1;
            $Product->save();
			 $notification = array(
                'msg' => ' updated Successfully',
                'alert-type' => 'info'
            );
		return redirect()->route('admin.products')->with($notification);
           // return redirect()->route('admin.products')->with(['success' => 'تم الحفظ بنجاح']);
        } catch (\Exception $ex) {
            //return $ex;
            return redirect()->route('admin.products')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }
    public function destroy($id)
    {

        try {
            //get specific categories and its translations
            //  $category = Product::orderBy('id', 'DESC')->find($id);
            $Product = Product::find($id);
            if (!$Product)
                return redirect()->route('admin.products')->with(['error' => 'هذا القسم غير موجود ']);



            $old_img = $Product->img;
            $old_img_path = 'assets/images/products/' . $old_img;

            if (file_exists($old_img_path)) {
                unlink($old_img_path);
            }

            $Product->delete();
 $notification = array(
                'msg' => ' deleted Successfully',
                'alert-type' => 'success'
            );
			return redirect()->route('admin.products')->with($notification);
           // return redirect()->route('admin.products')->with(['success' => 'تم  الحذف بنجاح']);
        } catch (\Exception $ex) {
            //return $ex;
           // return redirect()->route('admin.products')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
		    return redirect()->route('admin.products')->with(['msg' => 'حدث خطا ما برجاء المحاوله لاحقا',  'alert-type' => 'danger'] );

        }
    }

    public function delAll(Request $request)
    {
        try {
            if ($request->ids == "")
               // return redirect()->route('admin.products')->with(['error' => 'من فضلك قم بالاختيار ليتم الحذف']);
 return redirect()->route('admin.products')->with(['msg' => 'من فضلك قم بالاختيار ليتم الحذف',  'alert-type' => 'danger'] );
            $ids = $request->ids;
            Product::whereIn('id', $ids)->delete();
 $notification = array(
                'msg' => ' deleted Successfully',
                'alert-type' => 'info'
            );
			return redirect()->route('admin.products')->with($notification);
           // return redirect()->route('admin.products')->with(['success' => 'تم  الحذف بنجاح']);
        } catch (\Exception $ex) {
            return $ex;
           // return redirect()->route('admin.products')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
 return redirect()->route('admin.products')->with(['msg' => 'حدث خطا ما برجاء المحاوله لاحقا',  'alert-type' => 'danger'] );        }
    }



	///// ////get pivot relation size and color of that product/////////



	 public function getSizes(Request $request)
    {
        try {
              //$products=Product::all();
			 //return Product::with('sizes')->get();
			  return Product::with('colors')->get();
			    // $products->sizes;

			   foreach ($products->sizes as $size)
					{
						echo $size->pivot->quantity;
						echo $size->name_ar;
					}

            // return redirect()->route('admin.products')->with(['success' => 'تم  الحذف بنجاح']);
        } catch (\Exception $ex) {
            //return $ex;
           //return redirect()->route('admin.products')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
       }
    }

	///////////////////////////////////////////
	////////////////////////////////////////////////////////////

    public function add_more_choice_option(Request $request) {
        $all_attribute_values = AttributeValue::with('attribute')->where('attribute_id', $request->attribute_id)->get();

        $html = '';

        foreach ($all_attribute_values as $row) {
            $html .= '<option value="' . $row->value . '">' . $row->value . '</option>';
        }

        echo json_encode($html);
    }




	/////////////////////////////////////

	public function getStocks($id)
    {

	// return $id;
	/*The join is INNER JOIN and it selects records that have matching values in both tables.

The leftJoin is LEFT JOIN and it returns all records from the left table (in your case users), and the matched records from the right table (data table). The result is NULL from the right side if there is no match.*/

	     $stocks = DB::table('color_product_size')->where('product_id',$id)
	  -> leftJoin('products', 'products.id', '=', 'color_product_size.product_id')
              ->leftJoin('sizes','sizes.id', '=', 'color_product_size.size_id')

			 ->leftJoin('colors','colors.id','=','color_product_size.color_id')
            -> select('color_product_size.id','color_product_size.quantity','products.name_ar as product_name','sizes.name_ar as size_name','colors.name_en as color_name')->get();



			 	/*return $stocks= Product::with(['sizes' =>  function ($q) {

            $q->select('name_en');}])->whereHas('colors')->get();*/



	 	//return $stocks=   $products->with('sizes')->with('colors')->get();
		 $product_id=$id;

        return view('dashboard.products.stocks.index_stock', compact('stocks','product_id'));

    }
//////////////////////////////////////////////////////////

	 public function addStocks($product_id){
		 $Product = Product::find($product_id);
            if (!$Product)
                return redirect()->route('admin.products')->with(['error' => 'هذا القسم غير موجود']);
            /////////////upload image/////////////////////

		   $sizes = Size::orderBy('type', 'ASC')->get();
		    $colors = Color::orderBy('name_en', 'ASC')->get();
		  //return $pro_stock_count = $Product->colors()->count();
		   $count = DB::table('color_product_size')->where('product_id',$product_id)->count();

		//return ColorProductSize::where('product_id',$product_id)->count();
        return view('dashboard.products.stocks.create_stocks')->withId($product_id)->with(['sizes' => $sizes,'colors'=>$colors,'count'=> $count,'product_id'=>$product_id]);
    }



	//////////////////////////////////////////////////////

 public function saveStocks(Request $request)
    {

        try {

                DB::beginTransaction();

            //validation

           // return $request;

     	  $product_id= $request->product_id;
		    $Product = Product::find($product_id);
             //dd($product_id);
            /////////////////////////////////////////////////////////////////////////////////////
			//dd( $request->size_id);

			 if($request->has('size_active') && $request->has('size_id')  ){
          		$size_id =$request->size_id;
		     	$Product->update([
               'size_id' => json_encode($request->size_id)]);
         	}
        else {
           // $colors = array();
           // $product->colors = json_encode($colors);
		   $size_id ="";
		    $Product->update([
               //'sizes' => json_encode($sizes)
			   'size_id' =>''
			   ]);
        }
//dd($request->colors_ids);

 if($request->has('color_active') && $request->has('colors_ids') && count($request->colors_ids) > 0){
          $colors =$request->colors_ids;
		     $Product->update([
               'color_id' => json_encode($request->colors_ids)]);
			   $colors_id_alls="";
			     foreach ($request->colors_ids as $color_id) {
                    $colors_id_alls= $color_id.",".$colors_id_alls;
					 	}
					 ColorProductSize::create([
                        'product_id' => $request->product_id,
                        'color_id' => $colors_id_alls,
						'size_id' => $size_id,
						'quantity' =>$request->quantity
                    ]);
        }
        else {

           // $colors = array();
           // $product->colors = json_encode($colors);
		   $color_id ="";
		    $Product->update([
               //'colors' => json_encode($colors)
			   'color_id' => ''
			   ]);

			   ColorProductSize::create([
                        'product_id' => $request->product_id,
                       // 'color_id' =>(int) $color_id,
						'color_id' =>  $color_id,
						'size_id' => $size_id,
						'quantity' =>$request->quantity
                    ]);

        }

     /*  $choice_options = array();

        if($request->has('choice_no')){
            foreach ($request->choice_no as $key => $no) {
                $str = 'choice_options_'.$no;

                $item['attribute_id'] = $no;

                $data = array();
                // foreach (json_decode($request[$str][0]) as $key => $eachValue) {
                foreach ($request[$str] as $key => $eachValue) {
                    // array_push($data, $eachValue->value);
                    array_push($data, $eachValue);
                }*/




            $notification = array(
                'msg' => ' Added Successfully',
                'alert-type' => 'success'
            );

///////////////////////insert multiple images to product /////////////////////////////////////




 DB::commit();
            // return redirect()->route('admin.products')->with(['success' => 'تم الاضافة بنجاح']);;
            return redirect()->route('admin.products.stocks',$product_id)->with($notification);

        } catch (\Exception $ex) {
            DB::rollback();
            return $ex;
            //return redirect()->route('admin.products')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
			 return redirect()->route('admin.products.stocks',$product_id)->with(['msg' => 'حدث خطا ما برجاء المحاوله لاحقا',  'alert-type' => 'danger'] );
        }
    }

/////////////////////////////////



	 public function EditStocks($stock_id){

		   $stocks = ColorProductSize::find($stock_id);
            if (!$stocks)
                return redirect()->route('admin.products.stocks')->with(['error' => 'هذا القسم غير موجود']);
            /////////////upload image/////////////////////

		   $sizes = Size::orderBy('type', 'ASC')->get();
		    $colors = Color::orderBy('name_en', 'ASC')->get();




        return view('dashboard.products.stocks.edit_stocks')->withId($stock_id)->with(['stocks'=>$stocks,'sizes' => $sizes,'colors'=>$colors]);
    }


	 public function UpdateStocks(Request $request)
    {

        try {

                DB::beginTransaction();

            //validation

           // return $request;

     	 $product_id= $request->product_id;
		    $Product = Product::find($product_id);
			 $stocks = ColorProductSize::find($request->stock_id);
            if (!$stocks)
                return redirect()->route('admin.products.stocks',$product_id)->with(['error' => 'هذا القسم غير موجود']);
            /////////////upload image/////////////////////
             //dd($product_id);
            /////////////////////////////////////////////////////////////////////////////////////
			//dd( $request->size_id);

			 if($request->has('size_active') && $request->has('size_id')  ){
          		$size_id =$request->size_id;
		     	$Product->update([
               'size_id' => json_encode($request->size_id)]);
         	}
        else {
           // $colors = array();
           // $product->colors = json_encode($colors);
		   $size_id ="";
		    $Product->update([
               //'sizes' => json_encode($sizes)
			   'size_id' =>''
			   ]);
        }
//dd($request->colors_ids);

 if($request->has('color_active') && $request->has('colors_ids') && count($request->colors_ids) > 0){
          $colors =$request->colors_ids;
		     $Product->update([
               'color_id' => json_encode($request->colors_ids)]);
			   $colors_id_alls="";
			     foreach ($request->colors_ids as $color_id) {
                    $colors_id_alls= $color_id.",".$colors_id_alls;
					 	}
					$stocks->update([
                        'product_id' => $request->product_id,
                        'color_id' => $colors_id_alls,
						'size_id' => $size_id,
						'quantity' =>$request->quantity
                    ]);
        }
        else {

           // $colors = array();
           // $product->colors = json_encode($colors);
		   $color_id ="";
		    $Product->update([
               //'colors' => json_encode($colors)
			   'color_id' => ''
			   ]);

			  $stocks->update([
                        'product_id' => $request->product_id,
                       // 'color_id' =>(int) $color_id,
						'color_id' =>  $color_id,
						'size_id' => $size_id,
						'quantity' =>$request->quantity
                    ]);

        }

     /*  $choice_options = array();

        if($request->has('choice_no')){
            foreach ($request->choice_no as $key => $no) {
                $str = 'choice_options_'.$no;

                $item['attribute_id'] = $no;

                $data = array();
                // foreach (json_decode($request[$str][0]) as $key => $eachValue) {
                foreach ($request[$str] as $key => $eachValue) {
                    // array_push($data, $eachValue->value);
                    array_push($data, $eachValue);
                }*/

            $notification = array(
                'msg' => ' Added Successfully',
                'alert-type' => 'success'
            );

///////////////////////insert multiple images to product /////////////////////////////////////




 DB::commit();
            // return redirect()->route('admin.products')->with(['success' => 'تم الاضافة بنجاح']);;
            return redirect()->route('admin.products.stocks',$product_id)->with($notification);

        } catch (\Exception $ex) {
            DB::rollback();
            return $ex;
            //return redirect()->route('admin.products')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
			 return redirect()->route('admin.products.stocks',$product_id)->with(['msg' => 'حدث خطا ما برجاء المحاوله لاحقا',  'alert-type' => 'danger'] );
        }
    }



//////////////////////////////////////////////////////////

 public function destroyStock($id)
 //public function destroyStock($id,$product_id)
    {

        try {

             $stock = ColorProductSize::find($id);
			 $product_id= $stock->product_id;
            if (!$stock)
                return redirect()->route('admin.products.stocks',$product_id)->with(['error' => 'هذا القسم غير موجود ']);

            $stock->delete();
 $notification = array(
                'msg' => ' deleted Successfully',
                'alert-type' => 'success'
            );
			return redirect()->route('admin.products.stocks',$product_id)->with($notification);
           // return redirect()->route('admin.products')->with(['success' => 'تم  الحذف بنجاح']);
        } catch (\Exception $ex) {
            //return $ex;

		     return redirect()->route('admin.products.stocks',$product_id)->with(['msg' => 'حدث خطا ما برجاء المحاوله لاحقا',  'alert-type' => 'danger'] );

        }
    }

    public function delAllStocks(Request $request)
    {
        try {
			$product_id= $request->product_id;
            if ($request->ids == "")
               // return redirect()->route('admin.products')->with(['error' => 'من فضلك قم بالاختيار ليتم الحذف']);
 return redirect()->route('admin.products.stocks',$product_id)->with(['msg' => 'من فضلك قم بالاختيار ليتم الحذف',  'alert-type' => 'danger'] );
            $ids = $request->ids;
            ColorProductSize::whereIn('id', $ids)->delete();
 $notification = array(
                'msg' => ' deleted Successfully',
                'alert-type' => 'info'
            );
			return redirect()->route('admin.products.stocks',$product_id)->with($notification);
           // return redirect()->route('admin.products')->with(['success' => 'تم  الحذف بنجاح']);
        } catch (\Exception $ex) {
            return $ex;
           // return redirect()->route('admin.products')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
 return redirect()->route('admin.products.stocks',$product_id)->with(['msg' => 'حدث خطا ما برجاء المحاوله لاحقا',  'alert-type' => 'danger'] );        }
    }









//////////////////////////add multiple quanty at one

/////////////////product controller/////////////////////////////////////////

	 public function addMultipleStocks(Request $request ,$product_id){

		 $Product = Product::find($product_id);

            if (!$Product)
                return redirect()->route('admin.products')->with(['error' => 'هذا القسم غير موجود']);
            /////////////upload image/////////////////////

		   $sizes = Size::orderBy('type', 'ASC')->get();
		    $colors = Color::orderBy('name_en', 'ASC')->get();
		  //return $pro_stock_count = $Product->colors()->count();
		    $count = DB::table('color_product_size')->where('product_id',$product_id)->count();

		//return ColorProductSize::where('product_id',$product_id)->count();->with(['sizes' => $sizes,'colors'=>$colors,'count'=> $count,'product_id'=>$product_id])
        return view('dashboard.products.stocks.create_multiple_stock_quantity')->withId($product_id)->with(['sizes' => $sizes,'colors'=>$colors,'count'=> $count,'product_id'=>$product_id]);
    }





	//////////////////////////////////////////////////////

 public function savemMultipleStocks(Request $request)
    {

        try {

							/*$key=0;
				 if( isset($request->cmbService )){
					 $colors =array();
				   $colors =$request->cmbService ;
				  // echo"<br>color=".$colors;
				  //  print_r($colors);

			  foreach ( $colors   as $color_id) {
                   // $colors_id_alls= $color_id.",".$colors_id_alls;
					echo "<br>color=".$color_id  ;
					 	}


				   }else{
					   $colors_id_alls="empty color";
					   }

			exit;
			*/
			//###########################################

 //dd($request->all());
                DB::beginTransaction();

            //validation

           // return $request;

     	  $product_id= $request->product_id;
		     $Product = Product::find($product_id);
            // dd($product_id);
            /////////////////////////////////////////////////////////////////////////////////////
			/* echo implode(', ', array_map(function ($entry) {
  return ($entry[key($entry)]);
}, $request->colors_ids));
			 exit; */
            //  dd(count($request->quantity));


 //  dd(count($request->colors_ids));

			/*foreach ($request->quantity as $key => $value) {
				 if(isset($request->quantity[$key])){

					//   echo"<br>sizes===".$size_id[$key];
					 //
				//	 echo"<br>key".$key;
			//   echo"<br>quantitiy===".$value;

 			 if($request->has('size_active') && isset($request->size_id[$key])){
				  $size_id =$request->size_id[$key];
				//$size_id =$size_id[$key];

				   }else{
					   $size_id="empty size_id";
					   }
			    $colors_id_alls="";

				 if($request->has('color_active') && isset($request->colors_ids[$key])){
					   $colors=array();
				    $colors =$request->colors_ids[$key];
				   //echo"<br>color=".$colors;
				  //   print_r($colors);
			  foreach ($colors as $color_id) {
				  echo"<br>color=".$color_id;
                    $colors_id_alls= $color_id.",".$colors_id_alls;
					 	}


				   }else{
					   $colors_id_alls="empty color";
					   }





			     echo"<br>sizes===".$size_id ;
			    // echo"<br>sizes===".$colors_id_alls ;
			}
			}
		  exit;
			*/


			foreach ($request->quantity as $key => $value) {
				 if(isset($request->quantity[$key])){
			  //$size_id =$request->size_id[$key];


 			 if($request->has('size_active') && isset($request->size_id[$key])){
			// if($request->has('size_active') && $request->has('size_id')  ){
          		$size_id =$request->size_id;
				 echo"<br>sizes===".$size_id =$size_id[$key];
		     	$Product->update([
               'size_id' => json_encode($request->size_id)]);
         	}
        else {
           // $colors = array();
           // $product->colors = json_encode($colors);
		   $size_id ="";
		    $Product->update([
               //'sizes' => json_encode($sizes)
			   'size_id' =>''
			   ]);
        }
//dd($request->colors_ids);
            $colors_ids_alls="";
// if($request->has('color_active') && $request->has('colors_ids') && count($request->colors_ids) > 0){
 if($request->has('color_active') && isset($request->colors_ids[$key]) && count($request->colors_ids) > 0){

		   $colors=array();
				    $colors =$request->colors_ids[$key];
					 //echo"<br>color=".$colors;
				  //   print_r($colors);

			   //$colors_id_all="";
			   $colors_id_all=implode(",", $colors);
			     /*foreach ($colors  as $color_id) {
					// $color_id=json_encode($color_id);
                     echo "<br>". $colors_id_all= $color_id.",".$colors_id_all;
					 	}*/
					 ColorProductSize::create([
                        'product_id' => $request->product_id,
                         'color_id' => $colors_id_all,
						'size_id' => $size_id,
						'quantity' =>$value
                    ]);

					 echo  $colors_ids_alls= $colors_id_all.",".$colors_ids_alls;

        }
        else {

           // $colors = array();
           // $product->colors = json_encode($colors);
		   $color_id ="";
		     $Product->update([
               //'colors' => json_encode($colors)
			   'color_id' => $colors_ids_alls
			   ]);

			  ColorProductSize::create([
                        'product_id' => $request->product_id,
                       // 'color_id' =>(int) $color_id,
						'color_id' =>  $color_id,
						'size_id' => $size_id,
						'quantity' =>$value
                    ]);

       }


		//------- end if isset Main loop
	}
		//------- end foreach Main loop
}
 $Product->update([
              'color_id' =>  $colors_ids_alls
                //'color_id' => json_encode($request->colors_ids)

			   ]);
 // exit;
     /*  $choice_options = array();

        if($request->has('choice_no')){
            foreach ($request->choice_no as $key => $no) {
                $str = 'choice_options_'.$no;

                $item['attribute_id'] = $no;

                $data = array();
                // foreach (json_decode($request[$str][0]) as $key => $eachValue) {
                foreach ($request[$str] as $key => $eachValue) {
                    // array_push($data, $eachValue->value);
                    array_push($data, $eachValue);
                }*/




            $notification = array(
                'msg' => ' Added Successfully',
                'alert-type' => 'success'
            );

///////////////////////  /////////////////////////////////////




 DB::commit();
            // return redirect()->route('admin.products')->with(['success' => 'تم الاضافة بنجاح']);;
            return redirect()->route('admin.products.stocks',$product_id)->with($notification);

        } catch (\Exception $ex) {
            DB::rollback();
            return $ex;
            //return redirect()->route('admin.products')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
			 return redirect()->route('admin.products.stocks',$product_id)->with(['msg' => 'حدث خطا ما برجاء المحاوله لاحقا',  'alert-type' => 'danger'] );
        }
    }

/////////////////////////////////







//----------------------------------------------------------







}

// https://stackoverflow.com/questions/65040457/laravel-eloquent-3-tables-1-pivot-table     شرح حل دمج 3 جداول

	//https://www.youtube.com/watch?v=V5xINbA-z9o    شرح  pivot
	//https://laracasts.com/discuss/channels/eloquent/how-do-you-add-to-a-pivot-table-that-has-multiple-relationships

//https://pretagteam.com/question/how-to-query-pivot-table-with-multiple-foreign-keys-laravel-5-eloquent









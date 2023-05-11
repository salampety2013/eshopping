<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Product;
use Illuminate\Support\Carbon;
//use App\Http\Requests\ProductRequest;
use Exception;
use Illuminate\Support\Facades\DB;


class PoductsController extends Controller
{
    public function index()
    {

        $products = Product::latest()->get();
		 $subcategories = SubCategory::latest()->get();
        $categories = Category::orderBy('name_ar', 'ASC')->get();
        // $products=Product::orderBy('name_en','Desc')->get();
        //return $categories;
        return view('dashboard.products.index_pro', compact('products', 'categories','subcategories'));
        //return view('backend.category.view',compact('category'));

    }
    public function create()
    {
        $categories = Category::orderBy('name_ar', 'ASC')->get();
        $products = Product::latest()->get();
        
        return view('dashboard.products.create_pro', compact('categories','products'));
    }
    
    


         public function GetProduct($cat_id){
			// dd("ff");
			  $subcat = Product::where('cat_id',$cat_id)->orderBy('name_en','ASC')->get();
        return json_encode($subcat);
  // $subcat =Product::where('cat_id',$cat_id)->get();
    //  return  $subcat = Product::where('cat_id',$cat_id)->orderBy('name_en','ASC')->get();
       // return json_encode($subcat);
    } 
////////////////////////////////////////////////////////
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

    public function saveProductImagesDB(ProductImagesRequest $request){

        try {
			
            // save dropzone images
            if ($request->has('document') && count($request->document) > 0) {
                foreach ($request->document as $image) {
                    Image::create([
                        'product_id' => $request->product_id,
                        'photo' => $image,
                    ]);
                }
            }

            return redirect()->route('admin.products')->with(['success' => 'تم الحفظ بنجاح']);

        }catch(\Exception $ex){

        }
    }
	
	

     public function store(Request $request)
    {

        try {

            //   DB::beginTransaction();

            //validation

             

            ////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////

            $filePath = "";
            if ($request->has('img')) {
                //dd($request->img);
                // $filePath = uploadImage('products', $request->img);
                $filePath = uploadImage('assets/images/products/', $request->img);
            }
            /////////////////////////////////////////////////////////////////////////////////////

            if (!$request->has('is_active'))
                $flag = 0;
            else
                $flag = 1;

            $pro_id =Product::insertGetId([
                'name_ar' => $request->name_ar,
                'name_en' => $request->name_en,
                'cat_id' => $request->cat_id,
				 'sub_cat_id' => $request->subcategory_id,
                'slug_en' => strtolower(str_replace(' ', '-', $request->name_en)),
                'slug_ar' => str_replace(' ', '-', $request->name_ar),
				 'code' => $request->code,
				  'price' => $request->price,
				   'discount_price' => $request->discount_price,
                //'img' => $last_img,
                'img' => $filePath,
                'is_active' => $flag,
                'created_at' => Carbon::now()
            ]);
			
			
			///////////////////////insert multiple images to product /////////////////////////////////////

            $notification = array(
                'msg' => ' Added Successfully',
                'alert-type' => 'sucess'
            );



            // return redirect()->route('admin.products')->with($notification);
            return redirect()->route('admin.products')->with($notification)->with(['success' => 'تم الاضافة بنجاح']);
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            return $ex;
            return redirect()->route('admin.products')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }





    public function edit($id)
    {
        $categories = Category::latest()->get();
        $Product = Product::find($id);

        if (!$Product)
            return redirect()->route('admin.products')->with(['error' => 'هذا القسم غير موجود']);
        // $Product = Product::findOrFail($id);
        return view('dashboard.products.edit_pro', compact('Product', 'categories'));
    }




    public function update($id, Request $request)
    {
        try {
            //return $request->all();
            $Product = Product::find($request->id);
            if (!$Product)
                return redirect()->route('admin.products')->with(['error' => 'هذا القسم غير موجود']);
            /////////////upload image/////////////////////
            $old_img = $Product->img;

            $old_img_path = 'assets/images/products/' . $old_img;
            $filePath = "";
            if ($request->has('img')) {
                //dd($request->img);
                // $filePath = uploadImage('products', $request->img);
                 if($old_img!=null){
					
						if (file_exists($old_img_path)) {
						unlink($old_img_path);
                		}
				}

                $filePath = uploadImage('assets/images/products/', $request->img);
            } else {

                $filePath = $old_img;
            }
            /////////////////////////////////////////////////////////////////////////////////////

            if (!$request->has('is_active'))
                $flag = 0;
            else
                $flag = 1;
            // return $request->all();
            //return $request->quantity ;

            $Product->update([
                'name_ar' => $request->name_ar,
                'name_en' => $request->name_en,

                'slug_ar' => str_replace(' ', '-', $request->name_ar),
                'slug_en' => strtolower(str_replace(' ', '-', $request->name_en)),

                'img' => $filePath,
                'is_active' => $flag,

                'cat_id' => $request->cat_id,
                // 'sub_name_en' => $request->sub_name_en,
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
                'msg' => 'Product Updated Successfully',
                'alert-type' => 'info'
            );

            return redirect()->route('admin.products')->with(['success' => 'تم الحفظ بنجاح']);

            // return redirect()->route('admin.products')->with($notification);

        } catch (\Exception $ex) {

            return redirect()->route('admin.products')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }





    public function deactivate($id)
    {

        try {
            //get specific categories and its translations
            //  $category = Product::orderBy('id', 'DESC')->find($id);
            $Product = Product::find($id);
            if (!$Product)
                return redirect()->route('admin.products')->with(['error' => 'هذا القسم غير موجود ']);

            $Product->is_active = 0;
            $Product->save();

            return redirect()->route('admin.products')->with(['success' => 'تم الحفظ بنجاح']);
        } catch (\Exception $ex) {
            //return $ex;
            return redirect()->route('admin.products')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }



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

            return redirect()->route('admin.products')->with(['success' => 'تم الحفظ بنجاح']);
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

            return redirect()->route('admin.products')->with(['success' => 'تم  الحذف بنجاح']);
        } catch (\Exception $ex) {
            //return $ex;
            return redirect()->route('admin.products')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }

    public function delAll(Request $request)
    {
        try {
            if ($request->ids == "")
                return redirect()->route('admin.products')->with(['error' => 'من فضلك قم بالاختيار ليتم الحذف']);

            $ids = $request->ids;
            Product::whereIn('id', $ids)->delete();

            return redirect()->route('admin.products')->with(['success' => 'تم  الحذف بنجاح']);
        } catch (\Exception $ex) {
            return $ex;
            return redirect()->route('admin.products')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    } 
}

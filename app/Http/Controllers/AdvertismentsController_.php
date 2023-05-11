<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Brand;
use App\Models\Product;
use App\Models\advertisment;
use App\Models\ProPic;
use App\Models\Size;
use App\Models\Color;
 use App\Models\Coloradvertismentsize;


use App\Http\Requests\AdvertismentImagesRequest;


use Illuminate\Support\Carbon;
//use App\Http\Requests\ProductRequest;
use Exception;
use Illuminate\Support\Facades\DB;

class AdvertismentsController extends Controller
{
    public function index()
    {


       // $advertisments = Product::latest()->get();
		// $subcategories = SubCategory::latest()->get();
       // $categories = Category::orderBy('name_ar', 'ASC')->get();


		 $advertisments = Advertisment::with(['category' => function ($q) {

            return $q->select('id', 'name_ar','name_en');

        }
         ])->latest()->get();

        //return view('dashboard.advertisments.index_pro', compact('advertisments', 'categories','subcategories'));
              return view('dashboard.advertisments.index', compact('advertisments'));

			  /*      $advertisments = Product::with(['category' => function ($q) {

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
     public function create( Request $request)
    {
         $categories = Category:: where('is_active',"1")->orderBy('name_ar', 'ASC')->get();
         $advertisment = new Advertisment();


     // $brands = Brand::where('is_active',"1")->orderBy('name_ar', 'ASC')->get();


	 /* if(isset( $request->type)) {

		  $type=$request->type	;
		 }else{
			 $type="";
			 } */


	 return view('dashboard.advertisments.create', compact('categories','advertisment'));

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

	 public function addImages($advertisment_id){
        return view('dashboard.advertisments.images.create')->withId($advertisment_id);
    }



	//////////////////////////////////////////////////////
  public function saveAdvertismentImages(AdvertismentImagesRequest $request ){


            $filePath = "";
            if ($request->has('dzfile')) {
                //dd($request->img);

                $filePath = uploadImage('assets/images/advertisments/', $request->dzfile);

            /////////////////////////////////////////////////////////////////////////////////////

        $file = $request->file('dzfile');
       // $filename = uploadImage('advertisments', $file);

        return response()->json([
            'name' => $filePath,
            'original_name' => $file->getClientOriginalName(),
        ]);
 }
    }
////////////////////////////////////////////////////////////////
    public function saveAdvertismentImagesDB(AdvertismentImagesRequest $request){
dd('xxxx');
        try {

            // save dropzone images
            if ($request->has('document') && count($request->document) > 0) {
                foreach ($request->document as $image) {
                    ProPic::create([
                        'pro_id' => $request->advertisment_id,
                        'img' => $image,
                    ]);
                }
            }

            return redirect()->route('admin.advertisments')->with(['success' => 'تم الحفظ بنجاح']);

        }catch(\Exception $ex){

        }
    }

       /////////////////////////////////////////////////////////////

 public function delDropzoneImages(Request $request)
    {

             $img =$request->filename;

            $img_path = 'assets/images/advertisments/'.$img;

            if (file_exists($img_path)) {
                unlink($img_path);
				//dd($img_path);
			}
			return response()->json(['name'=>$img]);

    }




 public function store(Request $request)
    {

        try {
                //validation

                        // return $request;

                DB::beginTransaction();


            ////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////

            $filePath = "";
            if ($request->has('img')) {
               // dd($request->img);
                // $filePath = uploadImage('advertisments', $request->img);
                $filePath = uploadImage('assets/images/advertisments/', $request->img);
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
                // $filePath = uploadImage('advertisments', $request->img);
                $start_date = $request->start_date;
				$end_date = $request->end_date;
            }else{

                $start_date = $request->start_date;
				$end_date = $request->end_date;
				}
            $pro_id =Advertisment::insertGetId([
                'name_ar' => $request->name_ar,
                'name_en' => $request->name_en,
                'cat_id' => $request->cat_id,

                'slug_en' => strtolower(str_replace(' ', '-', $request->name_en)),
                'slug_ar' => str_replace(' ', '-', $request->name_ar),

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




            $notification = array(
                'msg' => ' Added Successfully',
                'alert-type' => 'success'
            );

///////////////////////insert multiple images to Advertisment /////////////////////////////////////




			 if ($request->has('document') && count($request->document) > 0) {
                foreach ($request->document as $image) {
                    ProPic::create([
                        'pro_id' => $pro_id,
                        'img' => $image,
                    ]);
                }
			 }
 DB::commit();
            // return redirect()->route('admin.advertisments')->with(['success' => 'تم الاضافة بنجاح']);;
            return redirect()->route('admin.advertisments')->with($notification);

        } catch (\Exception $ex) {
            DB::rollback();
            return $ex;
            //return redirect()->route('admin.advertisments')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
			 return redirect()->route('admin.advertisments')->with(['msg' => 'حدث خطا ما برجاء المحاوله لاحقا',  'alert-type' => 'danger'] );
        }
    }


     public function edit($id)
    {
         $categories = Category::where('is_active',"1")->orderBy('name_ar', 'ASC')->get();





       $advertisment = Advertisment::with('images')->find($id);
       //  $advertisment = Advertisment::find($id);

   //  return $rr= $advertisment->images;

        if (!$advertisment)
            return redirect()->route('admin.advertisments')->with(['error' => 'هذا القسم غير موجود']);
        // $Advertisment = Advertisment::findOrFail($id);
        return view('dashboard.advertisments.edit', compact('advertisment', 'categories'));
    }




    public function update($id, Request $request)
    {
        try {
             //return $request->all();
			   DB::beginTransaction();

            $advertisment = Advertisment::find($request->id);
            if (!$advertisment)
                return redirect()->route('admin.advertisments')->with(['error' => 'هذا المنتج غير موجود']);
            /////////////upload image/////////////////////
            $old_img = $advertisment->img;

            $old_img_path = 'assets/images/advertisments/' . $old_img;
            $filePath = "";
			// dd($old_img_path);
            if ($request->has('img')) {
                //  dd($request->img);
                  //$filePath = uploadImage('advertisments', $request->img);
                if($old_img!=null){

					if (file_exists($old_img_path)) {
						unlink($old_img_path);
                }
				}

                $filePath = uploadImage('assets/images/advertisments/', $request->img);
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
            $advertisment->update([
               'name_ar' => $request->name_ar,
                'name_en' => $request->name_en,
                'cat_id' => $request->cat_id,

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


            //  $advertisment->name_en = $request->name_en;
            // $advertisment->save();
            //$advertisment->update($request->all());
            //$advertisment = advertisment::get();
            // DB::enableQueryLog();
            //$query = DB::getQueryLog();
            //$query = end($query);
            //dd($query);


            $notification = array(
                'msg' => 'Updated Successfully',
                'alert-type' => 'success'
            );


///////////////////////insert multiple images to advertisment /////////////////////////////////////




			 if ($request->has('document') && count($request->document) > 0) {
                foreach ($request->document as $image) {
                    ProPic::create([
                        'pro_id' => $id,
                        'img' => $image,
                    ]);
                }
			 }
 DB::commit();
            // return redirect()->route('admin.advertisments')->with(['success' => 'تم الاضافة بنجاح']);;
            return redirect()->back()->with($notification);
        } catch (\Exception $ex) {
 		DB::rollback();
           // return redirect()->route('admin.advertisments')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
		    return redirect()->route('admin.advertisments')->with(['msg' => 'حدث خطا ما برجاء المحاوله لاحقا',  'alert-type' => 'danger'] );
        }
    }





    public function deactivate($id)
    {

        try {

            //  $category = Advertisment::orderBy('id', 'DESC')->find($id);
            $advertisment = Advertisment::find($id);
            if (!$advertisment)
                return redirect()->route('admin.advertisments')->with(['error' => 'هذا القسم غير موجود ']);

            $advertisment->is_active = 0;
            $advertisment->save();
			 $notification = array(
                'msg' => ' updated Successfully',
                'alert-type' => 'info'
            );
			return redirect()->route('admin.advertisments')->with($notification);
         //   return redirect()->route('admin.advertisments')->with(['success' => 'تم الحفظ بنجاح']);
        } catch (\Exception $ex) {
            //return $ex;
           // return redirect()->route('admin.advertisments')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
		    return redirect()->route('admin.advertisments')->with(['msg' => 'حدث خطا ما برجاء المحاوله لاحقا',  'alert-type' => 'danger'] );
        }
    }


//-----------------------------------------------
//delete images of advertisment
//--------------------------------------

 public function deleteImage($id)
    {

        try {
             $image = ProPic::find($id);
            if (!$image)
                return redirect()->route('admin.advertisments')->with(['error' => 'هذا القسم غير موجود ']);



            $image_name = $image->img;
            $img_path = 'assets/images/advertisments/' . $image_name;

            if (file_exists($img_path)) {
                unlink($img_path);
            }

            $image->delete();
 $notification = array(
                'msg' => ' deleted Successfully',
                'alert-type' => 'success'
            );
			return redirect()->back()->with($notification);
           // return redirect()->route('admin.advertisments')->with(['success' => 'تم  الحذف بنجاح']);
        } catch (\Exception $ex) {
            //return $ex;
           // return redirect()->route('admin.advertisments')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
		    return redirect()->back()->with(['msg' => 'حدث خطا ما برجاء المحاوله لاحقا',  'alert-type' => 'danger'] );

        }
    }
//--------------------------------------------------------

    public function activate($id)
    {

        try {
            //get specific categories and its translations
            //  $category = advertisment::orderBy('id', 'DESC')->find($id);
            $advertisment = Advertisment::find($id);
            if (!$advertisment)
                return redirect()->route('admin.advertisments')->with(['error' => 'هذا القسم غير موجود ']);

            $advertisment->is_active = 1;
            $advertisment->save();
			 $notification = array(
                'msg' => ' updated Successfully',
                'alert-type' => 'info'
            );
		return redirect()->route('admin.advertisments')->with($notification);
           // return redirect()->route('admin.advertisments')->with(['success' => 'تم الحفظ بنجاح']);
        } catch (\Exception $ex) {
            //return $ex;
            return redirect()->route('admin.advertisments')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }
    public function destroy($id)
    {

        try {
            //get specific categories and its translations
            //  $category = Advertisment::orderBy('id', 'DESC')->find($id);
            $advertisment = Advertisment::find($id);
            if (!$advertisment)
                return redirect()->route('admin.advertisments')->with(['error' => 'هذا القسم غير موجود ']);



            $old_img = $advertisment->img;
            $old_img_path = 'assets/images/advertisments/' . $old_img;

            if (file_exists($old_img_path)) {
                unlink($old_img_path);
            }

            $advertisment->delete();
 $notification = array(
                'msg' => ' deleted Successfully',
                'alert-type' => 'success'
            );
			return redirect()->route('admin.advertisments')->with($notification);
           // return redirect()->route('admin.advertisments')->with(['success' => 'تم  الحذف بنجاح']);
        } catch (\Exception $ex) {
            //return $ex;
           // return redirect()->route('admin.advertisments')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
		    return redirect()->route('admin.advertisments')->with(['msg' => 'حدث خطا ما برجاء المحاوله لاحقا',  'alert-type' => 'danger'] );

        }
    }

    public function delAll(Request $request)
    {
        try {
            if ($request->ids == "")
               // return redirect()->route('admin.advertisments')->with(['error' => 'من فضلك قم بالاختيار ليتم الحذف']);
 return redirect()->route('admin.advertisments')->with(['msg' => 'من فضلك قم بالاختيار ليتم الحذف',  'alert-type' => 'danger'] );
            $ids = $request->ids;
            Advertisment::whereIn('id', $ids)->delete();
 $notification = array(
                'msg' => ' deleted Successfully',
                'alert-type' => 'info'
            );
			return redirect()->route('admin.advertisments')->with($notification);
           // return redirect()->route('admin.advertisments')->with(['success' => 'تم  الحذف بنجاح']);
        } catch (\Exception $ex) {
            return $ex;
           // return redirect()->route('admin.advertisments')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
 return redirect()->route('admin.advertisments')->with(['msg' => 'حدث خطا ما برجاء المحاوله لاحقا',  'alert-type' => 'danger'] );        }
    }



	///// ////get pivot relation size and color of that advertisment/////////





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


//////////////////////////////////////////////////////////










}


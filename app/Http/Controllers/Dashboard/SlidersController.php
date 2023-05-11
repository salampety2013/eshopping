<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
 
use App\Models\Slider;
use Illuminate\Support\Carbon;
 use Exception;
use Illuminate\Support\Facades\DB;


class SlidersController extends Controller
{
    public function index()
    {
		//$sliders = Slider::latest()->paginate(2);;
          $sliders = Slider::latest()->get();
        
        // $sliders=Slider::orderBy('title_en','Desc')->get();
        //return $categories;
        return view('dashboard.sliders.index_slider', compact('sliders'));
        //return view('backend.category.view',compact('category'));

    }
    public function create()
    {
        
        return view('dashboard.sliders.create_slider' );
    }

///--------------------------------------------------------------
  public function saveSliderImages(Request $request ){


            $filePath = "";
            if ($request->has('dzfile')) {
                //dd($request->img);
                
                $filePath = uploadImage('assets/images/sliders/', $request->dzfile);
           
            /////////////////////////////////////////////////////////////////////////////////////

        $file = $request->file('dzfile');
       // $filename = uploadImage('sliders', $file);

        return response()->json([
            'name' => $filePath,
            'original_name' => $file->getClientOriginalName(),
        ]);
 }
    }
////////////////////////////////////////////////////////////////

//--------------------------------------------------
public function delDropzoneImages(Request $request)
    {

             $img =$request->filename;
            $img_path = 'assets/images/sliders/'.$img;

            if (file_exists($img_path)) {
				//dd($img_path);
                unlink($img_path);
			}
			return response()->json(['name'=>$img]);
			 
    }


//---------------------------------------------
    public function store(Request $request)
    {

        try {

               DB::beginTransaction();

            //validation

            //dd( $request);
            /* $request->validate([
            'title_en' => 'required',
            'title_ar' => 'required',
            'img' => 'required',
        ], [
            'title_en.required' => 'Input Slider English Name',
            'title_ar.required' => 'Input Slider AR Name',
        ]);*/

            ////////////////////////////////////////////////////////
            
            /////////////////////////////////////////////////////////////////////////////////////

 if ($request->has('document') && count($request->document) > 0) {
	  
               foreach ($request->document as $image) {
				   $img  = $image;
                    /* ProPic::create([
                        'pro_id' => $pro_id,
                        'img' => $image,
                    ]);*/
                }
			 }
			 
			 //------------------------------------------------------
            if (!$request->has('is_active'))
                $flag = 0;
            else
                $flag = 1;

            Slider::insert([
                'title_ar' => $request->title_ar,
                'title_en' => $request->title_en,
                
                'link' => $request->link,
               //  'img' => $filePath,
                 'img' => $img,
                'is_active' => $flag,
                'created_at' => Carbon::now()
            ]);

            $notification = array(
                'msg' => 'Slider Added Successfully',
                'alert-type' => ' success'
            );
             DB::commit();
             return redirect()->route('admin.sliders')->with($notification);
          //  return redirect()->route('admin.sliders')->with(['success' => 'تم ألاضافة بنجاح']);
          
        } catch (\Exception $ex) {
            DB::rollback();
            return $ex;
            return redirect()->route('admin.sliders')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }





    public function edit($id)
    {
       
        $slider = Slider::find($id);

        if (!$slider)
            return redirect()->route('admin.sliders')->with(['error' => 'هذا القسم غير موجود']);
        // $sliders = Slider::findOrFail($id);
        return view('dashboard.sliders.edit_slider', compact('slider'));
    }




    public function update($id, Request $request)
    {
        try {
			DB::beginTransaction();

            //return $request->all();
            $sliders = Slider::find($request->id);
            if (!$sliders)
                return redirect()->route('admin.sliders')->with(['error' => 'هذا القسم غير موجود']);
            /////////////upload image/////////////////////
            $old_img = $sliders->img;

            $old_img_path = 'assets/images/sliders/' . $old_img;
            $filePath = "";
            if ($request->has('img')) {
                //dd($request->img);
                // $filePath = uploadImage('sliders', $request->img);
               if($old_img!=null){
					
					if (file_exists($old_img_path)) {
						unlink($old_img_path);
                }
				}

                $filePath = uploadImage('assets/images/sliders/', $request->img);
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

            $sliders->update([
                'title_ar' => $request->title_ar,
                'title_en' => $request->title_en,

                'link' => $request->link,

                'img' => $filePath,
                'is_active' => $flag,

                
                // 'sub_title_en' => $request->sub_title_en,
                'updated_at' => Carbon::now()
            ]);


            //  $sliders->title_en = $request->title_en;
            // $sliders->save();
            //$sliders->update($request->all());
            //$product = Slider::get();
            // DB::enableQueryLog();
            //$query = DB::getQueryLog();
            //$query = end($query);
            //dd($query);
			DB::commit();


            $notification = array(
                'msg' => 'Slider Updated Successfully',
                'alert-type' => 'info'
            );

           // return redirect()->route('admin.sliders')->with(['success' => 'تم الحفظ بنجاح']);

             return redirect()->route('admin.sliders')->with($notification);

        } catch (\Exception $ex) {
//return  $ex;
			DB::rollback();

            return redirect()->route('admin.sliders')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }





    public function deactivate($id)
    {

        try {
            //get specific categories and its translations
            //  $category = Slider::orderBy('id', 'DESC')->find($id);
            $sliders = Slider::find($id);
            if (!$sliders)
                return redirect()->route('admin.sliders')->with(['error' => 'هذا القسم غير موجود ']);

            $sliders->is_active = 0;
            $sliders->save();

            return redirect()->route('admin.sliders')->with(['success' => 'تم الحفظ بنجاح']);
        } catch (\Exception $ex) {
            //return $ex;
            return redirect()->route('admin.sliders')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }



    public function activate($id)
    {

        try {
            //get specific categories and its translations
            //  $category = Slider::orderBy('id', 'DESC')->find($id);
            $sliders = Slider::find($id);
            if (!$sliders)
                return redirect()->route('admin.sliders')->with(['error' => 'هذا القسم غير موجود ']);

            $sliders->is_active = 1;
            $sliders->save();
$notification = array(
                'msg' => 'Updated Successfully',
                'alert-type' => 'success'
            );
			
           // return redirect()->route('admin.sliders')->with(['success' => 'تم الحفظ بنجاح']);

             return redirect()->route('admin.sliders')->with($notification)->with(['success' => 'تم الحفظ بنجاح']);

         } catch (\Exception $ex) {
            //return $ex;
            return redirect()->route('admin.sliders')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }
    public function destroy($id)
    {

        try {
			DB::beginTransaction();

            //get specific categories and its translations
            //  $category = Slider::orderBy('id', 'DESC')->find($id);
            $sliders = Slider::find($id);
			
            if (!$sliders)
                return redirect()->route('admin.sliders')->with(['error' => 'هذا القسم غير موجود ']);



            $old_img = $sliders->img;
            $old_img_path = 'assets/images/sliders/' . $old_img;

            if (file_exists($old_img_path)) {
                unlink($old_img_path);
            }

            $sliders->delete();
			DB::commit();
		

            return redirect()->route('admin.sliders')->with(['success' => 'تم  الحذف بنجاح']);
        } catch (\Exception $ex) {
            //return $ex;
				DB::rollback();
            return redirect()->route('admin.sliders')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }

    public function delAll(Request $request)
    {
        try {
            if ($request->ids == "")
                return redirect()->route('admin.sliders')->with(['error' => 'من فضلك قم بالاختيار ليتم الحذف']);

            $ids = $request->ids;
            Slider::whereIn('id', $ids)->delete();

            return redirect()->route('admin.sliders')->with(['success' => 'تم  الحذف بنجاح']);
        } catch (\Exception $ex) {
            return $ex;
            return redirect()->route('admin.sliders')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }
	
	//---------------------------------------------
	// add multiple images without title to Slider 
	//---------------------------------------------
	
	
	
	
	 public function createMultiple()
    {
        
        return view('dashboard.sliders.create_multiPic_slider' );
    }

///--------------------------------------------------------------
 
//---------------------------------------------
    public function storeMultiple(Request $request)
    {

        try {

               DB::beginTransaction();

            //validation

            //dd( $request);
            /* $request->validate([
            'title_en' => 'required',
            'title_ar' => 'required',
            'img' => 'required',
        ], [
            'title_en.required' => 'Input Slider English Name',
            'title_ar.required' => 'Input Slider AR Name',
        ]);*/

            ////////////////////////////////////////////////////////
            
            /////////////////////////////////////////////////////////////////////////////////////

 if ($request->has('document') && count($request->document) > 0) {
	  
               foreach ($request->document as $image) {
				  
                    Slider::insert([
                 
                 'img' => $image,
                'is_active' => 1,
                'created_at' => Carbon::now()
            ]);
                }
			 }
			 
			 //------------------------------------------------------
             

            

            $notification = array(
                'msg' => 'Slider Added Successfully',
                'alert-type' => ' success'
            );
             DB::commit();
             return redirect()->route('admin.sliders')->with($notification);
          //  return redirect()->route('admin.sliders')->with(['success' => 'تم ألاضافة بنجاح']);
          
        } catch (\Exception $ex) {
            DB::rollback();
            return $ex;
            return redirect()->route('admin.sliders')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }



	
	
	
	
	
	
	
	
	
	
	
	
}

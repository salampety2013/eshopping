<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
 use Illuminate\Support\Carbon;
 use Exception;
use Illuminate\Support\Facades\DB;
use App\Models\Color;


class ColorsController extends Controller
{
    public function index()
    {

        $colors =  Color::latest()->get();
       
        // $colors= Color::orderBy('id','Desc')->get();
        //return $colors;
        return view('dashboard.colors.index_colors', compact('colors'));
        

    }
    public function create()
    {
      
        return view('dashboard.colors.create_colors');
    }

      public function store(Request $request)
    {

        try {

            	  //DB::beginTransaction();
      //  return  $request; 
	  
	    $request->validate([ 
		// 'moreFields.*.name_ar' => 'required',
            //'name_ar.*' => 'required',
			'code' => 'required',
        ], [
            'type.required' => 'Input Category English Name',
//'name_ar.required' => 'Input Category AR Name',
        ]); 
	  
		 
		 
	$name_ar=$request->name_ar;
	$name_en=$request->name_en;
	$code=$request->code;
	  foreach ($name_ar as $key => $value) {
             
			  
			 Color::create([
            'name_ar' => $name_ar[$key],
            
            'name_en' => $name_en[$key],
           
            'code' => $code[$key],
            
        ]); }
		 
       
            $notification = array(
                'msg' => ' Color Updated Successfully',
                'alert-type' => 'success'
            );



             return redirect()->route('admin.colors')->with($notification);
           // return redirect()->route('admin.colors')->with(['success' => 'تم ألاضافة بنجاح']);
         //  DB::commit();
        } catch (\Exception $ex) {
          //  DB::rollback();
             return $ex;
			$notification = array(
                'msg' => 'حدث خطا ما برجاء المحاوله لاحقا',
                'alert-type' => 'danger'
            );
             return redirect()->route('admin.colors')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }





    public function edit($id)
    {
        
        $colors =  Color::find($id);

        if (!$colors)
            return redirect()->route('admin.colors')->with(['error' => 'هذا القسم غير موجود']);
        // $colors =  Color::findOrFail($id);
        return view('dashboard.colors.edit_colors', compact('colors'));
    }




    public function update($id, Request $request)
    {
        try {
            //return $request->all();
            $colors =  Color::find($request->id);
            if (!$colors)
                return redirect()->route('admin.colors')->with(['error' => 'هذا القسم غير موجود']);
            ///////////// /////////////////////
             

             
           

            

 

 $colors->update([
                'name_ar' => $request->name_ar,
                'name_en' => $request->name_ar,
                 'code' => $request->code,
                 'updated_at' => Carbon::now()
            ]);

 


            $notification = array(
                'msg' => 'Updated Successfully',
                'alert-type' => 'success'
            );

           // return redirect()->route('admin.colors')->with(['success' => 'تم الحفظ بنجاح']);

             return redirect()->route('admin.colors')->with($notification)->with(['success' => 'تم الحفظ بنجاح']);

        } catch (\Exception $ex) {
		//return  $ex;
           // return redirect()->route('admin.colors')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
                   return redirect()->route('admin.colors')->with(['msg' => 'حدث خطا ما برجاء المحاوله لاحقا','alert-type' => 'danger']);

	   
	    }
		}




    public function deactivate($id)
    {

        try {
            //get specific categories and its translations
            //  $category =  Color::orderBy('id', 'DESC')->find($id);
            $colors =  Color::find($id);
            if (!$colors)
                return redirect()->route('admin.colors')->with(['error' => 'هذا القسم غير موجود ']);

            $colors->is_active = 0;
            $colors->save();

            return redirect()->route('admin.colors')->with(['success' => 'تم الحفظ بنجاح']);
        } catch (\Exception $ex) {
            //return $ex;
            return redirect()->route('admin.colors')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }



    public function activate($id)
    {

        try {
            //get specific categories and its translations
            //  $category =  Color::orderBy('id', 'DESC')->find($id);
            $colors =  Color::find($id);
            if (!$colors)
                return redirect()->route('admin.colors')->with(['error' => 'هذا القسم غير موجود ']);

            $colors->is_active = 1;
            $colors->save();

            return redirect()->route('admin.colors')->with(['success' => 'تم الحفظ بنجاح']);
        } catch (\Exception $ex) {
            //return $ex;
            return redirect()->route('admin.colors')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }
    public function destroy($id)
    {

        try {
            //get specific categories and its translations
            //  $category =  Color::orderBy('id', 'DESC')->find($id);
            $colors =  Color::find($id);
            if (!$colors)
                return redirect()->route('admin.colors')->with(['error' => 'هذا القسم غير موجود ']);



            $old_img = $colors->img;
            $old_img_path = 'assets/images/colors/' . $old_img;

            if (file_exists($old_img_path)) {
                unlink($old_img_path);
            }

            $colors->delete();

            return redirect()->route('admin.colors')->with(['success' => 'تم  الحذف بنجاح']);
        } catch (\Exception $ex) {
            //return $ex;
            return redirect()->route('admin.colors')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }

    public function delAll(Request $request)
    {
        try {
            if ($request->ids == "")
                return redirect()->route('admin.colors')->with(['error' => 'من فضلك قم بالاختيار ليتم الحذف']);

            $ids = $request->ids;
             Color::whereIn('id', $ids)->delete();

            return redirect()->route('admin.colors')->with(['success' => 'تم  الحذف بنجاح']);
        } catch (\Exception $ex) {
            return $ex;
            return redirect()->route('admin.colors')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }
}

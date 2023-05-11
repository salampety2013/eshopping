<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
 use Illuminate\Support\Carbon;
 use Exception;
use Illuminate\Support\Facades\DB;
use App\Models\Size;


class SizesController extends Controller
{
    public function index()
    {

        $sizes =  Size::latest()->get();
       
        // $sizes= Size::orderBy('id','Desc')->get();
        //return $sizes;
        return view('dashboard.sizes.index_sizes', compact('sizes'));
        

    }
    public function create()
    {
      
        return view('dashboard.sizes.create_sizes');
    }

    public function store(Request $request)
    {

        try {

            	   DB::beginTransaction();
      //  return  $request; 
	  
	    $request->validate([ 
		// 'moreFields.*.name_ar' => 'required',
            //'name_ar.*' => 'required',
			'type' => 'required',
        ], [
            'type.required' => 'Input Category English Name',
//'name_ar.required' => 'Input Category AR Name',
        ]); 
	  
   
		 
		 
		 $type = $request->type;
		 
		 
		 /* $count = count($request->name_ar);

    for ($i=0; $i < $count; $i++) { 
	  $task = new Size();
	  $task->name_ar = $request->name_ar[$i];
	  $task->name_en = $request->name_ar[$i];
	  $task->type = $request->type;
	  $task->save();
    }*/
	$size_count=$request->name_ar;
	  foreach ($size_count as $key => $value) {
             
			  
			 Size::create([
            'name_ar' => $size_count [$key],
            
            'name_en' => $size_count [$key],
           
            'type' => $type,
            
        ]); }
		 
        DB::commit();
            $notification = array(
                'msg' => ' Size Added Successfully',
                'alert-type' => 'success'
            );


		
             return redirect()->route('admin.sizes')->with($notification);
           // return redirect()->route('admin.sizes')->with(['success' => 'تم ألاضافة بنجاح']);
          
        } catch (\Exception $ex) {
             DB::rollback();
            //return $ex;
			$notification = array(
                'msg' => 'حدث خطا ما برجاء المحاوله لاحقا',
                'alert-type' => 'danger'
            );
           // return redirect()->route('admin.sizes')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }





    public function edit($id)
    {
        
        $sizes =  Size::find($id);

        if (!$sizes)
            return redirect()->route('admin.sizes')->with(['error' => 'هذا القسم غير موجود']);
        // $sizes =  Size::findOrFail($id);
        return view('dashboard.sizes.edit_sizes', compact('sizes'));
    }




    public function update($id, Request $request)
    {
        try {
            //return $request->all();
			DB::beginTransaction();
            $sizes =  Size::find($request->id);
            if (!$sizes)
                return redirect()->route('admin.sizes')->with(['error' => 'هذا القسم غير موجود']);
            ///////////// /////////////////////
             

             
           

            
            // return $request->all();
 
            $sizes->update([
                'name_ar' => $request->name_ar,
                'name_en' => $request->name_ar,
                 'type' => $request->type,
                 'updated_at' => Carbon::now()
            ]);

 
			DB::commit();

            $notification = array(
                'msg' => 'Updated Successfully',
                'alert-type' => 'success'
            );
			
           // return redirect()->route('admin.sizes')->with(['success' => 'تم الحفظ بنجاح']);

             return redirect()->route('admin.sizes')->with($notification)->with(['success' => 'تم الحفظ بنجاح']);

        } catch (\Exception $ex) {
			 DB::rollback();
		//return  $ex;
           // return redirect()->route('admin.sizes')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
                   return redirect()->route('admin.sizes')->with(['msg' => 'حدث خطا ما برجاء المحاوله لاحقا','alert-type' => 'danger']);

	   
	    }
    }





    public function deactivate($id)
    {

        try {
			DB::beginTransaction();
            //get specific categories and its translations
            //  $category =  Size::orderBy('id', 'DESC')->find($id);
            $sizes =  Size::find($id);
            if (!$sizes)
                return redirect()->route('admin.sizes')->with(['error' => 'هذا القسم غير موجود ']);

            $sizes->is_active = 0;
            $sizes->save();
			DB::commit();
            return redirect()->route('admin.sizes')->with(['success' => 'تم الحفظ بنجاح']);
        } catch (\Exception $ex) {
			 DB::rollback();
            //return $ex;
            return redirect()->route('admin.sizes')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }

    public function activate($id)
    {

        try {
			DB::beginTransaction();
            //get specific categories and its translations
            //  $category =  Size::orderBy('id', 'DESC')->find($id);
            $sizes =  Size::find($id);
            if (!$sizes)
                return redirect()->route('admin.sizes')->with(['error' => 'هذا القسم غير موجود ']);

            $sizes->is_active = 1;
            $sizes->save();
			DB::commit();
            return redirect()->route('admin.sizes')->with(['success' => 'تم الحفظ بنجاح']);
        } catch (\Exception $ex) {
            //return $ex;
			 DB::rollback();
            return redirect()->route('admin.sizes')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }
    public function destroy($id)
    {

        try {
			DB::beginTransaction();
            //get specific categories and its translations
            //  $category =  Size::orderBy('id', 'DESC')->find($id);
            $sizes =  Size::find($id);
            if (!$sizes)
                return redirect()->route('admin.sizes')->with(['error' => 'هذا القسم غير موجود ']);



            $old_img = $sizes->img;
            $old_img_path = 'assets/images/sizes/' . $old_img;

            if (file_exists($old_img_path)) {
                unlink($old_img_path);
            }

            $sizes->delete();
			DB::commit();
            return redirect()->route('admin.sizes')->with(['success' => 'تم  الحذف بنجاح']);
        } catch (\Exception $ex) {
            //return $ex;
			 DB::rollback();
            return redirect()->route('admin.sizes')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }

    public function delAll(Request $request)
    {
        try {
			DB::beginTransaction();
            if ($request->ids == "")
                return redirect()->route('admin.sizes')->with(['error' => 'من فضلك قم بالاختيار ليتم الحذف']);

            $ids = $request->ids;
             Size::whereIn('id', $ids)->delete();
				DB::commit();
            return redirect()->route('admin.sizes')->with(['success' => 'تم  الحذف بنجاح']);
        } catch (\Exception $ex) {
            return $ex;
			 DB::rollback();
            return redirect()->route('admin.sizes')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }
}

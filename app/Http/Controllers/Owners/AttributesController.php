<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
 use Illuminate\Support\Carbon;
 use Exception;
use Illuminate\Support\Facades\DB;
use App\Models\Attribute;


class AttributesController extends Controller
{
    public function index()
    {

        $attributes =  Attribute::latest()->get();
       
        // $attributes= Attribute::orderBy('id','Desc')->get();
        //return $attributes;
        return view('dashboard.attributes.index_attribute', compact('attributes'));
        

    }
    public function create()
    {
      
        return view('dashboard.attributes.create_attribute');
    }

    public function store(Request $request)
    {

        try {

            	  //DB::beginTransaction();

            //validation

            //dd( $request);
            /* $request->validate([
            'name_en' => 'required',
            'name_ar' => 'required',
            
        ], [
            'name_en.required' => 'Input  Attribute English Name',
            'name_ar.required' => 'Input  Attribute AR Name',
        ]);*/

            ////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////

            if (!$request->has('is_active'))
            $request->request->add(['is_active' => 0]);
        else
            $request->request->add(['is_active' => 1]);
			 $attribute = Attribute::create($request->except('_token'));
//return $attribute;	
           /* if (!$request->has('is_active'))
                $flag = 0;
            else
                $flag = 1;

             Attribute::insert([
                'name_ar' => $request->name_ar,
                'name_en' => $request->name_en,
                
                
                'is_active' => $flag,
                'created_at' => Carbon::now()
            ]);
*/
            $notification = array(
                'msg' => ' Attribute Updated Successfully',
                'alert-type' => 'info'
            );



            // return redirect()->route('admin.attributes')->with($notification);
            return redirect()->route('admin.attributes')->with(['success' => 'تم ألاضافة بنجاح']);
         //  DB::commit();
        } catch (\Exception $ex) {
          //  DB::rollback();
            return $ex;
            return redirect()->route('admin.attributes')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }





    public function edit($id)
    {
        
        $attributes =  Attribute::find($id);

        if (!$attributes)
            return redirect()->route('admin.attributes')->with(['error' => 'هذا القسم غير موجود']);
        // $attributes =  Attribute::findOrFail($id);
        return view('dashboard.attributes.create_attribute', compact('attributes'));
    }




    public function update($id, Request $request)
    {
        try {
            //return $request->all();
            $attributes =  Attribute::find($request->id);
            if (!$attributes)
                return redirect()->route('admin.attributes')->with(['error' => 'هذا القسم غير موجود']);
            ///////////// /////////////////////
             

             
           

            if (!$request->has('is_active'))
                $flag = 0;
            else
                $flag = 1;
            // return $request->all();
 
            $attributes->update([
                'name_ar' => $request->name_ar,
                'name_en' => $request->name_en,
                 'is_active' => $flag,
                 'updated_at' => Carbon::now()
            ]);

 


            $notification = array(
                'msg' => ' Attribute Updated Successfully',
                'alert-type' => 'info'
            );

            return redirect()->route('admin.attributes')->with(['success' => 'تم الحفظ بنجاح']);

            // return redirect()->route('admin.attributes')->with($notification);

        } catch (\Exception $ex) {
return  $ex;
            return redirect()->route('admin.attributes')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }





    public function deactivate($id)
    {

        try {
            //get specific categories and its translations
            //  $category =  Attribute::orderBy('id', 'DESC')->find($id);
            $attributes =  Attribute::find($id);
            if (!$attributes)
                return redirect()->route('admin.attributes')->with(['error' => 'هذا القسم غير موجود ']);

            $attributes->is_active = 0;
            $attributes->save();

            return redirect()->route('admin.attributes')->with(['success' => 'تم الحفظ بنجاح']);
        } catch (\Exception $ex) {
            //return $ex;
            return redirect()->route('admin.attributes')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }



    public function activate($id)
    {

        try {
            //get specific categories and its translations
            //  $category =  Attribute::orderBy('id', 'DESC')->find($id);
            $attributes =  Attribute::find($id);
            if (!$attributes)
                return redirect()->route('admin.attributes')->with(['error' => 'هذا القسم غير موجود ']);

            $attributes->is_active = 1;
            $attributes->save();

            return redirect()->route('admin.attributes')->with(['success' => 'تم الحفظ بنجاح']);
        } catch (\Exception $ex) {
            //return $ex;
            return redirect()->route('admin.attributes')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }
    public function destroy($id)
    {

        try {
            //get specific categories and its translations
            //  $category =  Attribute::orderBy('id', 'DESC')->find($id);
            $attributes =  Attribute::find($id);
            if (!$attributes)
                return redirect()->route('admin.attributes')->with(['error' => 'هذا القسم غير موجود ']);



            $old_img = $attributes->img;
            $old_img_path = 'assets/images/attributes/' . $old_img;

            if (file_exists($old_img_path)) {
                unlink($old_img_path);
            }

            $attributes->delete();

            return redirect()->route('admin.attributes')->with(['success' => 'تم  الحذف بنجاح']);
        } catch (\Exception $ex) {
            //return $ex;
            return redirect()->route('admin.attributes')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }

    public function delAll(Request $request)
    {
        try {
            if ($request->ids == "")
                return redirect()->route('admin.attributes')->with(['error' => 'من فضلك قم بالاختيار ليتم الحذف']);

            $ids = $request->ids;
             Attribute::whereIn('id', $ids)->delete();

            return redirect()->route('admin.attributes')->with(['success' => 'تم  الحذف بنجاح']);
        } catch (\Exception $ex) {
            return $ex;
            return redirect()->route('admin.attributes')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }
}

<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
 
use App\Models\Brand;
use Illuminate\Support\Carbon;
use App\Http\Requests\BrandRequest;
use Exception;
use Illuminate\Support\Facades\DB;


class BrandsController extends Controller
{
    public function index()
    {
		//$brands = Brand::latest()->paginate(2);;
          $brands = Brand::latest()->get();
        
        // $brands=Brand::orderBy('name_en','Desc')->get();
        //return $categories;
        return view('dashboard.brands.index_brands', compact('brands'));
        //return view('backend.category.view',compact('category'));

    }
    public function create()
    {
        
        return view('dashboard.brands.create_brands' );
    }

    public function store(Request $request)
    {

        try {

               DB::beginTransaction();

            //validation

            //dd( $request);
            /* $request->validate([
            'name_en' => 'required',
            'name_ar' => 'required',
            'img' => 'required',
        ], [
            'name_en.required' => 'Input Brand English Name',
            'name_ar.required' => 'Input Brand AR Name',
        ]);*/

            ////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////

            $filePath = "";
            if ($request->has('img')) {
                //dd($request->img);
                // $filePath = uploadImage('brands', $request->img);
                $filePath = uploadImage('assets/images/brands/', $request->img);
            }
            /////////////////////////////////////////////////////////////////////////////////////

            if (!$request->has('is_active'))
                $flag = 0;
            else
                $flag = 1;

            Brand::insert([
                'name_ar' => $request->name_ar,
                'name_en' => $request->name_en,
                
                'slug_en' => strtolower(str_replace(' ', '-', $request->name_en)),
                'slug_ar' => str_replace(' ', '-', $request->name_ar),
                //'img' => $last_img,
                'img' => $filePath,
                'is_active' => $flag,
                'created_at' => Carbon::now()
            ]);

            $notification = array(
                'msg' => 'Brand Added Successfully',
                'alert-type' => ' success'
            );
             DB::commit();
             return redirect()->route('admin.brands')->with($notification);
          //  return redirect()->route('admin.brands')->with(['success' => 'تم ألاضافة بنجاح']);
          
        } catch (\Exception $ex) {
            DB::rollback();
            return $ex;
            return redirect()->route('admin.brands')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }





    public function edit($id)
    {
       
        $brand = Brand::find($id);

        if (!$brand)
            return redirect()->route('admin.brands')->with(['error' => 'هذا القسم غير موجود']);
        // $brands = Brand::findOrFail($id);
        return view('dashboard.brands.edit_brands', compact('brand'));
    }




    public function update($id, Request $request)
    {
        try {
			DB::beginTransaction();

            //return $request->all();
            $brands = Brand::find($request->id);
            if (!$brands)
                return redirect()->route('admin.brands')->with(['error' => 'هذا القسم غير موجود']);
            /////////////upload image/////////////////////
            $old_img = $brands->img;

            $old_img_path = 'assets/images/brands/' . $old_img;
            $filePath = "";
            if ($request->has('img')) {
                //dd($request->img);
                // $filePath = uploadImage('brands', $request->img);
                if (file_exists($old_img_path)) {
                    unlink($old_img_path);
                }

                $filePath = uploadImage('assets/images/brands/', $request->img);
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

            $brands->update([
                'name_ar' => $request->name_ar,
                'name_en' => $request->name_en,

                'slug_ar' => str_replace(' ', '-', $request->name_ar),
                'slug_en' => strtolower(str_replace(' ', '-', $request->name_en)),

                'img' => $filePath,
                'is_active' => $flag,

                
                // 'sub_name_en' => $request->sub_name_en,
                'updated_at' => Carbon::now()
            ]);


            //  $brands->name_en = $request->name_en;
            // $brands->save();
            //$brands->update($request->all());
            //$product = Brand::get();
            // DB::enableQueryLog();
            //$query = DB::getQueryLog();
            //$query = end($query);
            //dd($query);
			DB::commit();


            $notification = array(
                'msg' => 'Brand Updated Successfully',
                'alert-type' => 'info'
            );

           // return redirect()->route('admin.brands')->with(['success' => 'تم الحفظ بنجاح']);

             return redirect()->route('admin.brands')->with($notification);

        } catch (\Exception $ex) {
//return  $ex;
			DB::rollback();

            return redirect()->route('admin.brands')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }





    public function deactivate($id)
    {

        try {
            //get specific categories and its translations
            //  $category = Brand::orderBy('id', 'DESC')->find($id);
            $brands = Brand::find($id);
            if (!$brands)
                return redirect()->route('admin.brands')->with(['error' => 'هذا القسم غير موجود ']);

            $brands->is_active = 0;
            $brands->save();

            return redirect()->route('admin.brands')->with(['success' => 'تم الحفظ بنجاح']);
        } catch (\Exception $ex) {
            //return $ex;
            return redirect()->route('admin.brands')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }



    public function activate($id)
    {

        try {
            //get specific categories and its translations
            //  $category = Brand::orderBy('id', 'DESC')->find($id);
            $brands = Brand::find($id);
            if (!$brands)
                return redirect()->route('admin.brands')->with(['error' => 'هذا القسم غير موجود ']);

            $brands->is_active = 1;
            $brands->save();
$notification = array(
                'msg' => 'Updated Successfully',
                'alert-type' => 'success'
            );
			
           // return redirect()->route('admin.brands')->with(['success' => 'تم الحفظ بنجاح']);

             return redirect()->route('admin.brands')->with($notification)->with(['success' => 'تم الحفظ بنجاح']);

         } catch (\Exception $ex) {
            //return $ex;
            return redirect()->route('admin.brands')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }
    public function destroy($id)
    {

        try {
			DB::beginTransaction();

            //get specific categories and its translations
            //  $category = Brand::orderBy('id', 'DESC')->find($id);
            $brands = Brand::find($id);
			
            if (!$brands)
                return redirect()->route('admin.brands')->with(['error' => 'هذا القسم غير موجود ']);



            $old_img = $brands->img;
            $old_img_path = 'assets/images/brands/' . $old_img;

            if (file_exists($old_img_path)) {
                unlink($old_img_path);
            }

            $brands->delete();
			DB::commit();
		

            return redirect()->route('admin.brands')->with(['success' => 'تم  الحذف بنجاح']);
        } catch (\Exception $ex) {
            //return $ex;
				DB::rollback();
            return redirect()->route('admin.brands')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }

    public function delAll(Request $request)
    {
        try {
            if ($request->ids == "")
                return redirect()->route('admin.brands')->with(['error' => 'من فضلك قم بالاختيار ليتم الحذف']);

            $ids = $request->ids;
            Brand::whereIn('id', $ids)->delete();

            return redirect()->route('admin.brands')->with(['success' => 'تم  الحذف بنجاح']);
        } catch (\Exception $ex) {
            return $ex;
            return redirect()->route('admin.brands')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }
}

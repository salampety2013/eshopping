<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Support\Carbon;
use App\Http\Requests\SubCategoryRequest;
use Exception;
use Illuminate\Support\Facades\DB;


class SubCategoriesController extends Controller
{
    public function index()
    {
		//$subcategories = SubCategory::latest()->paginate(2);;
          $subcategories = SubCategory::latest()->get();
        $categories = Category::orderBy('name_ar', 'ASC')->get();
        // $subcategories=SubCategory::orderBy('name_en','Desc')->get();
        //return $categories;
        return view('dashboard.subcategories.index_subcat', compact('subcategories', 'categories'));
        //return view('backend.category.view',compact('category'));

    }
    public function create()
    {
        $categories = Category::orderBy('name_ar', 'ASC')->get();
        return view('dashboard.subcategories.create_subcat', compact('categories'));
    }

    public function store(Request $request)
    {
 //validation

            //dd( $request);
            /* $request->validate([
            'name_en' => 'required',
            'name_ar' => 'required',
            'img' => 'required',
        ], [
            'name_en.required' => 'Input SubCategory English Name',
            'name_ar.required' => 'Input SubCategory AR Name',
        ]);*/

            ////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////
        try {

            //   DB::beginTransaction();

           

            $filePath = "";
            if ($request->has('img')) {
                //dd($request->img);
                // $filePath = uploadImage('subcategories', $request->img);
                $filePath = uploadImage('assets/images/subcategories/', $request->img);
            }
            /////////////////////////////////////////////////////////////////////////////////////

            if (!$request->has('is_active'))
                $flag = 0;
            else
                $flag = 1;

            SubCategory::insert([
                'name_ar' => $request->name_ar,
                'name_en' => $request->name_en,
                'cat_id' => $request->cat_id,
                'slug_en' => strtolower(str_replace(' ', '-', $request->name_en)),
                'slug_ar' => str_replace(' ', '-', $request->name_ar),
                //'img' => $last_img,
                'img' => $filePath,
                'is_active' => $flag,
                'created_at' => Carbon::now()
            ]);

            $notification = array(
                'msg' => 'SubCategory Updated Successfully',
                'alert-type' => 'info'
            );



            // return redirect()->route('admin.subcategories')->with($notification);
            return redirect()->route('admin.subcategories')->with(['success' => 'تم ألاضافة بنجاح']);
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            return $ex;
            return redirect()->route('admin.subcategories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }





    public function edit($id)
    {
        $categories = Category::latest()->get();
        $subcategory = SubCategory::find($id);

        if (!$subcategory)
            return redirect()->route('admin.subcategories')->with(['error' => 'هذا القسم غير موجود']);
        // $subcategory = SubCategory::findOrFail($id);
        return view('dashboard.subcategories.edit_subcat', compact('subcategory', 'categories'));
    }




    public function update($id, Request $request)
    {
        try {
            //return $request->all();
            $subcategory = SubCategory::find($request->id);
            if (!$subcategory)
                return redirect()->route('admin.subcategories')->with(['error' => 'هذا القسم غير موجود']);
            /////////////upload image/////////////////////
            $old_img = $subcategory->img;

            $old_img_path = 'assets/images/subcategories/' . $old_img;
            $filePath = "";
            if ($request->has('img')) {
                //dd($request->img);
                // $filePath = uploadImage('subcategories', $request->img);
                if($old_img!=null){
					
					if (file_exists($old_img_path)) {
						unlink($old_img_path);
                }
				}

                $filePath = uploadImage('assets/images/subcategories/', $request->img);
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

            $subcategory->update([
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


            //  $subcategory->name_en = $request->name_en;
            // $subcategory->save();
            //$subcategory->update($request->all());
            //$product = SubCategory::get();
            // DB::enableQueryLog();
            //$query = DB::getQueryLog();
            //$query = end($query);
            //dd($query);


            $notification = array(
                'msg' => 'SubCategory Updated Successfully',
                'alert-type' => 'info'
            );

            return redirect()->route('admin.subcategories')->with(['success' => 'تم الحفظ بنجاح']);

            // return redirect()->route('admin.subcategories')->with($notification);

        } catch (\Exception $ex) {
return  $ex;
            return redirect()->route('admin.subcategories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }





    public function deactivate($id)
    {

        try {
            //get specific categories and its translations
            //  $category = SubCategory::orderBy('id', 'DESC')->find($id);
            $subcategory = SubCategory::find($id);
            if (!$subcategory)
                return redirect()->route('admin.subcategories')->with(['error' => 'هذا القسم غير موجود ']);

            $subcategory->is_active = 0;
            $subcategory->save();

            return redirect()->route('admin.subcategories')->with(['success' => 'تم الحفظ بنجاح']);
        } catch (\Exception $ex) {
            //return $ex;
            return redirect()->route('admin.subcategories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }



    public function activate($id)
    {

        try {
            //get specific categories and its translations
            //  $category = SubCategory::orderBy('id', 'DESC')->find($id);
            $subcategory = SubCategory::find($id);
            if (!$subcategory)
                return redirect()->route('admin.subcategories')->with(['error' => 'هذا القسم غير موجود ']);

            $subcategory->is_active = 1;
            $subcategory->save();

            return redirect()->route('admin.subcategories')->with(['success' => 'تم الحفظ بنجاح']);
        } catch (\Exception $ex) {
            //return $ex;
            return redirect()->route('admin.subcategories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }
    public function destroy($id)
    {

        try {
            //get specific categories and its translations
            //  $category = SubCategory::orderBy('id', 'DESC')->find($id);
            $subcategory = SubCategory::find($id);
            if (!$subcategory)
                return redirect()->route('admin.subcategories')->with(['error' => 'هذا القسم غير موجود ']);



            $old_img = $subcategory->img;
            $old_img_path = 'assets/images/subcategories/' . $old_img;

            if (file_exists($old_img_path)) {
                unlink($old_img_path);
            }

            $subcategory->delete();

            return redirect()->route('admin.subcategories')->with(['success' => 'تم  الحذف بنجاح']);
        } catch (\Exception $ex) {
            //return $ex;
            return redirect()->route('admin.subcategories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }

    public function delAll(Request $request)
    {
        try {
            if ($request->ids == "")
                return redirect()->route('admin.subcategories')->with(['error' => 'من فضلك قم بالاختيار ليتم الحذف']);

            $ids = $request->ids;
            SubCategory::whereIn('id', $ids)->delete();

            return redirect()->route('admin.subcategories')->with(['success' => 'تم  الحذف بنجاح']);
        } catch (\Exception $ex) {
            return $ex;
            return redirect()->route('admin.subcategories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }
}

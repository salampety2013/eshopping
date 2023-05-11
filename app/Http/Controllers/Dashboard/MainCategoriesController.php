<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Carbon;
use App\Http\Requests\MainCategoryRequest;
use Exception;
use Illuminate\Support\Facades\DB;


class MainCategoriesController extends Controller
{
    public function index()
    {
      //  $categories = Category::latest()->get();
      //  $categories = Category:: withCount('products')->latest()->dd();
      //  $categories = Category:: withCount('products as products_count')->latest()->get();
        $categories = Category:: withCount(
           ['products as products_count'=>function($query){
            $query->where ('is_active','=',1);
           }]
            )->latest()->get();
        //$categories=Category::orderBy('name_en','Desc')->get();
        //return $categories;
        return view('dashboard.categories.index_cat', compact('categories'));
        //return view('backend.category.view',compact('category'));

    }
    ///////////////////////////////////////
    public function show($id)
    {
        $category = Category::find($id);
        if (!$category)
            return redirect()->route('admin.maincategories.show')->with(['error' => 'هذا القسم غير موجود']);
       // $category = Category::findOrFail($id);
        return view('dashboard.categories.show_cat', compact('category'));
    }




    ///////////////////////////////////////
    public function create()
    {

        return view('dashboard.categories.create_cat');
    }

    public function store(MainCategoryRequest $request)
    {
        try {

            //   DB::beginTransaction();
             
               //validation

        //dd( $request);
        /* $request->validate([
            'name_en' => 'required',
            'name_ar' => 'required',
            'img' => 'required',
        ], [
            'name_en.required' => 'Input Category English Name',
            'name_ar.required' => 'Input Category AR Name',
        ]);*/

        ////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////
        /*  $img =  $request->file('img');
         $up_location = 'assets/images/maincategories/';
        $img_name = hexdec(uniqid()).'.'.strtolower($img->getClientOriginalExtension());
        $img->move($up_location, $img_name);
        $last_img = $up_location . $img_name;
        

        if (!$request->has('is_active'))
                $request->request->add(['is_active' => 0]);
            else
                $request->request->add(['is_active' => 1]);
*/
        /////////////upload image/////////////////////

        $filePath = "";
        if ($request->has('img')) {
            //dd($request->img);
            // $filePath = uploadImage('maincategories', $request->img);
            $filePath = uploadImage('assets/images/maincategories/', $request->img);
        }
        /////////////////////////////////////////////////////////////////////////////////////

        if (!$request->has('is_active'))
            $flag = 0;
        else
            $flag = 1;

        Category::insert([
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
            'msg' => 'Category Updated Successfully',
            'alert-type' => 'info'
        );

        

       // return redirect()->route('admin.maincategories')->with($notification);
       return redirect()->route('admin.maincategories',app()->getLocale())->with(['success' => 'تم ألاضافة بنجاح']);
          DB::commit();

     } catch (\Exception $ex) {
          DB::rollback();
         return $ex;
         return redirect()->route('admin.maincategories',app()->getLocale())->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
     }

    }





    public function edit($id)
    {
        $category = Category::find($id);
        if (!$category)
            return redirect()->route('admin.maincategories')->with(['error' => 'هذا القسم غير موجود']);
       // $category = Category::findOrFail($id);
        return view('dashboard.categories.edit_cat', compact('category'));
    }




    public function update($id, Request $request)
    {
        try {
     
        $category = Category::find($request->id);
        if (!$category)
            return redirect()->route('admin.maincategories')->with(['error' => 'هذا القسم غير موجود']);
        /////////////upload image/////////////////////
        $old_img = $category->img;
      
        $old_img_path = 'assets/images/maincategories/'.$old_img;
        $filePath = "";
        if ($request->has('img')) {
            //dd($request->img);
            // $filePath = uploadImage('maincategories', $request->img);
            if( file_exists($old_img_path))
                {
                    unlink($old_img_path);
                }
            
            $filePath = uploadImage('assets/images/maincategories/', $request->img);
        } else {

            $filePath = $old_img;
        }
        /////////////////////////////////////////////////////////////////////////////////////

        if (!$request->has('is_active'))
            $flag = 0;
        else
            $flag = 1;

        $category->update([
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,

            'slug_en' => strtolower(str_replace(' ', '-', $request->name_en)),
            'slug_ar' => str_replace(' ', '-', $request->name_ar),
            //'img' => $last_img,
            'img' => $filePath,
            'is_active' => $flag,

            'updated_at' => Carbon::now()
        ]);

        $notification = array(
            'msg' => 'Category Updated Successfully',
            'alert-type' => 'info'
        );

          return redirect()->route('admin.maincategories')->with(['success' => 'تم التحديث بنجاح']);

       // return redirect()->route('admin.maincategories')->with($notification);
        return redirect()->route('admin.maincategories')->with(['success' => 'تم التحديث بنجاح']);

    } catch (\Exception $ex) {

        return redirect()->route('admin.maincategories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
    }
    }

    public function destroy($id)
    {

        try {
            //get specific categories and its translations
            //  $category = Category::orderBy('id', 'DESC')->find($id);
            $category = Category::find($id);
            if (!$category)
                return redirect()->route('admin.maincategories')->with(['error' => 'هذا القسم غير موجود ']);



                $old_img = $category->img;
                $old_img_path = 'assets/images/maincategories/'.$old_img;

                if( file_exists($old_img_path))
                {
                 unlink($old_img_path);
                }
               
                $category->delete();

            return redirect()->route('admin.maincategories')->with(['success' => 'تم  الحذف بنجاح']);
        } catch (\Exception $ex) {
            return $ex;
            return redirect()->route('admin.maincategories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }

    public function delAll(Request $request)
    {
        try {
            if ( $request->ids=="")
            return redirect()->route('admin.maincategories')->with(['error' => 'من فضلك قم بالاختيار ليتم الحذف']);

            $ids = $request->ids;
            Category::whereIn('id', $ids)->delete();

            return redirect()->route('admin.maincategories')->with(['success' => 'تم  الحذف بنجاح']);
        } catch (\Exception $ex) {
            return $ex;
            return redirect()->route('admin.maincategories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }
}

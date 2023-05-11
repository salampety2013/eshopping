<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
 
use App\Models\Page;
use Illuminate\Support\Carbon;
use App\Http\Requests\PageRequest;
use Exception;
use Illuminate\Support\Facades\DB;


class PagesController extends Controller
{
    public function index()
    {
		//$pages = Page::latest()->paginate(2);;
          $pages = Page::latest()->get();
        
        // $pages=Page::orderBy('title_en','Desc')->get();
        //return $categories;
        return view('dashboard.pages.index_pages', compact('pages'));
        //return view('backend.category.view',compact('category'));

    }
    public function create()
    {
        
        return view('dashboard.pages.create_pages' );
    }

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
            'title_en.required' => 'Input Page English Name',
            'title_ar.required' => 'Input Page AR Name',
        ]);*/

            ////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////

            
            if (!$request->has('status'))
                $flag = 0;
            else
                $flag = 1;
             Page::insert([
                'title_ar' => $request->title_ar,
                'title_en' => $request->title_en,
 			   'description_ar' => $request->description_ar,
			   'description_en' =>  $request->description_en,
			   'meta_title' => $request->meta_title,
			   'meta_description' => $request->meta_description,
			   'meta_keywords' => $request->meta_keywords,
 			   'status' => $flag,
                'created_at' => Carbon::now()
            ]);

            $notification = array(
                'msg' => 'Page Added Successfully',
                'alert-type' => ' success'
            );
             DB::commit();
             return redirect()->route('admin.pages')->with($notification);
          //  return redirect()->route('admin.pages')->with(['success' => 'تم ألاضافة بنجاح']);
          
        } catch (\Exception $ex) {
            DB::rollback();
            return $ex;
            return redirect()->route('admin.pages')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }





    public function edit($id)
    {
       
        $page = Page::find($id);

        if (!$page)
            return redirect()->route('admin.pages')->with(['error' => 'هذا القسم غير موجود']);
        // $pages = Page::findOrFail($id);
        return view('dashboard.pages.edit_pages', compact('page'));
    }




    public function update($id, Request $request)
    {
        try {
			DB::beginTransaction();

            //return $request->all();
            $pages = Page::find($request->id);
            if (!$pages)
                return redirect()->route('admin.pages')->with(['error' => 'هذا القسم غير موجود']);
             /////////////////////////////////////////////////////////////////////////////////////

            if (!$request->has('status'))
                $flag = 0;
            else
                $flag = 1;
            // return $request->all();
            //return $request->quantity ;

            $pages->update([
 			'title_ar' => $request->title_ar,
                'title_en' => $request->title_en,
 			   'description_ar' => $request->description_ar,
			   'description_en' =>  $request->description_en,
			   'meta_title' => $request->meta_title,
			   'meta_description' => $request->meta_description,
			   'meta_keywords' => $request->meta_keywords,
 			   'status' => $flag,
                
                // 'sub_title_en' => $request->sub_title_en,
                'updated_at' => Carbon::now()
            ]);


           
			DB::commit();


            $notification = array(
                'msg' => 'Page Updated Successfully',
                'alert-type' => 'info'
            );

 
             return redirect()->route('admin.pages')->with($notification);

        } catch (\Exception $ex) {
//return  $ex;
			DB::rollback();

            return redirect()->route('admin.pages')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }





    public function deactivate($id)
    {

        try {
            //get specific categories and its translations
            //  $category = Page::orderBy('id', 'DESC')->find($id);
            $pages = Page::find($id);
            if (!$pages)
                return redirect()->route('admin.pages')->with(['error' => 'هذا القسم غير موجود ']);

            $pages->status = 0;
            $pages->save();

            return redirect()->route('admin.pages')->with(['success' => 'تم الحفظ بنجاح']);
        } catch (\Exception $ex) {
            //return $ex;
            return redirect()->route('admin.pages')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }



    public function activate($id)
    {

        try {
            //get specific categories and its translations
            //  $category = Page::orderBy('id', 'DESC')->find($id);
            $pages = Page::find($id);
            if (!$pages)
                return redirect()->route('admin.pages')->with(['error' => 'هذا القسم غير موجود ']);

            $pages->status = 1;
            $pages->save();
$notification = array(
                'msg' => 'Updated Successfully',
                'alert-type' => 'success'
            );
			
           // return redirect()->route('admin.pages')->with(['success' => 'تم الحفظ بنجاح']);

             return redirect()->route('admin.pages')->with($notification)->with(['success' => 'تم الحفظ بنجاح']);

         } catch (\Exception $ex) {
            //return $ex;
            return redirect()->route('admin.pages')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }
    public function destroy($id)
    {

        try {
			DB::beginTransaction();

            //get specific categories and its translations
            //  $category = Page::orderBy('id', 'DESC')->find($id);
            $pages = Page::find($id);
			
            if (!$pages)
                return redirect()->route('admin.pages')->with(['error' => 'هذا القسم غير موجود ']);



            $old_img = $pages->img;
            $old_img_path = 'assets/images/pages/' . $old_img;

            if (file_exists($old_img_path)) {
                unlink($old_img_path);
            }

            $pages->delete();
			DB::commit();
		

            return redirect()->route('admin.pages')->with(['success' => 'تم  الحذف بنجاح']);
        } catch (\Exception $ex) {
            //return $ex;
				DB::rollback();
            return redirect()->route('admin.pages')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }

    public function delAll(Request $request)
    {
        try {
            if ($request->ids == "")
                return redirect()->route('admin.pages')->with(['error' => 'من فضلك قم بالاختيار ليتم الحذف']);

            $ids = $request->ids;
            Page::whereIn('id', $ids)->delete();

            return redirect()->route('admin.pages')->with(['success' => 'تم  الحذف بنجاح']);
        } catch (\Exception $ex) {
            return $ex;
            return redirect()->route('admin.pages')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }
}

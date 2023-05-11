<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
 
use App\Models\Option;
use App\Models\Category;
use Illuminate\Support\Carbon;
use App\Http\Requests\OptionRequest;
use Exception;
use Illuminate\Support\Facades\DB;
///////////////////////print sql////////////////
 use Illuminate\Database\Query\Builder;
use Illuminate\Support\Str;
//////////////////////////////


class OptionsController extends Controller
{
    public function index()
    {

         // request() function return all items in request or use index(Request $request)
        $request = request(); 
        

        $options = option::latest('name_ar')->paginate(10);
        
        return view('dashboard.options.index', compact('options'));
      

    }
    public function create()
           {  
              $categories = Category:: where('is_active',"1")->orderBy('name_ar', 'ASC')->get();

           $option =new option();
        
        return view('dashboard.options.create' ,compact('categories','option'));
    }


    public function store(optionRequest $request)
//    public function store(Request $request)
    {
 //validation

            //dd( $request);
          /*  $request->validate([
            'name_en' => 'required',
            'name_ar' => 'required',
            'img' => 'required',
        ], [
           // 'name_en.required' => 'Input option English Name required',
           // 'name_ar.required' => 'Input option AR Name required',
           // 'requiired'=>'This field (:attribute) is required'
        ]); */
        try {

               DB::beginTransaction();

           

            ////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////

            $filePath = "";
            if ($request->has('img')) {
                //dd($request->img);
                // $filePath = uploadImage('options', $request->img);
                $filePath = uploadImage('assets/images/options/', $request->img);
            }
            /////////////////////////////////////////////////////////////////////////////////////

            if (!$request->has('is_active'))
                $flag = 0;
            else
                $flag = 1;
				
				
				 


// $p->name = $request->name[array_search('en', $request->lang)];
//dd($name_json);
//YourModel::create(['jsonColumn' => $arr_tojson]);

            option::insert([
                'name_ar' => $request->name_ar,
                'name_en' => $request->name_en,
                 
                'img' => $filePath,
                'is_active' => $flag,
                'created_at' => Carbon::now()
            ]);

            $notification = array(
                'msg' => 'option Added Successfully',
                'alert-type' => ' success'
            );
             DB::commit();
             return redirect()->route('admin.options')->with($notification);
          //  return redirect()->route('admin.options')->with(['success' => 'تم ألاضافة بنجاح']);
          
        } catch (\Exception $ex) {
            DB::rollback();
            return $ex;
            return redirect()->route('admin.options')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }





   
         public function edit($id)
    {
       
        $option = option::find($id);
        $categories = Category:: where('is_active',"1")->orderBy('name_ar', 'ASC')->get();
//$option=option::where('id',$id);
  //ddb($option);
         if (!$option)
            return redirect()->route('admin.options')->with(['error' => 'هذا القسم غير موجود']);
        // $options = option::findOrFail($id);
         return view('dashboard.options.edit', compact('option','categories'));
    }




    public function update(optionRequest $request,$id)
   // public function update($id, Request $request)
    {
        try {
			DB::beginTransaction();

            //return $request->all();
            $options = option::find($request->id);
            if (!$options)
                return redirect()->route('admin.options')->with(['error' => 'هذا القسم غير موجود']);
            /////////////upload image/////////////////////
            $old_img = $options->img;

            $old_img_path = 'assets/images/options/' . $old_img;
            $filePath = "";
            if ($request->has('img')) {
                //dd($request->img);
                // $filePath = uploadImage('options', $request->img);
                if($old_img!=null){
					
					if (file_exists($old_img_path)) {
						unlink($old_img_path);
                }
				}

                $filePath = uploadImage('assets/images/options/', $request->img);
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
// DB::enableQueryLog();

       $options->   update([
                'name_ar' => $request->name_ar,
                'name_en' => $request->name_en,
                'img' => $filePath,
                'is_active' => $flag,
                             'updated_at' => Carbon::now()
            ]) ;
 
			DB::commit();


            $notification = array(
                'msg' => 'option Updated Successfully',
                'alert-type' => 'info'
            );

           // return redirect()->route('admin.options')->with(['success' => 'تم الحفظ بنجاح']);

             return redirect()->route('admin.options')->with($notification);

        } catch (\Exception $ex) {
//return  $ex;
			DB::rollback();

            return redirect()->route('admin.options')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }





    public function deactivate($id)
    {

        try {
            //get specific categories and its translations
            //  $category = option::orderBy('id', 'DESC')->find($id);
            $options = option::find($id);
            if (!$options)
                return redirect()->route('admin.options')->with(['error' => 'هذا القسم غير موجود ']);

            $options->is_active = 0;
            $options->save();

            return redirect()->route('admin.options')->with(['success' => 'تم الحفظ بنجاح']);
        } catch (\Exception $ex) {
            //return $ex;
            return redirect()->route('admin.options')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }



    public function activate($id)
    {

        try {
            //get specific categories and its translations
            //  $category = option::orderBy('id', 'DESC')->find($id);
            $options = option::find($id);
            if (!$options)
                return redirect()->route('admin.options')->with(['error' => 'هذا القسم غير موجود ']);

            $options->is_active = 1;
            $options->save();
$notification = array(
                'msg' => 'Updated Successfully',
                'alert-type' => 'success'
            );
			
           // return redirect()->route('admin.options')->with(['success' => 'تم الحفظ بنجاح']);

             return redirect()->route('admin.options')->with($notification)->with(['success' => 'تم الحفظ بنجاح']);

         } catch (\Exception $ex) {
            //return $ex;
            return redirect()->route('admin.options')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }
    public function destroy($id)
    {

        try {
			DB::beginTransaction();

            
            $options = option::find($id);
			
            if (!$options)
                return redirect()->route('admin.options')->with(['error' => 'هذا القسم غير موجود ']);



            $old_img = $options->img;
            $old_img_path = 'assets/images/options/' . $old_img;

            if (file_exists($old_img_path)) {
                unlink($old_img_path);
            }

            $options->delete();
			DB::commit();
		

            return redirect()->route('admin.options')->with(['success' => 'تم  الحذف بنجاح']);
        } catch (\Exception $ex) {
            //return $ex;
				DB::rollback();
            return redirect()->route('admin.options')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }

    public function delAll(Request $request)
    {
        try {
            if ($request->ids == "")
                return redirect()->route('admin.options')->with(['error' => 'من فضلك قم بالاختيار ليتم الحذف']);

            $ids = $request->ids;
            option::whereIn('id', $ids)->delete();

            return redirect()->route('admin.options')->with(['success' => 'تم  الحذف بنجاح']);
        } catch (\Exception $ex) {
            return $ex;
            return redirect()->route('admin.options')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }
}

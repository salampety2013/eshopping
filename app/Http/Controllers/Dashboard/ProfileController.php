<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class ProfileController extends Controller
{

	 public function index()
    {
		//$members = User::latest()->paginate(2);;
          $members = User::latest()->get();

        // $members=User::orderBy('name_en','Desc')->get();
        //return $categories;
        return view('dashboard.members.index_members', compact('members'));
        //return view('backend.category.view',compact('category'));

    }
    public function create()
    {

        return view('dashboard.members.create_members' );
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
            'name_en.required' => 'Input User English Name',
            'name_ar.required' => 'Input User AR Name',
        ]);*/

            ////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////

            $filePath = "";
            if ($request->has('img')) {
                //dd($request->img);
                // $filePath = uploadImage('members', $request->img);
                $filePath = uploadImage('assets/images/members/', $request->img);
            }
            /////////////////////////////////////////////////////////////////////////////////////

            if (!$request->has('status'))
                $flag = 0;
            else
                $flag = 1;


				$name_json = array(
    'ar' => $request->name_ar,
    'en' =>$request->name_en,
);
$name_json = json_encode($name_json);


// $p->name = $request->name[array_search('en', $request->lang)];
//dd($name_json);
//YourModel::create(['jsonColumn' => $arr_tojson]);

            User::insert([
                'name_ar' => $request->name_ar,
                'name_en' => $request->name_en,
                'name' => $name_json,

                'slug_en' => strtolower(str_replace(' ', '-', $request->name_en)),
                'slug_ar' => str_replace(' ', '-', $request->name_ar),
                //'img' => $last_img,
                'img' => $filePath,
                'status' => $flag,
                'created_at' => Carbon::now()
            ]);

            $notification = array(
                'msg' => 'User Added Successfully',
                'alert-type' => ' success'
            );
             DB::commit();
             return redirect()->route('admin.members')->with($notification);
          //  return redirect()->route('admin.members')->with(['success' => 'تم ألاضافة بنجاح']);

        } catch (\Exception $ex) {
            DB::rollback();
            return $ex;
            return redirect()->route('admin.members')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }



 public function show($id)
    {

        $member = User::find($id);
//$User=User::where('id',$id);
  //ddb($User);
         if (!$member)
            return redirect()->route('admin.members')->with(['error' => 'هذا العضو غير موجود']);
        // $members = User::findOrFail($id);
         return view('dashboard.members.show_member', compact('member'));
    }


    public function edit($id)
    {

        $member = User::find($id);
//$User=User::where('id',$id);
  //ddb($User);
         if (!$member)
            return redirect()->route('admin.members')->with(['error' => 'هذا العضو غير موجود']);
        // $members = User::findOrFail($id);
         return view('dashboard.members.edit_members', compact('member'));
    }




    public function update($id, Request $request)
    {
        try {
			DB::beginTransaction();

            //return $request->all();
            $members = User::find($request->id);
            if (!$members)
                return redirect()->route('admin.members')->with(['error' => 'هذا القسم غير موجود']);
            /////////////upload image/////////////////////
            $old_img = $members->img;

            $old_img_path = 'assets/images/members/' . $old_img;
            $filePath = "";
            if ($request->has('img')) {
                //dd($request->img);
                // $filePath = uploadImage('members', $request->img);
				if($old_img!=null){

					if (file_exists($old_img_path)) {
						unlink($old_img_path);
                }
				}



                $filePath = uploadImage('assets/images/members/', $request->img);
            } else {

                $filePath = $old_img;
            }
            /////////////////////////////////////////////////////////////////////////////////////

            if (!$request->has('status'))
                $flag = 0;
            else
                $flag = 1;
            // return $request->all();
            //return $request->quantity ;
// DB::enableQueryLog();

       $members->   update([
                'name_ar' => $request->name_ar,
                'name_en' => $request->name_en,

                'slug_ar' => str_replace(' ', '-', $request->name_ar),
                'slug_en' => strtolower(str_replace(' ', '-', $request->name_en)),

                'img' => $filePath,
                'status' => $flag,


                // 'sub_name_en' => $request->sub_name_en,
                'updated_at' => Carbon::now()
            ]) ;
// $query = DB::getQueryLog();
// dd($query);

//
            //  $members->name_en = $request->name_en;
            // $members->save();
            //$members->update($request->all());
            //$product = User::get();
            // DB::enableQueryLog();
            //$query = DB::getQueryLog();
            //$query = end($query);
            //dd($query);
			DB::commit();


            $notification = array(
                'msg' => 'User Updated Successfully',
                'alert-type' => 'info'
            );

           // return redirect()->route('admin.members')->with(['success' => 'تم الحفظ بنجاح']);

             return redirect()->route('admin.members')->with($notification);

        } catch (\Exception $ex) {
//return  $ex;
			DB::rollback();

            return redirect()->route('admin.members')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }





    public function deactivate($id)
    {

        try {
            //get specific categories and its translations
            //  $category = User::orderBy('id', 'DESC')->find($id);
            $members = User::find($id);
            if (!$members)
                return redirect()->route('admin.members')->with(['error' => 'هذا القسم غير موجود ']);

            $members->status = 0;
            $members->save();

            return redirect()->route('admin.members')->with(['success' => 'تم الحفظ بنجاح']);
        } catch (\Exception $ex) {
            //return $ex;
            return redirect()->route('admin.members')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }



    public function activate($id)
    {

        try {
            //get specific categories and its translations
            //  $category = User::orderBy('id', 'DESC')->find($id);
            $members = User::find($id);
            if (!$members)
                return redirect()->route('admin.members')->with(['error' => 'هذا القسم غير موجود ']);

            $members->status = 1;
            $members->save();
$notification = array(
                'msg' => 'Updated Successfully',
                'alert-type' => 'success'
            );

           // return redirect()->route('admin.members')->with(['success' => 'تم الحفظ بنجاح']);

             return redirect()->route('admin.members')->with($notification)->with(['success' => 'تم الحفظ بنجاح']);

         } catch (\Exception $ex) {
            //return $ex;
            return redirect()->route('admin.members')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }
    public function destroy($id)
    {

        try {
			DB::beginTransaction();

            //get specific categories and its translations
            //  $category = User::orderBy('id', 'DESC')->find($id);
            $members = User::find($id);

            if (!$members)
                return redirect()->route('admin.members')->with(['error' => 'هذا القسم غير موجود ']);




            $old_img = $members->img;
            $old_img_path = 'assets/images/members/' . $old_img;

            if (file_exists($old_img_path)) {
                unlink($old_img_path);
            }

            $members->delete();
			DB::commit();


            return redirect()->route('admin.members')->with(['success' => 'تم  الحذف بنجاح']);
        } catch (\Exception $ex) {
            //return $ex;
				DB::rollback();
            return redirect()->route('admin.members')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }

    public function delAll(Request $request)
    {
        try {
            if ($request->ids == "")
                return redirect()->route('admin.members')->with(['error' => 'من فضلك قم بالاختيار ليتم الحذف']);

            $ids = $request->ids;
            User::whereIn('id', $ids)->delete();

            return redirect()->route('admin.members')->with(['success' => 'تم  الحذف بنجاح']);
        } catch (\Exception $ex) {
            return $ex;
            return redirect()->route('admin.members')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }
	//------------------------------------------------
    public function editProfile()
    {

        $admin = Admin::find(auth('admin')->user()->id);

        return view('dashboard.profile.edit', compact('admin'));

    }

    public function updateProfile(ProfileRequest $request)
    {
        //validate
        // db

        try {
			 DB::beginTransaction();
            $admin = Admin::find(auth('admin')->user()->id);


            if ($request->filled('password')) {
                $request->merge(['password' => bcrypt($request->password)]);
            }

            unset($request['id']);
            unset($request['password_confirmation']);

            $admin->update($request->all());
			 $notification = array(
                'msg' => ' Edit Successfully',
                'alert-type' => ' success'
            );
             DB::commit();
            //  return redirect()->back()->with($notification);
           return redirect()->back()->with(['success' => 'تم التحديث بنجاح']);

        } catch (\Exception $ex) {
            return $ex;
			 DB::rollback();
            return redirect()->back()->with(['error' => 'هناك خطا ما يرجي المحاولة فيما بعد']);
          //  $10$YKCn2f/pdmmRqdlRWogBaesMRFz5O0SHWyAqqbHIcxMTioSMD1Klq
        }

    }






}

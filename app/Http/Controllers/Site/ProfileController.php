<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
  use Illuminate\Support\Carbon;
 use Exception;
use Session;
use Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
	
	 
    


 public function show($id)
    {
       
        $member = User::find($id);
//$User=User::where('id',$id);
  //ddb($User);
         if (!$member)
            return redirect()->back()->with(['error' => 'هذا العضو غير موجود']);
        // $members = User::findOrFail($id);
         return view('front.members.show_member', compact('member'));
    }


    public function edit()
    {
       
        $member = User::find(Auth()->user()->id);
//$User=User::where('id',$id);
  //ddb($User);
         if (!$member)
            return redirect()->back()->with(['error' => 'هذا العضو غير موجود']);
        // $members = User::findOrFail($id);
         return view('front.members.edit_members', compact('member'));
    }




    public function update( Request $request)
    {
        try {
			DB::beginTransaction();

            //return $request->all();
              $members = User::find($request->id);
            // if (!$members)
          //  return redirect()->back()->with(['error' => 'هذا العضو غير موجود']);
            /////////////upload image/////////////////////
            $old_img = $members->img;

               $old_img_path = 'assets/images/members/' . $old_img;
            $filePath = "";
            if ($request->has('img')) {
                  // dd($request->img);
                // $filePath = uploadImage('members', $request->img);
                if($old_img!=null){
					
						if (file_exists($old_img_path)) {
						unlink($old_img_path);
                		}
				}
                 $filePath = uploadImage('assets/images/members/', $request->img);
				 //  dd( $filePath);
            } else {

                $filePath = $old_img;
            }
		//  dd( $filePath);
            /////////////////////////////////////////////////////////////////////////////////////
         // return $request->all();

        //  DB::enableQueryLog();  
       $members->   update([
                'name' => $request->name,
                'email' => $request->email,
                 'mobile' => $request->mobile,
               //'password' =>  bcrypt($request->password),
                 'img' => $filePath,
                 'updated_at' => Carbon::now()
            ]) ;
 //$query = DB::getQueryLog();
   // dd($query);
     // dd($members);
	     // return $request->all();

			DB::commit();


            $notification = array(
                'msg' => 'User Updated Successfully',
                'alert-type' => 'info'
            );

           // return redirect()->route('admin.members')->with(['success' => 'تم الحفظ بنجاح']);

            return redirect()->back()->with($notification);
        } catch (\Exception $ex) {
            //return $ex;
			 //return redirect()->back()->with(['success' => 'تم  الحذف بنجاح']);
            return redirect()->back()->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }





    
	//--------------------change Pass---------------------------- 
	public function changePass()
    {
       
        $member = User::find(Auth()->user()->id);
          if (!$member)
            return redirect()->back()->with(['error' => 'هذا العضو غير موجود']);
        // $members = User::findOrFail($id);
         return view('front.members.change_pass', compact('member'));
    }




    public function updatePass( Request $request)
    {
        try {
			DB::beginTransaction();
			  $member = User::find(Auth()->user()->id);
		 
			//dd( decrypt( Auth::user()->password));
 //if ( decrypt($request->old_pass) != Auth::user()->password) {
 if (!Hash::check($request->old_pass,Auth::user()->password )) {
            // return $request->all();
           // return  $member  = User::where(['id'=>$request->id,'password'=> bcrypt($request->old_pass)])->get();
			  $notification = array(
                'sweet-alert-msg1' => 'Sorry',
				'sweet-alert-msg' => 'كلمة المرور القديمه غير صحيحه',
                'sweet-alert-type' => 'error'
            );
            // if (!$member )
            return redirect()->back()->with($notification);
 }
 
        //  DB::enableQueryLog();  
       $member->update([
                 
               
               'password' =>  bcrypt($request->password),
                 
                 'updated_at' => Carbon::now()
            ]) ;
 	     // return $request->all();

			DB::commit();

 $notification = array(
                'sweet-alert-msg1' => 'good',
				'sweet-alert-msg' => 'Password Updated Successfully',
                'sweet-alert-type' => 'success'
            );
           /* $notification = array(
                'msg' => 'User Updated Successfully',
                'alert-type' => 'info'
            );*/

           // return redirect()->route('admin.members')->with(['success' => 'تم الحفظ بنجاح']);

            return redirect()->back()->with($notification);
        } catch (\Exception $ex) {
            //return $ex;
			 //return redirect()->back()->with(['success' => 'تم  الحذف بنجاح']);
            return redirect()->back()->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }





    
	//------------------------------------------------
    public function editProfile()
    {

        $admin = Admin::find(auth('admin')->user()->id);

        return view('front.profile.edit', compact('admin'));

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
             return redirect()->back()->with($notification);
          //  return redirect()->back()->with(['success' => 'تم التحديث بنجاح']);

        } catch (\Exception $ex) {
			 DB::rollback();
            return redirect()->back()->with(['error' => 'هناك خطا ما يرجي المحاولة فيما بعد']);

        }

    }


 ///////////////////////////////////////////
 
 
 public function checkEmail(Request $request)
    {
		
		 //dd( $request->all());
		
		  $user = User::where('email',$request->user_email)->count();
			   // dd( $data);
			     if($user>0){
		$img=asset("images/delete1.png");			
  $html = '<img src='.$img.' />
<span class="invalid-feedback text-danger">غير متاح </span>'; 
	  
 }else{
	  $img=asset("images/a-apply_.png");
     $html = '<img src='.$img.'     /><span class="invalid-feedback text-success">  متاح </span>';
  }
			   
			   
			    
         return json_encode($html);
	}
 
 
 
  
 
 public function checkMobile(Request $request)
    {
		
		 //dd( $request->all());
		
		  $user = User::where('mobile',$request->user_mobile)->count();
			   // dd( $data);
			     if($user>0){
		$img=asset("images/delete1.png");			
  $html = '<img src='.$img.' />
<span class="invalid-feedback text-danger">غير متاح </span>'; 
	  
 }else{
	  $img=asset("images/a-apply_.png");
     $html = '<img src='.$img.'     /><span class="invalid-feedback text-success">  متاح </span>';
  }
			   
			   
			    
         return json_encode($html);
	}
 
 
 
 
 
 
 
 
 
}

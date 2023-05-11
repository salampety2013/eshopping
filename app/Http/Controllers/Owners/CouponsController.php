<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
 use Illuminate\Support\Carbon;
  
 use Illuminate\Support\Str;
 use Exception;
use Illuminate\Support\Facades\DB;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use App\Models\SubCategory;

use Auth;



class couponsController extends Controller
{
    public function index()
    {

           $coupons =  Coupon::latest()->get();
       
        // $coupons= Coupon::orderBy('id','Desc')->get();
        //return $coupons;
         return view('dashboard.coupons.index_coupons', compact('coupons'));
        

    }
    public function create_update(Request $request,$id=null)
    {
       // return $id;
		  $subcategories = SubCategory::orderBy('name_en','ASC')->get();
		    $users = User::select('email')->get();
			if($id==null){
				$selcted_subcategories=array();
				$selcted_users=array();
				 //$coupons=new Coupon;
				 
				  return view('dashboard.coupons.add_edit_coupon',compact('subcategories','users',  'selcted_subcategories','selcted_users'));
				}else{
					
					 	 $coupons =  Coupon::findOrFail($id);
						 $selcted_subcategories= explode(',',$coupons->categories_ids);
				$selcted_users=explode(',',$coupons->users_ids); 
				
				 return view('dashboard.coupons.add_edit_coupon',compact('subcategories','users','coupons','selcted_subcategories','selcted_users'));
					}
			
      
    }

      

/////////////////////////////////////////////////////////////////////////////

 public function add_edit_coupon(Request $request,$id=null )
    {
		DB::enableQueryLog();	
 	//return	$request->all();
	if (!$request->has('status'))
           { 
		   $status = "0";
		   }else{
            $status = "1";
		   }
		   //return $status;
	 //////////////////////////////
			
			if(isset($request->users_ids)){
				$users=implode(',',$request->users_ids);
				 
				}else{ $users=""; }
	//////////////////////////////////
	
	if(isset($request->categories_ids)){
			 	$categories_ids=implode(',',$request->categories_ids);
				}else{ $categories_ids=""; }
	////////////////////////////////////////
	if(isset($request->coupon_option)&& $request->coupon_option=="Automatic"){
			  		$coupon_code=Str::random(8);
				 
				}else{
					$coupon_code=$request->coupon_code;
					
					} 
	////////////////////////////////////////////////			
				
	if($id==null){
		 
		   try {

            	  //DB::beginTransaction();
        //return  $request->all(); 
	  
	   /* $request->validate([ 
		 
			'code' => 'required',
        ], [
            'type.required' => 'Input Category English Name',
//'name_ar.required' => 'Input Category AR Name',
        ]); */
			 Coupon::create([
           'coupon_option' => $request->coupon_option,
                'coupon_code' => $coupon_code,
                'coupon_type' => $request->coupon_type,
				'categories_ids' => $categories_ids,
				'amount' => $request->amount,
				'amount_type' => $request->amount_type,
				 'users_ids' => $users,
			    'expiry_date' => $request->expiry_date,
				'status' => $status,
            
        ]); 
		 
       
            $notification = array(
                'msg' => ' Coupon Updated Successfully',
                'alert-type' => 'success'
            );



             return redirect()->route('admin.coupons')->with($notification);
           // return redirect()->route('admin.coupons')->with(['success' => 'تم ألاضافة بنجاح']);
         //  DB::commit();
        } catch (\Exception $ex) {
          //  DB::rollback();
             return $ex;
			$notification = array(
                'msg' => 'حدث خطا ما برجاء المحاوله لاحقا',
                'alert-type' => 'danger'
            );
             return redirect()->route('admin.coupons')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
		
		
		///////////////////////update coupon ////////////////////
		}else{
			
			 try {
				 	DB::enableQueryLog();	
					
             // return $request->all();
              $coupons =  Coupon::find($id);
            if (!$coupons)
                return redirect()->route('admin.coupons')->with(['error' => 'هذا الكوبون غير موجود']);
            ///////////// /////////////////////
 //return $status;
    $data=[
                'coupon_option' => $request->coupon_option,
                'coupon_code' => $coupon_code,
                'coupon_type' => $request->coupon_type,
				'categories_ids' => $categories_ids,
				'amount' => $request->amount,
				'amount_type' => $request->amount_type,
				'users_ids' => $users,
			    'expiry_date' => $request->expiry_date,
				'status' => $status,
                  'updated_at' => Carbon::now()
            ]; 
	
  $coupons->update([
                'coupon_option' => $request->coupon_option,
                'coupon_code' => $coupon_code,
                'coupon_type' => $request->coupon_type,
				'categories_ids' => $categories_ids,
				'amount' => $request->amount,
				'amount_type' => $request->amount_type,
				'users_ids' => $users,
			    'expiry_date' => $request->expiry_date,
				 'status' => $status,
                  'updated_at' => Carbon::now()
            ]);

            $notification = array(
                'msg' => 'Updated Successfully',
                'alert-type' => 'success'
            );

           // return redirect()->route('admin.coupons')->with(['success' => 'تم الحفظ بنجاح']);

             return redirect()->route('admin.coupons')->with($notification)->with(['success' => 'تم الحفظ بنجاح']);

        } catch (\Exception $ex) {
		//return  $ex;
                    return redirect()->route('admin.coupons')->with(['msg' => 'حدث خطا ما برجاء المحاوله لاحقا','alert-type' => 'danger']);

	   
	    }
			
			
			}
	  dd(DB::getQueryLog());

 }
	
	///////////////////////////////////////////////////////////////

      


public function deactivateAjax(Request $request)
    {

        try {
             $coupons_id=$request->coupons_id;
            //  $category =  Coupon::orderBy('id', 'DESC')->find($id);
            $coupons =  Coupon::find($id);
           /* if (!$coupons)
                return redirect()->route('admin.coupons')->with(['error' => 'هذا القسم غير موجود ']);
*/
            $coupons->status = "0";
            $coupons->save();

            return response()->json(['status' =>0,'coupons_id'=>$coupons_id]);
        } catch (\Exception $ex) {
            //return $ex;
           // return redirect()->route('admin.coupons')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }

    public function deactivate($id)
    {

        try {
            //get specific categories and its translations
            //  $category =  Coupon::orderBy('id', 'DESC')->find($id);
             $coupons =  Coupon::find($id);
            if (!$coupons)
                return redirect()->route('admin.coupons')->with(['error' => 'هذا القسم غير موجود ']);

            $coupons->status = "0";
            $coupons->save();

            return redirect()->route('admin.coupons')->with(['success' => 'تم الحفظ بنجاح']);
        } catch (\Exception $ex) {
            //return $ex;
            return redirect()->route('admin.coupons')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }



    public function activate($id)
    {

        try {
            //get specific categories and its translations
            //  $category =  Coupon::orderBy('id', 'DESC')->find($id);
            $coupons =  Coupon::find($id);
            if (!$coupons)
                return redirect()->route('admin.coupons')->with(['error' => 'هذا القسم غير موجود ']);

            $coupons->status = "1";
            $coupons->save();

            return redirect()->route('admin.coupons')->with(['success' => 'تم الحفظ بنجاح']);
        } catch (\Exception $ex) {
            //return $ex;
            return redirect()->route('admin.coupons')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }
    public function destroy($id)
    {

        try {
            //get specific categories and its translations
            //  $category =  Coupon::orderBy('id', 'DESC')->find($id);
            $coupons =  Coupon::find($id);
            if (!$coupons)
                return redirect()->route('admin.coupons')->with(['error' => 'هذا القسم غير موجود ']);



            $old_img = $coupons->img;
            $old_img_path = 'assets/images/coupons/' . $old_img;

            if (file_exists($old_img_path)) {
                unlink($old_img_path);
            }

            $coupons->delete();

            return redirect()->route('admin.coupons')->with(['success' => 'تم  الحذف بنجاح']);
        } catch (\Exception $ex) {
            //return $ex;
            return redirect()->route('admin.coupons')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }

    public function delAll(Request $request)
    {
        try {
            if ($request->ids == "")
                return redirect()->route('admin.coupons')->with(['error' => 'من فضلك قم بالاختيار ليتم الحذف']);

            $ids = $request->ids;
             Coupon::whereIn('id', $ids)->delete();

            return redirect()->route('admin.coupons')->with(['success' => 'تم  الحذف بنجاح']);
        } catch (\Exception $ex) {
            return $ex;
            return redirect()->route('admin.coupons')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }
}

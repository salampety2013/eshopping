<?php

namespace App\Http\Controllers\Site;

use App\Http\Requests;
use App\Basket\Basket;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
 use App\Models\Cart;
use App\Models\Product;
 use App\Models\Country;
use App\Models\City;
use App\Models\Governate;
use App\Models\Address;

use Illuminate\Support\Facades\Session;
//use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
 use Illuminate\Support\Carbon;
 use App\Exceptions\QuantityExceededException;

 use Exception;
// use Session;
use Auth;

class AdressController extends Controller
{



		public function  AddEditAddress(Request $request,$id=null ){

       // return $id;

			if($id==null){


				 	   $countries=Country::where('status',1)->get();
				     return view('front.addresses.add_edit_address',compact('countries'));

			}else{

 					$deliveryAddress=Address::findOrFail($id);
		  			$countries=Country::where('status',1)->get();
			 return view('front.addresses.add_edit_address',compact('deliveryAddress','countries'));

					}

		}

		///////////////////////////////get city///////////////////////////////////////
 public function getCitiesAjax(Request $request){

			   $cities = City::where('country_id',$request->country_id)->orderBy('name_en','ASC')->get();
			   //dd( $cities);
        return json_encode($cities);
     }

	//////////////////////////////////////////////////////////

public function  updateStoreAddress(Request $request,$id=null ){

       // return $id;
	    $user_id=Auth::user()->id;
		   $countries=Country::where('status',1)->get();
		    if (!$request->has('default_address'))
            $default_address = "false";
        else
            $default_address = "true";
			if($id==null){


				$address_id =Address::insertGetId([
                'name' => $request->name,

                'country_id' => $request->country_id,
				 'city_id' => $request->city_id,

				 'pincode' => $request->pincode,
				  'address' => $request->address,
				  'user_id' => $user_id,
				  'default_address' => $default_address,
                   'created_at' => Carbon::now()
            ]);


			if($address_id!="" && $default_address == "true"){
///update all the default_address of that user to false except this address id make default true

			 	$address_update=Address::select('id')->where('id','!=',$address_id)->where('user_id',$user_id)->get();
			  Address::whereIn('id', $address_update)->update([
             'default_address' => "false",
             'updated_at' => Carbon::now()
        ]);
				}
				    // return view('front.addresses.add_edit_address',compact('countries'));
				  return redirect()->route('site.checkout');


				  ////////////Edit mode ///////////////////////////////////
				}else{


				 $update_address=Address::findOrFail($id);
				$update_address->update([
                'name' => $request->name,
                'country_id' => $request->country_id,
				 'city_id' => $request->city_id,
				 'pincode' => $request->pincode,
				  'address' => $request->address,
				  'user_id' => $user_id,
				  'default_address' => $default_address,
                   'updated_at' => Carbon::now()
            ]);

			//update default address to false to all adresses except that address
				$address_id=$id;
				if($address_id!="" && $default_address == "true"){

			  	$address_update=Address::select('id')->where('id','!=',$address_id)->where('user_id',$user_id)->get();
			  Address::whereIn('id', $address_update)->update([
             'default_address' => "false",
             'updated_at' => Carbon::now()
        ]);
				}
 			$deliveryAddress=Address::findOrFail($id);


				// return redirect()->route('site.add_edit_address',$id)->with(compact('deliveryAddress','countries'));

				 return redirect()->route('site.checkout');
					}

		}



		//////////////////////////////delete//////////////////////////////////////////
		///////////////////////////////////////////////////////////////////////
		public function deleteAddress($id)
    {

        try {
            //get specific categories and its translations
            //  $category =  Address::orderBy('id', 'DESC')->find($id);
            $address =  Address::find($id);
            /*if (!$address)
                return redirect()->route('admin.colors')->with(['error' => 'هذا القسم غير موجود ']);
*/



            $address->delete();

            return redirect()->back()->with(['success' => 'تم  الحذف بنجاح']);
        } catch (\Exception $ex) {
            //return $ex;
			 //return redirect()->back()->with(['success' => 'تم  الحذف بنجاح']);
            return redirect()->back()->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }





}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests;
use App\Basket\Basket;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

 use App\Models\Country;
use App\Models\City;
use App\Models\Governate;
use App\Models\Address;
use App\Models\User;

use App\Traits\GeneralTrait;
use Validator;

use App\Http\Resources\V1\AddressResource;



 use Illuminate\Support\Carbon;
 use App\Exceptions\QuantityExceededException;

 use Exception;

use Auth;

class AdressController extends Controller
{

    use GeneralTrait;
    public function index(Request $request)
    {

      try {
 
           $countries = Country::where('status', 1)->latest()->get() ;
          if (!$countries)
              //  return $this->returnError('001', 'هذا القسم غير موجود');
              return $this->returnErrors('202', __('general.not found'));

        return $this->returnData('data',CountryResource::collection($countries));
        // return $this->returnData('data',new ProductResource($product));

      } catch (\Exception $e) {
        return $this->returnErrors(201, $e->getMessage());
      }
    }


//------------------------------------------------------------
    


//------------------------------------------------------------
public function getAddresses( Request $request,$id)
{

       try {	
	   
	   	     //$user_id= $request->merge(['user_id'=>auth()->user()->id]);
$user_id= auth()->user()->id;
       // $id=$request->id;
      //  $user_id=$request->user_id;
/*      $user_id=$id;
        $user =  User::find($id);
         if (!$user)
        return $this->returnErrors('202', __('general.invalid user'));
*/

         $address=Address::where('user_id',$user_id)->latest()->get();

        if (!$address)
        return $this->returnErrors('202', __('general.not found'));

          return $this->returnData('data',  AddressResource::collection($address));
        // return $this->returnData('data', $address,'' );


     } catch (\Exception $e) {
       return $this->returnErrors(201, $e->getMessage());
     }


}//--------------------------------------------------


public function  updateStoreAddress(Request $request,$id=null ){
    try {

        $rules = [
            'name' => 'required|',
            'user_id' => 'nullable|numeric|exists:users,id',
            'mobile' => 'required|numeric',
            'city_id' => 'required|numeric|exists:cities,id',
            'country_id' => 'required|numeric|exists:countries,id',
            'pincode' => 'required|numeric',
            'address' => 'required',
            'id' => 'nullable|numeric|exists:addresses,id'


        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);
            // return $this->returnValidationErrorAll('422',$validator);

        }

           // return $id;
         // return  $user_id=Auth::user()->id;
		   $user_id= request()->merge(['user_id'=>auth()->user()->id]);

           $id=$request->id;
          $user_id=$request->user_id;
               $countries=Country::where('status',1)->get();

                $default_address =$request->default_address ;

                if($id==null){


                  //  $address_id =Address::insertGetId([
                    $address =Address::create([
                    'name' => $request->name,

                    'country_id' => $request->country_id,
                     'city_id' => $request->city_id,
                     'mobile' => $request->mobile,

                     'pincode' => $request->pincode,
                      'address' => $request->address,
                      'notes' => $request->notes,
                      'user_id' => $user_id,
                      'default_address' => $default_address,
                       'created_at' => Carbon::now()
                ]);

                  $address_id=$address ->id;
                if($address_id!="" && $default_address == "true"){
    ///update all the default_address of that user to false except this address id make default true

                     $address_update=Address::select('id')->where('id','!=',$address_id)->where('user_id',$user_id)->get();
                  Address::whereIn('id', $address_update)->update([
                 'default_address' => "false",
                 'updated_at' => Carbon::now()
            ]);
                    }



                      ////////////Edit mode ///////////////////////////////////
                    }  else{


                     $address=Address::findOrFail($id);
                    $address->update([
                    'name' => $request->name,
                    'country_id' => $request->country_id,
                     'city_id' => $request->city_id,
                     'mobile' => $request->mobile,
                     'pincode' => $request->pincode,
                      'address' => $request->address,
                      'notes' => $request->notes,
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

                        }

                       if($address) {
                         //  return $this->returnSuccessMessage('Added successfully');
                         return $this->returnData('data', new AddressResource($address),'Added successfully');
                        // return $this->returnData('data', $address,'Added successfully' );
                        }else{

                         return $this->returnErrors(201, 'not Added');

                       }
    } catch (\Exception $e) {
     // return $this->returnError(201, $e->getMessage());
       return $this->returnErrors(201, $e->getMessage()); // if u return all data with response لازم نةحد شكل الداتا اللى راجعه
    }


		}



		//////////////////////////////delete//////////////////////////////////////////
		///////////////////////////////////////////////////////////////////////
		public function deleteAddress( Request $request,$id)
    {

           try {
           // $id=$request->id;
            $address =  Address::find($id);
            if (!$address)

                 return $this->returnErrors('202', __('general.not found'));
                 $address->delete();
                 return $this->returnSuccessMessages('deleted successfully');


         } catch (\Exception $e) {
           return $this->returnError(201, $e->getMessage());
         }


    }





}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests;
use App\Basket\Basket;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

 use App\Models\Country;
use App\Models\City;
use App\Models\Governate;



use App\Traits\GeneralTrait;
use Validator;

use App\Http\Resources\V1\CountryResource;
use App\Http\Resources\V1\cityResource;
use App\Http\Resources\V1\CountryAndCityResource;



 use Illuminate\Support\Carbon;
 use App\Exceptions\QuantityExceededException;

 use Exception;

use Auth;

class CountriesController extends Controller
{

    use GeneralTrait;

    public function index(Request $request)
    {

      try {


           $countries = Country::where('status', 1)->latest()->get() ;
          if (!$countries)
              //  return $this->returnError('001', 'هذا القسم غير موجود');
              return $this->returnErrors('202', __('general.not found'));

       // return $this->returnData('data',CountryResource::collection($countries));
        return $this->returnData('data',CountryAndCityResource::collection($countries));
        
      } catch (\Exception $e) {
        return $this->returnErrors(201, $e->getMessage());
      }
    }


//------------------------------------------------------------
public function getCities( Request $request)
{

       try {
        $id=$request->id;
      //  $country_id=$request->country_id;
        $country_id=$id;
        $rules = [

                'id' => 'required|numeric|exists:countries,id',
               ];

        $validator = Validator::make($request->all(), $rules);
//return $id;
        if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);
            // return $this->returnValidationErrorAll('422',$validator);

        }
            $cities =  City::where('status', 1)->where('country_id',$id)->latest()->get() ;

          return $this->returnData('data',  cityResource::collection($cities));
        // return $this->returnData('data', $address,'' );


     } catch (\Exception $e) {
       return $this->returnErrors(201, $e->getMessage());
     }


}//--------------------------------------------------






		//////////////////////////////delete//////////////////////////////////////////
		///////////////////////////////////////////////////////////////////////
		public function getCountriesAndCities( Request $request)
    {

           try {



            $countries  = Country::select('id','name_ar','name_en')
            ->with(['cities' =>function($q) {
               $q->select('id','name_ar','name_en','country_id')-> where('status',"1"); }])
               -> where('status',"1")->get();

            //  return $this -> returnData('data',$data);
           // return BookResource::collection(Book::with('ratings')->paginate(25));
          //     return AlbumResource::collection(Album::where('user_id', $request->user()->id)->paginate());
            return $this -> returnData('data', CountryAndCityResource::collection($countries));


         } catch (\Exception $e) {
           return $this->returnError(201, $e->getMessage());
         }


    }





}

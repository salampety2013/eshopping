<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Http\Requests\RatingRequest;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;
use Auth;





class ContactController extends Controller
{
    use GeneralTrait;

    public function sendEmail(Request $request)
    {
        try {

            $product_id=$request->product_id;
            $rules = [

                'name' => 'required',
                'email' => 'required|email',
                'subject' => 'required',
            //    'phone_number' => 'required',
                'message' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
                // return $this->returnValidationErrorAll('422',$validator);

            }


            $email= $request->email;
            $messageData=[
            'name'=>$request->name,
           'mobile'=>$request->mobile,
           'email'=>$request->email,
           'message'=>$request->message
           ];
           Mail::send('emails.contact_us',$messageData,function($message) use($request)
               {
                $message->from($request->email);
               $message->to($request->email)->subject('Contact us siteName');
               });



           // return $this->returnData('data',$data );
           return $this->returnSuccessMessage('message sent successfully');



          } catch (\Exception $e) {
            return $this->returnError(201, $e->getMessage());
          }



    }




}

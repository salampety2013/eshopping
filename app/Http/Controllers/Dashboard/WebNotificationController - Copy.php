<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Support\Carbon;
use App\Http\Requests\SubCategoryRequest;
use Exception;
use Illuminate\Support\Facades\DB;


 class WebNotificationController extends Controller
{
 //documentation  web setup:  https://firebase.google.com/docs/web/setup
 // show error in firbase documentation            https://firebase.google.com/docs/reference/fcm/rest/v1/ErrorCode 
    public function index()
    {
		// return User::whereNotNull('fmctoken')->pluck('fmctoken')->all();
		//return User::where('id',1)->first();
        return view('dashboard.notificaitons.create_notifications');
    }
  
    public function storeToken(Request $request)
    {
		//dd( $request->token); 
		User::where('id',1)->update(['fmctoken'=>$request->token]);
       //  auth()->user()->update(['fmctoken'=>$request->token]);
       // auth('admin')->user()->update(['fmctoken'=>$request->token]);
        return response()->json(['Token successfully stored.']);
    }
  
    public function sendWebNotification(Request $request)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $FcmToken = User::whereNotNull('fmctoken')->pluck('fmctoken')->all();
         //  $serverKey = 'AAAA3tfNYNU:APA91bGfyhA-52AQTJ7xJpDX_dITTtCwLmm0Jjn85yWSDDqJONSgM6Ow03DVxKfzrDO-jHvSazXipBlaMbA2SEEekPuZRCxCVU3vBosvJCfKKZHrNcBh8RAIA8h28-K4IIT_Pzwlz6Ah';

  //Sender ID=957103300821
  $FcmToken='yrS2rocGMJa1NFnV6A6gkGYEoJT2PXORXjlHu8z3';
  
  
   $serverKey ='AAAAZYwY7Yk:APA91bFN00pRQQzaJQn0KvhahUtqeJ--oocs61O8Wu7TYbE_e6IkJRbwB-ziukkiLtRmJiSfxkgEUabpwI4CTjKD6DQ3osvN7rCh2w9MEPblozE8li-BY13c3hwgcwH0hM08saXd9Q58';
        $data = [
           //'to' => $FcmToken,
		    "registration_ids" => $FcmToken,
            "notification" => [
                "title" => $request->title,
                "body" => $request->body,  
            ]
        ];
        $encodedData = json_encode($data);
    
        $headers = [
            'Authorization:key=' . $serverKey,
            'Content-Type: application/json',
        ];
    
        $ch = curl_init();
      
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);        
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);

        // Execute post
        $result = curl_exec($ch);

        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }        

        // Close connection
        curl_close($ch);

        // FCM response
        dd($result);        
    }
}
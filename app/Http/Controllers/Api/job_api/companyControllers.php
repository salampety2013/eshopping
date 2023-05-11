<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\company as companyResource;
use App\Company;
use Validator;
use Hash;
/*
|--------------------------------------------------------------------------
| companyControllers
|--------------------------------------------------------------------------
| this will handle all comapy part 
| R 
*/
/**
   _____                                        
 / ____|                                       
| |     ___  _ __ ___  _ __   __ _ _ __  _   _ 
| |    / _ \| '_ ` _ \| '_ \ / _` | '_ \| | | |
| |___| (_) | | | | | | |_) | (_| | | | | |_| |
 \_____\___/|_| |_| |_| .__/ \__,_|_| |_|\__, |
                      | |                 __/ |
                      |_|                |___/ 
 */
class companyControllers extends Controller
{

/**  
* This api will be used to register new company
* -*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*
* @param $request Illuminate\Http\Request;
* @author ಠ_ಠ Abdelrahman Mohamed <abdomohamed00001@gmail.com>
*/
public function register(Request $request){
$rules=[
    'logo'=>'required|image',
    'name'=>'required',
    'email'=>'required|email|unique:company,email',
    'password'=>'required|min:6',
    'language'=>'required|in:ar,en'
];

$messages=[
    'logo.required'=>'400',
    'logo.image'=>'400',
    'name.required'=>'400',
    'email.required'=>'400',
    'email.email'=>'400',
    'email.unique'=>'409',
    'password.required'=>'400',
    'language.required'=>'400',
    'password.min'=>'400',
    'language.in'=>'400'
];
    try{
        $validator = Validator::make($request->all(), $rules, $messages);
        if($validator->fails()) {
            return response()->json(['status'=>(int)$validator->errors()->first()]);
        }
#Start logic
$company=new Company;
$company->name=$request->name;
$company->apiToken=\Str::random(64);
$company->email=$request->email;
$company->password=Hash::make($request->password);
$company->language=$request->language;
$this->SaveFile($company,'logo','logo','images');
$company->save();
return response()->json(['status'=>200,'company'=>new companyResource($company)]);
#end logic
        }catch(Exception $e) {
           return response()->json(['status' =>404]);
         }
    }// end funcrion



/**  
* This api will be used to login  company with (password & email)
* -*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*
* @param $request Illuminate\Http\Request;
* @author ಠ_ಠ Abdelrahman Mohamed <abdomohamed00001@gmail.com>
*/
public function login(Request $request){
    $rules=[
        'email'=>'required|email|exists:company,email',
        'password'=>'required|min:6',

    ];
    
    $messages=[
        'email.required'=>'400',
        'email.email'=>'400',
        'email.exists'=>'415',
        'password.required'=>'400',
        'password.min'=>'400',
    ];
        try{
            $validator = Validator::make($request->all(), $rules, $messages);
            if($validator->fails()) {
                return response()->json(['status'=>(int)$validator->errors()->first()]);
            }
    #Start logic
    #password check

    $company=Company::where('email',$request->email)->first();
    if(!Hash::check($request->password,$company->password)){
        return response()->json(['status'=>410]);
    }
    #login Okay 
    return response()->json(['status'=>200,'company'=>new companyResource($company)]);
    #end logic
            }catch(Exception $e) {
               return response()->json(['status' =>404]);
             }
        }// end funcrion    


/**  
* This api will be used to forget Password company.  
* -*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*
* @param $request Illuminate\Http\Request;
* @author ಠ_ಠ Abdelrahman Mohamed <abdomohamed00001@gmail.com>
*/
public function forgetPassword(Request $request){
    $rules=[
        'email'=>'required|email|exists:company,email',
    ];
    
    $messages=[
        'email.required'=>'400',
        'email.email'=>'400',
        'email.exists'=>'412',

    ];
        try{
            $validator = Validator::make($request->all(), $rules, $messages);
            if($validator->fails()) {
                return response()->json(['status'=>(int)$validator->errors()->first()]);
            }
    #Start logic
    $company=Company::where('email',$request->email)->first();
    $code=12345678;
    $company->code=$code;
    $company->save();

    #send Email
    $this->sendEmail('email.sendEmail',$request->email,['code'=>$code],'Alhin ');       

    return response()->json(['status'=>200]);
    #end logic
            }catch(Exception $e) {
               return response()->json(['status' =>404]);
             }
        }// end funcrion    

/**  
* This api will be used in 1 cases to validate the comapny verification code. 
* -*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*
* @param $request Illuminate\Http\Request;
* @author ಠ_ಠ Abdelrahman Mohamed <abdomohamed00001@gmail.com>
*/
public function validateCode(Request $request){
    $rules=[
        'email'=>'required|email|exists:company,email',
        'code'=>'required|exists:company,code',
    ];
    
    $messages=[
        'email.required'=>'400',
        'email.email'=>'400',
        'email.exists'=>'412',
        'code.required'=>'400',
        'code.exists'=>'408',
    ];
        try{
            $validator = Validator::make($request->all(), $rules, $messages);
            if($validator->fails()) {
                return response()->json(['status'=>(int)$validator->errors()->first()]);
            }
    #Start logic
    $company=Company::where('email',$request->email)->first();
    #check if code is right
    if($request->code !== $company->code){
        return response()->json(['status'=>408]);
    }
    $company->code=NULL;
    $company->tmpApiToken=Str::random(64);
    $company->save();

    return response()->json(['status'=>200,'tmpApiToken'=>$company->tmpApiToken]);
    #end logic
            }catch(Exception $e) {
               return response()->json(['status' =>404]);
             }
        }// end funcrion  
        
/**  
* This api will be used to change the password of the company if his account is exists.
* -*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*
* @param $request Illuminate\Http\Request;
* @author ಠ_ಠ Abdelrahman Mohamed <abdomohamed00001@gmail.com>
*/
public function changePassword(Request $request){
    $rules=[
        'tmpToken'=>'required|exists:company,tmpApiToken',
        'newPassword'=>'required|min:6',
    ];
    
    $messages=[
        'tmpToken.required'=>'400',
        'tmpToken.exists'=>'400',
        'newPassword.required'=>'400',
        'newPassword.min'=>'400',
    ];
        try{
            $validator = Validator::make($request->all(), $rules, $messages);
            if($validator->fails()) {
                return response()->json(['status'=>(int)$validator->errors()->first()]);
            }
    #Start logic
    $company=Company::where('tmpApiToken',$request->tmpToken)->first();
    #check if code is right

    $company->code=NULL;
    $company->tmpApiToken=NULL;
    $company->password=Hash::make($request->newPassword);
    $company->save();

    return response()->json(['status'=>200]);
    #end logic
            }catch(Exception $e) {
               return response()->json(['status' =>404]);
             }
        }// end funcrion          
          
    }//end Class

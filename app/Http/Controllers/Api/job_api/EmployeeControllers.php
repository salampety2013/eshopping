<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\employee as employeeResource;
use App\Employee;
use Validator;
use Hash;
/*
|--------------------------------------------------------------------------
| EmployeeControllers
|--------------------------------------------------------------------------
| this will handle all employee part 
| R 
*/
/**
                      _                       
                     | |                      
  ___ _ __ ___  _ __ | | ___  _   _  ___  ___ 
 / _ \ '_ ` _ \| '_ \| |/ _ \| | | |/ _ \/ _ \
|  __/ | | | | | |_) | | (_) | |_| |  __/  __/
 \___|_| |_| |_| .__/|_|\___/ \__, |\___|\___|
               | |             __/ |          
               |_|            |___/      
 */
class EmployeeControllers extends Controller
{
/**  
* This api will be used to register new employee
* -*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*
* @param $request Illuminate\Http\Request;
* @author ಠ_ಠ Abdelrahman Mohamed <abdomohamed00001@gmail.com>
*/
public function register(Request $request){
    $rules=[
        'logo'=>'required|image',
        'name'=>'required',
        'email'=>'required|email|unique:employee,email',
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
    $employee=new Employee;
    $employee->name=$request->name;
    $employee->apiToken=\Str::random(64);
    $employee->email=$request->email;
    $employee->password=Hash::make($request->password);
    $employee->language=$request->language;
    $this->SaveFile($employee,'logo','logo','images');
    $employee->save();
    return response()->json(['status'=>200,'employee'=>new employeeResource($employee)]);
    #end logic
            }catch(Exception $e) {
               return response()->json(['status' =>404]);
             }
        }// end funcrion
    
    
    
    /**  
    * This api will be used to login  employee with (password & email)
    * -*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*
    * @param $request Illuminate\Http\Request;
    * @author ಠ_ಠ Abdelrahman Mohamed <abdomohamed00001@gmail.com>
    */
    public function login(Request $request){
        $rules=[
            'email'=>'required|email|exists:employee,email',
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
    
        $employee=Employee::where('email',$request->email)->first();
        if(!Hash::check($request->password,$employee->password)){
            return response()->json(['status'=>410]);
        }
        #login Okay 
        return response()->json(['status'=>200,'employee'=>new employeeResource($employee)]);
        #end logic
                }catch(Exception $e) {
                   return response()->json(['status' =>404]);
                 }
            }// end funcrion    
    
    
    /**  
    * This api will be used to forget Password employee.  
    * -*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*
    * @param $request Illuminate\Http\Request;
    * @author ಠ_ಠ Abdelrahman Mohamed <abdomohamed00001@gmail.com>
    */
    public function forgetPassword(Request $request){
        $rules=[
            'email'=>'required|email|exists:employee,email',
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
        $employee=Employee::where('email',$request->email)->first();
        $code="12345678";
        $employee->code=$code;
        $employee->save();
    
        #send Email
        $this->sendEmail('email.sendEmail',$request->email,['code'=>$code],'Alhin ');       
    
        return response()->json(['status'=>200]);
        #end logic
                }catch(Exception $e) {
                   return response()->json(['status' =>404]);
                 }
            }// end funcrion    
    
    /**  
    * This api will be used in 1 cases to validate the employee verification code. 
    * -*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*
    * @param $request Illuminate\Http\Request;
    * @author ಠ_ಠ Abdelrahman Mohamed <abdomohamed00001@gmail.com>
    */
    public function validateCode(Request $request){

        $rules=[
            'email'=>'required|email|exists:employee,email',
            'code'=>'required|exists:employee,code',
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
        $employee=Employee::where('email',$request->email)->first();
        #check if code is right
       
        if($request->code !== $employee->code){
            return response()->json(['status'=>408]);
        }

        $employee->code=NULL;
        $employee->tmpApiToken=\Str::random(64);
        $employee->save();
    
        return response()->json(['status'=>200,'tmpApiToken'=>$employee->tmpApiToken]);
        #end logic
                }catch(Exception $e) {
                   return response()->json(['status' =>404]);
                 }
            }// end funcrion  
            
    /**  
    * This api will be used to change the password of the employee if his account is exists.
    * -*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*
    * @param $request Illuminate\Http\Request;
    * @author ಠ_ಠ Abdelrahman Mohamed <abdomohamed00001@gmail.com>
    */
    public function changePassword(Request $request){
        $rules=[
            'tmpToken'=>'required|exists:employee,tmpApiToken',
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
        $employee=Employee::where('tmpApiToken',$request->tmpToken)->first();
        #check if code is right
    
        $employee->code=NULL;
        $employee->tmpApiToken=NULL;
        $employee->password=Hash::make($request->newPassword);
        $employee->save();
    
        return response()->json(['status'=>200]);
        #end logic
                }catch(Exception $e) {
                   return response()->json(['status' =>404]);
                 }
            }// end funcrion        
}

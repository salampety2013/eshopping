<?php

namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
 use App\Http\Requests;
 use Illuminate\Http\Request;
 use Illuminate\Support\Carbon;
 use App\Exceptions\QuantityExceededException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

 use Exception;
 use Auth;
 use App\Traits\GeneralTrait;
use Validator;



//use Request;
 class LoginController extends Controller
{

	   use GeneralTrait;
    public function getToken($user)
    {
		$token=$user->createToken(str()->random(40))->plainTextToken;
 return response()->json([

 'user'=>$user,
 'token'=>$token,
 'token_type'=>'Bearer',
 ]);

    }


    public function register(Request $request)
    {
  $rules = [
           // 'name' => 'required|',
           // 'email' => 'required|email|unique:users,id',
           // 'mobile' => 'required|numeric|max:255|unique:users',
           // 'password' => 'required|min:6|confirmed',
             'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
			 'mobile' => ['required', 'numeric',  'min:12', 'unique:users'],
             'password' => ['required', 'string', 'min:8', 'confirmed'],

        ];

        $validator = Validator::make($request->all(), $rules);
         if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);
            // return $this->returnValidationErrorAll('422',$validator);

        }


       $user= User::create([
            'name' => ucwords($request->name),
            'email' => $request['email'],
			'mobile' => $request['mobile'],
            'password' => Hash::make($request['password']),
            'status' =>1,

        ]);
	  $token=$user->createToken(Str::random(40))->plainTextToken;

 return response()->json([

 'user'=>$user,
 'token'=>$token,
 'token_type'=>'Bearer',
 ]);
    }


	public function login(Request $request)
    {
        try{

 			$rules = [

           // 'email' => 'required|email|unique:users,id',
           // 'mobile' => 'required|numeric',
            'username' => 'required',
            'password' => 'required|min:8',


       		 ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);
            // return $this->returnValidationErrorAll('422',$validator);

        }
        $remember_me = $request->has('remember_me') ? true : false;

 $login = request()->input('username');

        $field= filter_var($login, FILTER_VALIDATE_EMAIL) ?  'email':'mobile';

        /*
		// -----use this code or ---------
		request()->merge([$field => $login]);
 			 $credentials = $request->only($field, 'password');
        if (!Auth::attempt($credentials))
 */
		       //---------or  use this code-------

			    if (!Auth()->attempt([ $field => $request->input("username"), 'password' => $request->input("password"),'status' => 1], $remember_me))
 				  return $this->returnErrors('401', __('invalid username or password '));


		 $user = User::where('email', $request->username)
        ->orWhere('mobile', $request->username)
        ->firstOrFail();
    $token = $user->createToken('auth_token')->plainTextToken;
    return response()->json([
        'user'=>$user,
		// 'user'=>Auth::user(),
 'token'=>$token,
 'token_type'=>'Bearer',
    ]);


	 } catch (\Exception $e) {
           return $this->returnError(201, $e->getMessage());
         }


    }
	//---------------------------------------------------

   public function logout (Request $request)
   {
	     //return $request->all();
		// return $request->user()->tokens;
		if(! auth()->user()->tokens()->delete())// write only that  or
		  return $this->returnErrors('422', __('some thing went wrong'));
	   // return $this->returnData('data', [],'You have been successfully logged out');
 	    return $this->returnSuccessMessages('You have been successfully logged out');
	  // write all  that
	 /* // $accessToken = auth()->user()->token();
	 $token= $request->user()->tokens->find($accessToken);
	$token->revoke(); */
	 return response(['message' => 'You have been successfully logged out.'], 200);
}






 //-----------------------anthoer login-------------------------------------

 public function login2(LoginRequest $request)
{
    try{

		$rules= [
    'emailOrPhone' => 'required|string',
    'password' => 'required|string',
    'remember_me' => 'boolean'
    ];
        $userCred = User::where('email', $request->emailOrPhone)
        ->orWhere('mobile', $request->emailOrPhone)
        ->first();
        $credentials = request(['email', 'password']);
        $credentials['status'] = 1;
        $credentials['deleted_at'] = null;
        if(!Auth::attempt($credentials))
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        $user = $request->user();
        $tokenResult = $user->createToken('iDriver');
        $token = $tokenResult->token;
        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();
        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString()
        ]);
    }catch(\Exception $e) {
        return $this->error($e->getMessage(), $e->getCode());
    }
}











}

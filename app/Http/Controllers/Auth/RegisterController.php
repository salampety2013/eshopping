<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests;
class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;
//protected $redirectTo = RouteServiceProvider::PROFILE;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
			 'mobile' => ['required', 'string',  'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
		
		/*$user_email_count=User::where('email',$data['email'])->count();
		$user_mobile_count=User::where('mobile',$data['mobile']))->count();
		if($user_email_count>0)
		{
			return redirect()->back();
			}
			if($user_mobile_count >0)
			{
			return redirect()->back();
			}*/
			
			 date_default_timezone_set('Africa/Cairo');

//$script_tz = date_default_timezone_get();
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
			'mobile' => $data['mobile'],
            'password' => Hash::make($data['password']),
        ]);
    }
	
	
	///////////////check email//////////
	public function checkEmail(Request $request)
    {
		
		dd( $request);
		
		 $data = User::where('email',$request->user_email)->get();
			   //dd( $cities);
        return json_encode($data);
	}
	
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;
	
	

    /**
     * Create a new controller instance.
     *
     * @return void
     */
	 
	 
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
		//////////////////////////////login with mobile //////////////////////////////

	/* public function username()
    {
		//dd($data);
         return 'mobile';
    }
	*/
	
	//////////////////////////////login with mobile or user name or email//////////////////////////////
	/*public function username()
    {
        $login = request()->input('username');

        if(is_numeric($login)){
            $field = 'phone';
        } elseif (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $field = 'email';
        } else {
            $field = 'username';
        }

        request()->merge([$field => $login]);

        return $field;
    }
	
	 public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
      
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
   
            return redirect()->route('home');
        }
     
        return redirect("login")->withSuccess('Oppes! You have entered invalid credentials');
    }
	
	*/
	
	//////////////////////////////login with mobile   or email//////////////////////////////
	 public function username()
    {
        $login = request()->input('username');

        $field= filter_var($login, FILTER_VALIDATE_EMAIL) ?  'email':'mobile';

        request()->merge([$field => $login]);

        return $field;
    } 
	
}

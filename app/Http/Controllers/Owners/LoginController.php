<?php

namespace App\Http\Controllers\Owners;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminLoginRequest;
//use Request;
use Illuminate\Support\Facades\Session;
class LoginController extends Controller
{
    public function login()
    {

        return view('owners.auth.login');
    }


    public function postLogin(AdminLoginRequest $request)
    {
        //AdminLoginRequest $request
        //validation

        //check , store , update
//dd($request);
        $remember_me = $request->has('remember_me') ? true : false;

        if (auth()->guard('admin')->attempt(['email' => $request->input("email"), 'password' => $request->input("password"),'type' =>'owner'] , $remember_me)) 
		{
			 
         //  dd(auth('admin')->user()->type);
		   
		    // session()->put('locale', 'ar');
			//if(auth('admin')->user()->type=="seller")
			
					//return redirect()->route('seller.dashboard');
			//else	
            
           // dd(auth('admin')->user()->type);
					return redirect()->route('owners.dashboard');
			//return redirect()->route('admin.dashboard',app()->getLocale());
        }
        return redirect()->back()->with(['error' => ' هناك خطا بالبيانات']);

    }

    public function logout()
    {

        $gaurd = $this->getGaurd();
        $gaurd->logout();
		Session::flush();
		return redirect()->route('owners.login');
       // return redirect()->route('admin.login',app()->getLocale());
    }

    private function getGaurd()
    {
        return auth('admin');
    }
}
//////////////////////////////////////admin tinker
/*
php artisan tinker
 $admin->email = "admin@admin.com";
$admin->password = Hash::make("123456"); 
$admin->name = "reda";
 $admin->save(); 

 exit
 
 


*/
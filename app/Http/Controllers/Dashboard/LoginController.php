<?php

namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminLoginRequest;
//use Request;
use Illuminate\Support\Facades\Session;
class LoginController extends Controller
{
    public function login()
    {

        return view('dashboard.auth.login');
    }


    public function postLogin(AdminLoginRequest $request)
    {
        //AdminLoginRequest $request
        //validation

        //check , store , update
//dd($request);
        $remember_me = $request->has('remember_me') ? true : false;

        if (auth()->guard('admin')->attempt(['email' => $request->input("email"), 'password' => $request->input("password"),'type' =>'admin'] , $remember_me))
		{

            // dd(auth('admin')->user()->type);


					return redirect()->route('admin.dashboard');
			//return redirect()->route('admin.dashboard',app()->getLocale());
        }
        return redirect()->back()->with(['error' => ' هناك خطا بالبيانات']);

    }

    public function logout()
    {

        $gaurd = $this->getGaurd();
        $gaurd->logout();
		Session::flush();
		return redirect()->route('admin.login');
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

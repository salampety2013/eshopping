<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    
public function handle($request, Closure $next, $guard = null)
    {
		 // dd($guard);
        if (Auth::guard($guard)->check()) {

            if($guard === 'admin'){
             //  dd(auth('admin') -> user()->type);
			 if(auth('admin') -> user() -> type=="owner"){
                return redirect(RouteServiceProvider::OWNER);
			 	}else if(auth('admin') -> user() -> type=="admin"){
				 return redirect(RouteServiceProvider::ADMIN);}
			}
 			else{
                return redirect(RouteServiceProvider::HOME);}

        }

        return $next($request);
    }
}
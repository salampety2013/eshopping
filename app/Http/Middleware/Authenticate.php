<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Request;
class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
   /* protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }
}*/

 protected function redirectTo($request)
    {
		// $path = $request->path();
		
        if (!$request->expectsJson()){
			  if (Request::is('admin*')){
			// if (Request::is(app()->getLocale().'admin*')) {
			           // dd($path);

                return route('admin.login');
			}else{
                return route('login');}

        }
    }
}
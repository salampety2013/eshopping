<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class OwnersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    
    {
         //dd(auth('admin') -> user() -> type);
        
        if(auth('admin') -> user() -> type=="owner")
        {
            return $next($request);
        }
        else
        {
            return abort(404);
        }
           
     
      
   }
}

 
 

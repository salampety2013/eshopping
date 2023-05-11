<?php
namespace App\Http\Middleware;
use App;
use Closure;
use Illuminate\Http\Request;

class ChangeApiLanguage
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
        app()->setLocale('ar');

        if(isset($request -> lang)  && $request -> lang == 'en' )
            app()->setLocale('en');

        return $next($request);
    }
}

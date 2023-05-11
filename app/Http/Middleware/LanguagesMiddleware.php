<?php

namespace App\Http\Middleware;
use App;
use Closure;
use Illuminate\Http\Request;

class LanguagesMiddleware
{
    /**LanguageManager
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
		//dd(session()->get('locale'));
		  if (session()->has('locale')) {
            App::setLocale(session()->get('locale'));
         } 
		// App::setLocale($request->locale);
        return $next($request);
    }
	
	/* public function handle($request, Closure $next)
    { 
        $languages = array_keys(config('app.languages'));
        $route = $request->route();
        if (request('change_language')) {
			//dd("request");
            session()->put('language', request('change_language'));
            $language = request('change_language');
            if (array_key_exists('locale', $route->parameters) && $route->parameters['locale'] != $language)
            {
                $route->parameters['locale'] = $language;

                if (in_array($language, $languages)) {
                    app()->setLocale($language);
                }else{
					return redirect(route($route->getName(),app()->getLocale()));
					}
 		return redirect(route($route->getName(),session('language')));
                //return redirect(route($route->getName(), $route->parameters));
            }
        }
		
		/////////////////////////////////////////////////////////////
		 elseif (session('language')) {
			
			//dd("session");
            $language = session('language');
 			  //dd(app()->getLocale());
			  // dd($language );
            if (array_key_exists('locale', $route->parameters) && $route->parameters['locale'] != $language )
            {
             // && in_array($route->parameters['locale'], $languages)
			
 			 $language = $route->parameters['locale'];
			
			 if (in_array($language, $languages)) { 
			   //dd($route->parameters); 
			  // dd("sessions");
			   // dd( "===".$language);
			  	app()->setLocale($language);
				
                    session()->put('language', $language);
					 
					// dd(app()->getLocale());
                }else{
					 //dd($route->parameters['locale']);
					 // dd("false");
					 return redirect(route($route->getName(),session('language')));
					}
               
            } 
        } 
		
	/////////////////////////////////////////////////////////////	
		
		elseif (config('app.locale')) {
			//dd("config");
        $language = config('app.locale');
			if (array_key_exists('locale', $route->parameters))
            {  
			 

                $language= $route->parameters['locale'] ;
                if (in_array($language, $languages)) { 
				//dd($route->parameters['locale']);
                    app()->setLocale($language);
                }else{
					return redirect(route($route->getName(),app()->getLocale()));
					}
            }
        }

        if (isset($language) && in_array($language, $languages)) {
            app()->setLocale($language);
        }else{
					 //dd($route->parameters['locale']);
					 // dd("false");
					 return redirect(route($route->getName(),App::getLocale()));
					}

        return $next($request);
    }*/
}

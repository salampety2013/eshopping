<?php
namespace App\Http\Middleware;
use App;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class UpdateUserLastActiveAt
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
        //يتم اضافة   middleware in  kernal in protected $middlewareGroups = [ 'web' => [] after session start to read user data
       
       $user= $request -> user();
       //forceFill    force to fill attribute in user table (fillable attributes)
        if($user){
             $user->forceFill([
               'last_active_at' =>Carbon::now(),
             ])->save();
                }
        return $next($request);
    
}
}
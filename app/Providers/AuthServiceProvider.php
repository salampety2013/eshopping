<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
         'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies(); 

 
        foreach ( config('global.permissions') as $permission_name => $value) { //brands
            Gate::define($permission_name, function ($auth) use ($permission_name){
                return $auth->hasAbility($permission_name);
            });   // it take permission name and return it allowed =true or not allow=false
			// mean Gate::define("brands",true)
        }
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;


///////////////////////print sql////////////////
use Illuminate\Support\Facades\DB;

 use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Str;
//////////////////////////////

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Schema::defaultStringLength(191);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
		//------change bootstrap default style in pagination in links() in all application---------
//Paginator::useBootstrap(); 
// or use  defaultView() to applay this view to all site
//Paginator::defaultView('vendor.pagination.bootstrap-4');
        //-----------------------------------------------------



	//----------Begin  print sql with binging ? -------------------------------	
    /*   Builder::macro('ddb', function () {
      $bindings = array_map(
          fn ($value) => is_numeric($value) ? $value : "'{$value}'",
          $this->getBindings()
      );

      return Str::replaceArray('?', $bindings, $this->toSql());
  });
    
	
	//------End-----------------------------------
	
	
	 DB::listen(function ($query) {
              $query->sql;
             $query->bindings;
             $query->time;
        });*/
}	
}

<?php

namespace App\EssentialEntities\GeneralHelperTools\Providers;

use Illuminate\Support\ServiceProvider;

class GeneralHelperToolsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->generalHelperTools();
    }
    
    /**
     * Instantiate an object from GeneralHelperTools class
     * 
     */
    protected function generalHelperTools() {
        $this->app->singleton('generalHelperTools', function($app){
            return new \App\EssentialEntities\GeneralHelperTools\GeneralHelperTools();
        });
    }

}

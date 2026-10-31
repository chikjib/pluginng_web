<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */


    public function boot()
    {
        //

        if (App::environment('production', 'staging')) {

            $this->app['request']->server->set('HTTPS', 'on'); // this line
            URL::forceScheme('https');
        }
    }
}

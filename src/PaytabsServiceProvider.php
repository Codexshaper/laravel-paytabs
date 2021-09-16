<?php

namespace Paytabs;

use Paytabs\Contracts\Paytabs as PaytabsContract;
use Paytabs\Paytabs;
use Illuminate\Support\ServiceProvider;

class PaytabsServiceProvider extends ServiceProvider 
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/paytabs.php', 'paytabs'
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/paytabs.php' => config_path('paytabs.php'),
        ], 'paytabs');

        $this->app->singleton(PaytabsContract::class, function($app){
            return new Paytabs(config('paytabs.profile_id'), config('paytabs.server_key'), config('paytabs.region'));
        });
    }
}
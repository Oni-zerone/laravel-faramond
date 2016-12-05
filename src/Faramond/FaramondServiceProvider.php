<?php

namespace Ennetech\Faramond;

use Ennetech\Faramond\Commands\DeployCommand;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class FaramondServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/faramond.php' => config_path('faramond.php')
        ], 'config');

        $this->commands(
            DeployCommand::class
        );
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        Route::group([
            'prefix' => config('faramond.route-prefix'),
        ], function ($router) {
            Route::get('/version', function () {
                return config('faramond.version');
            });
        });
    }
}

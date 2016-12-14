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
            __DIR__ . '/../config/faramond.php' => config_path('faramond.php')
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
            Route::get('/version/{key}', function ($key) {
                if ($key === config('faramond.secret')) {
                    return config('faramond.version');
                } else {
                    return response("Invalid secret", 403);
                }
            });

            Route::post('/update/{key}', function ($key) {
                if ($key === config('faramond.secret')) {
                    $branch = null;
                    if (isset($this->app->request->all()['branch'])) {
                        $branch = $this->app->request->all()['branch'];
                    };
                    $deploy_result = (new FaramondManager())->deploy($branch, false);
                    $response = new \Illuminate\Http\Response();
                    $response->setStatusCode(200);
                    $response->setContent(json_encode($deploy_result));
                    $response->header("Content-Type", "application/json; charset=UTF-8", true);
                    return $response;
                } else {
                    return response("Invalid secret", 403);
                }
            });
        });
    }
}

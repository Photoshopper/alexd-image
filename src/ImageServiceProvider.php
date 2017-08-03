<?php

namespace Alexd\Image;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class ImageServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Routes
        $this->setupRoutes($this->app['router']);

        // Views
        $this->loadViewsFrom(__DIR__ . '/views', 'ImageManager');

        // Publish
        $this->publishes([
            __DIR__ . '/views' => base_path('resources/views/vendor/alexd/image'),
            __DIR__ . '/public' => base_path('public/vendor/alexd/image'),
            __DIR__ . '/lang' => base_path('resources/lang'),
            __DIR__.'/../database/migrations/' => database_path('migrations'),
            __DIR__ . '/config' => base_path('config'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function setupRoutes(Router $router)
    {
        $router->group(['namespace' => 'Alexd\Image\Http\Controllers'], function($router) {
            require __DIR__.'/Http/routes.php';
        });
    }
}

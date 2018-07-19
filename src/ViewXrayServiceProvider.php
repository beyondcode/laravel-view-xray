<?php

namespace BeyondCode\ViewXray;

use View;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;

class ViewXrayServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('xray.php'),
            ], 'config');
        }

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'xray');

        $this->registerMiddleware(XrayMiddleware::class);
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->singleton(Xray::class);

        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'xray');
    }

    /**
     * Register the middleware
     *
     * @param  string $middleware
     */
    protected function registerMiddleware($middleware)
    {
        $kernel = $this->app[Kernel::class];
        $kernel->pushMiddleware($middleware);
    }
}

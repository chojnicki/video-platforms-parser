<?php

namespace Chojnicki\VideoPlatformsParser;

use Illuminate\Foundation\Application as LaravelApplication;
use Laravel\Lumen\Application as LumenApplication;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    protected $defer = true;

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(VideoPlatformsParser::class, function ($app) {
            if (! $this->app['config']->get('video-platforms-parser')) {
                return $app;
            }

            return new VideoPlatformsParser(
                $this->app['config']->get('video-platforms-parser')
            );
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/config/video-platforms-parser.php' => config_path('video-platforms-parser.php'),
            ]);
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('video-platforms-parser');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [VideoPlatformsParser::class];
    }
}

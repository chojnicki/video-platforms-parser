<?php

namespace Chojnicki\VideoPlatformsParser;


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
            if (!$this->app['config']->get('video-platforms-parser')) return $app;
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
        $this->publishes([
            __DIR__.'/config/video-platforms-parser.php' => config_path('video-platforms-parser.php'),
        ]);
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

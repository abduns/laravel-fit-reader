<?php

namespace Dunn\FitReader;

use Illuminate\Support\ServiceProvider;
use Dunn\FitReader\Contracts\FitReader;
use Dunn\FitReader\Services\FitReaderService;

class FitReaderServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/fit-reader.php', 'fit-reader');

        $this->app->bind(FitReader::class, function ($app) {
            return new FitReaderService($app['config']['fit-reader'] ?? []);
        });
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/fit-reader.php' => config_path('fit-reader.php'),
            ], 'fit-reader-config');
        }
    }
}

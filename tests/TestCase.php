<?php

namespace Dunn\FitReader\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Dunn\FitReader\FitReaderServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            FitReaderServiceProvider::class,
        ];
    }
}

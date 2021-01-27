<?php

namespace Chojnicki\VideoPlatformsParser\Tests;

use Chojnicki\VideoPlatformsParser\ServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }
}

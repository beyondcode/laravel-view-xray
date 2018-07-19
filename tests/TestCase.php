<?php

namespace BeyondCode\ViewXray\Tests;

use View;
use BeyondCode\ViewXray\ViewXrayServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            ViewXrayServiceProvider::class
        ];
    }

    public function setUp()
    {
        parent::setUp();

        View::addLocation(__DIR__ . '/views');
    }
}

<?php

namespace Jhavenz\LaravelMigrationTableParser\Tests;

use Jhavenz\LaravelMigrationTableParser\LaravelMigrationTableParserServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            LaravelMigrationTableParserServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }
}

<?php

declare(strict_types=1);

namespace Jhavenz\LaravelMigrationTableParser;

use Illuminate\Foundation\Application;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelMigrationTableParserServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('migration-table-parser')
            ->hasConfigFile('migration-table-parser');
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(Parser::class, function (Application $app) {
            return new Parser(
                (new ParserFactory)->createForHostVersion(),
                new NodeTraverser,
                $app['events']
            );
        });

        $this->app->alias(Parser::class, 'migration-table-parser');
    }
}

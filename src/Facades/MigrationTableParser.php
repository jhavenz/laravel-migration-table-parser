<?php

namespace Jhavenz\LaravelMigrationTableParser\Facades;

use Illuminate\Support\Facades\Facade;
use Jhavenz\LaravelMigrationTableParser\Parser;

/**
 * @see \Jhavenz\LaravelMigrationTableParser\Parser
 */
class MigrationTableParser extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Parser::class;
    }
}

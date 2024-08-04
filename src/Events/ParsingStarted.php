<?php

declare(strict_types=1);

namespace Jhavenz\LaravelMigrationTableParser\Events;

use Jhavenz\LaravelMigrationTableParser\Parser;

readonly class ParsingStarted
{
    public function __construct(
        public string $migrationFile,
        public Parser $parser
    ) {}
}

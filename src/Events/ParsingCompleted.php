<?php

declare(strict_types=1);

namespace Jhavenz\LaravelMigrationTableParser\Events;

use Jhavenz\LaravelMigrationTableParser\Parser;
use Jhavenz\LaravelMigrationTableParser\ParserResult;

readonly class ParsingCompleted
{
    public function __construct(
        public ParserResult $result,
        public Parser $parser
    ) {}
}

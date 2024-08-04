<?php

declare(strict_types=1);

namespace Jhavenz\LaravelMigrationTableParser;

use Illuminate\Support\Collection;
use PhpParser\Node;
use PhpParser\NodeVisitor;

interface TableNameVisitor extends NodeVisitor
{
    /** @return Collection<string> */
    public function tablesFound(): Collection;

    public function nodeIsInScope(Node $node): bool;
}

<?php

declare(strict_types=1);

namespace Jhavenz\LaravelMigrationTableParser\ExtractionStrategies;

use Jhavenz\LaravelMigrationTableParser\TableNameVisitor;
use PhpParser\Node;
use Prewk\Option;

class DropIfExists implements ExtractionStrategy
{
    use TraverseMigrationPredicates;

    protected Node $node;

    protected TableNameVisitor $visitor;

    public function extract(): Option
    {
        if (! $this->visitor->nodeIsInScope($this->node)) {
            return \none();
        }

        if (! $this->isAStandardMethodCall() && ! $this->isAStaticMethodCall()) {
            return \none();
        }

        if (! $this->isCallingMethod('dropIfExists')) {
            return \none();
        }

        if (! $this->nthArgumentIsA(0, Node\Scalar\String_::class)) {
            return \none();
        }

        return some(data_get($this->node, 'args.0.value.value'));
    }

    public function setNode(Node $node): static
    {
        $this->node = $node;

        return $this;
    }

    public function setVisitor(TableNameVisitor $visitor): static
    {
        $this->visitor = $visitor;

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace Jhavenz\LaravelMigrationTableParser\ExtractionStrategies;

use Jhavenz\LaravelMigrationTableParser\TableNameVisitor;
use PhpParser\Node;
use Prewk\Option;

class CreateOrAlterTableMethodIsCalled implements ExtractionStrategy
{
    use TraverseMigrationPredicates;

    protected Node $node;

    protected TableNameVisitor $visitor;

    /** @return Option<string> */
    public function extract(): Option
    {
        if (!$this->visitor->nodeIsInScope($this->node)) {
            return \none();
        }

        if (!$this->isAStandardMethodCall() && !$this->isAStaticMethodCall()) {
            return \none();
        }

        if (!$this->hasArgCount(2)) {
            return \none();
        }

        if (!$this->isCallingMethod('create', 'table')) {
            return \none();
        }

        if (!$this->nthArgumentIsA(0, Node\Scalar\String_::class)) {
            return \none();
        }

        if (!$this->nthArgumentIsA(1, Node\Expr\Closure::class)) {
            return \none();
        }

        return \some($this->node->args[0]->value->value);
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

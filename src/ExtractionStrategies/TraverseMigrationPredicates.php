<?php

declare(strict_types=1);

namespace Jhavenz\LaravelMigrationTableParser\ExtractionStrategies;

use PhpParser\Node;

trait TraverseMigrationPredicates
{
    protected function isAStaticMethodCall(): bool
    {
        return $this->node instanceof Node\Expr\StaticCall;
    }

    protected function isAStandardMethodCall(): bool
    {
        return $this->node instanceof Node\Expr\MethodCall;
    }

    protected function isCallingMethod(string ...$methodNames): bool
    {
        return in_array($this->node->name->name ?? null, $methodNames);
    }

    protected function hasArgCount(int $count): bool
    {
        $args = $this->node->args ?? null;

        return ! is_null($args) && count($args) === $count;
    }

    protected function nthArgumentIsA(int $argIndex, string $type): bool
    {
        $args = $this->node->args ?? [];

        if (! array_key_exists($argIndex, $args)) {
            return false;
        }

        return data_get($args[$argIndex], 'value') instanceof $type;
    }

    protected function isInsideUpOrDownMethod(Node $node): bool
    {
        if ($node instanceof Node\Stmt\ClassMethod && in_array($node->name->name, ['up', 'down'])) {
            $this->upOrDownNode = $node;

            /** We're just entering it... */
            return false;
        }

        if (! isset($this->upOrDownNode)) {
            return false;
        }

        if ($node->getStartLine() < $this->upOrDownNode->getStartLine()) {
            unset($this->upOrDownNode);

            return false;
        }

        if ($node->getEndLine() > $this->upOrDownNode->getEndLine()) {
            unset($this->upOrDownNode);

            return false;
        }

        return true;
    }
}

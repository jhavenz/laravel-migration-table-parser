<?php

declare(strict_types=1);

namespace Jhavenz\LaravelMigrationTableParser;

use Illuminate\Support\Collection;
use IteratorIterator;
use Jhavenz\LaravelMigrationTableParser\ExtractionStrategies\CreateOrAlterTableMethodIsCalled;
use Jhavenz\LaravelMigrationTableParser\ExtractionStrategies\Drop;
use Jhavenz\LaravelMigrationTableParser\ExtractionStrategies\DropIfExists;
use Jhavenz\LaravelMigrationTableParser\ExtractionStrategies\ExtractionStrategy;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use Prewk\Option;
use Traversable;

class ExtractMigrationTableVisitor extends NodeVisitorAbstract implements TableNameVisitor
{
    private array $parserStrategies;

    /** @var string[] */
    private array $tablesFound = [];

    private Traversable $strategyIterator;

    private Node\Stmt\ClassMethod $upOrDownMethod;

    public function table(int $index = 0): ?string
    {
        return $this->tablesFound[$index] ?? null;
    }

    public function tablesFound(): Collection
    {
        return collect($this->tablesFound);
    }

    public function enterNode(Node $node)
    {
        /** @var ExtractionStrategy $strategy */
        foreach ($this->strategies($node) as $strategy) {
            foreach ($strategy->extract()->iter() as $table) {
                $this->tablesFound[] = $table;
            }
        }
    }

    public function nodeIsInScope(Node $node): bool
    {
        if ($node instanceof Node\Stmt\ClassMethod && in_array($node->name->name, ['up', 'down'])) {
            $this->upOrDownMethod = $node;

            return false;
        }

        if (! isset($this->upOrDownMethod)) {
            return false;
        }

        if ($node->getStartLine() < $this->upOrDownMethod->getStartLine()) {
            unset($this->upOrDownMethod);

            return false;
        }

        if ($node->getEndLine() > $this->upOrDownMethod->getEndLine()) {
            unset($this->upOrDownMethod);

            return false;
        }

        return true;
    }

    public function upOrDownMethod(): Node\Stmt\ClassMethod
    {
        return $this->upOrDownMethod;
    }

    /** @return Option<Node\Stmt\Expression> */
    public function findCallSite(callable $callback): Option
    {
        if (! isset($this->upOrDownMethod)) {
            return \none();
        }

        foreach ($this->upOrDownMethod()->stmts as $stmt) {
            if (! $stmt instanceof Node\Stmt\Expression) {
                continue;
            }

            if (! $callback($stmt)) {
                continue;
            }

            return \some($stmt);
        }

        return \none();
    }

    public function setStrategyIterator(Traversable $strategyIterator): static
    {
        $this->strategyIterator = $strategyIterator;

        return $this;
    }

    private function strategies(Node $node): IteratorIterator
    {
        return new IteratorIterator($this->strategyIterator($node));
    }

    private function strategyIterator(Node $node): Traversable
    {
        $this->parserStrategies = array_map(function (ExtractionStrategy|string $strategy) use ($node) {
            return (is_string($strategy) ? app($strategy) : $strategy)
                ->setNode($node)
                ->setVisitor($this);
        }, config('migration-table-parser.strategies', [
            Drop::class,
            DropIfExists::class,
            CreateOrAlterTableMethodIsCalled::class,
        ]));

        return $this->strategyIterator ?? value(function () {
            /** @var ExtractionStrategy $strategy */
            foreach ($this->parserStrategies as $strategy) {
                yield $strategy;
            }
        });
    }
}

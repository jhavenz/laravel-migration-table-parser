<?php

declare(strict_types=1);

namespace Jhavenz\LaravelMigrationTableParser;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Collection;
use Jhavenz\LaravelMigrationTableParser\Events\ParsingCompleted;
use Jhavenz\LaravelMigrationTableParser\Events\ParsingStarted;
use Jhavenz\LaravelMigrationTableParser\ExtractionStrategies\ExtractionStrategy;
use PhpParser\Node;
use PhpParser\NodeTraverserInterface as NodeTraverserContract;
use PhpParser\Parser as ParserContract;
use Stringable;

class Parser
{
    public function __construct(
        protected ParserContract $parser,
        protected NodeTraverserContract $nodeTraverser,
        protected Dispatcher $dispatcher,
    ) {}

    /** @return Collection<ParserResult> */
    public function parseMany(Stringable|string ...$directoriesOrFiles): Collection
    {
        return array_reduce($directoriesOrFiles, function (Collection $carry, string $path): Collection {
            $carry->push($this->parse($path));

            return $carry;
        }, new Collection());
    }

    public function parse(Stringable|string $filePath): ParserResult
    {
        $visitors = config('migration-table-parser.visitors', [
            ExtractMigrationTableVisitor::class,
        ]);

        if (empty($visitors)) {
            $this->dispatcher->dispatch(new ParsingCompleted(
                $result = new ParserResult(false, 'No visitors provided', $filePath),
                $this
            ));

            return $result;
        }

        if (false === $path = realpath((string) $filePath)) {
            $this->dispatcher->dispatch(new ParsingCompleted(
                $result = new ParserResult(false, "Invalid path given", (string) $filePath),
                $this
            ));

            return $result;
        }

        $this->dispatcher->dispatch(new ParsingStarted($path, $this));

        foreach ($visitors as $visitor) {
            assert(is_a($visitor, TableNameVisitor::class, true),
                "Visitor must implement the `TableNameVisitor` contract: ".class_basename($visitor)
            );
        }

        try {
            /** @var Node\Stmt[]|null $ast */
            $ast = $this->parser->parse(file_get_contents($path));
        } catch (\Throwable $e) {
            $this->dispatcher->dispatch(new ParsingCompleted(
                $result = new ParserResult(false, "Parse error", $path, exception: $e),
                $this
            ));

            return $result;
        }

        if ($ast === null) {
            $this->dispatcher->dispatch(new ParsingCompleted(
                $result = new ParserResult(false, 'No AST was generated', $path),
                $this
            ));

            return $result;
        }

        try {
            $visitorInstances = [];

            /** @var class-string<TableNameVisitor>|TableNameVisitor $visitor */
            foreach ($visitors as $visitor) {
                $visitorInstance = is_string($visitor) ? app($visitor) : $visitor;

                $visitorInstances[] = $visitorInstance;

                $this->nodeTraverser->addVisitor($visitorInstance);
            }

            $this->nodeTraverser->traverse($ast);

            $tablesFound = [];
            foreach ($visitorInstances as $visitorInstance) {
                $tablesFound = $visitorInstance->tablesFound()->union($tablesFound)->unique()->all();
            }

            if (empty($tablesFound)) {
                $parserResult = new ParserResult(false, "No tables found in migration at path", $path);
            } else {
                $parserResult = new ParserResult(true, 'success', $path, $tablesFound);
            }

            $this->dispatcher->dispatch(new ParsingCompleted($parserResult, $this));

            return $parserResult;
        } catch (\Throwable $e) {
            $this->dispatcher->dispatch(new ParsingCompleted(
                new ParserResult(false, $e->getMessage(), $path, exception: $e),
                $this
            ));

            throw $e;
        }
    }

    public function setVisitors(string ...$visitors): Parser
    {
        foreach ($visitors as $visitor) {
            assert(class_exists($visitor), "Visitor [{$visitor}] class does not exist");
            assert(is_a($visitor, TableNameVisitor::class, true), "Visitor [{$visitor}] must implement the `TableNameVisitor` contract");
        }

        config(['migration-table-parser.visitors' => $visitors]);

        return $this;
    }

    public function setStrategies(string ...$strategies): Parser
    {
        foreach ($strategies as $strategy) {
            assert(class_exists($strategy), "Strategy [{$strategy}] class does not exist");
            assert(is_a($strategy, ExtractionStrategy::class, true), "Strategy [{$strategy}] must implement the `ExtractionStrategy` contract");
        }

        config(['migration-table-parser.strategies' => $strategies]);

        return $this;
    }
}

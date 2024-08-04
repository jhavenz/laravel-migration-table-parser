<?php

declare(strict_types=1);

namespace Jhavenz\LaravelMigrationTableParser\ExtractionStrategies;

use Jhavenz\LaravelMigrationTableParser\TableNameVisitor;
use PhpParser\Node;
use Prewk\Option;

/**
 * When traversing the AST, the current `PhpParser\Node` instance is passed to the constructor.
 * The signature is:
 * ```
 * public function __construct(Node $node)
 * ```
 *
 * @template T
 */
interface ExtractionStrategy
{
    /** @return Option<T> */
    public function extract(): Option;

    public function setNode(Node $node);

    public function setVisitor(TableNameVisitor $visitor);
}

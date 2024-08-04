<?php

use Jhavenz\LaravelMigrationTableParser\ExtractMigrationTableVisitor;

return [
    'visitors' => [
        ExtractMigrationTableVisitor::class
    ],
    'strategies' => [
        Jhavenz\LaravelMigrationTableParser\ExtractionStrategies\Drop::class,
        Jhavenz\LaravelMigrationTableParser\ExtractionStrategies\DropIfExists::class,
        Jhavenz\LaravelMigrationTableParser\ExtractionStrategies\CreateOrAlterTableMethodIsCalled::class
    ]
];

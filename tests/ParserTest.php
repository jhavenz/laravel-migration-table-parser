<?php

declare(strict_types=1);

use Jhavenz\LaravelMigrationTableParser\Facades\MigrationTableParser;

it("extracts the 'users' table for valid migrations", function () {
    foreach (File::files(__DIR__.'/stubs/valid_migrations') as $file) {
        $basename = str($file)->basename('.php');

        MigrationTableParser::parse($file)
            ->expect()
            ->tables()
            ->tap(fn ($tables) => expect('users')->toBeIn(
                $tables,
                "Failed asserting the 'users' table was found in the [{$basename}] migration. Tables found: '{$tables->join("', '")}'"
            ));
    }
});

it('extracts tables for migrations that declare multiple tables', function () {
    foreach (File::files(__DIR__.'/stubs/multi_table_migrations') as $file) {
        $expectedCount = str($file->getFilename())->before('_');

        expect($expectedCount->value())->toBeNumeric(
            "The migration file name should start with a number followed by an underscore. Got: {$file->getFilename()}"
        );

        MigrationTableParser::parse($file)
            ->expect()
            ->tables()
            ->tap(function ($tables) use ($expectedCount, $file) {
                $basename = str($file)->basename('.php');

                expect($tables)->toHaveCount($expectedCount->toInteger(), sprintf(
                    "Failed asserting %s tables were found when parsing [%s]. Found: %s",
                    $expectedCount,
                    $basename,
                    $tables->join(', ')
                ));
            });
    }
});

it('throws an error for invalid migrations', function () {
    foreach (File::files(__DIR__.'/stubs/invalid_migrations') as $file) {
        $basename = str($file)->basename('.php');

        MigrationTableParser::parse($file)
            ->expect()
            ->tables()
            ->tap(function ($tables) use ($basename) {
                expect($tables)->toBeEmpty(sprintf(
                    "The parser should have failed for the [%s] migration file, got [%s]",
                    $basename,
                    $tables->join(',')
                ));
            });
    }
});

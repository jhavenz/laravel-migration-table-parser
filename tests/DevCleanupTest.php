<?php

declare(strict_types=1);

use PHPUnit\Framework\Assert as PHPUnit;

/** @phpstan-ignore-next-line  */
arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

test('no commented code is leftover', function () {
    $command = implode(' ', [
        'php',
        'vendor/bin/swiss-knife',
        'check-commented-code',
        'src',
        'tests',
        'config',
    ]);

    $process = Process::timeout(10)->run($command);

    if ($errorOutput = $process->errorOutput()) {
        PHPUnit::fail(str($errorOutput)->before('check-commented-code')->rtrim()->toString());
    }

    expect($process->successful())->toBeTrue(
        PHP_EOL
        .'[Commented Code Was Found]'
        .PHP_EOL
        .PHP_EOL
        .'Double-check the following files:'
        .PHP_EOL
        .str($process->output())->after('*.php files')->beforeLast('[ERROR]')->trim()
    );
});

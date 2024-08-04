<?php

declare(strict_types=1);

namespace Jhavenz\LaravelMigrationTableParser;

use Illuminate\Support\Collection;
use Pest\Expectation;
use Prewk\Option;
use Throwable;

readonly class ParserResult
{
    public function __construct(
        private bool $successful,
        private string $message,
        private string $migrationFile,
        private array $tablesFound = [],
        private ?Throwable $exception = null,
    ) {}

    public function wasSuccessful(): bool
    {
        return $this->successful;
    }

    public function message(): string
    {
        return $this->message;
    }

    public function migrationPath(): string
    {
        return realpath($this->migrationFile);
    }

    public function exception(): ?Throwable
    {
        return $this->exception;
    }

    /** @return Expectation<static> */
    public function expect(): Expectation
    {
        return new Expectation($this);
    }

    public function table(int $index = 0): ?string
    {
        return $this->tablesFound[$index] ?? null;
    }

    public function tables(): Collection
    {
        return collect($this->tablesFound);
    }

    public function search(string $name): Option
    {
        $index = array_search($name, array_unique($this->tablesFound));

        return $index === false
            ? \none()
            : \some($this->tablesFound[$index]);
    }
}

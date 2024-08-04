## Laravel Migration Table Parser
Extracts the table name(s) from a Laravel migration with the assistance of the [nikic/php-parser](https://github.com/nikic/PHP-Parser)

### Basic Usage
This package looks at the code used inside the up/down method of a migration file.
```php
use Jhavenz\LaravelMigrationTableParser\Facades\MigrationTableParser;

$result = MigrationTableParser::parse($file);
```
_$result is a value object of type `\Jhavenz\LaravelMigrationTableParser\ParserResult`_

```php
$optionalTable = $result->search('my_table');
```
_$optionalTable is an instance of `\Prewk\Option`, a rust-inspired Some/None monad. [Read more here](https://github.com/prewk/option)_

```php
$tablesFoundCollection = $result->tables();
```
_$tablesFoundCollection is an instance of `\Illuminate\Support\Collection<string>`_

**Dive into the [/tests](tests) to learn more** 

### Implementation Notes

You can run any other logic within your migration file(s), but the table name is extracted based on when you call one of these methods:
- `->drop('users');`
- `->dropIfExists('users');`
- `->table('users', function (Blueprint $table) {...});`
- `->create('users', function (Blueprint $table) {...});`
_In all cases, the package will extract 'users' as the table name_

This package provides a couple extension points for you to implement your own logic.

Check out these interfaces:
- [Write your own extraction logic using this interface](src/ExtractionStrategies/ExtractionStrategy.php)
- [To write your own visitor logic using this interface](src/TableNameVisitor.php)

Once implemented, you can pass your custom classes to the parser like so:
```php
use Jhavenz\LaravelMigrationTableParser\Facades\MigrationTableParser;

$result = MigrationTableParser::parse($file)
    ->setStrategies(MyCustomExtractionStrategy::class)
    ->setVisitors(MyCustomTableNameVisitor::class)
    ->parse(database_path('migrations/2021_01_01_000000_create_some_table.php'));
```

Note: When the parser runs, these implementations get resolved by the service container.

An alternative way to add your own logic is to add your implentation(s) to the config file:
```php
// Push, leaving the default strategies in place
config()->push('migration-table-parser.strategies', [MyCustomExtractionStrategy::class]);

// Override the default strategies
config()->set('migration-table-parser.strategies', [MyCustomExtractionStrategy::class]);
````

## Installation

You can install the package via composer:

```bash
composer require jhavenz/laravel-migration-table-parser
```

### Publishing
You can publish the config file with:

```bash
php artisan vendor:publish --tag="migration-table-parser-config"
```

This is the contents of the published config file:

```php
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
```

### Advanced Usage
The `\Jhavenz\LaravelMigrationTableParser\ParserResult` provides a 
handful of convenience methods to help you work with each result that's
extracted from the migration file(s). It's basic makeup is:
```php
readonly class ParserResult
{
    public function __construct(
        private bool $successful,
        private string $message,
        private string $migrationFile,
        private array $tablesFound = [],
        private ?Throwable $error = null,
    ) {}

    // ... other methods
}
```

Beyond the getters for the properties listed above, it has methods with the following signatures:
- `public function search(string $name): Option`
- `public function table(int $index = 0): ?string`
- `public function expect(): \Pest\Expectation`

Lastly, the parser emits events that you can listen for.

Their basic makeup includes:
```php
readonly class ParsingStarted
{
    public function __construct(
        public string $migrationFile,
        public Parser $parser
    ) {}
}
```

and 

```php
readonly class ParsingCompleted
{
    public function __construct(
        public ParserResult $result,
        public Parser $parser
    ) {}
}
```

You can listen to these events using Laravel's event system, e.g.
```php
use Jhavenz\LaravelMigrationTableParser\Events\ParsingStarted;

Event::listen(ParsingStarted::class, function (ParsingStarted $event) {
    // do stuff...
});
```

## Testing
Pest is used for testing and the initial commit for this repo is being pushed with ~30-40 tests.

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Contributions are welcome.

## Security Vulnerabilities

Please [email me](mailto:jonathan.e.havens@gmail.com) asap if you discover any security related issues:

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

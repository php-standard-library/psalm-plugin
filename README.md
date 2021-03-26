# PSL Psalm Plugin

![Static analysis status](https://github.com/php-standard-library/psalm-plugin/workflows/static%20analysis/badge.svg)
[![Type Coverage](https://shepherd.dev/github/php-standard-library/psalm-plugin/coverage.svg)](https://shepherd.dev/github/php-standard-library/psalm-plugin)
[![Total Downloads](https://poser.pugx.org/php-standard-library/psalm-plugin/d/total.svg)](https://packagist.org/packages/php-standard-library/psalm-plugin)
[![Latest Stable Version](https://poser.pugx.org/php-standard-library/psalm-plugin/v/stable.svg)](https://packagist.org/packages/php-standard-library/psalm-plugin)
[![License](https://poser.pugx.org/php-standard-library/psalm-plugin/license.svg)](https://packagist.org/packages/php-standard-library/psalm-plugin)

## Installation

Supported installation method is via [composer](https://getcomposer.org):

```shell
composer install php-standard-library/psalm-plugin --dev
```

## Usage

To enable the plugin, add the `Psl\Psalm\Plugin` class to your psalm configuration using `psalm-plugin` binary as follows:

```shell
php vendor/bin/psalm-plugin enable php-standard-library/psalm-plugin
```

## Type improvements

Given the following example:

```php
use Psl\Type;

$specification = Type\shape([
  'name' => Type\string(),
  'age' => Type\int(),
  'location' => Type\optional(Type\shape([
    'city' => Type\string(),
    'state' => Type\string(),
    'country' => Type\string(),
  ]))
]);

$input = $specification->coerce($_GET['user']);

/** @psalm-trace $input */
```

Psalm assumes that `$input` is of type `array<"age"|"location"|"name", array<"city"|"country"|"state", string>|int|string>`.

If we enable the `php-standard-library/psalm-plugin` plugin, you will get a more specific
and correct type of `array{name: string, age: int, location?: array{city: string, state: string, country: string}}`.

## Sponsors

Thanks to our sponsors and supporters:

| JetBrains |
|---|
| <a href="https://www.jetbrains.com/?from=PSL ( PHP Standard Library )" title="JetBrains" target="_blank"><img src="https://res.cloudinary.com/azjezz/image/upload/v1599239910/jetbrains_qnyb0o.png" height="120" /></a> |

## License

The MIT License (MIT). Please see [`LICENSE`](./LICENSE) for more information.

<?php

declare(strict_types=1);

use Psl\Type;

$shape = Type\shape([
    'name' => Type\string(),
    'age' => Type\optional(Type\nullable(Type\int())),
]);
/** @psalm-check-type-exact $shape = Psl\Type\TypeInterface<array{name: string, age?: int|null}> */

function coerce(mixed $input): void
{
    $shape = Type\shape(['name' => Type\string()]);
    $value = $shape->coerce($input);
    /** @psalm-check-type-exact $value = array{name: string} */
}

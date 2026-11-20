<?php

declare(strict_types=1);

use Psl\Fun;

$pipe = Fun\pipe(
    static fn(string $x): int => (int) $x,
    static fn(int $x): float => $x / 2,
);
/** @psalm-check-type $pipe = Closure(string): (float|int) */

$result = $pipe('4');
/** @psalm-check-type-exact $result = float|int */

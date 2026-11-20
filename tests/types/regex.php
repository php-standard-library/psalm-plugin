<?php

declare(strict_types=1);

use Psl\Regex;

$groups = Regex\capture_groups(['year', 1]);
/** @psalm-check-type-exact $groups = Psl\Type\TypeInterface<array{0: string, 1: string, year: string}> */

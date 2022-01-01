<?php

declare(strict_types=1);

namespace Psl\Psalm;

use PhpParser;
use Psalm\StatementsSource;
use Psalm\Type;

final class Argument
{
    /**
     * @param list<PhpParser\Node\Arg> $arguments
     * @param int<0, max> $index
     */
    public static function getType(
        array $arguments,
        StatementsSource $statements_source,
        int $index
    ): ?Type\Union {
        $argument = $arguments[$index] ?? null;
        if (null === $argument) {
            return null;
        }

        return $statements_source->getNodeTypeProvider()->getType($argument->value);
    }
}

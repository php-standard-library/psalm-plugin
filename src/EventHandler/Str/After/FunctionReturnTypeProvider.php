<?php

declare(strict_types=1);

namespace Psl\Psalm\EventHandler\Str\After;

use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\FunctionReturnTypeProviderInterface;
use Psalm\Type;
use Psl\Psalm\Argument;

final class FunctionReturnTypeProvider implements FunctionReturnTypeProviderInterface
{
    /**
     * @return non-empty-list<lowercase-string>
     */
    public static function getFunctionIds(): array
    {
        return [
            'psl\str\after',
            'psl\str\after_ci',
            'psl\str\after_last',
            'psl\str\after_last_ci',
            'psl\str\byte\after',
            'psl\str\byte\after_ci',
            'psl\str\byte\after_last',
            'psl\str\byte\after_last_ci',
            'psl\str\grapheme\after',
            'psl\str\grapheme\after_ci',
            'psl\str\grapheme\after_last',
            'psl\str\grapheme\after_last_ci',
        ];
    }

    public static function getFunctionReturnType(FunctionReturnTypeProviderEvent $event): ?Type\Union
    {
        $argument_type = Argument::getType($event->getCallArgs(), $event->getStatementsSource(), 0);
        if ($argument_type === null || !$argument_type->hasLowercaseString()) {
            return Type::combineUnionTypes(Type::getNull(), Type::getString());
        }

        return new Type\Union([new Type\Atomic\TLowercaseString(), new Type\Atomic\TNull()]);
    }
}

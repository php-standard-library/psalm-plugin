<?php

declare(strict_types=1);

namespace Psl\Psalm\EventHandler\Str\Before;

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
            'psl\str\before',
            'psl\str\before_ci',
            'psl\str\before_last',
            'psl\str\before_last_ci',
            'psl\str\byte\before',
            'psl\str\byte\before_ci',
            'psl\str\byte\before_last',
            'psl\str\byte\before_last_ci',
            'psl\str\grapheme\before',
            'psl\str\grapheme\before_ci',
            'psl\str\grapheme\before_last',
            'psl\str\grapheme\before_last_ci',
        ];
    }

    public static function getFunctionReturnType(FunctionReturnTypeProviderEvent $event): ?Type\Union
    {
        $argument_type = Argument::getType($event->getCallArgs(), $event->getStatementsSource(), 0);
        if ($argument_type === null || !$argument_type->hasLowercaseString()) {
            return Type::combineUnionTypes(Type::getNull(), Type::getString());
        }

        return new Type\Union([new Type\Atomic\TNull(), new Type\Atomic\TLowercaseString()]);
    }
}

<?php

declare(strict_types=1);

namespace Psl\Psalm\EventHandler\Str\Lowercase;

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
            'psl\str\lowercase',
            'psl\str\byte\lowercase',
        ];
    }

    public static function getFunctionReturnType(FunctionReturnTypeProviderEvent $event): ?Type\Union
    {
        $argument_type = Argument::getType($event->getCallArgs(), $event->getStatementsSource(), 0);
        if ($argument_type === null) {
            return new Type\Union([new Type\Atomic\TLowercaseString()]);
        }

        $string_argument_type = $argument_type->getAtomicTypes()['string'] ?? null;
        if ($string_argument_type instanceof Type\Atomic\TNonEmptyString) {
            return new Type\Union([new Type\Atomic\TNonEmptyLowercaseString()]);
        }

        return new Type\Union([new Type\Atomic\TLowercaseString()]);
    }
}

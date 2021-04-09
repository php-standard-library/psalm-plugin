<?php

declare(strict_types=1);

namespace Psl\Psalm\EventHandler\Str\Uppercase;

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
            'psl\str\uppercase',
            'psl\str\byte\uppercase',
        ];
    }

    public static function getFunctionReturnType(FunctionReturnTypeProviderEvent $event): ?Type\Union
    {
        $argument_type = Argument::getType($event->getCallArgs(), $event->getStatementsSource(), 0);
        if (null === $argument_type) {
            return Type::getString();
        }

        $string_argument_type = $argument_type->getAtomicTypes()['string'] ?? null;
        if (null === $string_argument_type) {
            return Type::getString();
        }

        if ($string_argument_type instanceof Type\Atomic\TNonEmptyString) {
            return new Type\Union([new Type\Atomic\TNonEmptyString()]);
        }

        return Type::getString();
    }
}

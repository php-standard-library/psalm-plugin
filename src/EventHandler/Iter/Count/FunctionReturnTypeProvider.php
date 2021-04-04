<?php

declare(strict_types=1);

namespace Psl\Psalm\EventHandler\Iter\Count;

use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\FunctionReturnTypeProviderInterface;
use Psalm\Type;
use Psl\Psalm\Argument;

use function count;

final class FunctionReturnTypeProvider implements FunctionReturnTypeProviderInterface
{
    /**
     * @return non-empty-list<lowercase-string>
     */
    public static function getFunctionIds(): array
    {
        return [
            'psl\str\count',
        ];
    }

    public static function getFunctionReturnType(FunctionReturnTypeProviderEvent $event): ?Type\Union
    {
        $argument_type = Argument::getType($event->getCallArgs(), $event->getStatementsSource(), 0);
        if (null === $argument_type) {
            return Type::getInt();
        }

        $array_argument_type = $argument_type->getAtomicTypes()['array'] ?? null;
        if (null === $array_argument_type) {
            return Type::getInt();
        }

        // non-empty-array/non-empty-list -> positive-int / literal-int
        if ($array_argument_type instanceof Type\Atomic\TNonEmptyList) {
            $count = $array_argument_type->count;
            if (null === $count) {
                return Type::getPositiveInt();
            }

            return Type::getInt(false, $count);
        }

        if ($array_argument_type instanceof Type\Atomic\TNonEmptyArray) {
            $count = $array_argument_type->count;
            if (null === $count) {
                return Type::getPositiveInt();
            }

            return Type::getInt(false, $count);
        }

        // array{foo: bar} -> literal-int(1)
        if ($array_argument_type instanceof Type\Atomic\TKeyedArray) {
            return Type::getInt(false, count($array_argument_type->properties));
        }

        return Type::getInt();
    }
}

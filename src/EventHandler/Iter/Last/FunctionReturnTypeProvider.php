<?php

declare(strict_types=1);

namespace Psl\Psalm\EventHandler\Iter\Last;

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
            'psl\iter\last',
        ];
    }

    public static function getFunctionReturnType(FunctionReturnTypeProviderEvent $event): ?Type\Union
    {
        $argument_type = Argument::getType($event->getCallArgs(), $event->getStatementsSource(), 0);
        if (null === $argument_type) {
            return null;
        }

        $array_argument_type = $argument_type->getAtomicTypes()['array'] ?? null;
        if (null === $array_argument_type) {
            return null;
        }

        if ($array_argument_type instanceof Type\Atomic\TNonEmptyArray) {
            return $array_argument_type->type_params[1];
        }

        if ($array_argument_type instanceof Type\Atomic\TNonEmptyList) {
            return $array_argument_type->type_param;
        }

        if ($array_argument_type instanceof Type\Atomic\TKeyedArray) {
            // TODO(azjezz): add support for this once psalm starts enforcing the shape order ( if ever ).
            //
            // $properties = $array_argument_type->properties;
            // $last_property = null;
            // foreach ($properties as $property) {
            //     $last_property = $property;
            // }
            //
            // return $last_property;
            return $array_argument_type->getGenericValueType();
        }

        return null;
    }
}

<?php

declare(strict_types=1);

namespace Psl\Psalm\EventHandler\Type\Shape;

use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\FunctionReturnTypeProviderInterface;
use Psalm\Type;
use Psl\Psalm\Argument;

use function array_values;

final class FunctionReturnTypeProvider implements FunctionReturnTypeProviderInterface
{
    /**
     * @return non-empty-list<lowercase-string>
     */
    public static function getFunctionIds(): array
    {
        return [
            'psl\type\shape'
        ];
    }

    public static function getFunctionReturnType(FunctionReturnTypeProviderEvent $event): ?Type\Union
    {
        $argument_type = Argument::getType($event->getCallArgs(), $event->getStatementsSource(), 0);
        if (null === $argument_type) {
            return new Type\Union([new Type\Atomic\TGenericObject('Psl\Type\TypeInterface', [
                new Type\Union([
                    new Type\Atomic\TArray([
                        new Type\Union([new Type\Atomic\TArrayKey()]),
                        new Type\Union([new Type\Atomic\TMixed()])
                    ])
                ])
            ])]);
        }

        $argument_shape = $argument_type->getAtomicTypes()['array'] ?? null;
        if (!$argument_shape instanceof Type\Atomic\TKeyedArray) {
            return new Type\Union([new Type\Atomic\TGenericObject('Psl\Type\TypeInterface', [
                new Type\Union([
                    new Type\Atomic\TArray([
                        new Type\Union([new Type\Atomic\TArrayKey()]),
                        new Type\Union([new Type\Atomic\TMixed()])
                    ])
                ])
            ])]);
        }


        $properties = [];
        foreach ($argument_shape->properties as $name => $value) {
            $type = array_values($value->getAtomicTypes())[0] ?? null;
            if (!$type instanceof Type\Atomic\TGenericObject) {
                return null;
            }

            $property_type = clone $type->type_params[0];
            $property_type->possibly_undefined = $value->possibly_undefined;

            $properties[$name] = $property_type;
        }

        return new Type\Union([new Type\Atomic\TGenericObject('Psl\Type\TypeInterface', [
            new Type\Union([
                new Type\Atomic\TKeyedArray($properties)
            ])
        ])]);
    }
}

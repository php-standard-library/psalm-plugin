<?php

declare(strict_types=1);

namespace Psl\Psalm\EventHandler\Type\Shape;

use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\FunctionReturnTypeProviderInterface;
use Psalm\Type;
use Psl\Iter;
use Psl\Type\TypeInterface;

final class FunctionReturnTypeProvider implements FunctionReturnTypeProviderInterface
{
    /**
     * @return array<lowercase-string>
     */
    public static function getFunctionIds(): array
    {
        return [
            'psl\type\shape'
        ];
    }

    public static function getFunctionReturnType(FunctionReturnTypeProviderEvent $event): ?Type\Union
    {
        $argument = Iter\first($event->getCallArgs());
        if (null === $argument) {
            return new Type\Union([new Type\Atomic\TGenericObject(TypeInterface::class, [
                new Type\Union([
                    new Type\Atomic\TArray([
                        new Type\Union([new Type\Atomic\TArrayKey()]),
                        new Type\Union([new Type\Atomic\TMixed()])
                    ])
                ])
            ])]);
        }

        $statements_source = $event->getStatementsSource();
        $type = $statements_source->getNodeTypeProvider()->getType($argument->value);
        if (null === $type) {
            return new Type\Union([new Type\Atomic\TGenericObject(TypeInterface::class, [
                new Type\Union([
                    new Type\Atomic\TArray([
                        new Type\Union([new Type\Atomic\TArrayKey()]),
                        new Type\Union([new Type\Atomic\TMixed()])
                    ])
                ])
            ])]);
        }

        $atomic = $type->getAtomicTypes();
        $argument_shape = $atomic['array'] ?? null;
        if (!$argument_shape instanceof Type\Atomic\TKeyedArray) {
            return new Type\Union([new Type\Atomic\TGenericObject(TypeInterface::class, [
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
            $type = Iter\first($value->getAtomicTypes());
            if (!$type instanceof Type\Atomic\TGenericObject) {
                return null;
            }

            $property_type = clone $type->type_params[0];
            $property_type->possibly_undefined = $value->possibly_undefined;

            $properties[$name] = $property_type;
        }

        return new Type\Union([new Type\Atomic\TGenericObject(TypeInterface::class, [
            new Type\Union([
                new Type\Atomic\TKeyedArray($properties)
            ])
        ])]);
    }
}

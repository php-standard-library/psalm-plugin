<?php

declare(strict_types=1);

namespace Psl\Psalm\EventHandler\Regex\CaptureGroups;

use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\FunctionReturnTypeProviderInterface;
use Psalm\Type;
use Psl\Psalm\Argument;

final class FunctionReturnTypeProvider implements FunctionReturnTypeProviderInterface
{
    /**
     * @return array<lowercase-string>
     */
    public static function getFunctionIds(): array
    {
        return [
            'psl\regex\capture_groups'
        ];
    }

    public static function getFunctionReturnType(FunctionReturnTypeProviderEvent $event): ?Type\Union
    {
        $argument_type = Argument::getType($event->getCallArgs(), $event->getStatementsSource(), 0);
        if (null === $argument_type) {
            return self::fallbackType();
        }

        $atomic = $argument_type->getAtomicTypes();
        $capture_groups = $atomic['array'] ?? null;
        if (!$capture_groups instanceof Type\Atomic\TKeyedArray) {
            return self::fallbackType();
        }

        $properties = [
            0 => Type::getString()
        ];

        foreach ($capture_groups->properties as $value) {
            $type = array_values($value->getAtomicTypes())[0] ?? null;
            if (!$type instanceof Type\Atomic\TLiteralInt && !$type instanceof Type\Atomic\TLiteralString) {
                return self::fallbackType();
            }

            $name = $type->value;

            $properties[$name] = Type::getString();
        }

        return new Type\Union([new Type\Atomic\TGenericObject('Psl\Type\TypeInterface', [
            new Type\Union([
                new Type\Atomic\TKeyedArray($properties)
            ])
        ])]);
    }

    private static function fallbackType(): Type\Union
    {
        return new Type\Union([new Type\Atomic\TGenericObject('Psl\Type\TypeInterface', [
            new Type\Union([
                new Type\Atomic\TNonEmptyArray([
                    new Type\Union([new Type\Atomic\TArrayKey()]),
                    new Type\Union([new Type\Atomic\TString()])
                ])
            ])
        ])]);
    }
}

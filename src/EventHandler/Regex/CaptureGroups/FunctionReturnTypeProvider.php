<?php

declare(strict_types=1);

namespace Psl\Psalm\EventHandler\Regex\CaptureGroups;

use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\FunctionReturnTypeProviderInterface;
use Psalm\Type;

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
        $statements_source = $event->getStatementsSource();
        $call_args = $event->getCallArgs();

        $argument = $call_args[0] ?? null;
        if (null === $argument) {
            return self::fallbackType();
        }

        $argument_value = $argument->value;
        $type = $statements_source->getNodeTypeProvider()->getType($argument_value);
        if (null === $type) {
            return self::fallbackType();
        }

        $atomic = $type->getAtomicTypes();
        $capture_groups = $atomic['array'] ?? null;
        if (!$capture_groups instanceof Type\Atomic\TKeyedArray) {
            return self::fallbackType();
        }

        $string = static fn (): Type\Union => new Type\Union([new Type\Atomic\TString()]);
        $properties = [
            0 => $string()
        ];
        foreach ($capture_groups->properties as $value) {
            $type = array_values($value->getAtomicTypes())[0] ?? null;
            if (!$type instanceof Type\Atomic\TLiteralInt && !$type instanceof Type\Atomic\TLiteralString) {
                return self::fallbackType();
            }

            $name = $type->value;

            $properties[$name] = $string();
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
                new Type\Atomic\TArray([
                    new Type\Union([new Type\Atomic\TArrayKey()]),
                    new Type\Union([new Type\Atomic\TString()])
                ])
            ])
        ])]);
    }
}

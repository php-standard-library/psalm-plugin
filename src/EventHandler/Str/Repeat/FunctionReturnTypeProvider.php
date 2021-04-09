<?php

declare(strict_types=1);

namespace Psl\Psalm\EventHandler\Str\Repeat;

use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\FunctionReturnTypeProviderInterface;
use Psalm\Type;
use Psl\Psalm\Argument;

use function str_repeat;

final class FunctionReturnTypeProvider implements FunctionReturnTypeProviderInterface
{
    /**
     * @return non-empty-list<lowercase-string>
     */
    public static function getFunctionIds(): array
    {
        return [
            'psl\str\repeat',
        ];
    }

    public static function getFunctionReturnType(FunctionReturnTypeProviderEvent $event): ?Type\Union
    {
        $argument_type = Argument::getType($event->getCallArgs(), $event->getStatementsSource(), 0);
        if ($argument_type === null || !$argument_type->hasLowercaseString()) {
            return Type::getString();
        }

        $string_argument_type = $argument_type->getAtomicTypes()['string'] ?? null;
        if ($string_argument_type instanceof Type\Atomic\TNonEmptyString) {
            if ($string_argument_type instanceof Type\Atomic\TNonEmptyLowercaseString) {
                return new Type\Union([new Type\Atomic\TNonEmptyLowercaseString()]);
            }

            return new Type\Union([new Type\Atomic\TNonEmptyString()]);
        }

        if ($string_argument_type instanceof Type\Atomic\TLowercaseString) {
            return new Type\Union([new Type\Atomic\TLowercaseString()]);
        }

        if ($string_argument_type instanceof Type\Atomic\TLiteralString) {
            $multiplier_argument_type = Argument::getType($event->getCallArgs(), $event->getStatementsSource(), 1);
            if (null !== $multiplier_argument_type && $multiplier_argument_type->hasLiteralInt()) {
                /** @psalm-suppress MissingThrowsDocblock */
                return Type::getString(str_repeat(
                    $string_argument_type->value,
                    $multiplier_argument_type->getSingleIntLiteral()->value
                ));
            }
        }

        return Type::getString();
    }
}

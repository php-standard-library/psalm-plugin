<?php

declare(strict_types=1);

namespace Psl\Psalm\EventHandler\Str\Splice;

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
            'psl\str\splice',
            'psl\str\byte\splice',
        ];
    }

    public static function getFunctionReturnType(FunctionReturnTypeProviderEvent $event): ?Type\Union
    {
        $argument_type = Argument::getType($event->getCallArgs(), $event->getStatementsSource(), 0);
        $replacement_argument_type = Argument::getType($event->getCallArgs(), $event->getStatementsSource(), 1);

        if ($argument_type === null || $replacement_argument_type === null) {
            return Type::getString();
        }

        if (!$argument_type->hasLowercaseString() || !$replacement_argument_type->hasLowercaseString()) {
            return Type::getString();
        }

        return new Type\Union([new Type\Atomic\TLowercaseString()]);
    }
}

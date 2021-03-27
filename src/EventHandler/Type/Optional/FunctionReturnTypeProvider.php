<?php

declare(strict_types=1);

namespace Psl\Psalm\EventHandler\Type\Optional;

use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\FunctionReturnTypeProviderInterface;
use Psalm\Type;

final class FunctionReturnTypeProvider implements FunctionReturnTypeProviderInterface
{
    /**
     * @return non-empty-list<lowercase-string>
     */
    public static function getFunctionIds(): array
    {
        return [
            'psl\type\optional'
        ];
    }

    public static function getFunctionReturnType(FunctionReturnTypeProviderEvent $event): ?Type\Union
    {
        $argument = $event->getCallArgs()[0] ?? null;
        if (null === $argument) {
            return null;
        }

        $type = $event
            ->getStatementsSource()
            ->getNodeTypeProvider()
            ->getType($argument->value);

        if (null === $type) {
            return null;
        }

        $clone = clone $type;
        $clone->possibly_undefined = true;

        return $clone;
    }
}

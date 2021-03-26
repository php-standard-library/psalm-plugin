<?php

declare(strict_types=1);

namespace Psl\Psalm\EventHandler\Type\Optional;

use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\FunctionReturnTypeProviderInterface;
use Psalm\Type;
use Psl\Iter;

final class FunctionReturnTypeProvider implements FunctionReturnTypeProviderInterface
{
    /**
     * @return array<lowercase-string>
     */
    public static function getFunctionIds(): array
    {
        return [
            'psl\type\optional'
        ];
    }

    public static function getFunctionReturnType(FunctionReturnTypeProviderEvent $event): ?Type\Union
    {
        $argument = Iter\first($event->getCallArgs());
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

<?php

declare(strict_types=1);

namespace Psl\Psalm\EventHandler\Fun\Pipe;

use Closure;
use Psalm\Plugin\DynamicFunctionStorage;
use Psalm\Plugin\DynamicTemplateProvider;
use Psalm\Plugin\EventHandler\DynamicFunctionStorageProviderInterface;
use Psalm\Plugin\EventHandler\Event\DynamicFunctionStorageProviderEvent;
use Psalm\Storage\FunctionLikeParameter;
use Psalm\Type\Atomic\TClosure;
use Psalm\Type\Atomic\TTemplateParam;
use Psalm\Type\Union;

use function array_map;
use function count;
use function range;

final class PipeArgumentsProvider implements DynamicFunctionStorageProviderInterface
{
    /**
     * @return array<lowercase-string>
     */
    public static function getFunctionIds(): array
    {
        return ['psl\fun\pipe'];
    }

    public static function getFunctionStorage(DynamicFunctionStorageProviderEvent $event): ?DynamicFunctionStorage
    {
        $template_provider = $event->getTemplateProvider();
        $callable_args_count = count($event->getArgs());

        // Create AB closure pairs
        $pipe_callables = array_map(
            static fn(int $callable_offset) => self::createABClosure(
                self::createTemplateFromOffset($template_provider, $callable_offset),
                self::createTemplateFromOffset($template_provider, $callable_offset + 1),
            ),
            range(1, $callable_args_count)
        );

        $pipe_storage = new DynamicFunctionStorage();
        $pipe_storage->params = [
            ...array_map(
                static fn(TClosure $callable, int $offset) => self::createParam(
                    "fn_{$offset}",
                    new Union([$callable]),
                ),
                $pipe_callables,
                array_keys($pipe_callables)
            )
        ];

        // Add Pipe template list for each callable
        $pipe_storage->templates = array_map(
            static fn($offset) => self::createTemplateFromOffset($template_provider, $offset),
            range(1, $callable_args_count + 1),
        );

        // Pipe return type from templates T1 -> TLast (Where TLast could also be T1 when no arguments are provided.)
        $pipe_storage->return_type = new Union([
            self::createABClosure(
                current($pipe_storage->templates),
                end($pipe_storage->templates)
            )
        ]);

        return $pipe_storage;
    }

    private static function createTemplateFromOffset(
        DynamicTemplateProvider $template_provider,
        int $offset
    ): TTemplateParam {
        return $template_provider->createTemplate("T{$offset}");
    }

    private static function createABClosure(
        TTemplateParam $aType,
        TTemplateParam $bType
    ): TClosure {
        $a = self::createParam('input', new Union([$aType]));
        $b = new Union([$bType]);

        return new TClosure(Closure::class, [$a], $b);
    }

    private static function createParam(string $name, Union $type): FunctionLikeParameter
    {
        return new FunctionLikeParameter($name, false, $type);
    }
}

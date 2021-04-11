<?php

declare(strict_types=1);

namespace Psl\Psalm;

use Psalm\Plugin\EventHandler\FunctionReturnTypeProviderInterface;
use Psalm\Plugin\PluginEntryPointInterface;
use Psalm\Plugin\RegistrationInterface;
use SimpleXMLElement;

use function str_replace;

final class Plugin implements PluginEntryPointInterface
{
    public function __invoke(RegistrationInterface $registration, ?SimpleXMLElement $config = null): void
    {
        require_once __DIR__ . '/Argument.php';
        foreach ($this->getHooks() as $hook) {
            /** @psalm-suppress UnresolvableInclude */
            require_once __DIR__ . '/' . str_replace([__NAMESPACE__, '\\'], ['', '/'], $hook) . '.php';

            $registration->registerHooksFromClass($hook);
        }
    }

    /**
     * @return iterable<class-string<FunctionReturnTypeProviderInterface>>
     */
    private function getHooks(): iterable
    {
        // Psl\Iter hooks
        yield EventHandler\Iter\First\FunctionReturnTypeProvider::class;
        yield EventHandler\Iter\FirstKey\FunctionReturnTypeProvider::class;
        yield EventHandler\Iter\Last\FunctionReturnTypeProvider::class;
        yield EventHandler\Iter\LastKey\FunctionReturnTypeProvider::class;
        yield EventHandler\Iter\Count\FunctionReturnTypeProvider::class;

        // Psl\Regex hooks
        yield EventHandler\Regex\CaptureGroups\FunctionReturnTypeProvider::class;

        // Psl\Str hooks
        yield EventHandler\Str\After\FunctionReturnTypeProvider::class;
        yield EventHandler\Str\Before\FunctionReturnTypeProvider::class;
        yield EventHandler\Str\Chunk\FunctionReturnTypeProvider::class;
        yield EventHandler\Str\Lowercase\FunctionReturnTypeProvider::class;
        yield EventHandler\Str\Repeat\FunctionReturnTypeProvider::class;
        yield EventHandler\Str\Repeat\FunctionReturnTypeProvider::class;
        yield EventHandler\Str\Repeat\FunctionReturnTypeProvider::class;
        yield EventHandler\Str\Slice\FunctionReturnTypeProvider::class;
        yield EventHandler\Str\Splice\FunctionReturnTypeProvider::class;
        yield EventHandler\Str\Split\FunctionReturnTypeProvider::class;
        yield EventHandler\Str\Uppercase\FunctionReturnTypeProvider::class;

        // Psl\Iter hooks
        yield EventHandler\Type\Optional\FunctionReturnTypeProvider::class;
        yield EventHandler\Type\Shape\FunctionReturnTypeProvider::class;
    }
}

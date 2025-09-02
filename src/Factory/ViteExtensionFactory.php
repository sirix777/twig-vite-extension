<?php

declare(strict_types=1);

namespace Sirix\TwigViteExtension\Factory;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Sirix\TwigViteExtension\Twig\ViteExtension;
use Sirix\TwigViteExtension\Vite\AssetResolver;
use Sirix\TwigViteExtension\Vite\CssTagRenderer;
use Sirix\TwigViteExtension\Vite\ScriptTagRenderer;

class ViteExtensionFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): ViteExtension
    {
        $cssRenderer = $container->get(CssTagRenderer::class);
        $scriptRenderer = $container->get(ScriptTagRenderer::class);
        $assetResolver = $container->get(AssetResolver::class);

        return new ViteExtension(
            $cssRenderer,
            $scriptRenderer,
            $assetResolver,
        );
    }
}

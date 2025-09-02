<?php

declare(strict_types=1);

namespace Sirix\TwigViteExtension\Factory;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Sirix\TwigViteExtension\Vite\CssTagRenderer;
use Sirix\TwigViteExtension\Vite\ManifestProvider;
use Sirix\TwigViteExtension\Vite\ViteOptions;

class CssTagRendererFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): CssTagRenderer
    {
        $manifestProvider = $container->get(ManifestProvider::class);
        $viteOptions = $container->get(ViteOptions::class);

        return new CssTagRenderer(
            $manifestProvider,
            $viteOptions,
        );
    }
}

<?php

declare(strict_types=1);

namespace Sirix\TwigViteExtension\Factory;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Sirix\TwigViteExtension\Vite\AssetResolver;
use Sirix\TwigViteExtension\Vite\ManifestProvider;
use Sirix\TwigViteExtension\Vite\ViteOptions;

class AssetResolverFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): AssetResolver
    {
        $manifestProvider = $container->get(ManifestProvider::class);
        $viteOptions = $container->get(ViteOptions::class);

        return new AssetResolver(
            $manifestProvider,
            $viteOptions,
        );
    }
}

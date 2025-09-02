<?php

declare(strict_types=1);

namespace Sirix\TwigViteExtension\Factory;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Sirix\TwigViteExtension\Vite\ManifestProvider;
use Sirix\TwigViteExtension\Vite\ViteOptions;

class ManifestProviderFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): ManifestProvider
    {
        $viteOptions = $container->get(ViteOptions::class);

        return new ManifestProvider(
            $viteOptions,
        );
    }
}

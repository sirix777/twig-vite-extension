<?php

declare(strict_types=1);

namespace Sirix\TwigViteExtension;

use Sirix\Config\Factory\ValinorConfigFactory;
use Sirix\TwigViteExtension\Factory\AssetResolverFactory;
use Sirix\TwigViteExtension\Factory\CssTagRendererFactory;
use Sirix\TwigViteExtension\Factory\ManifestProviderFactory;
use Sirix\TwigViteExtension\Factory\ScriptTagRendererFactory;
use Sirix\TwigViteExtension\Factory\ViteExtensionFactory;
use Sirix\TwigViteExtension\Twig\ViteExtension;
use Sirix\TwigViteExtension\Vite\AssetResolver;
use Sirix\TwigViteExtension\Vite\CssTagRenderer;
use Sirix\TwigViteExtension\Vite\ManifestProvider;
use Sirix\TwigViteExtension\Vite\ScriptTagRenderer;
use Sirix\TwigViteExtension\Vite\ViteOptions;

/**
 * The configuration provider for the InertiaPsr15 module.
 *
 * @see https://docs.laminas.dev/laminas-component-installer/
 */
class ConfigProvider
{
    /**
     * Returns the configuration array.
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     *
     * @return array{
     *     dependencies: array{invokables: array<class-string, class-string>|array<empty, empty>,
     *     factories: array<class-string, array{class-string, string}|class-string>}
     *     }
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }

    /**
     * Returns the container dependencies.
     *
     * @return array{
     *     invokables: array<class-string, class-string>|array<empty, empty>,
     *     factories: array<class-string, array{class-string, string}|class-string>
     *         }
     */
    public function getDependencies(): array
    {
        return [
            'invokables' => [],
            'factories' => [
                AssetResolver::class => AssetResolverFactory::class,
                CssTagRenderer::class => CssTagRendererFactory::class,
                ManifestProvider::class => ManifestProviderFactory::class,
                ScriptTagRenderer::class => ScriptTagRendererFactory::class,
                ViteExtension::class => ViteExtensionFactory::class,
                ViteOptions::class => [ValinorConfigFactory::class, 'config.vite.options'],
            ],
        ];
    }
}

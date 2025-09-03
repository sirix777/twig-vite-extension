<?php

declare(strict_types=1);

namespace TwigViteExtensionTest\Util;

use Sirix\TwigViteExtension\Vite\ViteOptions;

final class TestViteOptionsFactory
{
    public static function dev(string $devServerUrl = 'http://localhost:5173', string $buildBase = 'public/build'): ViteOptions
    {
        return new ViteOptions(
            isDevMode: true,
            viteBuildDir: $buildBase,
            devServer: $devServerUrl,
            vitePublicBase: null,
        );
    }

    public static function prod(string $buildBase): ViteOptions
    {
        return new ViteOptions(
            isDevMode: false,
            viteBuildDir: $buildBase,
            devServer: 'http://localhost:5173',
            // For tests we expect full filesystem path in generated URLs
            vitePublicBase: $buildBase,
        );
    }

    public static function generic(
        bool $isDev,
        ?string $buildBase = null,
        ?string $devServerUrl = null,
        ?string $publicBase = null
    ): ViteOptions {
        return new ViteOptions(
            isDevMode: $isDev,
            viteBuildDir: $buildBase,
            devServer: $devServerUrl,
            vitePublicBase: $publicBase ?? $buildBase,
        );
    }
}

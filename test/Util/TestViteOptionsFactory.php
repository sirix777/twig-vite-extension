<?php

declare(strict_types=1);

namespace TwigViteExtensionTest\Util;

use Sirix\TwigViteExtension\Vite\ViteOptions;

final class TestViteOptionsFactory
{
    public static function dev(string $devServerUrl = 'http://localhost:5173', string $buildBase = 'public/build'): ViteOptions
    {
        return new ViteOptions(true, $buildBase, $devServerUrl);
    }

    public static function prod(string $buildBase): ViteOptions
    {
        return new ViteOptions(false, $buildBase);
    }

    public static function generic(bool $isDev, ?string $buildBase = null, ?string $devServerUrl = null): ViteOptions
    {
        return new ViteOptions($isDev, $buildBase, $devServerUrl);
    }
}

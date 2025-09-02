<?php

declare(strict_types=1);

namespace TwigViteExtensionTest\Vite;

use PHPUnit\Framework\TestCase;
use Sirix\TwigViteExtension\Vite\AssetResolver;
use Sirix\TwigViteExtension\Vite\ManifestProvider;
use Sirix\TwigViteExtension\Vite\ViteOptions;
use TwigViteExtensionTest\Util\TestViteOptionsFactory;

final class AssetResolverTest extends TestCase
{
    public function testResolveReturnsDevServerUrlInDevModeAndHandlesSlashes(): void
    {
        $options = TestViteOptionsFactory::dev('http://localhost:5173/');

        // Manifest shouldn't be used in dev mode; pass a real instance
        $manifest = new ManifestProvider(new ViteOptions(false));

        $resolver = new AssetResolver($manifest, $options);

        self::assertSame('http://localhost:5173/src/main.ts', $resolver->resolve('/src/main.ts'));
        self::assertSame('http://localhost:5173/src/main.ts', $resolver->resolve('src/main.ts'));

        $options2 = TestViteOptionsFactory::dev();
        $resolver2 = new AssetResolver($manifest, $options2);
        self::assertSame('http://localhost:5173/src/main.ts', $resolver2->resolve('/src/main.ts'));
    }

    public function testResolveReturnsBuiltFileFromManifestInProd(): void
    {
        $buildBase = __DIR__ . '/../Resource/build';
        $options = TestViteOptionsFactory::prod($buildBase);
        $manifest = new ManifestProvider($options);

        $resolver = new AssetResolver($manifest, $options);

        $entry = 'resources/js/app.ts';
        $result = $resolver->resolve($entry);

        self::assertSame($buildBase . '/assets/app-CfeQlW6r.js', $result);
    }

    public function testResolveFallsBackToOriginalPathWhenMissingInManifest(): void
    {
        $buildBase = __DIR__ . '/../Resource/build';
        $options = TestViteOptionsFactory::prod($buildBase);
        $manifest = new ManifestProvider($options);

        $resolver = new AssetResolver($manifest, $options);

        self::assertSame('images/logo.png', $resolver->resolve('images/logo.png'));
    }
}

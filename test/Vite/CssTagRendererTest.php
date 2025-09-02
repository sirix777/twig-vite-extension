<?php

declare(strict_types=1);

namespace TwigViteExtensionTest\Vite;

use PHPUnit\Framework\TestCase;
use Sirix\TwigViteExtension\Vite\CssTagRenderer;
use Sirix\TwigViteExtension\Vite\ManifestProvider;
use Sirix\TwigViteExtension\Vite\ViteOptions;
use TwigViteExtensionTest\Util\TestViteOptionsFactory;

final class CssTagRendererTest extends TestCase
{
    public function testRenderReturnsEmptyStringInDevMode(): void
    {
        $options = TestViteOptionsFactory::dev('http://localhost:5173/');
        $manifest = new ManifestProvider(new ViteOptions(false)); // not used in dev mode

        $renderer = new CssTagRenderer($manifest, $options);

        self::assertSame('', $renderer->render('resources/js/app.ts'));
    }

    public function testRenderReturnsLinkTagsForCssInProd(): void
    {
        $buildBase = __DIR__ . '/../Resource/build';
        $options = TestViteOptionsFactory::prod($buildBase);
        $manifest = new ManifestProvider($options);

        $renderer = new CssTagRenderer($manifest, $options);

        $html = $renderer->render('resources/js/app.ts');

        $expected = '<link rel="stylesheet" href="' . $buildBase . '/assets/app-DeVvsBrP.css">';
        self::assertSame($expected, $html);
    }

    public function testRenderReturnsEmptyStringWhenNoCssArray(): void
    {
        // Point to a build directory without a manifest entry for this file
        $buildBase = __DIR__ . '/../Resource/build';
        $options = TestViteOptionsFactory::prod($buildBase);
        $manifest = new ManifestProvider($options);

        $renderer = new CssTagRenderer($manifest, $options);

        // not present in manifest => empty string
        self::assertSame('', $renderer->render('resources/css/unknown.css'));
    }
}

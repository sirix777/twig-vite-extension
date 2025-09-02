<?php

declare(strict_types=1);

namespace TwigViteExtensionTest\Twig;

use PHPUnit\Framework\TestCase;
use Sirix\TwigViteExtension\Twig\ViteExtension;
use Sirix\TwigViteExtension\Vite\AssetResolver;
use Sirix\TwigViteExtension\Vite\CssTagRenderer;
use Sirix\TwigViteExtension\Vite\ManifestProvider;
use Sirix\TwigViteExtension\Vite\ScriptTagRenderer;
use Twig\Node\Node;
use Twig\TwigFunction;
use TwigViteExtensionTest\Util\TestViteOptionsFactory;

final class ViteExtensionTest extends TestCase
{
    public function testGetName(): void
    {
        $ext = $this->createRealExtensionForProd();
        self::assertSame('sirix.extension.vite', $ext->getName());
    }

    public function testTwigFunctionsAreRegisteredWithExpectedNamesAndSafety(): void
    {
        $ext = $this->createRealExtensionForProd();

        $functions = $ext->getFunctions();
        self::assertCount(3, $functions);

        $byName = [];
        foreach ($functions as $fn) {
            $byName[$fn->getName()] = $fn;
        }

        self::assertArrayHasKey('vite_entry_link_tags', $byName);
        self::assertArrayHasKey('vite_entry_script_tags', $byName);
        self::assertArrayHasKey('vite_asset', $byName);

        // Check is_safe options for html on link and script using TwigFunction::getSafe()
        $dummyArgs = new Node();
        $safeLink = $byName['vite_entry_link_tags']->getSafe($dummyArgs) ?? [];
        $safeScript = $byName['vite_entry_script_tags']->getSafe($dummyArgs) ?? [];
        self::assertContains('html', $safeLink);
        self::assertContains('html', $safeScript);

        // vite_asset should not be marked safe by default
        self::assertSame([], $byName['vite_asset']->getSafe($dummyArgs));
    }

    public function testTwigFunctionsDelegateToUnderlyingServicesUsingRealRenderers(): void
    {
        $ext = $this->createRealExtensionForProd();

        $functions = $ext->getFunctions();
        $byName = [];
        foreach ($functions as $fn) {
            $byName[$fn->getName()] = $fn;
        }

        $linkCallable = $byName['vite_entry_link_tags']->getCallable();
        $scriptCallable = $byName['vite_entry_script_tags']->getCallable();
        $assetCallable = $byName['vite_asset']->getCallable();

        $entry = 'resources/js/app.ts';
        $buildBase = __DIR__ . '/../Resource/build';

        self::assertIsCallable($linkCallable);
        self::assertIsCallable($scriptCallable);
        self::assertIsCallable($assetCallable);

        $expectedLink = '<link rel="stylesheet" href="' . $buildBase . '/assets/app-DeVvsBrP.css">';
        $expectedScript = '<script type="module" src="' . $buildBase . '/assets/app-CfeQlW6r.js"></script>';
        $expectedAsset = $buildBase . '/assets/app-CfeQlW6r.js';

        self::assertSame($expectedLink, $linkCallable($entry));
        self::assertSame($expectedScript, $scriptCallable($entry));
        self::assertSame($expectedAsset, $assetCallable($entry));

        // Non-manifest asset should fall back to original path
        self::assertSame('images/missing.png', $assetCallable('images/missing.png'));
    }

    private function createRealExtensionForProd(): ViteExtension
    {
        $viteOptions = TestViteOptionsFactory::prod(__DIR__ . '/../Resource/build');
        $manifest = new ManifestProvider($viteOptions);

        return new ViteExtension(
            new CssTagRenderer($manifest, $viteOptions),
            new ScriptTagRenderer($manifest, $viteOptions),
            new AssetResolver($manifest, $viteOptions),
        );
    }
}

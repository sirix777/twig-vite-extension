<?php

declare(strict_types=1);

namespace TwigViteExtensionTest\Vite;

use PHPUnit\Framework\TestCase;
use Sirix\TwigViteExtension\Vite\ManifestProvider;
use Sirix\TwigViteExtension\Vite\ScriptTagRenderer;
use Sirix\TwigViteExtension\Vite\ViteOptions;
use TwigViteExtensionTest\Util\TestViteOptionsFactory;

use function file_put_contents;
use function implode;
use function json_encode;
use function mkdir;
use function sys_get_temp_dir;
use function uniqid;

final class ScriptTagRendererTest extends TestCase
{
    public function testRenderDevModeOutputsViteClientAndEntryModule(): void
    {
        $options = TestViteOptionsFactory::dev('http://localhost:5173/');
        $manifest = new ManifestProvider(new ViteOptions(false)); // not used in dev mode

        $renderer = new ScriptTagRenderer($manifest, $options);

        $html = $renderer->render('resources/js/app.ts');

        $expected = implode("\n", [
            '<script type="module" src="http://localhost:5173/@vite/client"></script>',
            '<script type="module" src="http://localhost:5173/resources/js/app.ts"></script>',
        ]);

        self::assertSame($expected, $html);
    }

    public function testRenderProdModeRendersEntryScriptTag(): void
    {
        $buildBase = __DIR__ . '/../Resource/build';
        $options = TestViteOptionsFactory::prod($buildBase);
        $manifest = new ManifestProvider($options);

        $renderer = new ScriptTagRenderer($manifest, $options);

        $html = $renderer->render('resources/js/app.ts');

        $expected = '<script type="module" src="' . $buildBase . '/assets/app-CfeQlW6r.js"></script>';
        self::assertSame($expected, $html);
    }

    public function testRenderProdModeAddsModulePreloadForImportsWhenPresent(): void
    {
        // Prepare a temporary manifest with imports
        $tempDir = sys_get_temp_dir() . '/twig-vite-extension-script-imports-' . uniqid();
        $viteDir = $tempDir . '/.vite';
        mkdir($viteDir, 0o777, true);
        $manifest = [
            'main.ts' => [
                'file' => 'assets/main-ABC.js',
                'imports' => ['vendor.ts'],
            ],
            'vendor.ts' => [
                'file' => 'assets/vendor-XYZ.js',
            ],
        ];
        file_put_contents($viteDir . '/manifest.json', json_encode($manifest));

        $options = TestViteOptionsFactory::prod($tempDir);
        $provider = new ManifestProvider($options);
        $renderer = new ScriptTagRenderer($provider, $options);

        $html = $renderer->render('main.ts');

        $expected = implode("\n", [
            '<script type="module" src="' . $tempDir . '/assets/main-ABC.js"></script>',
            '<link rel="modulepreload" href="' . $tempDir . '/assets/vendor-XYZ.js">',
        ]);

        self::assertSame($expected, $html);
    }

    public function testRenderProdModeHandlesMissingEntryGracefully(): void
    {
        $buildBase = __DIR__ . '/../Resource/build';
        $options = TestViteOptionsFactory::prod($buildBase);
        $manifest = new ManifestProvider($options);

        $renderer = new ScriptTagRenderer($manifest, $options);

        // unknown entry -> empty string
        self::assertSame('', $renderer->render('unknown/entry.js'));
    }
}

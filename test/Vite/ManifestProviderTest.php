<?php

declare(strict_types=1);

namespace TwigViteExtensionTest\Vite;

use PHPUnit\Framework\TestCase;
use Sirix\TwigViteExtension\Vite\ManifestProvider;
use TwigViteExtensionTest\Util\TestViteOptionsFactory;

use function file_put_contents;
use function mkdir;
use function sys_get_temp_dir;
use function uniqid;

final class ManifestProviderTest extends TestCase
{
    public function testGetEntryReturnsNullInDevMode(): void
    {
        $options = TestViteOptionsFactory::dev('http://localhost:5173/');
        $provider = new ManifestProvider($options);

        self::assertNull($provider->getEntry('resources/js/app.ts'));
    }

    public function testGetEntryReturnsManifestEntryInProd(): void
    {
        $buildBase = __DIR__ . '/../Resource/build';
        $options = TestViteOptionsFactory::prod($buildBase);
        $provider = new ManifestProvider($options);

        $entry = $provider->getEntry('resources/js/app.ts');

        self::assertIsArray($entry);
        self::assertSame('assets/app-CfeQlW6r.js', $entry['file'] ?? null);
        self::assertSame(['assets/app-DeVvsBrP.css'], $entry['css'] ?? null);
    }

    public function testGetEntryReturnsNullWhenEntryMissing(): void
    {
        $buildBase = __DIR__ . '/../Resource/build';
        $options = TestViteOptionsFactory::prod($buildBase);
        $provider = new ManifestProvider($options);

        self::assertNull($provider->getEntry('unknown/entry.js'));
    }

    public function testNoErrorWhenManifestDoesNotExist(): void
    {
        // Point to a directory that has no .vite/manifest.json
        $tempDir = sys_get_temp_dir() . '/twig-vite-extension-missing-' . uniqid();
        // Ensure directory exists but without .vite/manifest.json
        mkdir($tempDir, 0o777, true);

        $options = TestViteOptionsFactory::prod($tempDir);
        $provider = new ManifestProvider($options);

        // Should behave as empty manifest
        self::assertNull($provider->getEntry('resources/js/app.ts'));
    }

    public function testInvalidJsonLeadsToEmptyManifestSafely(): void
    {
        $tempDir = sys_get_temp_dir() . '/twig-vite-extension-invalid-' . uniqid();
        $viteDir = $tempDir . '/.vite';
        mkdir($viteDir, 0o777, true);
        file_put_contents($viteDir . '/manifest.json', '{invalid json');

        $options = TestViteOptionsFactory::prod($tempDir);
        $provider = new ManifestProvider($options);

        // json_decode will return null, cast to array gives empty array, so no entries
        self::assertNull($provider->getEntry('resources/js/app.ts'));
    }
}

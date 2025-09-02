<?php

declare(strict_types=1);

namespace Sirix\TwigViteExtension\Vite;

use function file_exists;
use function file_get_contents;
use function json_decode;

final class ManifestProvider
{
    private const MANIFEST_FILE_URI = '/.vite/manifest.json';

    /**
     * @var array<string, array<string, mixed>>
     */
    private array $manifest = [];

    public function __construct(private readonly ViteOptions $viteOptions)
    {
        if (! $this->viteOptions->isDevMode) {
            $outDir = $this->viteOptions->viteOutDir ?? '';
            if (file_exists($outDir . self::MANIFEST_FILE_URI)) {
                $content = file_get_contents($outDir . self::MANIFEST_FILE_URI) ?: '';
                $this->manifest = '' !== $content ? (array) json_decode($content, true) : [];
            }
        }
    }

    /**
     * @return null|array<string, mixed>
     */
    public function getEntry(string $entryPoint): ?array
    {
        return $this->manifest[$entryPoint] ?? null;
    }
}

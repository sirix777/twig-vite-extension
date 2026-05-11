<?php

declare(strict_types=1);

namespace Sirix\TwigViteExtension\Vite;

use function ltrim;
use function rtrim;

final readonly class AssetResolver
{
    public function __construct(private ManifestProvider $manifestProvider, private ViteOptions $viteOptions) {}

    public function resolve(string $path): string
    {
        if ($this->viteOptions->isDevMode) {
            $devServer = $this->viteOptions->devServer ?? '';

            return rtrim($devServer, '/') . '/' . ltrim($path, '/');
        }

        $entry = $this->manifestProvider->getEntry($path);

        return $entry && isset($entry['file'])
            ? $this->viteOptions->publicBase() . '/' . ltrim((string) $entry['file'], '/')
            : $path;
    }
}

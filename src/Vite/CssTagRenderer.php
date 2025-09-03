<?php

declare(strict_types=1);

namespace Sirix\TwigViteExtension\Vite;

use function implode;
use function is_array;
use function ltrim;
use function sprintf;

final class CssTagRenderer
{
    public function __construct(private readonly ManifestProvider $manifestProvider, private readonly ViteOptions $viteOptions) {}

    public function render(string $entryPoint): string
    {
        if ($this->viteOptions->isDevMode) {
            return '';
        }

        $entry = $this->manifestProvider->getEntry($entryPoint);
        if (! $entry || ! isset($entry['css']) || ! is_array($entry['css'])) {
            return '';
        }

        $tags = [];
        foreach ($entry['css'] as $cssFile) {
            $tags[] = sprintf(
                '<link rel="stylesheet" href="%s">',
                $this->viteOptions->publicBase() . '/' . ltrim((string) $cssFile, '/')
            );
        }

        return implode("\n", $tags);
    }
}

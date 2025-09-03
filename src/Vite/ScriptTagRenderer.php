<?php

declare(strict_types=1);

namespace Sirix\TwigViteExtension\Vite;

use function implode;
use function is_array;
use function ltrim;
use function rtrim;
use function sprintf;

final class ScriptTagRenderer
{
    public function __construct(private readonly ManifestProvider $manifestProvider, private readonly ViteOptions $viteOptions) {}

    public function render(string $entryPoint): string
    {
        if ($this->viteOptions->isDevMode) {
            $base = rtrim($this->viteOptions->devServer ?? '', '/');

            return implode("\n", [
                sprintf('<script type="module" src="%s/@vite/client"></script>', $base),
                sprintf('<script type="module" src="%s/%s"></script>', $base, ltrim($entryPoint, '/')),
            ]);
        }

        $tags = [];
        $entry = $this->manifestProvider->getEntry($entryPoint);

        if ($entry && isset($entry['file'])) {
            $tags[] = sprintf(
                '<script type="module" src="%s"></script>',
                $this->viteOptions->publicBase() . '/' . ltrim((string) $entry['file'], '/')
            );
        }

        if ($entry && isset($entry['imports']) && is_array($entry['imports'])) {
            foreach ($entry['imports'] as $import) {
                $importEntry = $this->manifestProvider->getEntry((string) $import);
                if ($importEntry && isset($importEntry['file'])) {
                    $tags[] = sprintf(
                        '<link rel="modulepreload" href="%s">',
                        $this->viteOptions->publicBase() . '/' . ltrim((string) $importEntry['file'], '/')
                    );
                }
            }
        }

        return implode("\n", $tags);
    }
}

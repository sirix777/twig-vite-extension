<?php

declare(strict_types=1);

namespace Sirix\TwigViteExtension\Vite;

final class ViteOptions
{
    public function __construct(
        public readonly bool $isDevMode = false,
        public readonly ?string $viteBuildDir = 'public/build',
        public readonly ?string $devServer = 'http://localhost:5173',
        public readonly ?string $vitePublicBase = 'build',
    ) {}

    public function publicBase(): string
    {
        return $this->vitePublicBase ?? ($this->viteBuildDir ?? '');
    }
}

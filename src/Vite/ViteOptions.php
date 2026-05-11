<?php

declare(strict_types=1);

namespace Sirix\TwigViteExtension\Vite;

final readonly class ViteOptions
{
    public function __construct(
        public bool $isDevMode = false,
        public ?string $viteBuildDir = 'public/build',
        public ?string $devServer = 'http://localhost:5173',
        public ?string $vitePublicBase = 'build',
    ) {}

    public function publicBase(): string
    {
        return $this->vitePublicBase ?? ($this->viteBuildDir ?? '');
    }
}

<?php

declare(strict_types=1);

namespace Sirix\TwigViteExtension\Twig;

use Sirix\TwigViteExtension\Vite\AssetResolver;
use Sirix\TwigViteExtension\Vite\CssTagRenderer;
use Sirix\TwigViteExtension\Vite\ScriptTagRenderer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ViteExtension extends AbstractExtension
{
    public function __construct(
        private readonly CssTagRenderer $cssRenderer,
        private readonly ScriptTagRenderer $scriptRenderer,
        private readonly AssetResolver $assetResolver,
    ) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('vite_entry_link_tags', $this->cssRenderer->render(...), ['is_safe' => ['html']]),
            new TwigFunction('vite_entry_script_tags', $this->scriptRenderer->render(...), ['is_safe' => ['html']]),
            new TwigFunction('vite_asset', $this->assetResolver->resolve(...)),
        ];
    }

    public function getName(): string
    {
        return 'sirix.extension.vite';
    }
}

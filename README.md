# Twig Vite Extension

Inject your Vite entry points into Twig templates with ease.

This library provides a Twig extension that:
- In development: injects @vite/client and your entry as module scripts, and resolves asset URLs to the dev server.
- In production: reads Vite's manifest.json and renders the correct built asset tags and URLs.

It can be used:
- With Mezzio (Laminas) via the provided ConfigProvider and factories.
- Standalone, without Mezzio, by manually wiring the services.


## Installation

Composer:

```bash
composer require sirix/twig-vite-extension
```

Requirements: PHP 8.1+.


## Vite configuration (required)

Ensure Vite generates a manifest and places build output where your app can serve it.

Example vite.config.ts:

```ts
import { defineConfig } from 'vite'
import path from 'path'

export default defineConfig({
  build: {
    manifest: true,
    outDir: 'public/build', // must match vite_out_dir in your PHP config
    assetsDir: 'assets',
    rollupOptions: {
      input: {
        app: path.resolve(__dirname, 'resources/js/app.ts'),
      },
    },
  },
  server: {
    // Your dev server URL should match dev_server in your PHP config
    port: 5173,
    strictPort: true,
  },
})
```

Notes:
- In production, this library looks for `{vite_out_dir}/.vite/manifest.json` (e.g., `public/build/.vite/manifest.json`).
- The keys in manifest must match the entry names you pass in Twig (e.g., `resources/js/app.ts`).


## Twig functions provided

- `vite_entry_script_tags(entry)` → string
  - Dev: outputs two <script type="module"> tags (@vite/client and your entry).
  - Prod: outputs a single <script type="module"> tag for the built file and <link rel="modulepreload"> tags for imports.
  - Output is marked safe for HTML.

- `vite_entry_link_tags(entry)` → string
  - Dev: returns empty string (CSS is injected by Vite in dev).
  - Prod: outputs one or multiple <link rel="stylesheet"> tags for CSS emitted by the entry.
  - Output is marked safe for HTML.


## Configuration options

Options map to `Sirix\TwigViteExtension\Vite\ViteOptions`:

- `is_dev_mode` (bool) — default: `false`
- `vite_out_dir` (string|null) — default: `public/build`
- `dev_server` (string|null) — default: `http://localhost:5173`

In production (`is_dev_mode = false`), the library loads the manifest once from `{vite_out_dir}/.vite/manifest.json`.


## Usage with Mezzio (Laminas)

This package ships a `ConfigProvider` that registers all required factories.

1) Enable configuration (if you use laminas-component-installer, this is automatic). Otherwise, ensure your `composer.json` has:

```json
{
  "extra": {
    "laminas": {
      "config-provider": "Sirix\\TwigViteExtension\\ConfigProvider"
    }
  }
}
```

2) Provide options in a config file, e.g. `config/autoload/vite.config.global.php`:

```php
<?php
return [
    'vite' => [
        'options' => [
            'is_dev_mode' => false,                      // set true in local/dev
            'vite_out_dir' => 'public/build',            // location of your build output
            'dev_server' => 'http://localhost:5173',     // your vite dev server base URL
        ],
    ],
];
```

3) Register the Twig extension

Depending on how you wire Twig in Mezzio, you have two common options:

- If you use a Twig factory that honors a `twig.extensions` config key (e.g., mezzio-twigrenderer or a custom factory), add this:

```php
<?php
return [
    'twig' => [
        'extensions' => [
            Sirix\TwigViteExtension\Twig\ViteExtension::class,
        ],
    ],
];
```

4) Use in Twig templates

```twig
{# HTML head #}
{{ vite_entry_link_tags('resources/js/app.ts') }}

{# Before closing body #}
{{ vite_entry_script_tags('resources/js/app.ts') }}

```

Notes:
- You do not need `|raw` for the two tag-generating functions; they are marked HTML-safe.


## Usage without Mezzio (standalone Twig)

You can wire the extension manually:

```php
use Sirix\TwigViteExtension\Twig\ViteExtension;
use Sirix\TwigViteExtension\Vite\ViteOptions;
use Sirix\TwigViteExtension\Vite\ManifestProvider;
use Sirix\TwigViteExtension\Vite\CssTagRenderer;
use Sirix\TwigViteExtension\Vite\ScriptTagRenderer;
use Twig\Environment as TwigEnvironment;
use Twig\Loader\FilesystemLoader;

// 1) Configure
$isDev = ($_ENV['APP_ENV'] ?? 'prod') !== 'prod';
$options = new ViteOptions(
    isDevMode: $isDev,
    viteOutDir: 'public/build',            // same as Vite outDir
    devServer: 'http://localhost:5173',    // your Vite dev server
);

// 2) Build the services
$manifest = new ManifestProvider($options);
$css = new CssTagRenderer($manifest, $options);
$js = new ScriptTagRenderer($manifest, $options);
$viteExt = new ViteExtension($css, $js, new \Sirix\TwigViteExtension\Vite\AssetResolver($manifest, $options));

// 3) Register in Twig
$twig = new TwigEnvironment(new FilesystemLoader(__DIR__ . '/templates'));
$twig->addExtension($viteExt);

// 4) Render templates that call the functions
echo $twig->render('home.html.twig');
```

Twig template usage is the same as in the Mezzio section.


## Behavior details: dev vs prod

- Dev mode (`is_dev_mode = true`):
  - `vite_entry_script_tags(entry)` prints two module scripts: `@vite/client` and your entry from `dev_server`.
  - `vite_entry_link_tags(entry)` prints nothing (Vite handles CSS via HMR).

- Prod mode (`is_dev_mode = false`):
  - The library loads `{vite_out_dir}/.vite/manifest.json` once.
  - `vite_entry_script_tags(entry)` prints a module script to the built JS and modulepreload links for its imports.
  - `vite_entry_link_tags(entry)` prints stylesheet links for CSS emitted by the entry.


## Troubleshooting

- If nothing renders in prod, check that `manifest: true` is set and that the file exists at `{vite_out_dir}/.vite/manifest.json`.
- Ensure the entry name in Twig matches the manifest key (often your source path, e.g., `resources/js/app.ts`).
- In dev, verify `dev_server` matches where Vite is running (including protocol and port).


## Testing and quality

This repo includes PHPUnit tests.

Run the test suite:

```bash
composer test
```

Static analysis and code style tools are available via:

```bash
composer check
```


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

Note on public vs filesystem paths:
- Set `vite_build_dir` to where Vite writes the build on disk (e.g., `public/build`).
- Set `vite_public_base` to how that directory is exposed publicly (e.g., `build` so URLs become `/build/...`).
- If you omit `vite_public_base`, the library falls back to using `vite_build_dir` in URLs.


## Vite configuration (required)

Ensure Vite generates a manifest and places build output where your app can serve it.

Example vite.config.ts:

```ts
import { defineConfig } from 'vite'
import path from 'path'

export default defineConfig({
  build: {
    manifest: true,
    outDir: 'public/build', // must match vite_build_dir in your PHP config
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
- In production, this library looks for `{vite_build_dir}/.vite/manifest.json` (e.g., `public/build/.vite/manifest.json`).
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

- `vite_asset(path)` → string
  - Dev: returns `${dev_server}/${path}` (slashes handled), so assets are served from the Vite dev server.
  - Prod: resolves `path` via the manifest and returns a URL based on `vite_public_base` when set, otherwise `vite_build_dir`.
  - If `path` is not found in manifest (e.g., a static image not processed by Vite), it returns the original `path` unchanged.

### Examples

Twig:
```
{# Entry points #}
{{ vite_entry_script_tags('resources/js/app.ts') }}
{{ vite_entry_link_tags('resources/js/app.ts') }}

{# Referencing a processed asset from manifest #}
<img src="{{ vite_asset('resources/js/app.ts') }}" alt="app js as URL">

{# Referencing a static file that Vite did not fingerprint #}
<img src="{{ vite_asset('images/logo.svg') }}" alt="Logo">
```

Notes:
- In production, if you want URLs like `/build/...` (without `public/`), configure `vite_public_base: 'build'`.
- If you set `vite_public_base` to the same value as `vite_build_dir`, URLs will look like filesystem paths (used by our tests).


## Configuration options

Options map to `Sirix\TwigViteExtension\Vite\ViteOptions`:

- `is_dev_mode` (bool) — default: `false`
- `vite_build_dir` (string|null) — default: `public/build` (filesystem directory where Vite writes the build)
- `vite_public_base` (string|null) — default: `build` (public URL base used when rendering tags/URLs; if null, falls back to `vite_build_dir`)
- `dev_server` (string|null) — default: `http://localhost:5173`

Behavior:
- In production (`is_dev_mode = false`), the library loads the manifest once from `{vite_build_dir}/.vite/manifest.json`.
- In production, public URLs are built using `vite_public_base` when set, otherwise `vite_build_dir`.


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
            'vite_build_dir' => 'public/build',          // location of your build output (filesystem)
            'vite_public_base' => 'build',               // public base used for URLs, e.g. "/build"
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
    viteBuildDir: 'public/build',          // filesystem path (same as Vite outDir)
    devServer: 'http://localhost:5173',    // your Vite dev server
    // Use a shallow public base for URLs (omit "public/")
    // If you want full filesystem-like URLs in prod, set vitePublicBase to the same value as viteBuildDir
    vitePublicBase: 'build',
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

### Using `vite_asset` in templates

- Dev example: `{{ vite_asset('images/logo.svg') }}` → `http://localhost:5173/images/logo.svg`
- Prod example: with `vite_public_base: 'build'`, `{{ vite_asset('images/logo.svg') }}` → `/build/images/logo.svg` if present in manifest; otherwise returns `images/logo.svg`.
- To link to the JS file URL itself (rare), you may also pass the entry path: `{{ vite_asset('resources/js/app.ts') }}` which resolves to the built file URL in prod.


## Behavior details: dev vs prod

- Dev mode (`is_dev_mode = true`):
  - `vite_entry_script_tags(entry)` prints two module scripts: `@vite/client` and your entry from `dev_server`.
  - `vite_entry_link_tags(entry)` prints nothing (Vite handles CSS via HMR).

- Prod mode (`is_dev_mode = false`):
  - The library loads `{vite_build_dir}/.vite/manifest.json` once.
  - Public URLs are rendered using `vite_public_base` if provided (e.g., `/build/assets/...`), otherwise using `vite_build_dir`.
  - `vite_entry_script_tags(entry)` prints a module script to the built JS and modulepreload links for its imports.
  - `vite_entry_link_tags(entry)` prints stylesheet links for CSS emitted by the entry.


## Troubleshooting

- If nothing renders in prod, check that `manifest: true` is set and that the file exists at `{vite_build_dir}/.vite/manifest.json`.
- Ensure the entry name in Twig matches the manifest key (often your source path, e.g., `resources/js/app.ts`).
- In dev, verify `dev_server` matches where Vite is running (including protocol and port).
- If your production HTML shows URLs like `public/build/...` and you want `/build/...` instead, set `vite_public_base` to `build` (or your desired public base).
- `vite_asset` returns the original path if it’s missing in manifest. If you expect a fingerprinted URL, ensure the asset is imported somewhere so Vite processes it or add it to the manifest.


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


# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.0] - Unreleased

### Changed

- Supported PHP window is now 8.3–8.6 (PHP 8.2 is no longer supported, PHP 8.6 added).
- CI runs the full check suite on PHP 8.3, 8.4, 8.5 and 8.6.
- PHPUnit (dev) raised to `^12.5`.
- Rector level set raised to `UP_TO_PHP_83`; PHP CS Fixer migration ruleset raised to `@PHP8x3Migration`.
- Class constant in `ManifestProvider` now uses a typed declaration. Code style brought in line with the current PHP CS Fixer ruleset (no behavior changes).

### Removed

- Unused dev dependency `mezzio/mezzio-template` (its 2.x line does not install on PHP 8.6).

## [1.0.1] - 2026-05-11
### Added
- Add support for PHP 8.5.
- Introduce `bamarni/composer-bin-plugin` for isolated development tool management.

### Changed
- Bump minimum PHP version to 8.2.
- Upgrade `sirix/sirix-config` dependency to `^3.0`.
- Refactor Vite-related classes (`AssetResolver`, `CssTagRenderer`, `ScriptTagRenderer`, `ViteOptions`) to use PHP 8.2 `readonly` classes.
- Update PHP-CS-Fixer and Rector configurations for PHP 8.2 migration.
- Modernize `composer.json` scripts and tools management.

### Removed
- Drop support for PHP 8.1.

## [1.0.0] - 2025-09-03
- Add initial public README with installation, configuration, and usage docs.
- Provide Twig functions: `vite_entry_script_tags()`, `vite_entry_link_tags()` and `vite_asset` for dev/prod.
- Add Mezzio (Laminas) integration via `ConfigProvider` and factories.
- Implement Vite manifest loading and asset resolution.
- Add renderers for JS and CSS tags in production build.
- Provide comprehensive PHPUnit tests and tooling (PHP-CS-Fixer, PHPStan, Rector).

## [0.1.0] - 2025-09-02
- Initial release of Twig Vite Extension.

# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.1] - 11/05/2026
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

## [1.0.0] - 03/09/2025
- Add initial public README with installation, configuration, and usage docs.
- Provide Twig functions: `vite_entry_script_tags()`, `vite_entry_link_tags()` and `vite_asset` for dev/prod.
- Add Mezzio (Laminas) integration via `ConfigProvider` and factories.
- Implement Vite manifest loading and asset resolution.
- Add renderers for JS and CSS tags in production build.
- Provide comprehensive PHPUnit tests and tooling (PHP-CS-Fixer, PHPStan, Rector).

## [0.1.0] - 02/09/2025
- Initial release of Twig Vite Extension.

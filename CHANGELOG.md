# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
- Add initial public README with installation, configuration, and usage docs.
- Provide Twig functions: `vite_entry_script_tags()` and `vite_entry_link_tags()` for dev/prod.
- Add Mezzio (Laminas) integration via `ConfigProvider` and factories.
- Implement Vite manifest loading and asset resolution.
- Add renderers for JS and CSS tags in production build.
- Provide comprehensive PHPUnit tests and tooling (PHP-CS-Fixer, PHPStan, Rector).

## [0.1.0] - 02/09/2025
- Initial release of Twig Vite Extension.

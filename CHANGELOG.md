# Changelog

All notable changes to the BladePDF driver for Spatie Laravel PDF are documented here.

## 2.0.0

- Require `bladepdf/laravel:^2.0` while preserving `spatie/laravel-pdf:^2.10`.
- Preserve the `bladepdf` driver name, public Spatie calls, option mapping, and readiness behavior.
- Move driver test doubles to the core `RenderClient` and immutable `RenderRequest` contracts.
- Add an integration test proving Spatie HTML uses the Laravel-configured core CSS and image asset pipeline.

Driver 1.x is compatible with `bladepdf/laravel:^1.0`; driver 2.x is compatible with `bladepdf/laravel:^2.0`.

# Design: Laravel 12 & 13 Support

**Date:** 2026-06-23
**Status:** Approved

## Goal

Extend `vntrungld/prometheus-exporter-opcache-collector` to officially
support Laravel 13, while retaining support for all existing versions
(Laravel 6–12). Laravel 12 is already declared and tested in CI, so it
requires only a verification pass; the substantive work targets Laravel 13.

## Background

- Laravel 13 was released March 17, 2026 and requires PHP 8.3–8.5. It is a
  deliberately minimal-breaking-change release.
- The package's source (`src/`) only depends on PHP's `opcache_get_status()`
  and `illuminate/support`, so no application code changes are expected.
- **Dependency chain:** this package requires `vntrungld/prometheus-exporter`.
  The Laravel 13–supporting release of that parent package ships as a **new
  major version**. The parent has already been updated and published
  separately; this spec assumes it resolves.

## Changes

### 1. `composer.json`

`require`:
- Parent package: widen `"vntrungld/prometheus-exporter": "^1.1"` →
  `"^1.1|^2.0"`. This lets Composer pick the 1.x line for older Laravel and
  the 2.x line for Laravel 13.
- Framework: add Laravel 13 to
  `"illuminate/support": "^6.0|^7.0|^8.0|^9.0|^10.0|^11.0|^12.0|^13.0"`.
- PHP: **no change.** The existing `"^7.2|^8.0"` already covers PHP 8.3–8.5;
  Composer enforces Laravel 13's own PHP floor per resolution.

`require-dev`:
- Testbench: add `^11.0` (Laravel 13) →
  `"^4.0|^5.0|^6.0|^7.0|^8.0|^9.0|^10.0|^11.0"`.
- PHPUnit: add `^12.0` (testbench 11 requires PHPUnit 11.5+/12) →
  `"^9.0|^10.0|^11.0|^12.0"`.

### 2. `.github/workflows/tests.yml`

Append a Laravel 13 block to the matrix, matching the existing
two-rows-per-version convention:

```yaml
# Laravel 13
- php: '8.3'
  laravel: '13.*'
  testbench: '11.*'
  phpunit: '12.*'
- php: '8.4'
  laravel: '13.*'
  testbench: '11.*'
  phpunit: '12.*'
```

All existing legacy rows (Laravel 6–12) remain unchanged.

### 3. `readme.md`

Update any supported-version / compatibility references to include Laravel 13.

## Verification

- Run `composer update --prefer-dist` constrained to Laravel 13 and confirm
  `vntrungld/prometheus-exporter ^2.0` resolves.
- Run `vendor/bin/phpunit` against the Laravel 13 dependency set and confirm
  green.
- Confirm the existing Laravel 6–12 matrix rows still pass.

## Out of Scope

- No changes to `src/` (unless tests reveal a genuine incompatibility).
- No changes to the parent `vntrungld/prometheus-exporter` package — that work
  is handled separately and is a prerequisite, not part of this change.

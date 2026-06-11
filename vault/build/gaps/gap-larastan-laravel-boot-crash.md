---
type: gap
severity: high
category: architecture
status: resolved
resolved: 2026-06-11
domain: foundation
color: "#F97316"
discovered: 2026-06-11
discovered-in: foundation.scaffold
---

# Larastan crashes silently when analysing Laravel framework classes

## Context
During `foundation.scaffold` build, the static-analysis quality gate (`vendor/bin/phpstan analyse` with Larastan) could not be run. Environment: PHP 8.4.20 (ARM64/Apple Silicon), phpstan 2.2.2, larastan 3.x, Laravel 13.15.

## Problem
- `phpstan analyse` on plain PHP files works correctly (renders pass/fail JSON, exit 0/1 as expected).
- `phpstan analyse` on any file that autoloads Laravel/Illuminate classes (e.g. `app/Models/Admin.php` which extends `Illuminate\Foundation\Auth\User`) **exits 1 with zero stdout/stderr** — no error list, no crash message.
- Reproduced across: with/without the Larastan extension, single-process (`parallel.maximumNumberOfProcesses: 1`), `--debug`, memory_limit up to 8G, `opcache.jit=disable`, `pcre.jit=0`, all built-in error formats (raw/json/table/checkstyle), and sandbox disabled. All produce 0 bytes, exit 1.
- Not memory (8G), not opcache/JIT, not PCRE JIT, not the output formatter (renders errors fine for non-Laravel files).

## Impact
The Larastan quality gate in the Definition of Done cannot be satisfied for ANY module until resolved. Pint + Pest + migrations are unaffected and green. `foundation.scaffold` is otherwise functionally complete (Pest 10/10, Pint clean, ULID migrations valid via sqlite).

## Proposed Solution
Most likely cause: Larastan boots the Laravel kernel (`bootstrap/app.php`) during analysis; a service provider (Horizon / Reverb / Pulse / Telescope / Sentry) fatals in the phpstan boot context, killing the process before output. Investigate:
1. Run `php -d display_errors=1 -d log_errors=1 -d error_log=/tmp/stan-fatal.log vendor/bin/phpstan analyse app/Models/Admin.php` and inspect the fatal log.
2. Add a phpstan bootstrap file (`bootstrapFiles:` in phpstan.neon) that sets a minimal env / disables crashing providers during analysis.
3. Try pinning/bumping larastan (3.x) and phpstan, or excluding dev-only providers (Telescope) from the analysis boot.
4. Confirm `APP_KEY` and a parseable env are visible to the phpstan process.

Until fixed, gate modules on Pint + Pest + migrations; treat Larastan as deferred.

## Resolution

Resolved 2026-06-11 via [[build/decisions/decision-2026-06-11-static-analysis-without-larastan]]: the static-analysis gate runs **plain PHPStan level 5** (no Larastan include) plus explicit `@property` docblocks on models. Root cause narrowed to the Larastan extension load itself crashing on this stack — earlier "crashes" on `/tmp` configs were a separate red herring (relative paths in a neon located in `/tmp` not resolving, error swallowed by the JSON output wrapper). Scaffold now passes PHPStan (0 errors) + Pint + Pest 10/10. `larastan/larastan` remains a dev dependency, un-wired; re-enable when it loads cleanly.

## Related
- [[build/decisions/decision-2026-06-11-static-analysis-without-larastan]]
- [[domains/foundation/laravel-scaffold]]
- [[architecture/ci-cd]]
- [[architecture/patterns/testing-pattern]]

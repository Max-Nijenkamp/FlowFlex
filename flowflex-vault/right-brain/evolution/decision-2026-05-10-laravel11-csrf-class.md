---
type: adr
date: 2026-05-10
status: decided
color: "#F97316"
---

# Decision: Laravel 11 uses `PreventRequestForgery` in web middleware group (not `VerifyCsrfToken`)

## Context

`InviteAcceptanceTest` POST tests were returning 419 even with `$this->withoutMiddleware(VerifyCsrfToken::class)` in `beforeEach`. Investigation revealed that Laravel 11's web middleware group uses `Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class`, not `VerifyCsrfToken`.

Both classes exist in the framework (`PreventRequestForgery.php` and `VerifyCsrfToken.php` both extend `PreventRequestForgery` base), but only `PreventRequestForgery` is in the default web group as of Laravel 11.

## Options Considered

1. **Exclude `VerifyCsrfToken`** — had no effect; wrong class.
2. **Exclude `PreventRequestForgery`** — correct; 419s resolved.
3. **`$this->withoutMiddleware()` with no args** — disables all middleware, too blunt.

## Decision

In feature tests that POST to web routes without a CSRF token, exclude:
```php
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;

$this->withoutMiddleware(PreventRequestForgery::class);
```

## Consequences

- This is the correct class to exclude in all Laravel 11 Pest/PHPUnit feature tests
- `VerifyCsrfToken` still exists as an alias/backward-compat class but is NOT in the web group
- Document this in `TestCase.php` or test helpers if needed across multiple test files

## Related Left Brain

N/A — this is a Laravel internals finding, not a spec change.

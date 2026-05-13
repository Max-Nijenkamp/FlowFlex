---
type: adr
date: 2026-05-13
status: decided
color: "#F97316"
slug: filament-v5
---

# ADR: Use Filament 5 (not Filament 3)

## Context

The original vault specs reference "Filament 5" in the tech stack description, but the install command in the build instructions specified `filament/filament:"^3.3"`. At build time, `composer require filament/filament:"^3.3"` failed because Filament 3 does not support Laravel 13. Running `composer require filament/filament` without a version constraint resolved to Filament v5.6.3, which does support Laravel 13.

## Options Considered

1. Downgrade Laravel to 12.x to use Filament 3 — rejected, as Laravel 13 is the specified version
2. Use Filament 5 (latest stable) — accepted

## Decision

Use **Filament 5.6.3** for all panel development. The vault specs already described Filament 5 as the target version; the `^3.3` constraint in the build instructions was an error.

## Consequences

- `authModel()` method does not exist in Filament 5 — the model is resolved from the auth guard provider in `config/auth.php`. Panel providers do not call `->authModel()`.
- Panel providers live in `app/Providers/Filament/` (Filament 5 convention), not `app/Filament/Panels/` as described in some task instructions.
- Filament 5 `Panel` class uses `HasAuth` trait for auth configuration — `authGuard()` is the correct method.
- All future domain build sessions must target Filament 5 APIs.

## Related Files

- `app/Providers/Filament/AdminPanelProvider.php`
- `app/Providers/Filament/AppPanelProvider.php`
- `vault/domains/foundation/filament-panels.md`

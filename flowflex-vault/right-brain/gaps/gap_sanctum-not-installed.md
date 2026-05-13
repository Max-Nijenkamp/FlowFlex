---
type: gap
severity: critical
category: architecture
status: resolved
color: "#F97316"
discovered: 2026-05-11
discovered_in: api-integrations-layer
last_updated: 2026-05-11
---

# Gap: Laravel Sanctum Not Installed — API Auth Completely Non-Functional

## Context

Discovered during security hardening session 2026-05-11. Previous session built V1 REST API with `auth:sanctum` middleware. The `sanctum` guard was configured in `config/auth.php` but `laravel/sanctum` was never added to `composer.json`.

## The Problem

- `auth:sanctum` guard listed in `config/auth.php` with `driver: sanctum`
- Sanctum service provider never registered → driver not defined
- Any request to a protected API endpoint would throw `InvalidArgumentException: Auth driver [sanctum] for guard [sanctum] is not defined`
- `User::createToken()` method did not exist on User model (no `HasApiTokens` trait)
- `personal_access_tokens` migration used `morphs()` (bigint) but all PKs in the app are ULIDs (strings) → token creation would throw `SQLSTATE[22P02]: invalid input syntax for type bigint`

## Impact

Every protected API endpoint (`DELETE /auth/token`, all resource endpoints) was completely non-functional. The REST API built in a prior session never actually worked end-to-end.

## Resolution

1. `composer require laravel/sanctum` — installed v4.3
2. Published Sanctum migrations, changed `morphs('tokenable')` → `ulidMorphs('tokenable')` before running
3. Added `use Laravel\Sanctum\HasApiTokens;` + `use HasApiTokens;` to `App\Models\User`
4. Added `tests/Feature/Api/ApiAuthTest.php` — 5 tests covering token issue, revoke, bad credentials, protected endpoint
5. All 520 tests pass

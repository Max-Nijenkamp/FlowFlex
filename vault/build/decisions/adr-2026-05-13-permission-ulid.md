---
type: adr
date: 2026-05-13
status: decided
color: "#F97316"
slug: permission-ulid
---

# ADR: String(26) Columns for Spatie Permission ULID Compatibility

## Context

`spatie/laravel-permission` v7 publishes a migration that uses `unsignedBigInteger` for `model_morph_key` (the polymorphic model ID column) and for `team_foreign_key`. FlowFlex uses ULID strings (26-character) as primary keys on all models, including User and Admin. If the permission tables keep `unsignedBigInteger` for model IDs, assigning roles to ULID-keyed users will fail with a type mismatch.

## Options Considered

1. Keep `unsignedBigInteger` and cast ULIDs to integers — rejected, ULIDs are not integers
2. Use `uuid` column type — not necessary; ULID fits in `string(26)`
3. Replace `unsignedBigInteger` with `string(26)` for morph key and team foreign key — accepted

## Decision

The published permission migration is modified to use `string(26)` for:
- `model_morph_key` in `model_has_permissions` and `model_has_roles` tables
- `team_foreign_key` (company_id) in `roles`, `model_has_permissions`, and `model_has_roles` tables

This is a one-time migration change at project creation — no future migration needed.

## Consequences

- Role/permission assignment works correctly with ULID user and admin PKs
- The `setPermissionsTeamId()` call in `SetCompanyContext` passes a ULID string — this works correctly since the column is now `string(26)`
- Any future domain that introduces new morphable models with roles must use ULID PKs (already required by `HasUlid` trait convention)

## Related Files

- `database/migrations/2026_05_13_180357_create_permission_tables.php`
- `app/Support/Traits/HasUlid.php`
- `app/Http/Middleware/SetCompanyContext.php`
- `config/permission.php`

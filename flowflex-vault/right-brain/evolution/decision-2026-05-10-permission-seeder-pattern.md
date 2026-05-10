---
type: adr
date: 2026-05-10
status: decided
color: "#F97316"
---

# Decision: PermissionSeeder uses idempotent firstOrCreate, owner role synced in 3 places

## Context

Phase 1 introduced a `PermissionSeeder` that creates all `domain.module.action` permission strings. The question was when and how to sync permissions to the owner role, since permissions are added continuously as new domains are built.

## Options Considered

1. **Seed permissions once and never re-sync** — Simple but breaks when new permissions are added in Phase 2+; owner role misses new permissions.
2. **Sync in PermissionSeeder only** — Works for fresh installs but not for companies created before new permissions are added.
3. **Sync in 3 places** — PermissionSeeder (deploy), SyncOwnerPermissionsListener on CompanyCreated (new company), LocalCompanySeeder (local dev). Covers all cases.

## Decision

Sync owner role permissions in 3 places:
1. **`PermissionSeeder::run()`** — syncs `Permission::all()` to every `owner` role across all teams after creating permissions. Run on every deploy.
2. **`SyncOwnerPermissionsListener`** on `CompanyCreated` — ensures a new company's owner role immediately has all current permissions.
3. **`LocalCompanySeeder`** — ensures the demo owner user has all permissions in local dev.

`PermissionSeeder` uses `Permission::firstOrCreate()` — fully idempotent, safe to run on every deploy via `db:seed --class=PermissionSeeder`.

## Consequences

- Every `php artisan db:seed --class=PermissionSeeder` on deploy ensures all owner roles across all companies have all current permissions
- New Phase 2 module permissions are automatically propagated to existing owner roles on next deploy
- Custom roles must be manually updated by company admins via RBAC Management UI

## Related Left Brain

- [[rbac-management-ui]]
- [[company-workspace-settings]]

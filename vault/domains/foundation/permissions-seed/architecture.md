---
domain: foundation
module: permissions-seed
type: architecture
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Permissions Seeder — Architecture

The install-time bootstrap seeders: `PermissionSeeder` (core permission strings), `ModuleCatalogSeeder` (module catalog), and the non-prod `LocalDevSeeder` (demo company + working logins). Seeder chain, demo accounts, and owner-permission-sync detail live in [[_module]]; controls in [[security]].

## Filament Artifacts

**Filament Artifacts:** None (backend module — artisan seeders run at install/deploy; no panel UI. The permissions it creates become the assignable set in `core.rbac`'s role builder, but that UI is owned by RBAC, not here).

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Seeder execution | n/a | Idempotent upserts (`PermissionSeeder` const + `permission:cache-reset`) run once at install/deploy or manual re-seed — not a concurrent user write path; re-running is safe by design |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

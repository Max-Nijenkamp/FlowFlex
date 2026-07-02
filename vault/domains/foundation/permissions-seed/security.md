---
domain: foundation
module: permissions-seed
type: security
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Permissions Seeder — Security

Parent: [[_module]]. Seeders establish the permission universe and the first accounts — a security-sensitive bootstrap that must be safe to run and impossible to run wrongly in production.

## Controls

| Control | Implementation |
|---|---|
| No demo data in production | `DatabaseSeeder` runs `LocalDevSeeder` only when `! app()->environment('production')`; the seeder itself throws `RuntimeException` if forced in prod |
| Idempotent seeding | `PermissionSeeder` upserts via the `PERMISSIONS` const + `permission:cache-reset` — safe to re-run, no duplicate roles/permissions |
| Team-scoped permissions | permissions/roles seeded under `team_id = company_id` (Spatie teams) — no cross-tenant role bleed ([[../multi-tenancy-layer/security]]) |
| Owner permission sync | `$owner->syncPermissions(web-guard permissions)` — the owner always holds the full current set, no stale grants |
| Core-only permission surface | after the domain strip, only `core.*` permissions seed; domain perms return with their domains (smaller live surface) |

## Weak-password dev logins are dev-only

> The seeded logins (`admin@flowflex.nl`/`password`, `demo@flowflex.nl`/`password`, `test@test.nl`/`test1234`)
> exist **only** in non-production. `test@test.nl` is deliberately **both** a staff `super_admin` and the tenant
> `owner` of FlowFlex Demo — a dual-identity convenience login for local testing; it must never exist in prod.
> The env guard is the control that keeps these out of production.

## Spatie multi-tenant seeding pitfalls (handled)

> [!note] The industry's common Spatie-teams seeding bugs — "duplicate role" across tenants, forgetting to set
> the team context before seeding, unsafe force-seed in prod — are pre-empted here by team-scoping + the prod
> guard + idempotent upserts ([[../../_opportunities]] per-tenant-RBAC item).

> [!warning] UNVERIFIED — needs confirmation
> Whether `permission:cache-reset` runs per-tenant or globally after seeding, and the exact `PERMISSIONS` const
> contents, were not re-read from source.

## Related

- [[_module]] · [[unknowns]] · [[../../../security/authn-authz]] · [[../../../domains/core/rbac/_module]]
- [[../multi-tenancy-layer/security]] · [[../../../infrastructure/module-catalog]]

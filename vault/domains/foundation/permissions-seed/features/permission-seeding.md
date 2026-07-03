---
domain: foundation
module: permissions-seed
feature: permission-seeding
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Permission & Module-Catalog Seeding

Idempotent bootstrap of the permission universe and module catalog — runs in every environment, including production.

## Behaviour

- `DatabaseSeeder` always runs `PermissionSeeder` + `ModuleCatalogSeeder`.
- `PermissionSeeder` upserts core permissions (`core.settings.*`, `core.rbac.*`, …) from a `PERMISSIONS` const, then `permission:cache-reset`. Idempotent — safe to re-run.
- Domain permissions (`hr.*`, `finance.*`, `crm.*`, `access.*-panel`) were stripped with their domains; they return when rebuilt.
- `ModuleCatalogSeeder` seeds the module catalog ([[../../../../infrastructure/module-catalog]]) other domains gate on.
- Team-scoped: roles/permissions seed under `team_id = company_id`.

## UI

- **Kind**: background (artisan seeder — no screen). The permissions it creates become the assignable set in
  the RBAC role builder ([[../../../../domains/core/rbac/features/module-scoped-permissions]]).

## Data

- Owns: seeds Spatie permission tables + the module catalog (foundation/core-owned).
- Cross-domain writes: none — it seeds the shared permission/catalog reference, read by all domains.

## Relations

- Consumes: nothing. Feeds: RBAC (assignable permissions), module marketplace (catalog).
- Shared entity: the permission strings + module catalog.

## Test Checklist

### Unit
- [ ] `PERMISSIONS` const upsert is idempotent — a re-run creates no duplicate permissions

### Feature (Pest)
- [ ] `PermissionSeeder` idempotent across two runs (`SeederTest`)
- [ ] Permissions seed under `team_id = company_id` (no cross-tenant bleed)
- [ ] `ModuleCatalogSeeder` populates the catalog other modules gate on

## Unknowns

> [!warning] UNVERIFIED — exact `PERMISSIONS` const; cache-reset scope. See [[../unknowns]].

## Related

- [[../_module|Permissions Seeder]] · [[demo-data-seeding]] · [[../../../../domains/core/rbac/_module]] · [[../../../../infrastructure/module-catalog]]

---
domain: foundation
module: permissions-seed
type: unknowns
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Permissions Seeder — Unknowns

Parent: [[_module]].

| # | Item | State |
|---|---|---|
| 1 | Exact `PERMISSIONS` const contents (which `core.*` strings) | UNVERIFIED — "core only" confirmed, list not read |
| 2 | `permission:cache-reset` scope (global vs per-team) | UNVERIFIED |
| 3 | `test@test.nl` dual-identity (admin + tenant owner) context resolution | open — see [[../multi-tenancy-layer/unknowns]] |
| 4 | `ModuleCatalogSeeder` — full module list vs. what's active post-strip | *(assumed)* "all catalog modules active, billing rows only" |
| 5 | Whether prod first-tenant bootstrap (real owner) has its own seeder/flow | open — no public registration; staff-created in `/admin` |

## Related

- [[_module]] · [[security]] · [[../../../infrastructure/module-catalog]] · [[../../../domains/core/rbac/_module]]

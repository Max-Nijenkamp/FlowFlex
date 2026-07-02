---
domain: foundation
module: multi-tenancy-layer
type: unknowns
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Multi-Tenancy Layer — Unknowns

Parent: [[_module]].

| # | Item | State |
|---|---|---|
| 1 | Exact namespace allow-list for `withoutGlobalScope(CompanyScope)` | UNVERIFIED — "admin/support only" stated, list not read |
| 2 | HTTP status of `MissingCompanyContextException` (403 vs 500 render) | UNVERIFIED |
| 3 | `SetCompanyContextFromToken` (Sanctum) tested? | *(assumed)* present; no dedicated test cited — only web-guard `QueueContextTest`/`TenantIsolationTest` verified |
| 4 | Behaviour when a user has `company_id = null` (staff-as-tenant edge, e.g. `test@test.nl`) | open — see [[../permissions-seed/_module]] dual-identity login |
| 5 | Whether `LogsCompanyActivity` scope is applied uniformly or per-model opt-in | *(assumed)* |

## Related

- [[_module]] · [[security]]
- [[../../../architecture/patterns/tenant-context-pitfalls]] · [[../../../security/tenancy-isolation]]

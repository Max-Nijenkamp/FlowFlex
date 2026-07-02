---
domain: security
type: security
build-status: planned
status: unverified
color: "#EF4444"
updated: 2026-06-20
---

# Tenancy Isolation

Every tenant row carries `company_id`; isolation is enforced at three points.

| Mechanism | File | Role |
|---|---|---|
| `BelongsToCompany` trait | `app/Support/Traits/BelongsToCompany.php` | Auto-fills `company_id`, adds the global scope |
| `CompanyScope` | `app/Support/Scopes/CompanyScope.php` | Global query scope → every read filtered to the current Company |
| `CompanyContext` | `app/Support/Services/CompanyContext.php` | Holds the active Company; sets Spatie team id |
| `SetCompanyContext` middleware | `app/Http/Middleware/` | Web requests: resolve Company from the authed User |
| `SetCompanyContextFromToken` | `app/Http/Middleware/` | API: resolve Company from the Sanctum token |
| `WithCompanyContext` listener middleware | — | Queue jobs: rehydrate context (no session in workers) |

## The null-team 403 family

> [!danger] Three production bugs came from this
> Spatie permission checks use the **team id** (= `company_id`). If `can()` / `hasRole()` runs **before**
> `CompanyContext` has set the team, the role cache loads empty → a permitted user gets a phantom **403**.
> Rule: **set CompanyContext before any authorization check** — in middleware, panels, jobs, and tests.
> Codified in [[../architecture/patterns/tenant-context-pitfalls]]; live smoke via a scripted Livewire POST
> (`/flowflex:verify`) — plain GET 200s do **not** catch it.

## Verification

`TenantIsolationTest` + `QueueContextTest` (Architecture/Feature suites) assert scope + worker rehydration.

## Related

- [[authn-authz]] · [[../infrastructure/queue-horizon]] · [[../domains/foundation/multi-tenancy-layer/_module]] · [[_moc|Security MOC]]

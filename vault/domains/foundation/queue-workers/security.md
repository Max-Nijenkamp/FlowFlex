---
domain: foundation
module: queue-workers
type: security
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Queue Workers — Security

Parent: [[_module]]. Two security surfaces: the Horizon dashboard (an admin tool exposing job payloads) and the tenant integrity of async work.

## Permissions

None — no permission strings. The Horizon dashboard (`/horizon`) is gated by **guard** (`admin`, via `HorizonServiceProvider`), not by a permission; tenant users cannot reach it. Job execution carries no user permission — tenant integrity comes from `WithCompanyContext` (scope + team), not RBAC. See the Controls table below.

## Controls

| Control | Implementation |
|---|---|
| Horizon dashboard access | gated to the `admin` guard via `HorizonServiceProvider` gate — tenant users cannot reach `/horizon` |
| Tenant integrity in workers | every tenant job uses `WithCompanyContext` → correct company scope + team ([[../multi-tenancy-layer/features/queue-context]]) |
| No cross-tenant writes | listeners write only their own domain's tables ([[../../../security/data-ownership]]) |
| Payload hygiene | events carry `company_id` + IDs as scalars, never serialized models (smaller blast radius if a payload leaks) |
| Overlap / multi-server safety | scheduled commands use `withoutOverlapping()` + `onOneServer()` |
| Failed-job retention | `failed_jobs` kept 30 days then pruned |

## Notes

- The Horizon dashboard shows job payloads; because payloads are scalars (not full models), an admin viewing
  them sees IDs, not decrypted sensitive fields — but it is still an `admin`-only surface by design.
- A job dispatched **without** `WithCompanyContext` is the classic silent cross-tenant / null-tenant failure
  ([[../../_opportunities]] item 1); the arch/feature tests should catch missing middleware.

> [!warning] UNVERIFIED — needs confirmation
> The exact `HorizonServiceProvider` gate definition (which admin roles), and whether an arch test enforces
> `WithCompanyContext` on every tenant-touching job, were not re-read from source.

## Related

- [[_module]] · [[unknowns]] · [[../../../infrastructure/queue-horizon]]
- [[../multi-tenancy-layer/features/queue-context]] · [[../../../security/data-ownership]]

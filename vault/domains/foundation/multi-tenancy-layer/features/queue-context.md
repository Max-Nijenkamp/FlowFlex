---
domain: foundation
module: multi-tenancy-layer
feature: queue-context
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Queue Tenant-Context Propagation (`WithCompanyContext`)

Tenant context survives the queue boundary: a job that runs minutes later in a Horizon worker still knows which company it belongs to.

## Behaviour

- Every job/listener touching tenant models `implements ShouldQueue` + uses the `WithCompanyContext` job middleware.
- The middleware reads `company_id` from the job payload, calls `CompanyContext::set()` **and** `setPermissionsTeamId()` before `handle()` — restoring both the query scope and RBAC team.
- Without it, the worker runs with no tenant → `CompanyScope` generates `WHERE company_id IS NULL` → jobs process the wrong tenant or silently no-op (the industry's most-reported multi-tenant queue bug — see [[../../_opportunities]]).
- Events carry `company_id` as a scalar, never a model.

## UI

- **Kind**: background (queue middleware — no screen). Failures surface in the Horizon dashboard
  ([[../../queue-workers/_module|queue-workers]], admin-guard only).

## Data

- Owns: no tables. Restores context so downstream writes hit the correct company's tables.
- Cross-domain writes: none directly — each listener writes only its own domain's tables.

## Relations

- Consumes: dispatched jobs from every domain. Feeds: correct tenant scope for all async work.
- Shared entity: the `WithCompanyContext` middleware, mandatory across domains.

## Test Checklist

### Unit
- [ ] `WithCompanyContext` reads `company_id` from the job payload

### Feature (Pest)
- [ ] Queued listener restores company + team before `handle()` (`QueueContextTest`)
- [ ] A job dispatched without the middleware does not read another company's rows (null-tenant guard)

## Unknowns

> [!warning] UNVERIFIED — whether the Sanctum/token path (`SetCompanyContextFromToken`) has an equivalent
> queue-restore test; only the web-guard `QueueContextTest` is confirmed. See [[../unknowns]].

## Related

- [[../_module|Multi-Tenancy Layer]] · [[../../queue-workers/_module|Queue Workers]] · [[../../../../architecture/queue-jobs]] · [[../../../../architecture/event-bus]]

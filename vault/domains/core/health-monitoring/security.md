---
domain: core
module: health-monitoring
type: security
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Health Monitoring — Security

Parent: [[_module]]

## Permissions

`core.health.view` — owner only, in `/app` (`SystemStatusPage`). `/pulse` and `/horizon` gate on the admin guard (+ owner for Horizon read *(assumed: staff-only in v1, simpler)*).

## Authorization

`SystemStatusPage` gates on:
`canAccess() = Auth::user()->can('core.health.view-any') && BillingService::hasModule('core.health')`
per [[../../../architecture/filament-patterns]] #1 — stated explicitly on the custom page. See [[../../../security/authn-authz]].

## /health endpoint hardening

From `build/security-audit-2026-06-11` (medium):

- A throttle limiter on `GET /health` to prevent probing.
- Detailed output restricted to authenticated / monitoring callers (token-guarded); anonymous callers get minimal status only, so component topology (DB, Redis, Meilisearch, queue names) is not leaked.

## Admin dashboards

`/pulse` and `/horizon` are inaccessible to tenant non-owner users — enforced by the admin guard. See [[../../../infrastructure/deployment]] for the monitoring surface layout.

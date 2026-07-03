---
domain: procurement
module: spend-analytics
type: security
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
---

# Spend Analytics — Security

## Access contract

`canAccess() = Auth::user()->can('procurement.spend.view') && BillingService::hasModule('procurement.spend')` — [[../../../architecture/filament-patterns]] #1. The dashboard states this explicitly (custom page).

## Permissions

| Permission | Grants |
|---|---|
| `procurement.spend.view` | view the dashboard + export |

## Controls

- **Export rate limiter** (from [[../../../_archive/build-history/security-audit-2026-06-11]], medium): throttle the export action per [[../../../architecture/security]] — a heavy aggregation + file build is a DoS/scraping vector.
- Spend figures can be sensitive (supplier pricing) — a single coarse `view` permission v1; finer per-department scoping is a candidate (see [[unknowns]]).

## Data ownership

Owns **no** tables and **writes nothing**, anywhere. It reads other modules' data read-only. This is the safest ownership posture — no write path exists to abuse ([[../../../security/data-ownership]]).

## Tenancy

- All aggregation queries run under CompanyContext/CompanyScope; cache keys are company-scoped (`company:{id}:...`) — [[../../../security/tenancy-isolation]].

## Related

- [[_module]] · [[../../../security/data-ownership]] · [[../../../architecture/security]]

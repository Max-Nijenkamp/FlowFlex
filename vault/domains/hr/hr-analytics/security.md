---
domain: hr
module: hr-analytics
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# HR Analytics — Security

## Permissions

- `hr.analytics.view` — view the analytics dashboard + widgets.
- `hr.analytics.export` — export charts as PNG / data as CSV *(assumed — verb-per-command for the export action)*.

## Authorization

`HrAnalyticsDashboard` (custom page) gates on `canAccess()`:

```
Auth::user()->can('hr.analytics.view') && BillingService::hasModule('hr.analytics')
```

There is no `view-any` — this is a single dashboard, so `view` gates it; each widget is additionally `canView()`-gated and leave/cost widgets check their soft-dep module. The export action requires `hr.analytics.export`. Custom pages state the check explicitly. Combines RBAC ([[../../../security/authn-authz]]) with module billing. Public/portal surfaces would use a guest or scoped-portal guard.

## Tenancy

Metrics are computed over the **current company only** — all aggregate queries are company-scoped. See [[../../../security/tenancy-isolation]].

## Sensitive Data — Aggregate-Only Rule

`encrypted-fields: none` for this module — but it reads modules that hold encrypted salary and other sensitive HR data. **It must NEVER surface encrypted salary or DEI attributes at row level.** Cost charts expose `salary_band` aggregates only; no individual salary may appear in any payload. See [[../../../security/encryption]].

## Rate Limiting

The CSV/PNG export action cites the named `exports` rate limiter (per-user/company) per [[../../../architecture/security]] and [[../../../decisions/decision-2026-07-02-rate-limit-and-token-hardening]]. No comms, money, or inventory mutations in this read-only module.

---
domain: hr
module: hr-analytics
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# HR Analytics — Security

## Permissions

- `hr.analytics.view` — the single permission this module defines.

## Authorization

Every artifact gates on `canAccess()`:

```
Auth::user()->can('hr.analytics.view-any') && BillingService::hasModule('hr.analytics')
```

Custom pages must state the check explicitly. Combines RBAC ([[../../../security/authn-authz]]) with module billing. Public/portal surfaces would use a guest or scoped-portal guard.

## Tenancy

Metrics are computed over the **current company only** — all aggregate queries are company-scoped. See [[../../../security/tenancy-isolation]].

## Sensitive Data — Aggregate-Only Rule

`encrypted-fields: none` for this module — but it reads modules that hold encrypted salary and other sensitive HR data. **It must NEVER surface encrypted salary or DEI attributes at row level.** Cost charts expose `salary_band` aggregates only; no individual salary may appear in any payload. See [[../../../security/encryption]].

## Rate Limiting

Cite a named throttle on the CSV/PNG export action (medium-severity finding, per architecture/security.md).

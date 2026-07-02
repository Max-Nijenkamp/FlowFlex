---
domain: support
module: support-analytics
type: security
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# Support Analytics — Security

## Permissions

| Permission | Description |
|---|---|
| `support.analytics.view` | View the support dashboard + widgets |

Seeded in `PermissionSeeder`.

## Access Contract (panel)

```php
canAccess() = Auth::user()->can('support.analytics.view')
           && BillingService::hasModule('support.analytics')
```

Per [[../../../architecture/filament-patterns]] #1 — `SupportDashboardPage` states this explicitly.

## Public CSAT Guard (HIGH — per [[build/security-audit-2026-06-11]])

- The public CSAT submit endpoint runs under an **explicit unauthenticated guard/middleware** (token-only, no panel session) alongside a named rate limiter.
- One response per `token` / `ticket_id` (unique constraint) — replay/duplicate submissions rejected.

## Tenant Isolation

`sup_csat_responses` carries `company_id` (global `CompanyScope`). Aggregate queries read only the current company's tickets/replies/SLA events — [[../../../architecture/multi-tenancy]]. Cached metric keys are namespaced by `company_id`.

## Encrypted Fields

None. (Free-text CSAT `comment` is not treated as sensitive in v1 *(assumed)*.)

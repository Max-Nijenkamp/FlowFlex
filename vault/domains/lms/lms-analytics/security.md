---
domain: lms
module: lms-analytics
type: security
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# LMS Analytics — Security

## Permissions

| Permission | Grants |
|---|---|
| `lms.analytics.view` | View the LMS dashboard + export reports |

See [[../../../security/authn-authz]].

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('lms.analytics.view')
        && BillingService::hasModule('lms.analytics');
}
```

## Rate Limiting

- The **report export** action carries a rate limiter / throttle (per [[../../../_archive/build-history/security-audit-2026-06-11|security audit]] medium finding) — heavy aggregations should not be triggerable in a loop.

## Tenant Isolation

- Owns no tables; every aggregate query runs through the owning modules' scoped models under `CompanyContext`, so all metrics are inherently company-scoped.
- Analytics never bypasses `CompanyScope` — it has no direct table access.

See [[../../../security/tenancy-isolation]].

## Sensitive Data

- Mentoring session notes are **never** aggregated here (pair-private). Skill/certification data is aggregated at the count level, respecting the source modules' scopes.

## Module Gating

`BillingService::hasModule('lms.analytics')`. See [[../../../infrastructure/module-catalog]].

## Encrypted Fields

None (owns no tables).

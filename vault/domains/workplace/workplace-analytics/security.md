---
domain: workplace
module: workplace-analytics
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Workplace Analytics — Security

## Permissions

| Permission | Grants |
|---|---|
| `workplace.analytics.view-any` | View the workplace dashboard + widgets |
| `workplace.analytics.view` | (alias) view metrics |

See [[../../../security/authn-authz]].

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('workplace.analytics.view-any')
        && BillingService::hasModule('workplace.analytics');
}
```

## Read-only Data Ownership

This module **writes nothing**. It reads the other Workplace modules' data via their read models — never their tables directly. It cannot corrupt or escalate into any other bounded context because no write path exists. See [[../../../security/data-ownership]].

## Rate Limiting

- Export + metrics-generation endpoints are **throttled per user** (security audit 2026-06-11, medium), consistent with the cached-metrics strategy.

## Tenant Isolation

- All aggregation queries run under `CompanyContext`; every source table is `company_id`-scoped via `CompanyScope`. No cross-company leakage.

See [[../../../security/tenancy-isolation]].

## Module Gating

`BillingService::hasModule('workplace.analytics')`. Soft sections additionally require the source module active. See [[../../../infrastructure/module-catalog]].

## Encrypted Fields

None owned. Visitor aggregation reads counts/volumes only — not decrypted visitor PII *(assumed)* — see [[unknowns]].

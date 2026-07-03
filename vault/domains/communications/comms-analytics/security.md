---
domain: communications
module: comms-analytics
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Comms Analytics — Security

## Permissions

| Permission | Grants |
|---|---|
| `comms.analytics.view` | View the analytics dashboard + widgets |

Read-only module — no command actions or state transitions, so a single view verb suffices. Seeded in `PermissionSeeder`.
See [[../../../security/authn-authz]].

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('comms.analytics.view')
        && BillingService::hasModule('comms.analytics');
}
```

*(gate reconciled to `comms.analytics.view` — the prior `view-any` was never defined in the permission set)*

## Tenant Isolation

Read-only aggregate queries always run under `CompanyScope` — every metric is scoped to the acting company. No table is written. Cache keys are company-prefixed (`company:{id}:comms:metrics:...`). See [[../../../security/tenancy-isolation]].

## Data Ownership

Owns no tables; writes nothing. Reads inbox + broadcast data via aggregate queries — the canonical read-only cross-domain pattern. See [[../../../security/data-ownership]].

## Encrypted Fields

None.

## Related

- [[_module]] · [[../../../security/data-ownership]]

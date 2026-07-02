---
domain: events
module: event-analytics
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Event Analytics — Security

## Permissions

| Permission | Grants |
|---|---|
| `events.analytics.view` | View the analytics dashboard + export |

See [[../../../security/authn-authz]].

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('events.analytics.view')
        && BillingService::hasModule('events.analytics');
}
```

## Rate Limiting

- Report export is throttled (per [[../../../build/security-audit-2026-06-11]], medium).

## Tenant Isolation

- No tables of its own; every read goes through sibling services which run under `CompanyContext`, so aggregation is company-scoped by construction. See [[../../../security/tenancy-isolation]].

## Data Ownership

- Read-only consumer — writes nothing. Reads only through owning services, never a direct cross-domain write. See [[../../../security/data-ownership]].

## Encrypted Fields

None. Analytics aggregates counts + money; attendee PII is never surfaced (only aggregate counts).

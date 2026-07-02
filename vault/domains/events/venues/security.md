---
domain: events
module: venues
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Venues — Security

## Permissions

| Permission | Grants |
|---|---|
| `events.venues.view-any` | View venues + records |
| `events.venues.manage` | Create/edit venues + rooms |

See [[../../../security/authn-authz]].

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('events.venues.view-any')
        && BillingService::hasModule('events.venues');
}
```

## Tenant Isolation

- Both tables carry `company_id` (indexed); `CompanyScope` constrains queries. See [[../../../security/tenancy-isolation]].

## Referential Safety

- Venue delete is blocked while an upcoming event references it *(assumed)* — prevents dangling `venue_id` on live events.

## Encrypted Fields

None. Venue contact detail is business data; not encrypted *(assumed)* — see [[unknowns]].

---
domain: events
module: events
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Events — Security

## Permissions

| Permission | Grants |
|---|---|
| `events.events.view-any` | View the events list + records |
| `events.events.create` | Create events |
| `events.events.update` | Edit events |
| `events.events.publish` | Publish an event |
| `events.events.cancel` | Cancel an event |

See [[../../../security/authn-authz]].

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('events.events.view-any')
        && BillingService::hasModule('events.events');
}
```

Custom pages (`EventCalendarPage`) state the contract explicitly. The public landing route uses a guest guard and reveals only published events.

## Tenant Isolation

- `ev_events` + `ev_sessions` carry `company_id` (indexed) via `BelongsToCompany`; `CompanyScope` constrains all queries.
- Public landing scopes strictly to the `{company}` + published status.

See [[../../../security/tenancy-isolation]].

## Rich-text Sanitization

- `description` is sanitized via HTMLPurifier before persistence (per [[../../../_archive/build-history/security-audit-2026-06-11]], medium).

## Virtual Link Exposure

- `virtual_link` is never rendered on the public landing page and is revealed only to confirmed registrants *(assumed)* — see [[unknowns]].

## Encrypted Fields

None. Event metadata is not PII. Attendee PII lives in [[../registrations/_module|events.registrations]] (encrypted there).

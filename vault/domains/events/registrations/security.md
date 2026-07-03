---
domain: events
module: registrations
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Registrations — Security

## Permissions

| Permission | Grants |
|---|---|
| `events.registrations.view-any` | View the attendee list + records |
| `events.registrations.check-in` | Run the check-in action |
| `events.registrations.manage` | Manage registrations (cancel, export, edit) |

See [[../../../security/authn-authz]].

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('events.registrations.view-any')
        && BillingService::hasModule('events.registrations');
}
```

`CheckInPage` states the contract explicitly and additionally gates the action on `events.registrations.check-in`.

## Encrypted Fields (attendee PII)

- 🔐 `attendee_name`, 🔐 `attendee_email`, 🔐 `custom_answers` — Laravel `encrypted` cast, `text` columns.
- `attendee_email_hash` = `sha256(attendee_email)` backs the unique `(event_id, attendee_email_hash)` constraint because the encrypted email is not queryable.
- See [[../../../architecture/patterns/encryption]].

## Tenant Isolation

- `ev_registrations` carries `company_id` (indexed) via `BelongsToCompany`; `CompanyScope` constrains all queries. See [[../../../security/tenancy-isolation]].

## Rate Limiting

- **Public registration endpoint**: throttled + honeypot (bot/enumeration abuse).
- **Attendee export action**: throttled (per [[../../../_archive/build-history/security-audit-2026-06-11]], medium).

## Cross-Domain PII Flow

- `EventRegistrationReceived` carries `attendee_email` + `attendee_name` as scalars to CRM. This is an intentional, bounded PII handoff to the CRM contact record; the payload is minimal and CRM writes its **own** table. See [[../../../security/data-ownership]].

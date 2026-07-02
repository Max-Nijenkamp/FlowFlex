---
domain: workplace
module: room-booking
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Room Booking — Security

## Permissions

| Permission | Grants |
|---|---|
| `workplace.rooms.view-any` | View rooms + booking calendar |
| `workplace.rooms.book` | Book a room (all users) |
| `workplace.rooms.manage` | CRUD rooms, edit any booking |
| `workplace.rooms.cancel-any` | Cancel another user's booking |

See [[../../../security/authn-authz]].

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('workplace.rooms.view-any')
        && BillingService::hasModule('workplace.rooms');
}
```

Cancelling another user's booking requires `workplace.rooms.cancel-any`; owners can always cancel their own.

## Tenant Isolation

- `wp_rooms` + `wp_room_bookings` carry `company_id` (indexed) via `BelongsToCompany`; `CompanyScope` constrains all queries.
- The overlap check and recurrence materialisation run strictly within the acting company.

See [[../../../security/tenancy-isolation]].

## Module Gating

`BillingService::hasModule('workplace.rooms')` gates panel access. See [[../../../infrastructure/module-catalog]].

## Encrypted Fields

None. Booking titles + amenities are plaintext; organisers/attendees are internal employees (no external PII in this module — cf. [[../visitor-management/_module|Visitor Management]] which does hold external PII).

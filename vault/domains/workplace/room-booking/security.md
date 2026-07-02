---
domain: workplace
module: room-booking
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Room Booking — Security

## Permissions

| Permission | Grants |
|---|---|
| `workplace.rooms.view-any` | View rooms + booking calendar |
| `workplace.rooms.book` | Book a room, check in / cancel own booking (all users) |
| `workplace.rooms.manage` | CRUD rooms, edit any booking |
| `workplace.rooms.cancel-any` | Cancel another user's booking |

**Verb / transition → permission** (per the frozen [[../../../_meta/spec-template]] verb-per-command rule):

| Command / transition | Permission |
|---|---|
| Book (create `confirmed`) | `workplace.rooms.book` |
| Check-in (stamp `checked_in_at`) | `workplace.rooms.book` (own booking) |
| Cancel own booking | `workplace.rooms.book` |
| Cancel another user's booking | `workplace.rooms.cancel-any` |
| No-show release (`confirmed → released`) | system command — no user permission (`ReleaseNoShowsCommand`) |
| Room CRUD | `workplace.rooms.manage` |

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

## Rate Limiting

- The **Book** action on `RoomBookingPage` sends a confirmation notification (and an `.ics` invite *(assumed)*) — a comms-dispatching panel action, so it carries the named `panel-action` rate limiter per [[../../../decisions/decision-2026-07-02-rate-limit-and-token-hardening]]. Prevents booking-spam / notification flooding.

## Tenant Isolation

- `wp_rooms` + `wp_room_bookings` carry `company_id` (indexed) via `BelongsToCompany`; `CompanyScope` constrains all queries.
- The overlap check and recurrence materialisation run strictly within the acting company.

See [[../../../security/tenancy-isolation]].

## Module Gating

`BillingService::hasModule('workplace.rooms')` gates panel access. See [[../../../infrastructure/module-catalog]].

## Encrypted Fields

None. Booking titles + amenities are plaintext; organisers/attendees are internal employees (no external PII in this module — cf. [[../visitor-management/_module|Visitor Management]] which does hold external PII).

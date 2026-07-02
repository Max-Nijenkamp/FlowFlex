---
domain: events
module: registrations
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Registrations — Architecture

## Status Machine (spatie/laravel-model-states)

```
registered → confirmed → attended
     │           │    └────→ no_show
     │           └────────→ cancelled
     └──(full)──→ waitlisted ──(promoted)──→ registered
```

| State | Transition | Trigger |
|---|---|---|
| `registered` | → `confirmed` | Free: auto; Paid: on ticket purchase (`confirm()`) |
| `registered` | → `waitlisted` | Capacity full at register time |
| `waitlisted` | → `registered` | FIFO promotion on a cancellation |
| `confirmed` | → `attended` | QR/manual check-in |
| `confirmed` | → `no_show` | `MarkNoShowsCommand` post-event |
| any active | → `cancelled` | Attendee/admin cancel |

## Services & Actions

| Class | Type | Responsibility |
|---|---|---|
| `RegistrationService::register()` | service | Atomic capacity check → `registered`/`waitlisted`; free → auto-confirm + mail+`.ics`; paid → pending ticket purchase. Fires `EventRegistrationReceived`. |
| `RegistrationService::confirm()` | service | Called by Tickets on payment success (same-domain service call). |
| `RegistrationService::cancel()` | service | Cancels + FIFO waitlist promotion (notify promoted attendee). |
| `CheckInAction::run(qr\|registration_id)` | lorisleiva action | Confirmed-only → `attended`. |
| `MarkNoShowsCommand` | scheduled command | Post-event: confirmed-not-checked-in → `no_show`. |

## Atomicity

- Capacity is enforced with an atomic check against `ev_events.capacity` (row lock / conditional update) so concurrent registrations at the limit waitlist rather than oversell.

## Events

### Fires: `EventRegistrationReceived`

| Payload field | Type |
|---|---|
| `company_id` | string (scalar) |
| `event_id` | string |
| `registration_id` | string |
| `attendee_email` | string |
| `attendee_name` | string |

Consumer: CRM find-or-create contact. Payload contract: [[../../../architecture/event-bus]].

## Filament Artifacts

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `RegistrationResource` | Events | #1 CRUD resource | Per-event filter, check-in action, attendee export (throttled). |
| `CheckInPage` | Events | #7 custom page | QR scan (camera + token input). |
| `RegistrationStatsWidget` | Events | #6 widget | registered / confirmed / attended. |

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('events.registrations.view-any')
        && BillingService::hasModule('events.registrations');
}
```

Public form uses a guest guard (Vue + Inertia).

## Jobs & Scheduling

| Job / Command | Queue | Schedule |
|---|---|---|
| `RegistrationConfirmationMail` (+`.ics`) | mail | on confirm |
| `MarkNoShowsCommand` | default | post-event |

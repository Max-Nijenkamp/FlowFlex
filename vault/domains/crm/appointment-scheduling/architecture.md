---
domain: crm
module: appointment-scheduling
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Appointment Scheduling — Architecture

## State Machine

Bookings carry a simple status enum (`confirmed` / `cancelled` / `completed` / `no-show`) rather than a spatie/model-states machine. Transitions are driven by resource actions and the cancel action.

## Services & Actions

| Class | Signature | Notes |
|---|---|---|
| `SchedulingService` | `slots(meetingTypeSlug, day): array` | Working hours − existing bookings − buffers. |
| `SchedulingService` | `book(BookSlotData): BookingData` | Slot re-validated inside a transaction (`SlotTakenException`); round-robin = least-bookings-this-week *(assumed)*; find-or-create contact; logs activity; queues confirmation + `.ics`; creates Stripe PaymentIntent when priced. |
| `CancelBookingAction` | `run(bookingId): void` | Notifies both sides. |
| `SlotTakenException` | — | Thrown when a slot is claimed concurrently. |

## Events

None fired, none consumed. Side effects (contact create, activity log, mail) are performed inline within `book()`.

## Filament Artifacts

Nav group: **Activities**.

| Artifact | ui-strategy row | Purpose |
|---|---|---|
| `MeetingTypeResource` | #1 CRUD | Meeting types with a booking-link copy button. |
| `BookingResource` | #1 CRUD | Bookings; status actions incl. no-show marking. |
| `AvailabilityPage` | #7 custom page | Form for the rep's own working hours. |
| Public booking page | #16 (Vue + Inertia) | `/book/{company-slug}/{meeting-slug}` — guest-facing self-booking. |

Custom pages and the public Vue page follow [[../../../architecture/ui-strategy]] and [[../../../architecture/filament-patterns]].

**Access contract:** `canAccess()` = `can('crm.scheduling.view-any') && hasModule('crm.scheduling')`.

## Jobs & Scheduling

| Job | Queue | Schedule | Notes |
|---|---|---|---|
| `BookingReminderCommand` | notifications | Every 15 min | 24h window + `reminded_at` null guard. |
| `BookingConfirmationMail` + `.ics` | notifications | On booking | Queued from `book()`. |

See [[../../../infrastructure/queue-horizon]] and [[../../../infrastructure/mail]].

## Search & Realtime

None.

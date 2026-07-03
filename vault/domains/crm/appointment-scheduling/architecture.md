---
domain: crm
module: appointment-scheduling
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
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

**Nav group:** Activities

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `MeetingTypeResource` | #1 CRUD resource | tweaks: custom-header-actions (copy booking link) | meeting types (name, duration, buffers, price) |
| `BookingResource` | #1 CRUD resource | tweaks: state-badge-column (confirmed/cancelled/completed/no-show), custom-header-actions (cancel, mark no-show) | bookings; status actions |
| `AvailabilityPage` | #7 custom page | [[../../../architecture/patterns/page-blueprints#Wizard]] — single-step settings form for the rep's own working hours *(assumed: single-step; not a true multi-step wizard — see [[./unknowns]])* | route within `/crm` |
| Public booking page | #16 Vue + Inertia (public-vue) | guest-facing, no Filament — [[./features/public-booking]] | `/book/{company-slug}/{meeting-slug}` self-booking |

**Access contract (mandatory):** every panel artifact gates on
`canAccess() = Auth::user()->can('crm.scheduling.view-any') && BillingService::hasModule('crm.scheduling')`
per [[../../../architecture/filament-patterns]] #1. `AvailabilityPage` is a custom page and MUST state this explicitly — Filament does not auto-gate custom pages. The public booking page (`/book/{company-slug}/{meeting-slug}`) is Vue+Inertia per [[../../../architecture/ui-strategy]] on an isolated **guest guard** (tenant resolved from `{company-slug}`, honeypot), gated by the `public-booking` named rate limiter — not a Filament artifact, no session-guard leakage (see [[./security]]).

## Jobs & Scheduling

| Job | Queue | Schedule | Notes |
|---|---|---|---|
| `BookingReminderCommand` | notifications | Every 15 min | 24h window + `reminded_at` null guard. |
| `BookingConfirmationMail` + `.ics` | notifications | On booking | Queued from `book()`. |

See [[../../../infrastructure/queue-horizon]] and [[../../../infrastructure/mail]].

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Meeting-type / availability CRUD (form, API) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Slot booking (`SchedulingService::book`) | Pessimistic | `DB::transaction()` + `lockForUpdate()`, re-validate slot freeness → `SlotTakenException` on concurrent claim (capacity/slot contention) ([[../../../architecture/patterns/states]]) |
| Booking status transition (confirm / cancel / complete / no-show) | Pessimistic | `DB::transaction()` + `lockForUpdate()`, re-read, validate, write ([[../../../architecture/patterns/states]]) |
| `BookingReminderCommand` (stamps `reminded_at`) | n-a | append-only once-guard on a scheduled background job |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Search & Realtime

None.

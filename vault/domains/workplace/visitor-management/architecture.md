---
domain: workplace
module: visitor-management
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Visitor Management — Architecture

## Visit Lifecycle

Plain timestamps drive the lifecycle (no state-machine class *(assumed)*):

```
pre-registered (expected_at set)
   → checked_in  (checked_in_at, badge_number assigned)
      → checked_out (checked_out_at)
walk-in: checked_in directly (no pre-registration)
```

- Declaration (NDA) acceptance stamped `declaration_accepted_at` when the toggle is enabled — a hard gate on check-in.
- Recurring visitors (contractors) are re-registered from history.

## Services & Actions

| Class | Type | Responsibility |
|---|---|---|
| `App\Services\Workplace\VisitorService` | interface→service | `checkIn(...)` — assign badge number, gate on declaration, notify host (in-app + mail). |
| `App\Actions\Workplace\CheckOutAction` | lorisleiva action | Stamp `checked_out_at`. |
| `App\Jobs\Workplace\GenerateVisitorBadgeJob` | queued job | Produce a badge PDF (`spatie/laravel-pdf`) with badge number. |
| `App\Console\Commands\Workplace\PurgeVisitorsCommand` | command | Daily — delete visitor PII older than 12 months *(assumed)*. |

## Kiosk Model

Kiosk check-in runs on a **dedicated kiosk user/role**, not a public route *(assumed)*: an authenticated device session with only the `workplace.visitors.kiosk` permission. Lookup + check-in actions are **rate-limited** per device session / IP (security audit, medium).

## Events

None fired or consumed *(assumed)*. A `VisitorArrived` cross-domain event is an open question — see [[unknowns]]. Platform contract: [[../../../architecture/event-bus]].

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `GenerateVisitorBadgeJob` | default | on check-in | badge_number guard |
| `PurgeVisitorsCommand` | default | daily | 12-month age guard; already-purged rows skipped |

## Filament Artifacts

**Nav group:** Visitors

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `VisitorResource` | #1 CRUD resource | tweaks: custom-header-actions (pre-register, check-in / check-out) | log filters (date range, host, company); export |
| `VisitorKioskPage` | custom page (kiosk) *(no exact ui-strategy row — bespoke full-screen check-in page; blueprint gap, see QUESTIONS)* | [[../../../architecture/patterns/custom-page-checklist]] | self-service check-in, kiosk role only, rate-limited |

**Access contract (mandatory):** every artifact above gates on
`canAccess() = Auth::user()->can('workplace.visitors.view-any') && BillingService::hasModule('workplace.visitors')`
per [[../../../architecture/filament-patterns]] #1. `VisitorKioskPage` (custom page) states it explicitly and additionally requires `workplace.visitors.kiosk` — it runs on a dedicated kiosk device session, never a public route. Check-in / lookup / pre-registration actions dispatch comms (host ping + visitor mail) and are **rate-limited** (`panel-action` + kiosk device/IP limiter — see [[security#Rate Limiting]]). Any optional public-vue reception tablet uses a scoped-portal guard (Vue+Inertia per [[../../../architecture/ui-strategy]]), not Filament.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Pre-registration + visitor record CRUD | Optimistic | `updated_at` stale-check on save → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Kiosk check-in / check-out | Optimistic | status-timestamp guard on the single `wp_visitors` row (no capacity/slot contention — a badge is per-visit) |

Visitor records are ordinary shared-editable CRUD → **optimistic** default per [[../../decisions/decision-2026-07-02-optimistic-locking-standard|concurrency standard]]. No pessimistic path: there is no capacity or money mutation here.

## Encryption

`wp_visitors.name` + `wp_visitors.email` use the `encrypted` cast (`text` columns) — external-person PII. See [[../../../security/encryption]] and [[data-model]].

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

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `VisitorResource` | Visitors | Standard CRUD resource | pre-register, check-in/out actions, log filters |
| `VisitorKioskPage` | Visitors | Custom page (kiosk) | self-service check-in, kiosk role only, rate-limited |

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('workplace.visitors.view-any')
        && BillingService::hasModule('workplace.visitors');
}
```

## Encryption

`wp_visitors.name` + `wp_visitors.email` use the `encrypted` cast (`text` columns) — external-person PII. See [[../../../security/encryption]] and [[data-model]].

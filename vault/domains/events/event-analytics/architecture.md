---
domain: events
module: event-analytics
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Event Analytics — Architecture

## Services & Actions

| Class | Type | Responsibility |
|---|---|---|
| `EventAnalyticsService::metrics(?eventId, from, to): EventMetricsData` | service | Aggregate the funnel, attendance, and revenue for an event or range; revenue via brick/money; soft sections (tickets/sponsors) conditional on module activation. |

Pure read service — no writes. Reads sibling modules' data through their owning services / read models.

## Caching

| Key | TTL | Invalidated by |
|---|---|---|
| `company:{id}:events:metrics:{event}:{range}` | 1 h (past events) / 15 min (live) | TTL only |

## Filament Artifacts

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `EventAnalyticsDashboard` | Analytics | #6 dashboard page + apex charts | Event selector, comparison view, export. |
| `FunnelWidget` / `RevenueWidget` / `AttendanceWidget` | Analytics | #6 widgets | Composed on the dashboard. |

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('events.analytics.view')
        && BillingService::hasModule('events.analytics');
}
```

The custom dashboard page states the contract explicitly.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| All dashboard/comparison paths | n-a | Pure read service; no writes |
| Metrics cache writes | n-a | TTL-keyed, idempotent recompute |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Events

None fired or consumed. See [[../../../architecture/event-bus]].

---
domain: workplace
module: workplace-analytics
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Workplace Analytics — Architecture

## Aggregation Model

No tables. `WorkplaceAnalyticsService::metrics(from, to): WorkplaceMetricsData` computes:

- **Room** — booking rate, no-show rate, peak hours (from `wp_room_bookings`).
- **Desk** — occupancy %, weekday attendance distribution (from `wp_desk_bookings`).
- **Visitor** (soft) — volume trend (from `wp_visitors`).
- **Maintenance** (soft) — request volume, resolution time, by category (from `wp_maintenance_requests`).

Soft sections are conditional on the owning module being active; queries are batched to avoid N+1.

## Read-only Boundary

This module **writes nothing**. It reads the other Workplace modules' data through their read models / query APIs. It is the reference implementation of the query-side of [[../../../security/data-ownership]] — no write path exists here at all.

## Caching

| Key | TTL | Invalidated by |
|---|---|---|
| `company:{id}:workplace:metrics:{from}:{to}` | 1 h historical / 15 min current | TTL only |

See [[../../../architecture/caching]].

## Services & Actions

| Class | Type | Responsibility |
|---|---|---|
| `App\Services\Workplace\WorkplaceAnalyticsService` | interface→service | Compute `WorkplaceMetricsData`; soft sections conditional; cache per company + range. |

## Events

None fired or consumed.

## Filament Artifacts

**Nav group:** Analytics

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `WorkplaceDashboardPage` | #6 Dashboard | [[../../../architecture/patterns/page-blueprints#Dashboard]] | export action cites `exports` limiter |
| `RoomUtilisationWidget` | #6 widget | blueprint-cell stat + apex chart | booking/no-show/peak |
| `DeskOccupancyWidget` | #6 widget | blueprint-cell stat + apex chart | occupancy %, weekday distribution |
| `VisitorVolumeWidget` | #6 widget (soft) | apex chart | hidden if visitors inactive |
| `MaintenanceWidget` | #6 widget (soft) | apex chart | hidden if maintenance inactive |

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('workplace.analytics.view-any')
        && BillingService::hasModule('workplace.analytics');
}
```

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| — | n/a | Read-only module — writes nothing beyond cache entries; nothing to stale-check ([[../../../decisions/decision-2026-07-02-optimistic-locking-standard]]) |

## Search & Realtime

None. Metrics served from cache; export + metrics endpoints throttled per user (security audit 2026-06-11, medium).

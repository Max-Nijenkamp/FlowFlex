---
domain: workplace
module: workplace-analytics
type: data-model
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Workplace Analytics — Data Model

**Owns no tables.** This module is a read-only aggregator; it persists nothing.

## Sources (read-only)

| Source table | Owner module | Metrics derived |
|---|---|---|
| `wp_room_bookings` | [[../room-booking/_module\|workplace.rooms]] | booking rate, no-show rate, peak hours |
| `wp_desk_bookings` | [[../desk-booking/_module\|workplace.desks]] | occupancy %, weekday attendance distribution |
| `wp_visitors` | [[../visitor-management/_module\|workplace.visitors]] | visitor volume (soft) |
| `wp_maintenance_requests` | [[../maintenance/_module\|workplace.maintenance]] | request volume, resolution time, by category (soft) |

## Output DTO

`WorkplaceMetricsData` (output only) carries the computed sections. See [[api]].

## Aggregation Flow (no ERD — no owned entities)

```mermaid
flowchart LR
    rb[(wp_room_bookings)] --> svc[WorkplaceAnalyticsService]
    db[(wp_desk_bookings)] --> svc
    vis[(wp_visitors)] -.soft.-> svc
    maint[(wp_maintenance_requests)] -.soft.-> svc
    svc --> dto[WorkplaceMetricsData]
    dto --> dash[WorkplaceDashboardPage + widgets]
```

All arrows are **read-only queries** through the owning modules' read models — no writes ([[../../../security/data-ownership]]).

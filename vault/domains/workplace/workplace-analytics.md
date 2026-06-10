---
type: module
domain: Workplace & Facility
domain-key: workplace
panel: workplace
module-key: workplace.analytics
status: planned
priority: p3
depends-on: [workplace.rooms, core.billing, core.rbac]
soft-depends: [workplace.desks, workplace.visitors, workplace.maintenance]
fires-events: []
consumes-events: []
patterns: [custom-pages]
tables: []
permission-prefix: workplace.analytics
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Workplace Analytics

Room and desk utilisation, occupancy trends, visitor volume, and maintenance metrics for facility planning. Owns no tables.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/workplace/room-booking\|workplace.rooms]] | core utilisation data |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | desks / visitors / maintenance | sections hidden when inactive |

---

## Core Features

- Room utilisation: booking rate, no-show rate, peak hours
- Desk utilisation: occupancy %, hybrid attendance patterns (weekday distribution)
- Occupancy trends over time
- Visitor volume trends
- Maintenance metrics: request volume, resolution time, by category
- Space optimisation insights (underused rooms/desks list)
- Export reports

---

## Data Model

No additional tables. Aggregates from `wp_room_bookings`, `wp_desk_bookings`, `wp_visitors`, `wp_maintenance_requests`.

## DTOs

Output only: `WorkplaceMetricsData`.

## Services & Actions

- `WorkplaceAnalyticsService::metrics(from, to): WorkplaceMetricsData` — soft sections conditional; no N+1

## Caching

| Key | TTL | Invalidated by |
|---|---|---|
| `company:{id}:workplace:metrics:{from}:{to}` | 1 h historical / 15 min current | TTL only |

---

## Filament

**Nav group:** Analytics

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `WorkplaceDashboardPage` | #6 dashboard page + apex charts | export |

---

## Permissions

`workplace.analytics.view`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Utilisation + no-show math over fixtures
- [ ] Weekday attendance distribution
- [ ] Soft sections hidden when inactive

---

## Build Manifest

```
app/Data/Workplace/WorkplaceMetricsData.php
app/Services/Workplace/WorkplaceAnalyticsService.php
app/Filament/Workplace/Pages/WorkplaceDashboardPage.php
app/Filament/Workplace/Widgets/{RoomUtilisationWidget,DeskOccupancyWidget,VisitorVolumeWidget,MaintenanceWidget}.php
tests/Feature/Workplace/WorkplaceAnalyticsTest.php
```

---

## Related

- [[domains/workplace/room-booking]]
- [[domains/workplace/desk-booking]]
- [[architecture/caching]]

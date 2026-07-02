---
domain: workplace
module: workplace-analytics
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Workplace Analytics

Room + desk utilisation, occupancy trends, visitor volume, and maintenance metrics for facility planning. Owns no tables — aggregates from the other Workplace modules.

## Module-key

| Field | Value |
|---|---|
| key | `workplace.analytics` |
| priority | p3 |
| panel | workplace |
| permission-prefix | `workplace.analytics` |
| tables | *(none — read-only aggregation)* |

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../room-booking/_module\|workplace.rooms]] | core utilisation data |
| Hard | [[../../core/billing-engine/_module\|core.billing]] | module gating |
| Hard | [[../../core/rbac/_module\|core.rbac]] | permissions, `canAccess()` |
| Soft | [[../desk-booking/_module\|workplace.desks]] | occupancy section (hidden if inactive) |
| Soft | [[../visitor-management/_module\|workplace.visitors]] | visitor-volume section (hidden if inactive) |
| Soft | [[../maintenance/_module\|workplace.maintenance]] | maintenance section (hidden if inactive) |

## Core Features

- **Utilisation dashboard** — room booking rate, no-show rate, peak hours; desk occupancy %, hybrid weekday distribution. See [[features/utilisation-dashboard|Utilisation Dashboard]].
- **Utilisation widgets** — the individual stat/chart fragments (room, desk, visitor, maintenance). See [[features/utilisation-widgets|Utilisation Widgets]].
- **Space optimisation insights** — underused rooms/desks list. See [[features/space-optimisation|Space Optimisation]].
- **Export** — download the metrics as a report (throttled). See [[features/export-report|Export Report]].

## See features/

- [[features/utilisation-dashboard|Utilisation Dashboard]] · [[features/utilisation-widgets|Utilisation Widgets]] · [[features/space-optimisation|Space Optimisation]] · [[features/export-report|Export Report]]

## Build Manifest

```
app/Data/Workplace/WorkplaceMetricsData.php
app/Services/Workplace/WorkplaceAnalyticsService.php
app/Filament/Workplace/Pages/WorkplaceDashboardPage.php
app/Filament/Workplace/Widgets/{RoomUtilisationWidget,DeskOccupancyWidget,VisitorVolumeWidget,MaintenanceWidget}.php
tests/Feature/Workplace/WorkplaceAnalyticsTest.php
```

## Test Checklist

- [ ] Tenant isolation: metrics aggregate only the active company's data
- [ ] Module gating: dashboard + widgets hidden when `workplace.analytics` inactive
- [ ] Utilisation + no-show math over fixtures.
- [ ] Weekday attendance distribution.
- [ ] Soft sections hidden when their module is inactive.
- [ ] Export throttled per user.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | booking/occupancy data | workplace.rooms, workplace.desks | via each module's read model / query API |
| Reads | visitor volume | workplace.visitors | soft — hidden if inactive |
| Reads | maintenance metrics | workplace.maintenance | soft — hidden if inactive |

**Data ownership:** `workplace.analytics` **owns no tables and writes nothing**. It reads the other Workplace modules' data through their read models / services — never by writing their tables ([[../../../security/data-ownership]]). This is the canonical read-only aggregator: all cross-domain flow here is query-side.

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../room-booking/_module|Room Booking]] · [[../desk-booking/_module|Desk Booking]] · [[../../../architecture/caching]]

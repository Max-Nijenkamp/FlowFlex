---
domain: workplace
module: workplace-analytics
feature: utilisation-widgets
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Utilisation Widgets

The individual stat/chart fragments that compose the dashboard.

## Behaviour

- `RoomUtilisationWidget` — booking rate, no-show rate, peak hours.
- `DeskOccupancyWidget` — occupancy %, hybrid weekday attendance distribution.
- `VisitorVolumeWidget` (soft) — visitor volume trend; hidden when visitors inactive.
- `MaintenanceWidget` (soft) — request volume, resolution time, by category; hidden when maintenance inactive.

## UI

- **Kind**: widget
- **Page**: mounted on `WorkplaceDashboardPage` (and reusable on the `/workplace` panel dashboard).
- **Layout**: each widget is a card — a headline stat + apex chart (bar/line/donut).
- **Key interactions**: hover chart → tooltip; soft widgets simply absent when their module is off.
- **States**: empty (flat/zero series → "no data") · loading (card skeleton) · error (card error state) · n/a selected.
- **Gating**: `workplace.analytics.view-any`; soft widgets also require the source module active.

## Data

- Owns / writes: nothing.
- Reads: the relevant source table via `WorkplaceAnalyticsService` (read-only).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: metrics from `WorkplaceAnalyticsService` (which reads the sibling modules).
- Feeds: nothing.
- Shared entity: none.

## Related

- [[../_module|Workplace Analytics]] · [[utilisation-dashboard]] · [[../api]]

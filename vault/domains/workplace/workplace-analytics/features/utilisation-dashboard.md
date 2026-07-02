---
domain: workplace
module: workplace-analytics
feature: utilisation-dashboard
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Utilisation Dashboard

The facility-planning dashboard: room + desk utilisation, occupancy trends, visitor volume, maintenance metrics.

## Behaviour

- Renders `WorkplaceMetricsData` for a chosen date range across room, desk, visitor, maintenance sections.
- Soft sections (desk / visitor / maintenance) appear only when their module is active.
- Metrics served from cache; date-range picker drives the query.

## UI

- **Kind**: custom-page (dashboard)
- **Page**: `WorkplaceDashboardPage` — "Workplace Analytics" (`/workplace/analytics`), apex charts.
- **Layout**: date-range bar; grid of widget cards (room utilisation, desk occupancy, visitor volume, maintenance); export button top-right.
- **Key interactions**: change range → widgets refresh from cache; export → throttled download.
- **States**: empty (no data in range → "no activity yet") · loading (widget skeletons) · error (toast + retry) · selected (widget drill-down modal *(assumed)*).
- **Gating**: `workplace.analytics.view-any`.

## Data

- Owns / writes: nothing.
- Reads: `wp_room_bookings`, `wp_desk_bookings`, `wp_visitors`, `wp_maintenance_requests` via the owning modules' read models.
- Cross-domain writes: none — read-only aggregation ([[../../../../security/data-ownership]]).

## Relations

- Consumes: booking/occupancy/visitor/maintenance data from the four sibling modules (read-only).
- Feeds: nothing.
- Shared entity: none (reads projections, owns none).

## Related

- [[../_module|Workplace Analytics]] · [[utilisation-widgets]] · [[export-report]] · [[../architecture]]

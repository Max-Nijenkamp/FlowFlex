---
domain: workplace
module: workplace-analytics
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Workplace Analytics — API / DTOs

## `WorkplaceMetricsData` (output only)

| Section | Fields |
|---|---|
| `room` | booking_rate, no_show_rate, peak_hours[] |
| `desk` | occupancy_pct, weekday_distribution{} |
| `visitor` (soft) | volume_trend[] — present only when visitors active |
| `maintenance` (soft) | request_volume, avg_resolution_days, by_category{} — present only when maintenance active |

## `WorkplaceAnalyticsService::metrics(from, to): WorkplaceMetricsData`

- Read-only. Soft sections omitted when the owning module is inactive.
- Result cached per `company:{id}:workplace:metrics:{from}:{to}`.

## Public / Portal Endpoints

None. Dashboard + export live in the `/workplace` panel; the export endpoint is throttled per user.

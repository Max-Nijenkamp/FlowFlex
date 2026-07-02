---
domain: workplace
module: workplace-analytics
feature: export-report
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Export Report

Download the workplace metrics for a date range as a report file.

## Behaviour

- Exports the current `WorkplaceMetricsData` (rooms, desks, and any active soft sections) for the selected range.
- Format CSV / PDF *(assumed — undecided, see [[../unknowns]])*.
- The export endpoint is **throttled per user** to protect the cached-metrics strategy.

## UI

- **Kind**: custom-page action (export button on the dashboard)
- **Page**: "Export" action on `WorkplaceDashboardPage`.
- **Layout**: button top-right; a format picker if more than one format ships.
- **Key interactions**: click "Export" → generate → download; repeated clicks throttled.
- **States**: empty (nothing to export in range) · loading (generating) · error (throttled / failure toast) · n/a selected.
- **Gating**: `workplace.analytics.view-any`.

## Data

- Owns / writes: nothing.
- Reads: `WorkplaceMetricsData` from `WorkplaceAnalyticsService` (read-only).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: metrics from the four sibling modules (read-only, via the service).
- Feeds: nothing.
- Shared entity: none.

## Test Checklist

### Unit
- [ ] Export payload mirrors `WorkplaceMetricsData` for the selected range (active sections only)

### Feature (Pest)
- [ ] Export throttled per user (limit hit → friendly notification, no file); file tenant-scoped

### Livewire
- [ ] Export button generates + downloads; repeated clicks throttled with human copy

## Related

- [[../_module|Workplace Analytics]] · [[utilisation-dashboard]] · [[../security]]

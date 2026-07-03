---
domain: procurement
module: spend-analytics
feature: export
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Spend Report Export

Export the current spend view to xlsx/pdf for sharing with finance/leadership.

## Behaviour

- Renders the filtered `SpendMetricsData` to a file (pxlrbt/filament-excel / spatie/laravel-pdf).
- **Rate-limited** (throttle on the export action) — heavy aggregation + file build is a DoS/scraping vector ([[../security]]).
- Respects the active filters (period/supplier/category).

## UI

- **Kind**: background (an action on the dashboard, not a page of its own).
- **Page**: none — "Export" button on `SpendAnalyticsDashboard`.
- **Key interactions**: click export → throttled → file download (or queued + notification for large ranges).
- **States**: idle · generating (spinner/disabled while building) · rate-limited (toast "please wait") · error (toast + retry).
- **Gating**: `procurement.spend.view` + rate limiter.

## Data

- Owns / writes: nothing (transient file only).
- Reads: `SpendMetricsData` (from the service).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: `SpendAnalyticsService::metrics`.
- Feeds: nothing.

## Test Checklist

### Unit
- [ ] Export payload mirrors the current dashboard view (range + filters)

### Feature (Pest)
- [ ] xlsx/pdf export rate-limited (`exports` limiter) and permission-gated
- [ ] Tenant isolation: export contains own-company data only

### Livewire
- [ ] Export action on the dashboard header; over-limit surfaces a clear message

## Unknowns

- Sync download vs queued export threshold for large periods. `*(assumed: queue over N rows)*`

## Related

- [[../_module|Spend Analytics]] · [[spend-breakdown]] · [[../../../../architecture/security]]

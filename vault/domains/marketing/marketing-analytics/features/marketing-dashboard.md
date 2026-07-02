---
domain: marketing
module: marketing-analytics
feature: marketing-dashboard
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Marketing Dashboard

One dashboard rolling up campaign, form, landing, sequence and attribution metrics.

## Behaviour

- Pick a date range; `MarketingAnalyticsService::metrics` returns all sections (soft-dep sections null when inactive).
- Widgets render campaign performance, form conversion, page funnel, sequence engagement, attribution.
- CSV export of the current view.

## UI

- **Kind**: widget (composed on a dashboard page)
- **Page**: `MarketingDashboardPage` (`/marketing/analytics`) — Analytics nav group; apex-chart widgets (ui-strategy row #6), polling 60s.
- **Layout**: date-range filter header; grid of widgets (`CampaignPerformanceWidget`, `FormConversionWidget`, `AttributionWidget`, + funnel/sequence panels); inactive-module sections omitted.
- **Key interactions**: change date range → all widgets refresh; toggle first/last attribution; export CSV.
- **States**: empty (no data in range → per-widget empty state) · loading (skeleton widgets) · error (aggregate failure → retry toast) · selected (drill on a series).
- **Gating**: `marketing.analytics.view`; each soft-dep widget also requires its source module active.

## Data

- Owns / writes: nothing.
- Reads: campaigns / forms / landing-pages / sequences / utm tables via their read models (read-only).
- Cross-domain writes: none — read-only aggregation ([[../../../../security/data-ownership]]).

## Relations

- Reads: all sibling marketing modules (see [[../_module|module]] Cross-Domain Edges).
- Embeds: [[../../utm-tracking/features/attribution|UTM attribution]] tables.
- Shared entity: none.

## Unknowns

- Cross-channel double-count reconciliation; consolidation with platform Analytics. See [[../unknowns]].

## Related

- [[../_module|Marketing Analytics]] · [[../architecture]] · [[../../../architecture/caching]]

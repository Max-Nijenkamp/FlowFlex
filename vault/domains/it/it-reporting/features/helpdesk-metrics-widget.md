---
domain: it
module: it-reporting
feature: helpdesk-metrics-widget
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Helpdesk Metrics Widget

## Purpose

Show IT helpdesk performance — ticket volume over time, average resolution time, and a breakdown by category. Soft-dependent on `it.helpdesk` — hidden entirely when that module is inactive.

## Behavior

- Ticket volume series over the selected period.
- Average resolution time (created → resolved).
- Breakdown by category.
- Soft-dep: renders only when `it.helpdesk` is active; otherwise `ItMetricsData.helpdesk_series` is `null` and the widget is omitted (no error).

## UI

- **Kind**: widget
- **Page**: hosted on the "IT Reporting" dashboard (`/it/reporting`) — apex-chart widgets, not a page of its own.
- **Layout**: a volume line/area chart, a resolution-time stat, and a by-category bar/pie, in the dashboard grid under the shared header period filter — conditional on it.helpdesk.
- **Key interactions**: change the header period to re-scope; hover a series for tooltip.
- **States**: empty ("No tickets in range" placeholder) · loading (skeleton chart) · error (retry card) · selected (hovered point highlighted) · **inactive** (widget absent when it.helpdesk is off).
- **Gating**: visible with `it.reporting.view`, `it.reporting` active, **and** `it.helpdesk` active.

## Data

- **Owns NOTHING** — read-only aggregation, no tables, no writes.
- Reads: `it_tickets` (created/resolved timestamps, category, status) via the **it.helpdesk** read API; aggregated in `ItAnalyticsService::metrics` → `helpdesk_series` (nullable).
- **Cross-domain writes: none at all** — never writes another domain's tables ([[../../../../security/data-ownership]]).

## Relations

- Reads from `it.helpdesk` (read-only, **soft-dep** — section nulls out and widget hides when inactive).
- Consumes: nothing.
- Feeds: nothing (read-only).

## Unknowns

> [!warning] UNVERIFIED — resolution-time basis (first-response vs final-resolution timestamp) not specified on `it_tickets`.

- `*(assumed)*` category is a column on `it_tickets`; resolution time = resolved_at − created_at over resolved tickets only.

## Related

- [[../_module|IT Reporting]] · [[it-dashboard]] · [[../../helpdesk/_module|it.helpdesk]]
- [[../architecture|it-reporting.architecture]] · [[../../../../security/data-ownership]]

---
domain: it
module: it-reporting
feature: licence-spend-widget
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Licence Spend Widget

## Purpose

Show software licence spend (monthly and annual), seat utilisation rate, and waste (unused-seat cost). Soft-dependent on `it.licences` — hidden entirely when that module is inactive.

## Behavior

- Monthly and annual licence spend (brick/money, integer minor units).
- Utilisation rate — active seats / purchased seats.
- Waste — cost of unused seats (purchased − active × per-seat cost).
- Soft-dep: the widget renders only when `it.licences` is active; otherwise `ItMetricsData.licence_spend` is `null` and the widget is omitted (no error).

## UI

- **Kind**: widget
- **Page**: hosted on the "IT Reporting" dashboard (`/it/reporting`) — apex-chart widgets, not a page of its own.
- **Layout**: spend stats (monthly/annual), a utilisation gauge/bar, and a waste figure, in the dashboard grid under the shared header period filter — conditional on it.licences.
- **Key interactions**: change the header period to re-scope; hover a series for tooltip.
- **States**: empty ("No licences tracked" placeholder) · loading (skeleton chart) · error (retry card) · selected (hovered point highlighted) · **inactive** (widget absent when it.licences is off).
- **Gating**: visible with `it.reporting.view`, `it.reporting` active, **and** `it.licences` active.

## Data

- **Owns NOTHING** — read-only aggregation, no tables, no writes.
- Reads: `it_licences` (purchased seats, active seats, per-seat cost, billing cycle) via the **it.licences** read API; aggregated in `ItAnalyticsService::metrics` → `licence_spend` (nullable, brick/money).
- **Cross-domain writes: none at all** — never writes another domain's tables ([[../../../../security/data-ownership]]).

## Relations

- Reads from `it.licences` (read-only, **soft-dep** — section nulls out and widget hides when inactive).
- Consumes: nothing.
- Feeds: nothing (read-only).

## Unknowns

> [!warning] UNVERIFIED — waste definition: which signal (last-login vs assignment) marks a seat "unused" is not specified.

- `*(assumed)*` per-seat cost and billing cycle live on `it_licences`; utilisation = active/purchased seats.

## Related

- [[../_module|IT Reporting]] · [[it-dashboard]] · [[../../software-licences/_module|it.licences]]
- [[../architecture|it-reporting.architecture]] · [[../../../../security/data-ownership]]

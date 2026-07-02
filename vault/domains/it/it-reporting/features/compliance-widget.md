---
domain: it
module: it-reporting
feature: compliance-widget
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Compliance Widget

## Purpose

Show the device compliance rate reported by MDM — the share of enrolled devices meeting policy. Soft-dependent on `it.mdm` — hidden entirely when that module is inactive.

## Behavior

- Device compliance rate — compliant enrolled devices / total enrolled devices.
- Rendered as an apex-chart gauge/stat on the IT dashboard under the shared header period filter.
- Soft-dep: renders only when `it.mdm` is active; otherwise `ItMetricsData.compliance_rate` is `null` and the widget is omitted (no error).

## UI

- **Kind**: widget
- **Page**: hosted on the "IT Reporting" dashboard (`/it/reporting`) — apex-chart widgets, not a page of its own.
- **Layout**: a compliance-rate gauge/stat (optionally compliant vs non-compliant split), in the dashboard grid under the shared header period filter — conditional on it.mdm.
- **Key interactions**: change the header period to re-scope; hover for the exact percentage tooltip.
- **States**: empty ("No enrolled devices" placeholder) · loading (skeleton gauge) · error (retry card) · selected (hovered segment highlighted) · **inactive** (widget absent when it.mdm is off).
- **Gating**: visible with `it.reporting.view`, `it.reporting` active, **and** `it.mdm` active.

## Data

- **Owns NOTHING** — read-only aggregation, no tables, no writes.
- Reads: `it_mdm_devices` (enrolment + compliance status) via the **it.mdm** read API; aggregated in `ItAnalyticsService::metrics` → `compliance_rate` (nullable).
- **Cross-domain writes: none at all** — never writes another domain's tables ([[../../../../security/data-ownership]]).

## Relations

- Reads from `it.mdm` (read-only, **soft-dep** — section nulls out and widget hides when inactive).
- Consumes: nothing.
- Feeds: nothing (read-only).

## Unknowns

> [!warning] UNVERIFIED — compliance denominator: whether unenrolled/retired devices are excluded from the total is not specified.

- `*(assumed)*` `it_mdm_devices` carries a boolean/enum compliance flag; rate = compliant / total enrolled.

## Related

- [[../_module|IT Reporting]] · [[it-dashboard]] · [[../../mdm-integration/_module|it.mdm]]
- [[../architecture|it-reporting.architecture]] · [[../../../../security/data-ownership]]

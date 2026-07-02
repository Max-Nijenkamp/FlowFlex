---
domain: hr
module: hr-analytics
feature: cost-analytics
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Feature: Cost Analytics

## Purpose

Band-level workforce cost charts. Soft-dependent on [[../../payroll/_module]] (`hr.payroll`).

## Behavior

Cost charts at **`salary_band` level only** — the widget is **hidden** when `hr.payroll` is inactive. **Never** exposes individual salaries; only band aggregates appear in any payload. See [[../security]].

## Source Data

`hr_payroll_runs` (band-level aggregation). Individual salary values (encrypted) must never surface at row level.

## Permissions

`hr.analytics.view` + module gating (both `hr.analytics` and `hr.payroll` active).

## UI

- **Kind**: widget
- **Page**: hosted on the "HR Analytics" dashboard (`/hr/analytics`) as a cost chart widget
- **Layout**: chart of workforce cost aggregated at `salary_band` level only (never individual salaries); omitted from the dashboard grid when `hr.payroll` is inactive
- **Key interactions**: change the header period filter to re-scope; hover a band for its aggregate cost tooltip
- **States**: empty = "No payroll data for period" placeholder · loading = skeleton chart · error = "Couldn't load cost analytics" with retry · selected = hovered band shows aggregate cost · hidden = widget absent when `hr.payroll` inactive (degraded soft-dep)
- **Gating**: visible with `hr.analytics.view` and both `hr.analytics` + `hr.payroll` modules active; individual salaries never rendered — band aggregates only

## Data

- Owns / writes: none — read-only aggregation
- Reads: `hr_payroll_runs` (band-level only) via `hr.payroll` read API; individual encrypted salary values must never surface at row level
- Cross-domain writes: none — never writes another domain's tables ([[../../../../security/data-ownership]])

## Relations

- Consumes: `PayrollRunApproved` from `hr.payroll` → refresh band-level cost projection *(assumed — may recompute live per request)*
- Feeds: none (read-only dashboards)
- Shared entity: `hr_payroll_runs` (read-only, band-level aggregation)

Parent: [[../_module]]

---
domain: hr
module: hr-analytics
feature: turnover-attrition
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Turnover & Attrition

## Purpose

Turnover rate for the selected period.

## Behavior

Turnover rate = terminations / average headcount over the selected period. Surfaced via `TurnoverWidget`. Math correctness is intended to be validated against fixture data.

## Source Data

`hr_employees` (terminations and headcount over the period). Aggregated in `HrAnalyticsService::metrics` → `turnover_rate`.

## Permissions

`hr.analytics.view` + module gating.

## UI

- **Kind**: widget
- **Page**: hosted on the "HR Analytics" dashboard (`/hr/analytics`) as `TurnoverWidget`
- **Layout**: a stat/trend widget showing turnover rate % for the selected period, optionally with a small sparkline of the rate over prior periods
- **Key interactions**: change the header period filter to recompute the rate; hover for the terminations/avg-headcount breakdown tooltip
- **States**: empty = "—" / "No terminations in period" when denominator is 0 · loading = skeleton stat while aggregate resolves · error = "Couldn't load turnover" with retry · selected = hovered figure shows numerator/denominator tooltip
- **Gating**: visible with `hr.analytics.view` and `hr.analytics` module active

## Data

- Owns / writes: none — read-only aggregation
- Reads: `hr_employees` (terminations + headcount over the period) via `hr.profiles` read API; aggregated in `HrAnalyticsService::metrics` → `turnover_rate`
- Cross-domain writes: none — never writes another domain's tables ([[../../../../security/data-ownership]])

## Relations

- Consumes: `EmployeeOffboarded` from `hr.profiles` → refresh turnover projection *(assumed — may recompute live per request)*
- Feeds: none (read-only dashboards)
- Shared entity: `hr_employees` (read-only)

## Test Checklist

### Unit
- [ ] `turnover_rate = terminations / average headcount` over the period, validated against fixture data
- [ ] Zero average headcount (denominator 0) renders "—" / no-terminations state, not a divide error

### Feature (Pest)
- [ ] Turnover computed over the selected period is company-scoped — excludes other companies' terminations

### Livewire
- [ ] `TurnoverWidget` `canView()`-gated on `hr.analytics.view` + module active
- [ ] Changing the header period filter recomputes the rate

Parent: [[../_module]]

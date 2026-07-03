---
domain: hr
module: hr-analytics
feature: headcount-analytics
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Headcount Analytics

## Purpose

Workforce composition and headcount trend views.

## Behavior

- Headcount trend chart — monthly active employee count.
- Department breakdown — pie chart of headcount by department.
- New-hire velocity — hires per month.
- Tenure distribution — histogram of employee tenure.

Rendered as apex-chart widgets on the dashboard with a header period filter, 60s polling.

## Source Data

`hr_employees` (active status, department, hire date, tenure). Aggregated in `HrAnalyticsService::metrics` → `headcount_series[]`, `dept_breakdown[]`, `hires_per_month[]`, `tenure_histogram[]`.

## Permissions

`hr.analytics.view` + module gating.

## UI

- **Kind**: widget
- **Page**: hosted on the "HR Analytics" dashboard (`/hr/analytics`) — this feature ships several apex-chart widgets, not a page of its own
- **Layout**: HeadcountTrendWidget (line/area chart of monthly active count), DeptBreakdownWidget (pie), a new-hire-velocity bar (hires/month), and a TenureWidget histogram — arranged in the dashboard grid under a shared header period filter
- **Key interactions**: change the header period filter to re-scope all charts; hover a series for the point tooltip; export chart PNG / data CSV from the dashboard action (named-throttled)
- **States**: empty = "No employees yet" placeholder in each chart when headcount is 0 · loading = skeleton chart while the 60s poll / initial aggregate resolves · error = "Couldn't load metrics" card with retry · selected = hovered/focused data point highlighted with tooltip
- **Gating**: visible with `hr.analytics.view` and `hr.analytics` module active; export requires `hr.analytics.view` (throttled action)

## Data

- Owns / writes: none — read-only aggregation
- Reads: `hr_employees` (active status, department, hire date, tenure) via `hr.profiles` read API; aggregated in `HrAnalyticsService::metrics` → `headcount_series[]`, `dept_breakdown[]`, `hires_per_month[]`, `tenure_histogram[]`
- Cross-domain writes: none (dashboards only — never writes another domain's tables — [[../../../../security/data-ownership]])

## Relations

- Consumes: `EmployeeHired` / `EmployeeOffboarded` from `hr.profiles` → refresh headcount/tenure projections *(assumed — may instead recompute live per request)*
- Feeds: none (read-only dashboards)
- Shared entity: `hr_employees` (read-only)

## Test Checklist

### Unit
- [ ] `headcount_series[]`, `dept_breakdown[]`, `hires_per_month[]`, `tenure_histogram[]` compute correctly from fixture employees
- [ ] Zero-headcount period yields the empty-state series, not an error

### Feature (Pest)
- [ ] `HrAnalyticsService::metrics` aggregates are N+1-free (query-count assertion)
- [ ] Metrics are scoped to the current company — company A metrics never include company B employees
- [ ] Chart export (PNG/CSV) is throttled by the named `exports` rate limiter (see [[../security]])

### Livewire
- [ ] Widgets `canView()`-gated on `hr.analytics.view` + module active
- [ ] Changing the header period filter re-scopes all charts

Parent: [[../_module]]

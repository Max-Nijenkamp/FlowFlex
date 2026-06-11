---
type: module
domain: HR & People
domain-key: hr
panel: hr
module-key: hr.analytics
status: planned
priority: v1
depends-on: [hr.profiles, core.billing, core.rbac]
soft-depends: [hr.leave, hr.payroll]
fires-events: []
consumes-events: []
patterns: [custom-pages]
tables: []
permission-prefix: hr.analytics
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# HR Analytics

Headcount trends, turnover rate, department breakdown, leave utilisation, and workforce composition charts. Read-only dashboards built on existing HR data — owns no tables.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/hr/employee-profiles\|hr.profiles]] | all headcount metrics |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | [[domains/hr/leave-management\|hr.leave]] | leave utilisation chart; hidden without it |
| Soft | [[domains/hr/payroll\|hr.payroll]] | (band-level) cost charts; hidden without it |

---

## Core Features

- Headcount trend chart: monthly active employee count (via `leandrocfe/filament-apex-charts`)
- Turnover rate: terminations / average headcount for selected period
- Department breakdown: pie chart of headcount by department
- Leave utilisation: average days taken vs allocated per leave type
- New hire velocity: hires per month
- Tenure distribution: histogram of employee tenure
- Export all charts as PNG or data as CSV
- Never exposes individual salaries — aggregates use `salary_band` only

---

## Data Model

No additional tables — all data sourced from `hr_employees`, `hr_leave_requests`, `hr_payroll_runs`.

## DTOs

Output only: `HrMetricsData` — period, headcount_series[], turnover_rate, dept_breakdown[], leave_utilisation[], hires_per_month[], tenure_histogram[].

## Services & Actions

- `HrAnalyticsService::metrics(CarbonImmutable $from, CarbonImmutable $to): HrMetricsData` — single service, all aggregate queries, no N+1

## Caching

| Key | TTL | Invalidated by |
|---|---|---|
| `company:{id}:hr:analytics:{from}:{to}` | 1 h (historical) / 15 min (current period) | TTL only — dashboard staleness acceptable per [[architecture/caching]] |

---

## Filament

**Nav group:** Analytics

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `HrAnalyticsDashboard` | #6 dashboard page + apex-chart widgets | period filter in header; widget polling 60s; soft-dep widgets conditional |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('hr.analytics.view-any') && BillingService::hasModule('hr.analytics')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

**Security notes** (per [[build/security-audit-2026-06-11]]):

- **Rate limiter** (medium): Cite a named throttle on the CSV/PNG export action per architecture/security.md.

---

## Permissions

`hr.analytics.view`

---

## Test Checklist

- [ ] Tenant isolation: metrics computed over current company only
- [ ] Module gating verified
- [ ] Turnover math correct against fixture data (terminations / avg headcount)
- [ ] Leave widget hidden when hr.leave inactive
- [ ] No individual salary in any payload (band aggregates only)
- [ ] Aggregate queries N+1-free

---

## Build Manifest

```
app/Data/HR/HrMetricsData.php
app/Services/HR/HrAnalyticsService.php
app/Filament/HR/Pages/HrAnalyticsDashboard.php
app/Filament/HR/Widgets/{HeadcountTrendWidget,TurnoverWidget,DeptBreakdownWidget,LeaveUtilisationWidget,TenureWidget}.php
tests/Feature/HR/HrAnalyticsTest.php
```

---

## Related

- [[domains/hr/employee-profiles]]
- [[domains/hr/leave-management]]
- [[architecture/packages]] (`leandrocfe/filament-apex-charts`)
- [[architecture/caching]]

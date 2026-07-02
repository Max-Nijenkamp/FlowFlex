---
domain: hr
module: hr-analytics
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# HR Analytics

Read-only HR dashboards: headcount trends, turnover rate, department breakdown, leave utilisation, and workforce composition charts. **Owns no tables** — every metric is aggregated over other HR modules' data. Nothing here is built yet; this note is the rebuild blueprint following [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]].

- **module-key:** `hr.analytics`
- **panel:** hr — nav group **Analytics**
- **priority:** v1
- **permission-prefix:** `hr.analytics`
- **tables:** none
- **encrypted-fields:** none (must NEVER surface encrypted salary/DEI at row level — aggregates only)

## Intended Behavior

- Headcount trend chart — monthly active employee count.
- Turnover rate — terminations / average headcount for the selected period.
- Department breakdown — pie chart of headcount by department.
- Leave utilisation — average days taken vs allocated per leave type (soft-dep).
- New hire velocity — hires per month.
- Tenure distribution — histogram of employee tenure.
- Export all charts as PNG or data as CSV (named throttle intended on the export action).
- Never exposes individual salaries — aggregates use `salary_band` only.

Dashboard is a custom Filament page with apex-chart widgets, period filter in the header, 60s widget polling. Soft-dep widgets render conditionally on module activation. Access is intended to gate on both permission and module billing.

## Dependencies

| Type | Module | Status impact / degraded behavior |
|---|---|---|
| Hard | [[../employee-profiles/_module]] (`hr.profiles`) | all headcount metrics — blocking |
| Hard | `core.billing` + `core.rbac` | module gating + permissions — blocking |
| Soft | [[../leave-management/_module]] (`hr.leave`) | leave utilisation chart; **hidden** when inactive |
| Soft | [[../payroll/_module]] (`hr.payroll`) | band-level cost charts; **hidden** when inactive |

## Notes in this Folder

- [[architecture]] — service, dashboard page, widgets, caching of aggregates
- [[security]] — permissions, tenancy, aggregate-only exposure rule
- [[unknowns]] — assumptions + unverified items

## Features

- [[features/headcount-analytics]] — headcount trend, dept breakdown, new-hire velocity, tenure
- [[features/turnover-attrition]] — turnover rate math
- [[features/leave-analytics]] — leave utilisation (soft-dep hr.leave)
- [[features/cost-analytics]] — band-level cost charts (soft-dep hr.payroll)

## Build Manifest

```
app/Data/HR/HrMetricsData.php
app/Services/HR/HrAnalyticsService.php
app/Filament/HR/Pages/HrAnalyticsDashboard.php
app/Filament/HR/Widgets/{HeadcountTrendWidget,TurnoverWidget,DeptBreakdownWidget,LeaveUtilisationWidget,TenureWidget}.php
tests/Feature/HR/HrAnalyticsTest.php
```

## Data Ownership

**Owns no tables** — read-only aggregation over other HR modules' data. Any metric is aggregated live (or as a projection it refreshes from events) from `hr_employees`, `hr_leave_requests`, and `hr_payroll_runs` via each owning module's read API. Never writes another domain's tables ([[../../../security/data-ownership]]).

## Cross-Domain Edges

| Direction | Event / integration | Counterpart | Effect |
|---|---|---|---|
| Consumes | `EmployeeHired` / `EmployeeOffboarded` | `hr.profiles` | refresh headcount / turnover / tenure projections *(assumed)* |
| Consumes | `LeaveRequestApproved` | `hr.leave` | refresh leave-utilisation projection *(assumed)* |
| Consumes | `PayrollRunApproved` | `hr.payroll` | refresh band-level cost projection *(assumed)* |
| Fires | none | — | read-only dashboards; emits nothing outbound |

## Related

- [[../../../architecture/patterns/custom-pages]]
- [[../../../architecture/caching]]
- [[../../../glossary]]
- [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]]

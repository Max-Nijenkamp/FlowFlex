---
domain: hr
module: hr-analytics
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# HR Analytics

Read-only HR dashboards: headcount trends, turnover rate, department breakdown, leave utilisation, and workforce composition charts. **Owns no tables** — every metric is aggregated over other HR modules' data. Nothing here is built yet; this note is the rebuild blueprint following [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]].

---

## Module-key

`hr.analytics`

**Priority:** v1
**Panel:** hr
**Permission prefix:** `hr.analytics`
**Tables:** None — read-only aggregation over `hr_employees`, `hr_leave_requests`, `hr_payroll_runs`
**Nav group:** Analytics

Encrypted-fields: none — but it reads modules holding encrypted salary/DEI; must NEVER surface those at row level (aggregates only, see [[security]]).

---

## Dependencies

| Type | Module | Status impact / degraded behavior |
|---|---|---|
| Hard | [[../employee-profiles/_module]] (`hr.profiles`) | all headcount metrics — blocking |
| Hard | `core.billing` + `core.rbac` | module gating + permissions — blocking |
| Soft | [[../leave-management/_module]] (`hr.leave`) | leave utilisation chart; **hidden** when inactive |
| Soft | [[../payroll/_module]] (`hr.payroll`) | band-level cost charts; **hidden** when inactive |

---

## Core Features

- Headcount trend, department breakdown, new-hire velocity, tenure distribution — [[features/headcount-analytics|Headcount Analytics]]
- Turnover rate (terminations / average headcount) — [[features/turnover-attrition|Turnover & Attrition]]
- Leave utilisation — average days taken vs allocated per type (soft-dep hr.leave) — [[features/leave-analytics|Leave Analytics]]
- Band-level workforce cost charts (soft-dep hr.payroll) — [[features/cost-analytics|Cost Analytics]]
- Export all charts as PNG or data as CSV (named `exports` throttle on the export action)
- Never exposes individual salaries — aggregates use `salary_band` only

Dashboard is a custom Filament page with apex-chart widgets, period filter in the header, 60s widget polling. Soft-dep widgets render conditionally on module activation. Access gates on both permission and module billing.

---

## Build Manifest

```
app/Data/HR/HrMetricsData.php
app/Services/HR/HrAnalyticsService.php
app/Filament/HR/Pages/HrAnalyticsDashboard.php
app/Filament/HR/Widgets/{HeadcountTrendWidget,TurnoverWidget,DeptBreakdownWidget,LeaveUtilisationWidget,TenureWidget}.php
tests/Feature/HR/HrAnalyticsTest.php
```

Filament artifacts (dashboard + widgets) and per-write-path concurrency tiers: [[architecture]].

---

## Test Checklist

- [ ] Tenant isolation: metrics computed over the current company only — company A never sees company B data
- [ ] Module gating: artifacts hidden when `hr.analytics` inactive
- [ ] Aggregate-only: no individual salary or DEI attribute ever appears in any payload (band-level only)
- [ ] Headcount / turnover / tenure math validated against fixture data
- [ ] Soft-dep widgets (leave utilisation, cost) hidden when `hr.leave` / `hr.payroll` inactive
- [ ] Aggregate queries are N+1-free (query-count assertion)
- [ ] PNG/CSV export throttled by the named `exports` rate limiter

---

## Data Ownership

**Owns no tables** — read-only aggregation over other HR modules' data. Any metric is aggregated live (or as a projection it refreshes from events) from `hr_employees`, `hr_leave_requests`, and `hr_payroll_runs` via each owning module's read API. Never writes another domain's tables ([[../../../security/data-ownership]]).

---

## Cross-Domain Edges

| Direction | Event / integration | Counterpart | Effect |
|---|---|---|---|
| Consumes | `EmployeeHired` / `EmployeeOffboarded` | `hr.profiles` | refresh headcount / turnover / tenure projections *(assumed)* |
| Consumes | `LeaveRequestApproved` | `hr.leave` | refresh leave-utilisation projection *(assumed)* |
| Consumes | `PayrollRunApproved` | `hr.payroll` | refresh band-level cost projection *(assumed)* |
| Fires | none | — | read-only dashboards; emits nothing outbound |

---

## Related

- Entity notes: [[architecture]] · [[security]] · [[unknowns]]
- [[../../../architecture/patterns/custom-pages]]
- [[../../../architecture/caching]]
- [[../../../glossary]]
- [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]]
</content>
</invoke>

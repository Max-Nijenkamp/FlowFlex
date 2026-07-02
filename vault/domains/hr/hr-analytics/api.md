---
domain: hr
module: hr-analytics
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# HR Analytics — DTOs & Services

## DTO (output only)

`HrMetricsData` (`app/Data/HR/HrMetricsData.php`):

| Field | Notes |
|---|---|
| `period` | selected date range |
| `headcount_series[]` | monthly active employee count |
| `turnover_rate` | terminations / average headcount |
| `dept_breakdown[]` | headcount by department |
| `leave_utilisation[]` | avg days taken vs allocated per leave type (soft-dep hr.leave) |
| `hires_per_month[]` | new-hire velocity |
| `tenure_histogram[]` | tenure distribution |

## Service

`HrAnalyticsService::metrics(CarbonImmutable $from, CarbonImmutable $to): HrMetricsData`

Single service holding all aggregate queries; intended N+1-free and company-scoped. Reads `hr_employees`, `hr_leave_requests`, `hr_payroll_runs`. Results cached in Redis — see [[architecture]].

Related: [[_module]]

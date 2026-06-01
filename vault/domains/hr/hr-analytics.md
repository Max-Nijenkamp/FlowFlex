---
type: module
domain: HR & People
panel: hr
module-key: hr.analytics
status: planned
color: "#4ADE80"
---

# HR Analytics

Headcount trends, turnover rate, department breakdown, leave utilisation, and workforce composition charts. Read-only dashboards built on existing HR data.

---

## Core Features

- Headcount trend chart: monthly active employee count (via `leandrocfe/filament-apex-charts`)
- Turnover rate: terminations / average headcount for selected period
- Department breakdown: pie chart of headcount by department
- Leave utilisation: average days taken vs allocated per leave type
- New hire velocity: hires per month
- Tenure distribution: histogram of employee tenure
- Export all charts as PNG or data as CSV

---

## Data Model

No additional tables — all data sourced from `hr_employees`, `hr_leave_requests`, `hr_payroll_runs`.

---

## Filament

**Nav group:** Analytics

- `HrAnalyticsDashboard` (custom dashboard page) — widget grid with chart widgets

---

## Related

- [[domains/hr/employee-profiles]]
- [[domains/hr/leave-management]]
- [[architecture/packages]] (`leandrocfe/filament-apex-charts`)

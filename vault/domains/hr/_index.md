---
type: domain-index
domain: HR & People
panel: hr
color: "#4ADE80"
---

# HR & People

Complete employee lifecycle — hire to offboard. Leave management, payroll tracking, onboarding, performance, compensation, org chart, and workforce planning. **Panel:** `/hr` (Violet)

**Displaces**: BambooHR, Workday, HiBob, Personio

---

## Navigation Groups

- **Employees** — Profiles, Org Chart, Self-Service, Onboarding, Recruitment
- **Leave** — Leave Management, Time & Attendance, Shift Scheduling
- **Payroll** — Payroll, Compensation & Benefits
- **Performance** — Performance Reviews, Employee Feedback
- **Analytics** — HR Analytics, Workforce Planning

---

## Modules

| Module | Key | Status | Priority |
|---|---|---|---|
| [[domains/hr/employee-profiles\|Employee Profiles]] | `hr.profiles` | planned | **MVP core** |
| [[domains/hr/leave-management\|Leave Management]] | `hr.leave` | planned | **MVP core** |
| [[domains/hr/onboarding\|Onboarding]] | `hr.onboarding` | planned | **MVP core** |
| [[domains/hr/payroll\|Payroll]] | `hr.payroll` | planned | **MVP core** |
| [[domains/hr/org-chart\|Org Chart]] | `hr.org` | planned | MVP |
| [[domains/hr/employee-self-service\|Employee Self-Service]] | `hr.self-service` | planned | MVP |
| [[domains/hr/recruitment\|Recruitment]] | `hr.recruitment` | planned | Phase 2 |
| [[domains/hr/performance-reviews\|Performance Reviews]] | `hr.performance` | planned | Phase 2 |
| [[domains/hr/time-attendance\|Time & Attendance]] | `hr.time` | planned | Phase 2 |
| [[domains/hr/shift-scheduling\|Shift Scheduling]] | `hr.shifts` | planned | Phase 2 |
| [[domains/hr/compensation-benefits\|Compensation & Benefits]] | `hr.compensation` | planned | Phase 2 |
| [[domains/hr/hr-analytics\|HR Analytics]] | `hr.analytics` | planned | Phase 2 |
| [[domains/hr/workforce-planning\|Workforce Planning]] | `hr.workforce` | planned | Phase 3 |
| [[domains/hr/employee-feedback\|Employee Feedback]] | `hr.feedback` | planned | Phase 3 |
| [[domains/hr/dei-metrics\|DEI Metrics]] | `hr.dei` | planned | Phase 3 |

---

## Key Patterns

- [[architecture/patterns/belongs-to-company]] — all HR models are tenant-scoped
- [[architecture/patterns/interface-service]] — `EmployeeService`, `LeaveService`, `PayrollService`
- [[architecture/packages]] — `spatie/laravel-model-states` for leave status, employment status
- `saade/filament-fullcalendar` — leave calendar, shift calendar

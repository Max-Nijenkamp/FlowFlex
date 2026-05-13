---
type: module
domain: HR & People
panel: hr
module-key: hr.leave
status: planned
color: "#4ADE80"
---

# Leave Management

> Leave requests, approval workflows, leave balances, and leave type configuration — from annual holiday to sick leave, parental leave, and unpaid time off.

**Panel:** `hr`
**Module key:** `hr.leave`

## What It Does

Leave Management handles the complete leave lifecycle for every employee. HR defines leave policies (types, accrual rules, carry-over limits). Each employee has a balance per policy per year. Employees submit leave requests; managers approve or reject them via a simple approval workflow. Balances update automatically when a request is approved. The calendar view shows team availability so managers can spot conflicts before approving. Leave data feeds into payroll (to deduct unpaid leave days) and time & attendance (to explain absences).

## Features

### Core
- Leave types: annual, sick, maternity, paternity, unpaid, compassionate, study — configurable per company
- Leave balances: allocated days, used days, pending days, remaining days — tracked per employee per policy per year
- Request lifecycle: `pending` → `approved` / `rejected` / `cancelled`
- Approval workflow: request notifies the employee's direct manager; manager approves/rejects with optional comment
- Balance auto-update: on approval, `used_days` incremented; on cancellation, days returned to balance

### Advanced
- Carry-over rules per leave type: max carry-over days, expiry date for carried-over days
- Accrual: monthly or annual accrual with pro-rata calculation for partial year hires
- Calendar view: team leave calendar showing all approved and pending requests — filterable by department
- Multi-level approval: configurable second approval for leave exceeding a threshold (e.g. >10 days requires HR manager sign-off)
- Leave conflict detection: warn manager when approving leave that overlaps with another team member's approved leave
- Public holiday calendar: company-defined public holidays excluded from leave day counts

### AI-Powered
- Leave pattern analysis: flag employees with unusually high sick leave frequency — surfaced to HR manager as a wellbeing concern
- Coverage prediction: before approving, show AI estimate of team coverage impact based on current project workload

## Data Model

```erDiagram
    leave_policies {
        ulid id PK
        ulid company_id FK
        string name
        string type
        integer days_per_year
        integer max_carry_over
        boolean requires_approval
        timestamps created_at/updated_at
    }

    leave_balances {
        ulid id PK
        ulid company_id FK
        ulid employee_id FK
        ulid leave_policy_id FK
        integer year
        decimal allocated_days
        decimal used_days
        decimal pending_days
        decimal carried_over_days
        timestamps created_at/updated_at
    }

    leave_requests {
        ulid id PK
        ulid company_id FK
        ulid employee_id FK
        ulid leave_policy_id FK
        date start_date
        date end_date
        decimal days
        string status
        string reason
        ulid approver_id FK
        timestamp approved_at
        string rejection_reason
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `leave_balances.pending_days` | Days in pending requests — not yet deducted |
| `leave_requests.status` | pending / approved / rejected / cancelled |
| `leave_requests.days` | Calculated working days between start and end |

## Permissions

- `hr.leave.view-own`
- `hr.leave.view-team`
- `hr.leave.request`
- `hr.leave.approve`
- `hr.leave.manage-policies`

## Filament

- **Resource:** `LeaveRequestResource`, `LeavePolicyResource`
- **Pages:** `ListLeaveRequests`, `CreateLeaveRequest`, `ListLeavePolicies`
- **Custom pages:** `LeaveCalendarPage` — team calendar with approved and pending leave blocks
- **Widgets:** `LeaveBalanceSummaryWidget` — employee's own balances on HR dashboard
- **Nav group:** Leave (hr panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| BambooHR | Time-off management and approval workflows |
| Workday | Leave of absence management |
| HiBob | Leave and time-off requests |
| Personio | Absence management |

## Related

- [[employee-profiles]]
- [[time-attendance]]
- [[payroll]]
- [[employee-self-service]]
- [[hr-analytics]]

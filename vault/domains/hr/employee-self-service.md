---
type: module
domain: HR & People
panel: hr
module-key: hr.self-service
status: planned
color: "#4ADE80"
---

# Employee Self-Service

> Employee portal — view payslips, submit leave requests, update personal information, see your schedule, and access company resources — without going through HR.

**Panel:** `hr`
**Module key:** `hr.self-service`

## What It Does

Employee Self-Service gives every employee a personal dashboard within the HR panel where they can manage their own HR interactions without HR acting as an intermediary. Employees can view and download their payslips, check their leave balances and submit new requests, update their contact details and emergency info, see their published shift schedule, complete onboarding tasks, and access company-uploaded resources. All changes made via Self-Service that require approval (leave requests, personal info changes) go through the standard approval workflows defined in other modules.

## Features

### Core
- Personal dashboard: leave balance summary, upcoming shifts, pending tasks, latest payslip link
- Payslips: list of all payslips sorted by date — download PDF for any period
- Leave requests: submit new request, view pending/approved/rejected requests, check balance per leave type
- Personal info: update phone number, home address, emergency contact — changes logged and submitted for HR review
- Onboarding tasks: view and complete assigned onboarding tasks — mirrors Onboarding module progress

### Advanced
- Document vault: view all personal HR documents (employment contract, payslips, offer letter) stored via file-storage module — download any document
- Shift schedule: view published schedule for the current and next week — swap request button available on each shift
- Benefit enrollment: enrol in or change benefit plan selections during open enrollment periods — mirrors Compensation & Benefits module
- Profile photo: upload personal profile photo — used in Org Chart and across the panel
- Notification preferences: manage personal notification settings — mirrors Notifications module preferences

### AI-Powered
- Personal analytics: view own attendance trends, leave usage vs peers (anonymised), overtime hours over the last quarter
- Suggested leave: AI suggests good windows to take leave based on upcoming schedule density and team coverage

## Data Model

```erDiagram
    employee_documents {
        ulid id PK
        ulid employee_id FK
        ulid company_id FK
        string type
        string name
        string file_path
        boolean employee_visible
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `employee_documents` | Documents visible to employee in their self-service portal |
| `employee_visible` | HR controls whether the employee can see a document |
| All other data | Read from employee-profiles, leave-management, payroll, shift-scheduling modules |

## Permissions

- `hr.self-service.view-own`
- `hr.self-service.request-leave`
- `hr.self-service.update-personal-info`
- `hr.self-service.download-payslips`
- `hr.self-service.request-shift-swap`

## Filament

- **Resource:** None (all read from existing resources)
- **Pages:** `EmployeeDashboardPage` — personal dashboard with summary widgets
- **Custom pages:** `EmployeeDashboardPage`, `MyPayslipsPage`, `MyLeavePage`, `MySchedulePage`
- **Widgets:** `MyLeaveBalanceWidget`, `UpcomingShiftsWidget`, `LatestPayslipWidget`
- **Nav group:** (top-level in hr panel — visible to all employees, not just HR managers)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| BambooHR Self-Service | Employee self-service portal |
| Workday | Employee experience and self-service |
| HiBob | Employee self-service hub |
| Personio | Employee self-service |

## Related

- [[employee-profiles]]
- [[leave-management]]
- [[payroll]]
- [[shift-scheduling]]
- [[onboarding]]

---
domain: hr
module: payroll
feature: event-driven-inputs
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature — Event-Driven Inputs

## Purpose
Feed payroll records and runs from upstream HR/Finance domain events.

## Intended Behavior
Listeners (queued + `WithCompanyContext`, per [[../../../../architecture/event-bus]]):

| Event | Listener | Effect |
|---|---|---|
| `EmployeeHired` | `CreatePayrollRecordListener` | create stub payroll record, status `incomplete` |
| `EmployeeOffboarded` | `FinalPayListener` | flag final run incl. leave payout |
| `LeaveRequestApproved` | `UpdatePayrollDeductionsListener` | unpaid leave types only; paid leave is a no-op |
| `TimesheetApproved` | `ApplyTimesheetHoursListener` | hours feed hourly pay |
| `ExpenseApproved` | `AddReimbursementListener` | reimbursement line next run (employee_id non-null only) |

Soft deps (hr.leave, hr.time, finance.expenses) degrade gracefully when unbuilt — those inputs simply do not arrive.

## Tables / Permissions / Events
- Tables: `hr_payroll_employees`, `hr_payroll_runs` ([[../data-model]])
- Consumes: `EmployeeHired`, `EmployeeOffboarded`, `LeaveRequestApproved`, `TimesheetApproved`, `ExpenseApproved`
- Queue: [[../../../../infrastructure/queue-horizon]]

## UI

- **Kind**: background
- **Page**: none (queued listeners; results surface on the payroll-run page — [[payroll-run-lifecycle]])
- **Layout**: no standalone screen — listener effects (stub records, deduction lines, hourly hours, reimbursements) appear as rows/lines on the run detail and payroll-employee views
- **Key interactions**: none direct; upstream domain events trigger listeners automatically
- **States**: n/a (background) — failures land in Horizon `hr` queue and are visible via the failed-jobs dashboard; soft-dep events simply never arrive when those modules are unbuilt
- **Gating**: no UI gate; listeners run under `WithCompanyContext`; produced data is gated by the surfacing screen's `hr.payroll.*` permissions

## Data

- Owns / writes: `hr_payroll_employees`, `hr_payroll_runs` (via listeners)
- Reads: employee identity from the inbound event payloads (`company_id`, `employee_id`) — never another domain's tables
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]])

## Relations

- Consumes: `EmployeeHired` from `hr.profiles` → create stub payroll record (`incomplete`); `EmployeeOffboarded` from `hr.profiles` → flag final run + leave payout; `LeaveRequestApproved` from `hr.leave` → unpaid-leave deduction; `TimesheetApproved` from `hr.time` → hourly pay hours; `ExpenseApproved` from `finance.expenses` → reimbursement line
- Feeds: none (this feature is the consuming side; the run feed to Finance is [[ledger-journal-posting]])
- Shared entity: `hr_employees` read via `EmployeeService` (hr.profiles)

## Test Checklist

### Unit
- [ ] `UpdatePayrollDeductionsListener` acts on unpaid leave types only; paid leave is a no-op
- [ ] `AddReimbursementListener` skips events with a null `employee_id`

### Feature (Pest)
- [ ] `EmployeeHired` creates a stub payroll record with status `incomplete`
- [ ] Listeners run under `WithCompanyContext` — effects are tenant-scoped to the event's `company_id`
- [ ] Soft-dep events (`LeaveRequestApproved`, `TimesheetApproved`, `ExpenseApproved`) absent when those modules are unbuilt → payroll degrades gracefully (inputs simply do not arrive)

Back to [[../_module]].

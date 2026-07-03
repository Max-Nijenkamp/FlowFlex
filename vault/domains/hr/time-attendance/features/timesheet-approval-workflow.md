---
domain: hr
module: time-attendance
feature: timesheet-approval-workflow
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature — Timesheet Approval Workflow

## Purpose

Weekly timesheet lifecycle: employees submit, managers approve or reject before payroll runs.

## Behavior

- Weekly view: employee fills hours per day per project/task; entries roll up into one `hr_timesheet` per `(employee, week_start)`.
- `submitWeek(SubmitTimesheetData)` requires all days closed (no running timers) and locks entries.
- State machine (`draft → submitted → approved | rejected → submitted`) — see [[../architecture]].
- Approve requires `hr.time.approve`; **approver ≠ owner**; audited. Fires `TimesheetApproved`.
- Reject returns to employee with a note and unlocks entries.

## Tables / Permissions / Events

- Tables: `hr_timesheets` (+ linked `hr_time_entries`)
- Permissions: `hr.time.submit-own`, `hr.time.approve`, `hr.time.manage`
- Events: fires `TimesheetApproved` on approval (payload → [[../api]])
- Surfaces: `TimesheetResource` (pending-approval tab, approve/reject actions)

## UI

- **Kind**: custom-page (approval queue) *(TimesheetResource with pending-approval tab is the fallback shape)*
- **Page**: "Timesheet Approvals" (`/hr/timesheets`)
- **Layout**: weekly grid where an employee fills hours per day; managers get a pending-approval queue (employee, week, total minutes, overtime) with approve/reject actions (reject opens a note modal). Rolls up entries into one `hr_timesheet` per `(employee, week_start)`.
- **Key interactions**: `submitWeek` (all days closed, locks entries); manager approve (fires `TimesheetApproved`) or reject (returns to employee, unlocks entries).
- **States**: empty ("No timesheets awaiting approval") · loading (grid/queue skeleton) · error (toast on running-timer block / invalid transition) · selected (row opens week detail with day breakdown).
- **Gating**: submit own requires `hr.time.submit-own`; approve requires `hr.time.approve` (approver ≠ owner, audited); manage requires `hr.time.manage`. Custom page declares `canAccess()`.

## Data

- Owns / writes: `hr_timesheets` (locks/links `hr_time_entries` on submit)
- Reads: reads `hr_time_entries` (roll-up) within this module; reads `hr_employees` via EmployeeService
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]])

## Relations

- Consumes: none
- Feeds: `TimesheetApproved` → consumed by [[../../payroll/_module|hr.payroll]] (hourly pay calculation)
- Shared entity: `hr_employees` (read via EmployeeService)

## Test Checklist

### Unit
- [ ] `submitWeek` requires all days closed (no running timers) and locks entries
- [ ] State transitions valid only along `draft → submitted → approved` / `rejected → submitted`

### Feature (Pest)
- [ ] Approve requires `hr.time.approve` and approver ≠ owner (audited); fires `TimesheetApproved`
- [ ] Reject returns to employee with a note and unlocks entries; concurrent approve serialized by `lockForUpdate`; company A cannot approve company B timesheets

### Livewire
- [ ] Submit requires `hr.time.submit-own`; approve/reject actions require `hr.time.approve`; approval page `canAccess()` gated

## Related

- [[../_module]]
- [[../architecture]]

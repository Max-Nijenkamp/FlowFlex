---
domain: hr
module: leave-management
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Leave Management

> [!warning] Rebuild blueprint
> HR domain code was stripped per [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]]. This spec is the **intended** rebuild blueprint — nothing here is built, shipped, or tested yet. `build-status: planned`.

## Purpose

Leave requests, multi-level approval workflows, leave balances, accrual rules, and a team calendar view. Employees will submit requests via Self-Service; managers will approve in `/hr`. Intended to be one of the two most-used HR modules (with profiles) — the land-and-expand entry point for many customers.

## Intended Behavior (summary)

- Configurable leave types per company (annual, sick, parental, unpaid, custom).
- Request lifecycle `draft → submitted → approved | rejected | cancelled` via state machine.
- Multi-level (configurable) approval chain: employee → manager → HR.
- Leave balances with accrual, carry-over, and balance cap.
- Team calendar (monthly/weekly) of approved leave, overlap detection, public-holiday import.
- Approval/rejection notifications via in-app + email.

Detail lives in the entity notes below.

## Dependency Summary

| Type | Module | Why |
|---|---|---|
| Hard | [[../employee-profiles/_module\|hr.profiles]] | Requests belong to employees; manager chain from `manager_id` |
| Hard | [[../../core/billing-engine/_module\|core.billing]] | Module gating via `hasModule('hr.leave')` |
| Hard | [[../../core/rbac/_module\|core.rbac]] | Approval permissions |
| Hard | [[../../core/notifications/_module\|core.notifications]] | Approval/rejection notifications (in-app + email) |
| Soft | [[../payroll/_module\|hr.payroll]] | Consumes `LeaveRequestApproved` for deductions; without it the event fires with no listener |
| Soft | [[../shift-scheduling/_module\|hr.shifts]] | Consumes `LeaveRequestApproved` to block shifts; degrades to no shift blocking |
| Soft | [[../employee-self-service/_module\|hr.self-service]] | Submission UI for employees; without it HR staff submit on behalf *(assumed)* |

## Cross-Domain Edges

| Direction | Event | Counterpart | Effect |
|---|---|---|---|
| Fires | `LeaveRequestApproved` | [[../payroll/_module\|hr.payroll]] | leave deductions on payroll run |
| Fires | `LeaveRequestApproved` | [[../shift-scheduling/_module\|hr.shifts]] | blocks/unassigns shifts overlapping approved leave |
| Consumes | none | — | — |

**Data ownership:** owns `hr_leave_types`, `hr_leave_balances`, `hr_leave_requests`. Reads `hr_employees` (via EmployeeService) and public-holiday reference data; never writes another domain's tables — [[../../../security/data-ownership]].

## Entity Notes

- [[architecture]] — services, actions, state machine, flow diagram
- [[data-model]] — tables, columns, ERD
- [[api]] — events, DTOs, service surface
- [[security]] — permissions, authz, tenancy
- [[unknowns]] — open questions + assumptions

## Feature Notes

- [[features/leave-types]]
- [[features/leave-balances]]
- [[features/leave-request-workflow]]
- [[features/team-calendar]]
- [[features/accrual-jobs]]

## Siblings

- [[../employee-profiles/_module]]
- [[../employee-self-service/_module]]
- [[../payroll/_module]]
- [[../shift-scheduling/_module]]
- [[../../core/notifications/_module]]

## Build Manifest

```
database/migrations/xxxx_create_hr_leave_types_table.php
database/migrations/xxxx_create_hr_leave_balances_table.php
database/migrations/xxxx_create_hr_leave_requests_table.php
app/Models/HR/{LeaveType,LeaveBalance,LeaveRequest}.php
app/States/HR/LeaveRequest/{LeaveRequestState,Draft,Submitted,Approved,Rejected,Cancelled}.php
app/Data/HR/{SubmitLeaveRequestData,ApproveLeaveRequestData,RejectLeaveRequestData,LeaveRequestData,LeaveBalanceData}.php
app/Contracts/HR/LeaveServiceInterface.php
app/Services/HR/LeaveService.php
app/Exceptions/HR/{InsufficientLeaveBalanceException,OverlappingLeaveException,CannotApproveOwnRequestException}.php
app/Events/HR/LeaveRequestApproved.php
app/Mail/HR/{LeaveApprovedMail,LeaveRejectedMail}.php
app/Console/Commands/HR/{AccrueLeaveBalancesCommand,CarryOverLeaveBalancesCommand}.php
app/Filament/HR/Resources/{LeaveRequestResource,LeaveBalanceResource,LeaveTypeResource}.php
app/Filament/HR/Pages/LeaveCalendarPage.php
app/Filament/HR/Widgets/PendingApprovalsWidget.php
database/factories/HR/{LeaveTypeFactory,LeaveBalanceFactory,LeaveRequestFactory}.php
tests/Feature/HR/{LeaveRequestTest,LeaveBalanceTest,LeaveCalendarTest}.php
```

## Related

- [[../../../architecture/event-bus]]
- [[../../../architecture/patterns/states]]
- [[../../../architecture/patterns/interface-service]]
- [[../../../architecture/patterns/custom-pages]]
- [[../../../glossary]]

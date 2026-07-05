---
domain: hr
module: leave-management
type: module
build-status: in-progress
status: wip
color: "#4ADE80"
updated: 2026-07-05
---

# Leave Management

Leave requests, multi-level approval workflows, leave balances, accrual rules, and a team calendar view. Employees submit requests via Self-Service; managers approve in `/hr`. Intended to be one of the two most-used HR modules (with profiles) — the land-and-expand entry point for many customers.

> [!warning] Rebuild blueprint
> HR domain code was stripped per [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]]. This spec is the **intended** rebuild blueprint — nothing here is built, shipped, or tested yet. `build-status: planned`.

---

## Module-key

`hr.leave`

**Priority:** v1-core
**Panel:** hr
**Permission prefix:** `hr.leave`
**Tables:** `hr_leave_types`, `hr_leave_balances`, `hr_leave_requests`
**Nav group:** Leave

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../employee-profiles/_module\|hr.profiles]] | Requests belong to employees; manager chain from `manager_id` |
| Hard | [[../../core/billing-engine/_module\|core.billing]] | Module gating via `hasModule('hr.leave')` |
| Hard | [[../../core/rbac/_module\|core.rbac]] | Approval permissions |
| Hard | [[../../core/notifications/_module\|core.notifications]] | Approval/rejection notifications (in-app + email) |
| Soft | [[../payroll/_module\|hr.payroll]] | Consumes `LeaveRequestApproved` for deductions; without it the event fires with no listener |
| Soft | [[../shift-scheduling/_module\|hr.shifts]] | Consumes `LeaveRequestApproved` to block shifts; degrades to no shift blocking |
| Soft | [[../employee-self-service/_module\|hr.self-service]] | Submission UI for employees; without it HR staff submit on behalf *(assumed)* |

---

## Core Features

- Configurable leave types per company (annual, sick, parental, unpaid, custom) — [[features/leave-types|Leave Types]]
- Request lifecycle `draft → submitted → approved | rejected | cancelled` via state machine — [[features/leave-request-workflow|Leave Request Workflow]]
- Multi-level (configurable) approval chain: employee → manager → HR
- Leave balances with accrual, carry-over, and balance cap — [[features/leave-balances|Leave Balances]]
- Team calendar (monthly/weekly) of approved leave, overlap detection, public-holiday import — [[features/team-calendar|Team Calendar]]
- Scheduled accrual + carry-over commands — [[features/accrual-jobs|Accrual & Carry-Over Jobs]]
- Approval/rejection notifications via in-app + email

---

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

Filament artifacts (resources, calendar page, widget) and per-write-path concurrency tiers: [[architecture]].

---

## Test Checklist

- [ ] Tenant isolation: company A approver cannot see/approve company B requests
- [ ] Module gating: artifacts hidden when `hr.leave` inactive
- [ ] Approve transitions state, moves balance `pending → taken`, and fires `LeaveRequestApproved` with the contract payload
- [ ] Approver cannot approve their own request (`CannotApproveOwnRequestException`)
- [ ] Reject requires a reason, releases pending balance, notifies with reason
- [ ] Insufficient balance on submit throws `InsufficientLeaveBalanceException`
- [ ] Concurrent approvals serialized via pessimistic lock — no double-decrement of a balance
- [ ] `requires_approval = false` type auto-approves on submit
- [ ] Public holidays excluded from `days_requested`; overlap warning surfaces on overlapping approved leave
- [ ] Accrual command idempotent (run twice = same balances)

---

## Cross-Domain Edges

| Direction | Event | Counterpart | Effect |
|---|---|---|---|
| Fires | `LeaveRequestApproved` | [[../payroll/_module\|hr.payroll]] | leave deductions on payroll run |
| Fires | `LeaveRequestApproved` | [[../shift-scheduling/_module\|hr.shifts]] | blocks/unassigns shifts overlapping approved leave |
| Consumes | none | — | — |

**Data ownership:** owns `hr_leave_types`, `hr_leave_balances`, `hr_leave_requests`. Reads `hr_employees` (via EmployeeService) and public-holiday reference data; never writes another domain's tables — [[../../../security/data-ownership]].

---

## Related

- Entity notes: [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[unknowns]]
- Sibling modules: [[../employee-profiles/_module]] · [[../employee-self-service/_module]] · [[../payroll/_module]] · [[../shift-scheduling/_module]] · [[../../core/notifications/_module]]
- [[../../../architecture/event-bus]]
- [[../../../architecture/patterns/states]]
- [[../../../architecture/patterns/interface-service]]
- [[../../../architecture/patterns/custom-pages]]
- [[../../../glossary]]

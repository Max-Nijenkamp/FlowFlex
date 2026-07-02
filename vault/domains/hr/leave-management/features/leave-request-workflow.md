---
domain: hr
module: leave-management
feature: leave-request-workflow
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Feature — Leave Request Workflow & Approval

## Purpose

The core request lifecycle: submit, multi-level approval, rejection, cancellation — driven by a spatie/laravel-model-states state machine.

## Behavior

- Lifecycle: `draft → submitted → approved | rejected | cancelled`. Full transition table + diagram in [[../architecture]].
- Multi-level approval: configurable chain (employee → manager → HR); v1 intends single-level (direct manager) *(assumed)* — see [[../unknowns]].
- Approve sets `approved_by`/`approved_at`, moves balance pending → taken, fires `LeaveRequestApproved`, notifies.
- Reject requires a reason, releases pending balance, notifies with reason.
- Approver cannot approve their own request (`CannotApproveOwnRequestException`).
- Invalid transitions throw (`InvalidStateTransitionException`).
- Notifications (in-app + email) on approval/rejection via [[../../../../infrastructure/mail]] + [[../../core/notifications/_module\|core.notifications]].

## UI

- **Kind**: simple-resource (LeaveRequestResource with actions) + dashboard widget
- **Page**: "Leave Requests" (`/hr/leave-requests`)
- **Layout**: table with **Pending** and **All** tabs — employee, type, dates, days, status badge; approve & reject table actions (reject opens a reason modal). Companion `PendingApprovalsWidget` on the `/hr` dashboard shows the current approver's pending count.
- **Key interactions**: submit a request (draft → submitted); approver approves or rejects (with reason) from the Pending tab; cancel own request.
- **States**: empty ("No pending requests" on Pending tab) · loading (table skeleton, widget spinner) · error (inline banner; blocked transition toast) · selected (row opens detail with approve/reject).
- **Gating**: visible with `hr.leave.view`; create requires `hr.leave.create`; approve requires `hr.leave.approve`; reject requires `hr.leave.reject`. Approver cannot approve own request (`CannotApproveOwnRequestException`).

## Data

- Owns / writes: `hr_leave_requests` (writes `hr_leave_balances` pending/taken transitions within this module)
- Reads: reads `hr_leave_types` (approval rule) within this module; reads `hr_employees` + manager chain via EmployeeService
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]])

## Relations

- Consumes: none
- Feeds: `LeaveRequestApproved` → consumed by [[../../payroll/_module|hr.payroll]] (deductions) and [[../../shift-scheduling/_module|hr.shifts]] (blocks/unassigns shifts on approved leave)
- Shared entity: `hr_employees` + manager chain (read via EmployeeService)

## Related

- Table: `hr_leave_requests` (see [[../data-model]])
- DTOs: `SubmitLeaveRequestData`, `ApproveLeaveRequestData`, `RejectLeaveRequestData` (see [[../api]])
- Event: fires `LeaveRequestApproved` (see [[../api]])
- Permissions: `hr.leave.create`, `hr.leave.approve`, `hr.leave.reject` (see [[../security]])
- UI: `LeaveRequestResource` (#1 CRUD — Pending/All tabs, approve & reject table actions); `PendingApprovalsWidget` (#6 dashboard widget, count for current approver *(assumed)*)
- Tests: approve transitions state + fires event with contract payload; approver cannot approve own; invalid transitions throw
- Back to [[../_module]]

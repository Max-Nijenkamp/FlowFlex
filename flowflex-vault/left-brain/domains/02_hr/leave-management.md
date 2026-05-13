---
type: module
domain: HR & People
panel: hr
phase: 2
status: complete
migration_range: 100000–109999
last_updated: 2026-05-12
right_brain_log: "[[builder-log-hr-phase2]]"
---

# Leave Management

Manages employee leave policies, balances, and requests with approval workflow.

## Module Key
`hr.leave`

## Features
- Leave policies (annual, sick, maternity, paternity, unpaid, other)
- Leave balances per employee per policy per year
- Leave request lifecycle: pending → approved/rejected/cancelled
- Balance tracking: allocated, used, pending days
- Approval workflow with approver tracking

## Files
- Migrations: `100002_create_leave_policies_table`, `100003_create_leave_balances_table`, `100004_create_leave_requests_table`
- Models: `LeavePolicy`, `LeaveBalance`, `LeaveRequest`
- Service: `App\Services\HR\LeaveService`
- Interface: `App\Contracts\HR\LeaveServiceInterface`
- DTO: `RequestLeaveData`
- Events: `LeaveRequested`, `LeaveApproved`, `LeaveRejected`
- Filament: `LeavePolicyResource`, `LeaveRequestResource`
- Tests: `tests/Feature/HR/LeaveServiceTest.php`

---
domain: hr
module: leave-management
feature: leave-balances
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Feature — Leave Balances

## Purpose

Per employee, per leave type, per year ledger of allocated / taken / pending days, plus the balance report.

## Behavior

- `allocated_days` = accrual + carry-over + manual adjustment.
- Submit decrements available into `pending_days`; approve moves pending → `taken_days`; reject/cancel releases pending (see [[leave-request-workflow]]).
- `remaining_days` is computed: `allocated − taken − pending`.
- Balance report shows days taken / remaining / pending per employee per type.
- `submit()` throws `InsufficientLeaveBalanceException` when balance is too low.

## UI

- **Kind**: simple-resource (read-only table; may surface a summary widget)
- **Page**: "Leave Balances" (`/hr/leave-balances`)
- **Layout**: read-only table — employee, leave type, year, allocated / taken / pending / remaining (computed) columns; filter by employee, type, year. No create/edit.
- **Key interactions**: filter and inspect balances; drill into an employee's per-type ledger; managers view team balances.
- **States**: empty ("No balances for this period" once accrual runs) · loading (table skeleton) · error (inline banner) · selected (row expands to per-type breakdown).
- **Gating**: visible with `hr.leave.view`; view-any/team requires `hr.leave.view-any` *(assumed)*; no write permission (system-maintained).

## Data

- Owns / writes: `hr_leave_balances`
- Reads: reads `hr_leave_types` (type meta) within this module; reads `hr_employees` via EmployeeService for names
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]])

## Relations

- Consumes: none directly *(mutated in-process by [[leave-request-workflow]] submit/approve/reject and by [[accrual-jobs]])*
- Feeds: none
- Shared entity: `hr_employees` (read via EmployeeService)

## Related

- Table: `hr_leave_balances` (see [[../data-model]])
- DTO: `LeaveBalanceData`; service: `balanceFor()` (see [[../api]], [[../architecture]])
- UI: `LeaveBalanceResource` (#1 CRUD, read-only — no create/edit)
- Accrual/carry-over jobs: [[accrual-jobs]]
- Tests: submit decrements available into pending; reject/cancel releases it
- Back to [[../_module]]

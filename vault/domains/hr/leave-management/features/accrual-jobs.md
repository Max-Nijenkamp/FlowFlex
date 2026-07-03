---
domain: hr
module: leave-management
feature: accrual-jobs
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature — Accrual & Carry-Over Jobs

## Purpose

Scheduled background commands that build leave balances over time: monthly accrual and yearly carry-over.

## Behavior

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `AccrueLeaveBalancesCommand` | `default` | monthly, 1st 02:00 | upsert on `(company, employee, type, year)` — safe to re-run |
| `CarryOverLeaveBalancesCommand` | `default` | yearly, Jan 1 03:00 | skips rows already carried (`allocated_days` includes carry marker) *(assumed)* |

- Accrual rate from `hr_leave_types.accrual_days_per_year`; carry-over capped by `carry_over_days`.
- Service entry point: `accrueMonthly()` (see [[../architecture]]).

## UI

- **Kind**: background (scheduled console commands — no UI)
- **Page**: none (`AccrueLeaveBalancesCommand` monthly 1st 02:00; `CarryOverLeaveBalancesCommand` yearly Jan 1 03:00, `default` queue)
- **Layout**: no screen; outcomes are visible in [[leave-balances]] (allocated_days changes) and job status in Horizon.
- **Key interactions**: none (scheduled); operators may re-run manually — commands are idempotent (upsert on `(company, employee, type, year)`).
- **States**: n/a (no interactive UI) — failure surfaces via Horizon/queue monitoring; results reflected in balance rows.
- **Gating**: no permission (system/scheduler context); runs per-company under `WithCompanyContext` *(assumed)*.

## Data

- Owns / writes: `hr_leave_balances` (accrual + carry-over upserts)
- Reads: reads `hr_leave_types` (accrual_days_per_year, carry_over_days) within this module; reads `hr_employees` via EmployeeService for who to accrue
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]])

## Relations

- Consumes: none *(scheduler-driven, not event-driven)*
- Feeds: none
- Shared entity: `hr_employees` (read via EmployeeService)

## Test Checklist

### Unit
- [ ] Monthly accrual amount derived from `hr_leave_types.accrual_days_per_year`
- [ ] Carry-over capped by `carry_over_days`

### Feature (Pest)
- [ ] `AccrueLeaveBalancesCommand` is idempotent — running twice yields the same balances (upsert on `(company, employee, type, year)`)
- [ ] `CarryOverLeaveBalancesCommand` carries unused days up to the cap and skips already-carried rows
- [ ] Commands run per-company under `WithCompanyContext`; a company only accrues its own employees (tenant isolation)

## Related

- Table: `hr_leave_balances` (see [[../data-model]], [[leave-balances]])
- Infra: [[../../../../infrastructure/queue-horizon]]
- Tests: accrual command idempotent (run twice = same balances)
- Back to [[../_module]]

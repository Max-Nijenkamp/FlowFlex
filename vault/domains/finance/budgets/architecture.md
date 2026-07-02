---
domain: finance
module: budgets
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Budgets — Architecture

`BudgetServiceInterface` → `BudgetService` (Interface→Service per [[../../../architecture/patterns/interface-service]]) owns budget creation, approval, revision, variance, and the `remaining()` check. The module owns budget + line tables but reads actuals from the general ledger.

## Money handling

All amounts are integer **minor units** (cents) in `bigint` columns, manipulated with `brick/money` — never raw float math. Variance (`actual − budget`) and `remaining()` are integer-cent arithmetic; variance % is derived last. See [[../../../architecture/packages]] (brick/money).

## Variance computation

- `variance(budgetId, period)` sums journal lines from the ledger per account/period (actuals) and subtracts budgeted lines: `variance_cents = actual_cents − budgeted_cents`, plus `variance_percent`.
- `remaining(accountId, period)` returns budgeted minus consumed-to-date for that account/period, consumed by procurement/workforce budget checks.

## Versioning & approval

- Budgets move draft → approved. Approved budget lines are immutable — changes go through `revise()`, which creates a new `version` row copying the lines and leaving the prior version intact *(assumed: approved lines locked)*.
- `copyFromYear(fromYear, toYear)` bootstraps a new budget from a prior year's lines.

## Alerts & caching

- `BudgetVarianceAlertCommand` (notifications queue, monthly 2nd 08:00): fires once per (budget, period) over threshold via a flag column, using core.notifications. Default threshold 10% *(assumed)*.
- Variance results cache under `company:{id}:finance:budget-variance:{budget}:{period}` (1 h TTL), busted by journal posting in-period or a budget edit. See [[../../../architecture/caching]].

See [[../../../architecture/patterns/custom-pages]], [[../../../architecture/queue-jobs]], [[data-model]], [[api]].

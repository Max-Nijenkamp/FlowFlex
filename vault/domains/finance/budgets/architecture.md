---
domain: finance
module: budgets
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
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

## Filament Artifacts

**Nav group:** Planning

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `BudgetResource` | #1 CRUD resource | tweaks: custom-header-actions (approve / revise / copy-from-year), inline-relation-repeater (per-account/period line grid) | list filters: fiscal year, scope, status; version column; approved versions read-only |
| `BudgetVariancePage` | #9 custom page | [[../../../architecture/patterns/page-blueprints#Report Builder / Query UI]] — per account/period budgeted/actual/variance grid + journal-entry drilldown; realtime none | `/finance/budgets/variance` |
| `BudgetVarianceWidget` | #6 dashboard widget | [[../../../architecture/patterns/page-blueprints#Dashboard]] | surfaces over-budget accounts; polling 30–60s |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('finance.budgets.view-any') && BillingService::hasModule('finance.budgets')`
per [[../../../architecture/filament-patterns]] #1. `BudgetVariancePage` is a custom page and MUST state this
explicitly — Filament does not auto-gate custom pages. No public/portal surface in this module.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Budget + line CRUD (draft, form/API) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Approve (draft → approved; locks lines) | Pessimistic | `DB::transaction()` + `lockForUpdate()`, re-read, validate, write — status transition per [[../../../architecture/patterns/states]] |
| Revise (new version copying lines) / copy-from-year | Pessimistic | `DB::transaction()` + `lockForUpdate()` on the budget; atomic version insert under `unique (company_id, name, fiscal_year, version)` |
| Variance / `remaining()` (derived from ledger actuals) | n-a | read-only computation over cached ledger sums — no writes |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

See [[../../../architecture/patterns/custom-pages]], [[../../../architecture/queue-jobs]], [[data-model]], [[api]].

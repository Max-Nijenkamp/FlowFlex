---
domain: finance
module: budgets
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Budgets — DTOs, Services & Events

## DTOs

### CreateBudgetData
| Field | Type | Validation |
|---|---|---|
| name | string | required |
| fiscal_year | int | required |
| scope_type | string | in:company,department,project |
| scope_id | ulid nullable | required unless `scope_type = company` |
| lines | array | `[{account_id, period, budgeted_cents}]` |

### BudgetData (output)
Budget header + version + status + lines.

### BudgetVarianceData (output)
- Per account/period: `budgeted_cents`, `actual_cents`, `variance_cents`, `variance_percent`.

DTOs use `spatie/laravel-data` per [[../../../architecture/patterns/dto-pattern]].

## Services & Actions

`BudgetServiceInterface` → `BudgetService`:

- `create(CreateBudgetData $data): BudgetData`
- `approve(string $budgetId): void` — draft → approved; approved lines become immutable.
- `revise(string $budgetId): BudgetData` — new version, copies lines, preserves prior version.
- `variance(string $budgetId, ?string $period = null): BudgetVarianceData` — actuals summed from journal lines (brick/money).
- `remaining(string $accountId, string $period): Money` — consumed by procurement/workforce budget checks.
- `copyFromYear(int $fromYear, int $toYear): BudgetData`

## Events

This module fires and consumes no cross-domain events. It reads ledger actuals directly within the finance domain; procurement/workforce integrate by calling `remaining()`, not via events. Variance alerts route through core.notifications (`BudgetVarianceAlertCommand`).

See [[security]], [[features/variance-tracking]], [[features/budget-versioning]], [[../forecasting/_module]].

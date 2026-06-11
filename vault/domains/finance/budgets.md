---
type: module
domain: Finance & Accounting
domain-key: finance
panel: finance
module-key: finance.budgets
status: complete
priority: v1
depends-on: [finance.ledger, core.billing, core.rbac, core.notifications]
soft-depends: [finance.forecasting, procurement.requisitions, hr.workforce]
fires-events: []
consumes-events: []
patterns: [custom-pages, money]
tables: [fin_budgets, fin_budget_lines]
permission-prefix: finance.budgets
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Budgets

Department and company-level budgets with actual-vs-budget variance tracking. Absorbed from the former FP&A domain.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/finance/general-ledger\|finance.ledger]] | actuals from journal lines per account |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/notifications\|core.notifications]] | gating, permissions, variance alerts |
| Soft | [[domains/finance/forecasting\|finance.forecasting]] | rolling forecast view |
| Soft | procurement.requisitions, [[domains/hr/workforce-planning\|hr.workforce]] | consume budget checks via `BudgetService::remaining()` |

---

## Core Features

- Budget record: name, fiscal year, scope (company/department/project)
- Budget lines: per GL account, per period (monthly breakdown)
- Import actuals from General Ledger automatically
- Variance report: budget vs actual, absolute and %
- Variance alerts: notify when actual exceeds budget by threshold (default 10% *(assumed)*)
- Budget approval workflow (draft → approved, `finance.budgets.approve`)
- Copy budget from previous year as starting point
- Budget versions (revisions during the year — new version row, old kept)
- Rolling forecast view (links Forecasting)

---

## Data Model

### fin_budgets

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| name | string | |
| fiscal_year | int | |
| scope_type | string | company / department / project |
| scope_id | ulid nullable | dept/project ref |
| status | string default `draft` | draft / approved |
| version | int default 1 | unique `(company_id, name, fiscal_year, version)` |
| deleted_at | timestamp nullable | |

### fin_budget_lines

| Column | Type | Notes |
|---|---|---|
| id, budget_id FK, company_id | ulid | |
| account_id | ulid FK fin_accounts | |
| period | string | `YYYY-MM`, unique `(budget_id, account_id, period)` |
| budgeted_cents | bigint | |

---

## DTOs

### CreateBudgetData — name, fiscal_year, scope_type (in set), scope_id (required unless company), lines[{account_id, period, budgeted_cents}]
### BudgetVarianceData (output) — per account/period: budgeted_cents, actual_cents, variance_cents, variance_percent

## Services & Actions

Interface→Service: `BudgetServiceInterface` → `BudgetService`.

- `create(CreateBudgetData $data)` / `approve(string $budgetId)` / `revise(string $budgetId): BudgetData` (new version, copies lines)
- `variance(string $budgetId, ?string $period = null): BudgetVarianceData` — actuals summed from journal lines
- `remaining(string $accountId, string $period): Money` — consumed by procurement/workforce budget checks
- `copyFromYear(int $fromYear, int $toYear): BudgetData`

---

## Filament

**Nav group:** Planning

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `BudgetResource` | #1 CRUD resource | line editor grid; approve action; version column |
| `BudgetVariancePage` | #9 report custom page | variance highlighting, drill-down |
| `BudgetVarianceWidget` | #6 widget | over-budget alerts |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('finance.budgets.view-any') && BillingService::hasModule('finance.budgets')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

---

## Permissions

`finance.budgets.view-any` · `finance.budgets.create` · `finance.budgets.update` · `finance.budgets.approve`

---

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `BudgetVarianceAlertCommand` | notifications | monthly, 2nd 08:00 | once per (budget, period) over threshold *(flag column)* |

## Caching

| Key | TTL | Invalidated by |
|---|---|---|
| `company:{id}:finance:budget-variance:{budget}:{period}` | 1 h | journal posting in period (writer busts), budget edit |

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Variance = actual − budget per account/period from GL fixtures (brick/money)
- [ ] Revision creates new version, preserves old
- [ ] Approved budget lines immutable (revise instead) *(assumed)*
- [ ] Alert fires once per period over threshold
- [ ] `remaining()` correct for partially consumed account/period

---

## Build Manifest

```
database/migrations/xxxx_create_fin_budgets_table.php
database/migrations/xxxx_create_fin_budget_lines_table.php
app/Models/Finance/{Budget,BudgetLine}.php
app/Data/Finance/{CreateBudgetData,BudgetData,BudgetVarianceData}.php
app/Contracts/Finance/BudgetServiceInterface.php
app/Services/Finance/BudgetService.php
app/Console/Commands/Finance/BudgetVarianceAlertCommand.php
app/Filament/Finance/Resources/BudgetResource.php
app/Filament/Finance/Pages/BudgetVariancePage.php
app/Filament/Finance/Widgets/BudgetVarianceWidget.php
database/factories/Finance/{BudgetFactory,BudgetLineFactory}.php
tests/Feature/Finance/{BudgetVarianceTest,BudgetVersionTest}.php
```

---

## Related

- [[domains/finance/general-ledger]]
- [[domains/finance/forecasting]]
- [[domains/procurement/requisitions]]
- [[domains/hr/workforce-planning]]

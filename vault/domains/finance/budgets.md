---
type: module
domain: Finance & Accounting
panel: finance
module-key: finance.budgets
status: planned
color: "#4ADE80"
---

# Budgets

Department and company-level budgets with actual-vs-budget variance tracking. Absorbed from the former FP&A domain.

## Core Features

- Budget record: name, fiscal year, scope (company/department/project)
- Budget lines: per GL account, per period (monthly breakdown)
- Import actuals from General Ledger automatically
- Variance report: budget vs actual, absolute and %
- Variance alerts: notify when actual exceeds budget by threshold
- Budget approval workflow
- Copy budget from previous year as starting point
- Budget versions (revisions during the year)
- Rolling forecast view (links Forecasting)

## Data Model

| Table | Key Columns |
|---|---|
| `fin_budgets` | company_id, name, fiscal_year, scope_type, scope_id, status, version |
| `fin_budget_lines` | budget_id, company_id, account_id, period, budgeted_cents |

```mermaid
erDiagram
    fin_budgets {
        ulid id PK
        ulid company_id FK
        string name
        int fiscal_year
        string scope_type
        string status
    }
    fin_budget_lines {
        ulid id PK
        ulid budget_id FK
        ulid account_id FK
        string period
        int budgeted_cents
    }
    fin_budgets ||--o{ fin_budget_lines : "has"
```

## Filament

**Nav group:** Planning

- `BudgetResource` — create, edit budget lines, approve
- `BudgetVariancePage` (custom page) — budget vs actual with variance highlighting
- `BudgetVarianceWidget` — over-budget alerts

## Cross-Domain

- Actuals pulled from [[domains/finance/general-ledger]]
- Budget check consumed by Procurement requisitions, HR workforce planning

## Related

- [[domains/finance/general-ledger]]
- [[domains/finance/forecasting]]
- [[domains/procurement/requisitions]]

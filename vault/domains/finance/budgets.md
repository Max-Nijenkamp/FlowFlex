---
type: module
domain: Finance & Accounting
panel: finance
module-key: finance.budgets
status: planned
color: "#4ADE80"
---

# Budgets

> Department and project budgets, period-by-period allocation, variance tracking against actuals, and configurable overspend alerts.

**Panel:** `finance`
**Module key:** `finance.budgets`

## What It Does

The Budgets module lets Finance set spending targets per department or project and track actual spending against those targets in real time. A budget is created for a financial year (or shorter period) and broken down by month or quarter. Each budget line maps to a chart-of-accounts expense category. As expenses and invoices are posted to the General Ledger, actual spend is compared to the budget line automatically. Overspend alerts fire when actuals exceed a configurable threshold (e.g. 90% of budget consumed with 60% of the period elapsed).

## Features

### Core
- Budget creation: name, period (annual/quarterly), owner (department or project), currency
- Budget lines: one line per expense category (GL account), with monthly or quarterly allocation amounts
- Actuals feed: actual spend per budget line pulled automatically from GL postings for the period
- Variance view: budget vs actual per line — amount variance and percentage variance — colour-coded
- Budget status: draft / approved / active — only approved budgets show in variance tracking

### Advanced
- Budget revision: create a revised budget mid-year (keeping original as baseline) — variance compares against revised budget; original preserved
- Approval workflow: draft budget submitted by department manager → approved by Finance Director before going active
- Alerts: configurable threshold (e.g. 80%) — when actuals reach threshold, budget owner and finance team notified
- Rollup: department budgets roll up to a company-wide budget summary — total budget vs total actuals across all departments
- Headcount integration: pull planned headcount costs from HR Workforce Planning module as salary expense budget lines

### AI-Powered
- Spend forecast: based on current actuals and historical run rate, AI forecasts end-of-period actuals for each budget line — highlights lines likely to overrun before the period ends
- Budget template suggestions: when creating a new departmental budget, AI pre-populates budget lines and amounts based on prior year actuals for the same department

## Data Model

```erDiagram
    budgets {
        ulid id PK
        ulid company_id FK
        string name
        string period
        string scope_type
        ulid scope_id FK
        string currency
        string status
        ulid approved_by FK
        timestamp approved_at
        timestamps created_at/updated_at
    }

    budget_lines {
        ulid id PK
        ulid budget_id FK
        ulid account_id FK
        string period_key
        decimal budgeted_amount
        decimal actual_amount
        decimal variance_amount
        decimal variance_pct
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `scope_type` | department / project |
| `period_key` | e.g. `2026-01` for monthly, `2026-Q1` for quarterly |
| `actual_amount` | Pulled from GL actuals — updated on each journal post |

## Permissions

- `finance.budgets.view`
- `finance.budgets.create`
- `finance.budgets.approve`
- `finance.budgets.view-actuals`
- `finance.budgets.manage-alerts`

## Filament

- **Resource:** `BudgetResource`
- **Pages:** `ListBudgets`, `CreateBudget`, `ViewBudget` — variance table with period columns
- **Custom pages:** `BudgetRollupPage` — company-wide budget vs actual summary
- **Widgets:** `BudgetVarianceWidget` — top-3 most overspent budget lines on finance dashboard
- **Nav group:** Budgets (finance panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Xero Budgets | Departmental budget management |
| QuickBooks Budgeting | Budget vs actual tracking |
| Adaptive Insights | FP&A and budget management |
| Sage Budgets | Department and GL budgets |

## Related

- [[general-ledger]]
- [[financial-reporting]]
- [[expenses]]
- [[cash-flow]]
- [[workforce-planning]]

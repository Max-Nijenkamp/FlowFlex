---
type: module
domain: Finance & Accounting
panel: finance
cssclasses: domain-finance
phase: 3
status: complete
migration_range: 200011–200012
last_updated: 2026-05-12
right_brain_log: "[[builder-log-finance-phase3]]"
---

# Budgeting & Forecasting

Create department and company-wide budgets, track actuals vs planned, and analyse variances. Replaces Adaptive Insights, Workday Adaptive, Excel-based budget spreadsheets.

**Panel:** `finance`  
**Phase:** 3  
**Module key:** `finance.budgeting`

---

## Data Model

```erDiagram
    budgets {
        ulid id PK
        ulid company_id FK
        string name
        integer fiscal_year
        string period_type
        string status
        ulid created_by FK
        ulid approved_by FK
        timestamp approved_at
    }

    budget_lines {
        ulid id PK
        ulid budget_id FK
        ulid account_id FK
        string category
        string department
        integer period
        decimal planned_amount
        decimal actual_amount
    }
```

**Budget status flow:** `draft` → `submitted` → `approved` | `rejected`

**Period type:** `monthly` | `quarterly` | `annual`

---

## Features

### Budget Creation
- Create budget per fiscal year with monthly or quarterly breakdown
- Import prior year actuals as starting point
- Department-level budgets (line items per GL account code)
- Budget approval workflow: department head submits → finance manager approves

### Actuals vs Budget
- Pull actuals from `journal_lines` per account per period
- Variance: planned_amount − actual_amount (+ over, − under)
- Variance % highlighting: >10% variance flagged amber, >20% flagged red
- Drill-through: click variance → see underlying journal entries

### Forecasting
- Phase 3: simple forecast = remaining budget + actuals to date
- Phase 6: statistical forecast with trend analysis (see [[cash-flow-forecasting]])

---

## Permissions

```
finance.budgets.view
finance.budgets.create
finance.budgets.approve
finance.budgets.export
```

---

## Related

- [[MOC_Finance]]
- [[general-ledger-chart-of-accounts]] — account codes used for budget lines
- [[financial-reporting]] — P&L vs budget variance reports

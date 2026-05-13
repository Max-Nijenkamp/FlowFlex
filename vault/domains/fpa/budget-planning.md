---
type: module
domain: Financial Planning & Analysis
panel: fpa
module-key: fpa.budgets
status: planned
color: "#4ADE80"
---

# Budget Planning

> Annual budget creation by department, cost centre, and GL account — supporting both top-down allocation and bottom-up submission.

**Panel:** `fpa`
**Module key:** `fpa.budgets`

---

## What It Does

Budget Planning is the annual budgeting workflow. Finance sets up a budget cycle for the coming financial year and invites department heads to submit their bottom-up budget requests by GL account and cost centre. Finance reviews, adjusts, and consolidates submissions into the final approved budget. Alternatively, a top-down allocation can be set by finance directly. Once approved, the budget is locked and becomes the baseline for variance analysis throughout the year.

---

## Features

### Core
- Budget cycle creation: financial year, submission deadline, and approval workflow
- Budget structure: department → cost centre → GL account hierarchy
- Bottom-up submission: department heads enter budget by GL account line
- Top-down allocation: finance enters budget directly at department or cost centre level
- Submission workflow: draft → submitted → reviewed → approved
- Version control: maintain previous budget versions; final approved version locked
- Budget export: export the approved budget to CSV or Excel

### Advanced
- Scenario budgets: create alternative budget scenarios (optimistic, base, pessimistic) for the same cycle
- Prior-year actuals reference: show prior year actuals alongside the budget entry fields for context
- Commentary: department heads can add notes explaining specific budget line items
- Reforecast integration: mid-year budget revisions create a reforecast version rather than overwriting the original
- Template import: import budget figures from a spreadsheet template

### AI-Powered
- Budget variance pre-detection: compare submitted budgets against historical trends and flag outliers for finance review
- GL account suggestion: AI pre-populates common GL accounts for a department based on historical spending patterns
- Driver-based budgeting: link a budget line to a business driver (e.g. headcount × average salary) for automatic recalculation

---

## Data Model

```erDiagram
    budget_cycles {
        ulid id PK
        ulid company_id FK
        string name
        integer financial_year
        date submission_deadline
        date approval_deadline
        string status
        timestamps created_at_updated_at
    }

    budget_lines {
        ulid id PK
        ulid cycle_id FK
        ulid company_id FK
        string department
        string cost_centre
        string gl_account
        string scenario
        json monthly_amounts
        decimal annual_total
        text commentary
        string status
        timestamps created_at_updated_at
    }

    budget_cycles ||--o{ budget_lines : "contains"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `budget_cycles` | Budget cycle definitions | `id`, `company_id`, `name`, `financial_year`, `submission_deadline`, `status` |
| `budget_lines` | Budget line items | `id`, `cycle_id`, `department`, `cost_centre`, `gl_account`, `monthly_amounts`, `annual_total` |

---

## Permissions

```
fpa.budgets.view-own-department
fpa.budgets.submit-department
fpa.budgets.view-all
fpa.budgets.approve
fpa.budgets.export
```

---

## Filament

- **Resource:** `App\Filament\Fpa\Resources\BudgetCycleResource`
- **Pages:** `ListBudgetCycles`, `CreateBudgetCycle`, `ViewBudgetCycle`
- **Custom pages:** `BudgetEntryPage`, `BudgetConsolidationPage`, `BudgetApprovalPage`
- **Widgets:** `BudgetSubmissionProgressWidget`, `BudgetByDepartmentWidget`
- **Nav group:** Planning

---

## Displaces

| Feature | FlowFlex | Anaplan | Adaptive Insights | Cube |
|---|---|---|---|---|
| Bottom-up submission workflow | Yes | Yes | Yes | Yes |
| Top-down allocation | Yes | Yes | Yes | Yes |
| Scenario budgets | Yes | Yes | Yes | Yes |
| AI driver-based budgeting | Yes | Yes | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[financial-forecasting]] — budget provides the baseline that forecasts compare against
- [[variance-analysis]] — approved budget feeds variance calculations
- [[headcount-planning]] — headcount budget is a subset of the overall budget
- [[finance/INDEX]] — GL accounts aligned with the finance chart of accounts

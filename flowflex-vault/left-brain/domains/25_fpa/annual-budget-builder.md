---
type: module
domain: Financial Planning & Analysis
panel: fpa
phase: 4
status: planned
cssclasses: domain-fpa
migration_range: 985000–985499
last_updated: 2026-05-09
---

# Annual Budget Builder

Structured, collaborative budget creation process. Department managers submit bottom-up budgets, finance consolidates, CFO approves. Replaces spreadsheet chaos.

---

## Budget Process

```
Finance opens budget cycle → Templates sent to dept managers
→ Managers enter line-item budgets → Finance reviews submissions
→ Consolidation + top-down adjustments → CFO/board approval
→ Budget locked → Actuals tracking begins
```

### Budget Cycle
- Configurable open/close dates
- Submission deadlines with auto-reminders
- Lock after approval (no changes without amendment request)

---

## Department Budget Submission

Each manager submits for their cost centre:
- Personnel costs (links to HR headcount plan)
- Direct costs (materials, COGS)
- Operating expenses (software, services, travel)
- Capital expenditure (assets > threshold)
- Revenue targets (for revenue-generating departments)

Line item level: description, monthly phasing, category, GL account code.

---

## Consolidation

Finance view:
- Aggregate all department submissions
- Intercompany eliminations (multi-entity)
- Top-down adjustments with notes/reasoning
- Variance commentary (vs prior year, vs target)

Auto-generates P&L, Balance Sheet, and Cash Flow budget.

---

## Budget Templates

Configurable per org:
- % increase on prior year actuals (quick baseline)
- Zero-based (justify every line from zero)
- Driver-based (headcount × rate, units × price)

---

## Data Model

### `fpa_budget_cycles`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| name | varchar(100) | "FY2027 Budget" |
| fiscal_year | int | |
| status | enum | draft/open/review/approved/locked |
| submission_deadline | date | |
| approved_at | timestamp | nullable |

### `fpa_budget_lines`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| cycle_id | ulid | FK |
| cost_centre_id | ulid | FK |
| gl_account_id | ulid | FK |
| description | varchar(300) | |
| annual_amount | decimal(14,2) | |
| monthly_phasing | json | 12 values |
| budget_type | enum | opex/capex/revenue/headcount |

---

## Migration

```
985000_create_fpa_budget_cycles_table
985001_create_fpa_budget_lines_table
985002_create_fpa_budget_submissions_table
```

---

## Related

- [[MOC_FPA]]
- [[budget-vs-actual-reporting]]
- [[rolling-forecasts]]
- [[headcount-planning]]
- [[MOC_Finance]] — GL accounts + actuals

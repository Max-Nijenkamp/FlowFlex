---
type: module
domain: Projects & Work
panel: projects
phase: 2
status: planned
cssclasses: domain-projects
migration_range: 203000–203499
last_updated: 2026-05-09
---

# Project Budget & Costs

Track financial performance of projects. Budget vs actual spend, labour costs, margin tracking. Know if your project is profitable before it finishes.

---

## Project Budget

Set at project start:
- Labour budget: hours × blended rate (or by role)
- Expense budget: external costs (software, travel, subcontractors)
- Total budget: labour + expenses

Breakdown by phase if multi-phase project.

---

## Labour Cost Tracking

Hours logged (from [[project-time-tracking]]) × cost rate per person:
- Internal cost rate: salary fully-loaded ÷ annual billable hours
- Different rates by role/seniority
- Labour cost updates in real-time as time is logged

---

## Expense Tracking

Non-labour project costs:
- Software licences purchased for project
- Subcontractor invoices
- Travel and subsistence
- Equipment/materials

Linked to supplier invoices from Procurement or expense claims from Finance.

---

## Budget vs Actual

| | Budget | Actual | Remaining |
|---|---|---|---|
| Labour | €45,000 | €28,500 | €16,500 |
| Expenses | €8,000 | €3,200 | €4,800 |
| **Total** | **€53,000** | **€31,700** | **€21,300** |

% complete vs % budget consumed → health indicator:
- **Green**: % budget used ≤ % complete
- **Amber**: budget slightly ahead of progress
- **Red**: overspend likely, project at risk

---

## Earned Value

EVM (Earned Value Management) for complex projects:
- **Planned Value (PV)**: budgeted cost of work scheduled
- **Earned Value (EV)**: budgeted cost of work performed
- **Actual Cost (AC)**: real cost incurred

**Schedule Performance Index (SPI)** = EV / PV (>1 = ahead)  
**Cost Performance Index (CPI)** = EV / AC (>1 = under budget)

---

## Project Profitability (for client work)

Revenue: billable hours × billing rate + fixed fee
Cost: labour cost + expenses
Margin = (Revenue − Cost) / Revenue

Tracks alongside PSA [[project-profitability]] module.

---

## Data Model

### `proj_budgets`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| project_id | ulid | FK |
| labour_budget | decimal(14,2) | |
| expense_budget | decimal(14,2) | |
| currency | char(3) | |
| phase_breakdown | json | nullable |

### `proj_project_expenses`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| project_id | ulid | FK |
| category | varchar(100) | |
| amount | decimal(12,2) | |
| incurred_date | date | |
| description | varchar(300) | |
| supplier_invoice_id | ulid | nullable FK |

---

## Migration

```
203000_create_proj_budgets_table
203001_create_proj_project_expenses_table
```

---

## Related

- [[MOC_Projects]]
- [[project-time-tracking]]
- [[gantt-timeline]]
- [[MOC_PSA]] — project profitability
- [[MOC_Finance]] — cost reporting

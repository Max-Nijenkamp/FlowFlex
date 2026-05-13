---
type: module
domain: Financial Planning & Analysis
panel: fpa
phase: 4
status: complete
cssclasses: domain-fpa
migration_range: 985500–985999
last_updated: 2026-05-12
---

# Budget vs Actual Reporting

Real-time comparison of approved budget against actual financial results. Automated variance analysis with management commentary workflow.

---

## Core Report

For each cost centre + GL account:
| | Jan Budget | Jan Actual | Variance | % |
|---|---|---|---|---|
| Revenue | 500,000 | 487,000 | −13,000 | −2.6% |
| Payroll | 180,000 | 183,500 | +3,500 | +1.9% |
| Software | 12,000 | 9,800 | −2,200 | −18.3% |

Favourable/adverse variance colour-coded. Drill through to individual transactions.

---

## Committed Spend

In addition to actuals, shows "committed" spend:
- Open POs not yet invoiced (from Procurement)
- Approved requisitions not yet converted to PO
- Contract obligations (known future costs)

**Budget remaining = Budget − Actuals − Committed**

Prevents overspend that isn't visible until invoice arrives.

---

## Variance Commentary

Monthly close process:
1. Variances > threshold auto-flag (e.g., ±5% or ±€5,000)
2. Department managers receive commentary requests
3. Manager enters explanation for each flagged variance
4. Finance reviews + approves commentary
5. Consolidated commentary pack → board pack

---

## Report Views

- **P&L budget vs actual**: company-wide by month and YTD
- **Department view**: manager sees only their cost centres
- **Project view**: actual project spend vs project budget
- **Headcount view**: actual FTEs vs budgeted FTEs

---

## Actuals Ingestion

Pulls actuals from:
- GL journal entries (Finance module) — primary source
- Payroll runs (HR module) — payroll actuals
- Approved invoices (Procurement module) — AP actuals

Refresh: nightly or on-demand.

---

## Data Model

### `fpa_bva_snapshots`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| cycle_id | ulid | FK budget cycle |
| period | date | month |
| cost_centre_id | ulid | FK |
| gl_account_id | ulid | FK |
| budget_amount | decimal(14,2) | |
| actual_amount | decimal(14,2) | |
| committed_amount | decimal(14,2) | |
| variance | decimal(14,2) | computed |

---

## Migration

```
985500_create_fpa_bva_snapshots_table
985501_create_fpa_variance_comments_table
```

---

## Related

- [[MOC_FPA]]
- [[annual-budget-builder]]
- [[rolling-forecasts]]
- [[MOC_Finance]] — actuals source
- [[MOC_Procurement]] — committed spend

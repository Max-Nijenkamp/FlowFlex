---
type: module
domain: Finance & Accounting
panel: finance
module-key: finance.cashflow
status: planned
color: "#4ADE80"
---

# Cash Flow

Cash flow forecasting and receivables-vs-payables timeline. Short-term liquidity planning.

## Core Features

- 13-week rolling cash flow projection (standard treasury horizon)
- Inflows: expected customer payments (from AR aging + due dates)
- Outflows: scheduled supplier payments (from AP), payroll, recurring expenses
- Opening + closing cash position per week
- Actual vs projected cash comparison
- Low-cash alerts: warn when projected balance falls below threshold
- Scenario toggles: best/worst case collection timing
- Bank balance integration (from Bank Accounts)

## Data Model

| Table | Key Columns |
|---|---|
| `fin_cashflow_projections` | company_id, week_start, opening_cents, inflow_cents, outflow_cents, closing_cents, is_actual |
| `fin_cashflow_items` | projection_id, company_id, type (inflow/outflow), source, description, amount_cents, expected_date |

## Filament

**Nav group:** Planning

- `CashFlowPage` (custom page) — 13-week projection grid + chart
- Low-cash alert widget

## Cross-Domain

- Inflows from AR (invoices + aging), outflows from AP (bills) + Payroll
- Bank balance from [[domains/finance/bank-accounts]]

## Related

- [[domains/finance/accounts-receivable]]
- [[domains/finance/accounts-payable]]
- [[domains/finance/forecasting]]

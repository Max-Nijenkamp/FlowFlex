---
type: module
domain: Finance & Accounting
panel: finance
module-key: finance.cashflow
status: planned
color: "#4ADE80"
---

# Cash Flow

> Cash flow forecasting — projected inflows from outstanding invoices, projected outflows from scheduled payables, and a 13-week rolling cash position view.

**Panel:** `finance`
**Module key:** `finance.cashflow`

## What It Does

Cash Flow gives Finance a forward-looking view of cash position. It combines confirmed inflows (outstanding invoices with due dates) and confirmed outflows (approved supplier invoices due for payment, payroll runs, scheduled expenses) into a week-by-week cash forecast. The current bank balance (from Bank Accounts module) is the starting point. Finance can see whether the company will run low on cash in any future week, giving time to act — accelerate collections, delay payments, or arrange a credit facility. The module is read-only for most users — it aggregates from Invoicing, AP, and Bank modules.

## Features

### Core
- 13-week rolling cash forecast: week-by-week projected inflows and outflows with net cash position per week
- Inflows: outstanding invoices with expected payment dates (due date or historical payment lag)
- Outflows: approved supplier invoices due, payroll runs scheduled, recurring expense commitments
- Opening balance: taken from current reconciled bank balance in Bank Accounts module
- Low cash alert: configurable minimum cash threshold — alert fires when any forecast week drops below it

### Advanced
- Scenario modelling: Finance adds manual cash items (one-off payments, expected large receipts) to the forecast without creating a real GL transaction
- Payment lag adjustment: per-customer historical average payment lag applied to invoice due dates to improve inflow accuracy
- Actual vs forecast: as weeks pass, compare forecast to actual bank movements — track forecast accuracy
- Currency consolidation: if multiple bank accounts in different currencies, convert all to base currency for the consolidated view
- Export: download 13-week forecast as Excel — used for bank lending discussions

### AI-Powered
- Invoice payment prediction: AI predicts the probability and expected date of payment for each outstanding invoice based on customer payment history — improves inflow forecast accuracy beyond simple due-date assumptions
- Cash shortfall warning: AI identifies the week when a shortfall is most likely and surfaces recommended actions (which invoices to chase, which payments to delay)

## Data Model

```erDiagram
    cash_flow_scenarios {
        ulid id PK
        ulid company_id FK
        string name
        boolean is_base
        timestamps created_at/updated_at
    }

    cash_flow_manual_items {
        ulid id PK
        ulid scenario_id FK
        ulid company_id FK
        date item_date
        decimal amount
        string type
        string description
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `is_base` | True for the default scenario (no manual items removed) |
| `type` | inflow / outflow |
| Forecast data | Computed from invoices, payables, payroll — not stored |

## Permissions

- `finance.cashflow.view`
- `finance.cashflow.add-manual-items`
- `finance.cashflow.set-alert-threshold`
- `finance.cashflow.export`
- `finance.cashflow.view-scenarios`

## Filament

- **Resource:** None
- **Pages:** None
- **Custom pages:** `CashFlowForecastPage` — 13-week waterfall chart with inflow/outflow bar chart and cumulative balance line
- **Widgets:** `CashPositionWidget` — current cash balance and next-week projected position on finance dashboard
- **Nav group:** Budgets (finance panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Float | Cash flow forecasting |
| Pulse | Cash flow management |
| Dryrun | Cash flow scenario planning |
| Xero Cash Flow | Basic cash flow reporting |

## Related

- [[bank-accounts]]
- [[invoicing]]
- [[accounts-payable]]
- [[accounts-receivable]]
- [[budgets]]

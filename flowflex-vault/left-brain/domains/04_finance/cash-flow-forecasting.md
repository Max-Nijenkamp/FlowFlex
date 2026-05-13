---
type: module
domain: Finance & Accounting
panel: finance
phase: 3
status: complete
cssclasses: domain-finance
migration_range: 258000–258499
last_updated: 2026-05-12
---

# Cash Flow Forecasting

Real-time view of cash inflows and outflows. 13-week rolling cash forecast. Never be surprised by a cash crunch. Know exactly when you'll need to draw on credit facilities.

---

## Cash Position

Live bank balance (pulled from bank feeds / reconciliation):
- Current balance per bank account
- Available credit (overdraft / credit facility)
- Total liquidity

---

## 13-Week Rolling Forecast

Standard treasury tool: detailed week-by-week cash forecast for next 13 weeks:

**Inflows:**
- Receivables: invoices due by week (from AR module)
- Expected new sales (from CRM pipeline, probability-weighted)
- Subscription renewals (from billing module)
- Tax refunds, grant receipts (manually entered)

**Outflows:**
- Payables: supplier invoices due by payment date (from AP)
- Payroll: next pay dates + amounts (from HR)
- Loan repayments, lease payments (scheduled)
- VAT payments (from tax calendar)
- Committed PO spend (from Procurement)
- Capex planned (from FP&A headcount/project plan)

**Net Cash Flow per week = Inflows − Outflows**  
**Closing Cash = Opening Cash + Net Cash Flow**

---

## Scenarios

- **Base**: expected collection rates, normal timing
- **Stressed**: slower collections (DSO +15 days), delayed sales
- **Optimistic**: all receivables on time, deals close early

Red line: minimum cash balance threshold. Forecast below red = trigger alert.

---

## Cash Runway

For early-stage companies:
- Current cash ÷ monthly burn rate = months of runway
- Chart: when does cash hit zero under base/stressed scenarios?

---

## Data Model

### `fin_cash_forecasts`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| scenario | enum | base/stressed/optimistic |
| generated_at | timestamp | |

### `fin_cash_forecast_lines`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| forecast_id | ulid | FK |
| week_start | date | |
| category | varchar(100) | |
| direction | enum | inflow/outflow |
| amount | decimal(14,2) | |
| source | enum | ar/ap/payroll/manual/pipeline |
| source_id | ulid | nullable |

---

## Migration

```
258000_create_fin_cash_forecasts_table
258001_create_fin_cash_forecast_lines_table
```

---

## Related

- [[MOC_Finance]]
- [[accounts-receivable-automation]]
- [[MOC_FPA]] — budget integration
- [[MOC_Procurement]] — committed outflows

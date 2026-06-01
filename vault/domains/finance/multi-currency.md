---
type: module
domain: Finance & Accounting
panel: finance
module-key: finance.currency
status: planned
color: "#4ADE80"
---

# Multi-Currency

Support invoices, bills, and expenses in foreign currencies with exchange rates, base-currency conversion, and FX gain/loss tracking.

## Core Features

- Currency records: ISO 4217 code, symbol, decimal places (minor unit)
- Exchange rates: manual entry or API-fed (daily rates), historical rates kept
- Per-record currency: invoices/bills carry their own currency + rate at issue
- Conversion to base currency for reporting (rate locked at transaction date)
- Unrealised FX gain/loss: revalue open foreign-currency balances at period end
- Realised FX gain/loss: on payment when rate differs from invoice rate
- Currency display formatting per company locale
- Reporting always shows base currency totals

## Data Model

| Table | Key Columns |
|---|---|
| `fin_currencies` | company_id, code, symbol, minor_unit_digits, is_active |
| `fin_exchange_rates` | company_id, from_currency, to_currency, rate, effective_date |

Invoices/bills carry `currency` + `exchange_rate` columns (added when this module active).

## Filament

**Nav group:** Reporting

- `CurrencyResource` — manage active currencies
- `ExchangeRateResource` — manage/import rates
- FX gain/loss report page

## Cross-Domain

- Affects Invoicing, AP, Expenses, E-commerce, CRM Quotes when active
- Uses `brick/money` (handles per-currency minor units — see [[build/decisions/decision-2026-06-01-currency-precision]])

## Related

- [[domains/finance/invoicing]]
- [[build/decisions/decision-2026-06-01-currency-precision]]
- `brick/money`

---
type: module
domain: Finance & Accounting
panel: finance
module-key: finance.currency
status: planned
color: "#4ADE80"
---

# Multi-Currency

> Multi-currency support across all Finance modules — exchange rate management, automatic currency conversion on transactions, and FX gain/loss journal posting.

**Panel:** `finance`
**Module key:** `finance.currency`

## What It Does

Multi-Currency enables FlowFlex Finance to handle transactions in any currency while maintaining a single company base currency for reporting. Exchange rates are managed centrally — either manually entered monthly or fetched from an open exchange rate API. Every transaction (invoice, expense, payment) records both the original currency amount and the base currency equivalent at the prevailing rate. When a payment is received in a foreign currency at a rate different from the invoice rate, the FX gain or loss is calculated and posted to the GL automatically.

## Features

### Core
- Base currency: set during Company Settings — all reporting uses this currency
- Supported currencies: all ISO 4217 currencies — any currency can be activated for use
- Exchange rate table: per-currency monthly rates — entered manually or fetched via API (Open Exchange Rates, European Central Bank)
- Transaction conversion: every transaction in a foreign currency stores `original_amount`, `original_currency`, `fx_rate`, `base_amount` — computed at transaction date rate
- FX gain/loss: on payment receipt, if the rate differs from the invoice rate, the difference is computed and auto-posted as an FX gain/loss journal entry

### Advanced
- Rate source: configurable — manual entry, ECB daily rates (EUR-based), Open Exchange Rates API (requires API key)
- Historical rates: rates stored per date — historical transactions always use the rate at their transaction date, not today's rate
- Revaluation: month-end revaluation of open foreign currency balances (AR, AP, bank accounts) to current rate — FX revaluation journals posted to GL
- Multi-currency bank accounts: each bank account has a currency — bank transactions in that currency at their own rate
- Currency exposure report: total open AR, AP, and bank balances per currency — used for FX risk management

### AI-Powered
- Rate alert: if a currency moves more than 5% in a month, Finance is notified — prompt to review impact on open invoices and payables
- FX exposure forecast: based on scheduled invoices and payables in each foreign currency, AI projects the FX exposure for the next 90 days

## Data Model

```erDiagram
    currencies {
        string code PK
        string name
        string symbol
        boolean is_active
        timestamps created_at/updated_at
    }

    exchange_rates {
        ulid id PK
        ulid company_id FK
        string from_currency
        string to_currency
        date rate_date
        decimal rate
        string source
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `from_currency` | Foreign currency code |
| `to_currency` | Company base currency code |
| `source` | manual / ecb / open_exchange_rates |
| `rate_date` | Effective date for this rate |

## Permissions

- `finance.currency.view-rates`
- `finance.currency.manage-rates`
- `finance.currency.activate-currency`
- `finance.currency.run-revaluation`
- `finance.currency.view-exposure`

## Filament

- **Resource:** `ExchangeRateResource`, `CurrencyResource`
- **Pages:** `ListExchangeRates`, `ManageExchangeRates` — rate entry grid per currency per month
- **Custom pages:** `CurrencyExposurePage` — open balances per currency
- **Widgets:** `FxExposureWidget` — total open foreign currency balances on finance dashboard
- **Nav group:** Reporting (finance panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Xero Multi-Currency | Foreign currency transactions |
| QuickBooks Multi-Currency | Multi-currency accounting |
| Sage | Multi-currency GL and revaluation |
| Airwallex | Multi-currency financial management |

## Implementation Notes

**External dependency — exchange rate API:** Two options are named in the spec:
1. **Open Exchange Rates** (`openexchangerates.org`) — requires a paid API key (`OPEN_EXCHANGE_RATES_API_KEY` in `.env`). Free tier is limited to base USD and 1,000 requests/month. Returns JSON of all rates in one call. Recommended for USD-based companies.
2. **European Central Bank (ECB)** — free, no API key. Returns XML via `https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml`. EUR is the base — must convert if the company's base currency is not EUR. Suitable for EU companies.

**Rate fetch job:** `FetchExchangeRatesJob` runs daily via the Laravel scheduler. It fetches the day's rates and inserts rows into `exchange_rates`. The job must be idempotent — check `(company_id, from_currency, to_currency, rate_date)` unique constraint before inserting. Add a unique index to prevent duplicate rates for the same date.

**FX gain/loss posting:** When a payment is received on a foreign-currency invoice, `PaymentReceived` event handler `PostFxGainLossJournal` computes `(invoice_rate - payment_rate) × amount` and posts a journal entry. The GL accounts for FX Gain and FX Loss must be pre-configured in the chart of accounts setup — this is a dependency on the `general-ledger` module being built first.

**Month-end revaluation:** `RunRevaluationJob` is a queued job triggered manually from `ManageExchangeRates` page or by a scheduled monthly trigger. It queries all open AR, AP, and bank account balances in foreign currencies, applies the current month-end rate, computes the revaluation difference, and posts adjustment journals.

**AI features:** Rate alert is a PHP-only calculation — compare current month rate to prior month for each active currency. If deviation exceeds 5%, dispatch `FxRateAlertNotification`. No LLM needed. FX exposure forecast calls `app/Services/AI/FxForecastService.php` with scheduled invoice/payable data as input to OpenAI GPT-4o, which returns a narrative forecast.

**Missing from data model:** `currencies` table uses `string code PK` — this is correct for ISO 4217 but note the `BelongsToCompany` trait cannot apply to a shared lookup table. `currencies` is a global lookup (not scoped to a company). `exchange_rates` is company-scoped (companies may use different rate sources). Add a unique constraint on `exchange_rates(company_id, from_currency, to_currency, rate_date)`.

## Related

- [[general-ledger]]
- [[invoicing]]
- [[bank-accounts]]
- [[accounts-receivable]]
- [[accounts-payable]]

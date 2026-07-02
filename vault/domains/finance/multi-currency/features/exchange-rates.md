---
domain: finance
module: multi-currency
feature: exchange-rates
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature — Exchange Rates

Effective-dated exchange rates with conversion to base currency.

- `ExchangeRateResource` (#1 CRUD resource, [[../../../../architecture/ui-strategy]]): manual rate entry plus history.
- `CurrencyResource` (#1 CRUD resource) activates currencies (ISO 4217 code, symbol, `minor_unit_digits`).
- `fin_exchange_rates` rows are effective-dated and unique on `(company_id, from, to, effective_date)`; rates stored as `decimal(16,8)`.
- `CurrencyService::rateFor(from, to, date): BigDecimal` returns the most recent rate ≤ date; missing rate throws `MissingExchangeRateException`.
- `CurrencyService::toBase(Money, date): Money` converts at the rate locked at the transaction date.
- Rates are manual in v1 with an API feed hook deferred *(assumed)*; historical rates are kept.

## UI
- **Kind**: simple-resource
- **Page**: `ExchangeRateResource` under `/finance/currency/rates`; `CurrencyResource` under `/finance/currency/currencies`.
- **Layout**: rate resource = manual rate entry + effective-dated history table; currency resource = activate currencies (ISO 4217 code, symbol, `minor_unit_digits`).
- **Key interactions**: enter a manual rate for a (from, to, effective_date); activate/deactivate currencies; browse rate history.
- **States**: empty (no rates yet → prompt to add) · loading (list skeleton) · error (unique-constraint violation on duplicate effective date; `MissingExchangeRateException` surfaced to consumers) · selected (rate row / active currency)
- **Gating**: `finance.currency.manage-rates` *(assumed)*

## Data
- Owns / writes: `fin_exchange_rates`, `fin_currencies` only. Rates stored `decimal(16,8)`, effective-dated, unique `(company, from, to, effective_date)`. (Note: rates are decimal, not integer minor units; money amounts elsewhere remain integer minor units via brick/money.)
- Reads: own tables only.
- Cross-domain writes: none. Manual rates in v1, API feed hook deferred *(assumed)*. Never writes another domain's tables ([[../../../../security/data-ownership]]).

## Relations
- Consumes: no events.
- Feeds: no events. Read by finance.invoicing / finance.ap / finance.expenses via `CurrencyService::rateFor` / `toBase`.

See [[../api]], [[../data-model]], [[../architecture]].

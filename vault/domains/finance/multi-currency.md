---
type: module
domain: Finance & Accounting
domain-key: finance
panel: finance
module-key: finance.currency
status: planned
priority: v1
depends-on: [finance.ledger, core.billing, core.rbac, core.settings]
soft-depends: [finance.invoicing, finance.ap, finance.expenses]
fires-events: []
consumes-events: []
patterns: [money]
tables: [fin_currencies, fin_exchange_rates]
permission-prefix: finance.currency
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Multi-Currency

Support invoices, bills, and expenses in foreign currencies with exchange rates, base-currency conversion, and FX gain/loss tracking.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/finance/general-ledger\|finance.ledger]] | FX gain/loss accounts; base-currency postings |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/company-settings\|core.settings]] | gating, permissions, base currency |
| Soft | [[domains/finance/invoicing\|finance.invoicing]], [[domains/finance/accounts-payable\|finance.ap]], [[domains/finance/expenses\|finance.expenses]] | gain per-record currency support when this module is active |

---

## Core Features

- Currency records: ISO 4217 code, symbol, decimal places (minor unit — JPY=0, BHD=3; brick/money handles per [[build/decisions/decision-2026-06-01-currency-precision]])
- Exchange rates: manual entry or API-fed (daily rates *(assumed: manual v1, API hook later)*), historical rates kept
- Per-record currency: invoices/bills carry their own currency + rate locked at issue date
- Conversion to base currency for reporting (rate locked at transaction date — GL ALWAYS posts base currency)
- Unrealised FX gain/loss: revalue open foreign-currency balances at period end
- Realised FX gain/loss: on payment when rate differs from invoice rate → posts to FX gain/loss accounts
- Currency display formatting per company locale
- Reporting always shows base currency totals

---

## Data Model

### fin_currencies

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| code | string(3) | ISO 4217, unique per company |
| symbol | string | |
| minor_unit_digits | int | 0–3 |
| is_active | boolean | |

### fin_exchange_rates

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| from_currency / to_currency | string(3) | |
| rate | decimal(16,8) | |
| effective_date | date | unique `(company_id, from, to, effective_date)` |

Invoices/bills/expenses carry `currency` + `exchange_rate` columns (migration shipped by those modules; this module activates the input).

---

## DTOs

### SetExchangeRateData — from_currency/to_currency (active, ≠), rate (> 0), effective_date

## Services & Actions

- `CurrencyService::rateFor(string $from, string $to, CarbonImmutable $date): BigDecimal` — most recent rate ≤ date; throws `MissingExchangeRateException`
- `CurrencyService::toBase(Money $foreign, CarbonImmutable $date): Money`
- `RevalueOpenBalancesCommand` — period-end unrealised FX entries (reversing next period *(assumed)*)
- Realised FX: computed inside payment recording (invoicing/AP call `CurrencyService`), posted to FX accounts

---

## Filament

**Nav group:** Reporting

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `CurrencyResource` | #1 CRUD resource | activate currencies |
| `ExchangeRateResource` | #1 CRUD resource | manual rates, history |
| FX gain/loss report page | #9 report custom page | realised + unrealised per period |

---

## Permissions

`finance.currency.view` · `finance.currency.manage`

---

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `RevalueOpenBalancesCommand` | finance | monthly, last day | one revaluation entry per (period, currency) — unique guard |

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Rate lookup picks most recent ≤ date; missing rate throws
- [ ] JPY (0 minor units) + BHD (3) round-trip correctly via brick/money
- [ ] Payment at different rate posts realised FX gain/loss entry
- [ ] Revaluation idempotent per period
- [ ] GL never contains foreign-currency amounts (base only)

---

## Build Manifest

```
database/migrations/xxxx_create_fin_currencies_table.php
database/migrations/xxxx_create_fin_exchange_rates_table.php
app/Models/Finance/{Currency,ExchangeRate}.php
app/Data/Finance/SetExchangeRateData.php
app/Services/Finance/CurrencyService.php
app/Exceptions/Finance/MissingExchangeRateException.php
app/Console/Commands/Finance/RevalueOpenBalancesCommand.php
app/Filament/Finance/Resources/{CurrencyResource,ExchangeRateResource}.php
database/factories/Finance/{CurrencyFactory,ExchangeRateFactory}.php
tests/Feature/Finance/{CurrencyConversionTest,FxGainLossTest}.php
```

---

## Related

- [[domains/finance/invoicing]]
- [[build/decisions/decision-2026-06-01-currency-precision]]

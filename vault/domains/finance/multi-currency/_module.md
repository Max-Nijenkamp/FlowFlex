---
domain: finance
module: multi-currency
type: module
module-key: finance.currency
priority: v1
build-status: planned
status: wip
depends-on: [finance.ledger, core.billing, core.rbac, core.settings]
soft-depends: [finance.invoicing, finance.ap, finance.expenses]
fires-events: []
consumes-events: []
patterns: [money]
tables: [fin_currencies, fin_exchange_rates]
permission-prefix: finance.currency
encrypted-fields: []
color: "#4ADE80"
updated: 2026-07-03
---

# Multi-Currency

Support invoices, bills, and expenses in foreign currencies with exchange rates, base-currency conversion, and FX gain/loss tracking.

> Rebuild blueprint. Code was stripped to the [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell|app/admin shell]]; nothing here is built yet. This spec is the source of truth for the rebuild.

## Module-key

`finance.currency`

**Priority:** v1  
**Panel:** finance  
**Permission prefix:** `finance.currency`  
**Tables:** `fin_currencies`, `fin_exchange_rates`

## Purpose

Lets foreign-currency records exist while keeping the General Ledger purely base-currency. `CurrencyService` is the one sanctioned path for rate lookup and conversion; the GL always posts base currency, with realised/unrealised FX gain/loss captured against FX accounts.

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../general-ledger/_module\|finance.ledger]] | FX gain/loss accounts; base-currency postings |
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] + [[../../core/company-settings/_module\|core.settings]] | gating, permissions, base currency |
| Soft | [[../invoicing/_module\|finance.invoicing]], [[../accounts-payable/_module\|finance.ap]], [[../expenses/_module\|finance.expenses]] | gain per-record currency support when this module is active |

## Core Features

- Currency records: ISO 4217 code, symbol, decimal places (minor unit — JPY=0, BHD=3; brick/money handles per the currency-precision decision).
- Exchange rates: manual entry or API-fed (daily rates *(assumed: manual v1, API hook later)*), historical rates kept.
- Per-record currency: invoices/bills carry their own currency + rate locked at issue date.
- Conversion to base currency for reporting (rate locked at transaction date — GL ALWAYS posts base currency).
- Unrealised FX gain/loss: revalue open foreign-currency balances at period end.
- Realised FX gain/loss: on payment when rate differs from invoice rate → posts to FX gain/loss accounts.
- Currency display formatting per company locale.
- Reporting always shows base currency totals.

## Permissions

`finance.currency.view` · `finance.currency.manage`

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `RevalueOpenBalancesCommand` | finance | monthly, last day | one revaluation entry per (period, currency) — unique guard |

See [[../../../architecture/queue-jobs]].

## Test Checklist

- [ ] Tenant isolation: company A cannot see or edit company B currencies/rates
- [ ] Module gating: artifacts hidden when `finance.currency` inactive
- [ ] Rate lookup picks most recent ≤ date; missing rate throws
- [ ] JPY (0 minor units) + BHD (3) round-trip correctly via brick/money
- [ ] Payment at different rate posts realised FX gain/loss entry
- [ ] Revaluation idempotent per period
- [ ] GL never contains foreign-currency amounts (base only)

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

## Cross-Domain Edges

**Data ownership.** This module writes only its own tables (`fin_currencies`, `fin_exchange_rates`); all cross-domain effects happen via events or the owning domain's service — never a direct write into another domain's tables ([[../../../security/data-ownership]]).

| Direction | Event / Call | Counterpart |
|---|---|---|
| Reads by | rate lookups via `CurrencyService` | [[../invoicing/_module\|finance.invoicing]], [[../accounts-payable/_module\|finance.ap]], [[../expenses/_module\|finance.expenses]] |
| Calls | FX gain/loss posts to GL via `LedgerService::post` from within payment recording | [[../general-ledger/_module\|finance.ledger]] |

## Entity Notes

- [[architecture]] — services, FX/conversion logic, money handling
- [[data-model]] — tables + ERD
- [[api]] — DTOs, service methods, events
- [[security]] — access contract, permissions
- [[decisions]] — brick/money minor-unit + manual-rate deviations
- [[unknowns]] — `*(assumed)*` items
- Features: [[features/exchange-rates]], [[features/fx-gain-loss]]

## Related

- [[../invoicing/_module]]
- [[../financial-reporting/_module]]
- [[../../../architecture/event-bus]]
- [[../../../glossary]]

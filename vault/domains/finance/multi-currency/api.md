---
domain: finance
module: multi-currency
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Multi-Currency — DTOs, Services & Events

## DTOs

### SetExchangeRateData
| Field | Type | Validation |
|---|---|---|
| from_currency | string(3) | active currency |
| to_currency | string(3) | active currency, ≠ from |
| rate | decimal | > 0 |
| effective_date | date | required |

DTOs use `spatie/laravel-data` per [[../../../architecture/patterns/dto-pattern]].

## Services & Actions

- `CurrencyService::rateFor(string $from, string $to, CarbonImmutable $date): BigDecimal` — most recent rate ≤ date; throws `MissingExchangeRateException`.
- `CurrencyService::toBase(Money $foreign, CarbonImmutable $date): Money` — convert to base currency at the locked transaction-date rate.
- `RevalueOpenBalancesCommand` — period-end unrealised FX entries (reversing next period *(assumed)*).
- Realised FX: computed inside payment recording (invoicing/AP call `CurrencyService`), posted to FX accounts.

## Events

This module fires and consumes no events. Conversion and realised-FX math are invoked by consuming modules (invoicing, AP, expenses) as direct in-domain service calls. See [[../../../architecture/event-bus]].

See [[security]], [[../general-ledger/_module]], [[../financial-reporting/_module]].

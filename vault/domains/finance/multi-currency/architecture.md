---
domain: finance
module: multi-currency
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Multi-Currency — Architecture

`CurrencyService` is the one sanctioned path for rate lookup and base-currency conversion. It is called directly by consuming modules (invoicing, AP, expenses) within the finance domain — no events.

## FX / conversion logic

- `CurrencyService::rateFor(from, to, date): BigDecimal` returns the most recent rate with `effective_date ≤ date`; throws `MissingExchangeRateException` when none exists.
- `CurrencyService::toBase(Money $foreign, CarbonImmutable $date): Money` converts a foreign amount to base currency using the rate locked at the transaction date.
- Per-record currency: invoices/bills carry their own `currency` + `exchange_rate`, locked at issue date. The GL **always** posts base currency — it never contains foreign-currency amounts.

## Realised vs unrealised FX gain/loss

- **Realised** FX gain/loss is computed inside payment recording: invoicing/AP call `CurrencyService` when a payment rate differs from the invoice rate, and the difference posts to FX gain/loss accounts.
- **Unrealised** FX gain/loss is produced by `RevalueOpenBalancesCommand` at period end — it revalues open foreign-currency balances and writes reversing entries the next period *(assumed)*. One revaluation entry per (period, currency), guarded for idempotency.

## Money handling

All monetary amounts are integer **minor units** handled with `brick/money`. Per-currency precision comes from `minor_unit_digits` (ISO 4217: JPY=0, BHD=3, most=2). Exchange rates are stored as `decimal(16,8)` and applied with `BigDecimal` arithmetic. See [[../../../architecture/packages]] (brick/money) and [[../../../architecture/performance]].

## Per-record currency columns

Invoices/bills/expenses carry `currency` + `exchange_rate` columns (migration intended to be added by those modules; this module activates the input).

See [[../../../architecture/patterns/interface-service]], [[../../../architecture/event-bus]], [[../../../architecture/queue-jobs]], [[data-model]], [[api]].

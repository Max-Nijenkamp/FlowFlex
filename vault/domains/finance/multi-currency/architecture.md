---
domain: finance
module: multi-currency
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
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

## Filament Artifacts

**Nav group:** Currency *(assumed)*

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `CurrencyResource` | #1 CRUD resource | tweaks: state-badge-column (active/inactive) | activate currencies (ISO 4217 code, symbol, `minor_unit_digits`) |
| `ExchangeRateResource` | #1 CRUD resource | — | manual effective-dated rate entry + history; rows are append-only (new effective-dated row, not edits); `decimal(16,8)` |
| `FxGainLossPage` | #9 custom page | [[../../../architecture/patterns/page-blueprints#Report Builder / Query UI]] — per-period realised + unrealised FX gain/loss by currency; realtime none | `/finance/currency/fx-gain-loss` |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('finance.currency.view-any') && BillingService::hasModule('finance.currency')`
per [[../../../architecture/filament-patterns]] #1. `FxGainLossPage` is a custom page and MUST state this
explicitly — Filament does not auto-gate custom pages. No public/portal surface in this module.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Currency CRUD + exchange-rate entry (form, API) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]). Rates are append-only effective-dated rows (unique `(company_id, from, to, effective_date)`) rather than in-place edits |
| Realised FX gain/loss posting (payment recording → FX accounts via `LedgerService::post`) | Pessimistic | `DB::transaction()` + `lockForUpdate()`, re-read, validate, write — money mutation / journal posting per [[../../../architecture/patterns/states]] |
| Unrealised FX revaluation (`RevalueOpenBalancesCommand` → reversing GL entries) | Pessimistic | `DB::transaction()` + `lockForUpdate()`; idempotent one entry per `(period, currency)` via unique guard — money mutation / journal posting |
| Rate lookup / conversion (`rateFor`, `toBase`) | n-a | read-only computation over own rate rows — no writes |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

See [[../../../architecture/patterns/interface-service]], [[../../../architecture/event-bus]], [[../../../architecture/queue-jobs]], [[data-model]], [[api]].

---
domain: finance
module: multi-currency
feature: fx-gain-loss
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature — FX Gain/Loss

Realised and unrealised foreign-exchange gain/loss against dedicated FX accounts, reported per period.

- FX gain/loss report page (#9 report custom page, [[../../../../architecture/ui-strategy]]): realised + unrealised per period.
- **Realised**: on payment, when the payment rate differs from the invoice/bill rate, the difference posts to FX gain/loss accounts. Computed inside payment recording (invoicing/AP call `CurrencyService`).
- **Unrealised**: `RevalueOpenBalancesCommand` (queue `finance`, monthly on the last day) revalues open foreign-currency balances at period end and writes reversing entries the next period *(assumed)*.
- Idempotent: one revaluation entry per (period, currency), enforced by a unique guard.
- The GL always posts base currency — FX differences are the only foreign-rate effect that lands in it.

## UI
- **Kind**: custom-page (report) + background
- **Page**: FX gain/loss report page under `/finance/currency/fx-gain-loss`.
- **Layout**: per-period grid of realised + unrealised FX gain/loss by currency.
- **Key interactions**: select period, view realised vs unrealised breakdown per currency.
- **States**: empty (no foreign-currency activity in period) · loading (grid skeleton) · error (missing rate at period end) · selected (period/currency row)
- **Gating**: `finance.currency.view-any`

## Data
- Owns / writes: reads `fin_exchange_rates` (own module). All figures resolve to integer minor units via brick/money; the GL always posts base currency.
- Reads: own rate tables; realised FX is computed inside invoicing/AP payment recording (they call `CurrencyService`).
- Cross-domain writes: FX difference posts to FX gain/loss GL accounts via `LedgerService::post` only — never writes `fin_journal_*` directly ([[../../../../security/data-ownership]]). Unrealised reversing entries the next period *(assumed)*.

## Relations
- Consumes: no events; realised FX is driven by invoicing/AP payment recording calling `CurrencyService`.
- Feeds: `RevalueOpenBalancesCommand` (finance queue, monthly last day, idempotent one entry per period+currency). Cross-domain call to `LedgerService::post` for FX entries.

## Test Checklist

### Unit
- [ ] Realised FX difference = payment-rate value − invoice-rate value, integer minor units via brick/money; zero when rates match
- [ ] Unrealised revaluation amount computed from open foreign balances at the period-end rate (brick/money)

### Feature (Pest)
- [ ] A payment at a rate differing from the invoice rate posts a realised FX gain/loss entry to the FX accounts via `LedgerService::post` (base currency only — GL never holds foreign amounts)
- [ ] `RevalueOpenBalancesCommand` is idempotent — a second run in the same `(period, currency)` writes no duplicate revaluation entry (unique guard)
- [ ] Missing rate at period end surfaces `MissingExchangeRateException` rather than posting a wrong entry
- [ ] Tenant isolation: revaluation and realised-FX posting only touch the acting company's balances/accounts

### Livewire
- [ ] `FxGainLossPage` renders the per-period realised/unrealised grid by currency; `canAccess` denied without `finance.currency.view-any`

See [[../api]], [[../architecture]], [[../../general-ledger/_module]].

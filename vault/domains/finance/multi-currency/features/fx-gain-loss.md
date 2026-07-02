---
domain: finance
module: multi-currency
feature: fx-gain-loss
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
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

See [[../api]], [[../architecture]], [[../../general-ledger/_module]].

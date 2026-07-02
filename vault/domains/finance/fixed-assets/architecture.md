---
domain: finance
module: fixed-assets
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Fixed Assets — Architecture

`FixedAssetService` owns the register, depreciation calculation, and disposal. Every depreciation and disposal posts to the ledger through `LedgerService::post` — the module never writes journal lines directly.

## Money handling

All amounts are integer **minor units** (cents) in `bigint` columns, manipulated with `brick/money` — never raw float math. Depreciation is computed in cents and the **final period absorbs the rounding remainder** so a straight-line schedule sums exactly to `cost − salvage`. See [[../../../architecture/packages]] (brick/money) and [[../../../architecture/performance]].

## Depreciation logic

- **Straight-line**: `(cost − salvage) / useful_life_months` per period; final period absorbs rounding so accumulated depreciation lands exactly at `cost − salvage`.
- **Declining-balance**: applies a rate to NBV each period; **never depreciates below salvage** (period charge is clamped so NBV ≥ salvage). *(Rate basis not enumerated in the spec — see [[unknowns]].)*
- **Units-of-production**: listed in the spec but deferred for v1 *(assumed)*.
- `runMonthlyDepreciation(period)` iterates active assets: compute the period charge, post a balanced GL entry, record a `fin_depreciation_entries` row, and set status `fully-depreciated` once NBV reaches salvage.

## Disposal logic

`dispose(DisposeAssetData)` computes `gain/loss = proceeds − NBV` (NBV = `cost − accumulated_depreciation`), posts the resulting GL entry, sets status `disposed`, and stamps `disposed_at` + `disposal_proceeds_cents`. A second disposal throws `AlreadyDisposedException`.

## Jobs & scheduling

`RunDepreciationCommand` runs monthly (1st, 02:30, finance queue). Idempotent on unique `(asset, period)`: a re-run skips assets already done for the period and continues on per-asset failure rather than aborting the batch. See [[../../../architecture/queue-jobs]].

## GL coupling

Depreciation and disposal entries are direct, in-domain service calls to `LedgerService::post` (same finance domain — no events). The posted `journal_entry_id` is stored back on the `fin_depreciation_entries` row for traceability.

See [[../../../architecture/patterns/interface-service]], [[data-model]], [[api]], [[../general-ledger/_module]].

---
domain: finance
module: fixed-assets
feature: depreciation
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature — Depreciation

Monthly depreciation calculation, GL posting, and full-life schedule projection.

- `DepreciationRunPage` (#7 custom page, [[../../../../architecture/ui-strategy]]): pick a run month, preview the per-asset charge, post, and view a result summary.
- `FixedAssetService::runMonthlyDepreciation(period)` iterates active assets: compute the period charge, post a balanced GL entry via `LedgerService::post`, record a `fin_depreciation_entries` row (storing the posted `journal_entry_id`), and set `fully-depreciated` once NBV reaches salvage.
- `FixedAssetService::schedule(assetId)` returns the full-life projection; the **final period absorbs the rounding remainder** so straight-line sums exactly to `cost − salvage`.
- Straight-line: `(cost − salvage) / useful_life_months`. Declining-balance: rate applied to NBV, clamped so NBV never falls below salvage.
- `RunDepreciationCommand` (finance queue, monthly 1st 02:30) is idempotent on unique `(asset, period)` — a re-run skips done assets and continues on per-asset failure.

All amounts integer minor units via brick/money.

## UI
- **Kind**: custom-page + background
- **Page**: `DepreciationRunPage` under `/finance/assets/depreciation`.
- **Layout**: run-month picker, per-asset charge preview table, post action, post-run summary.
- **Key interactions**: pick run month, preview per-asset charges, post the run, review summary.
- **States**: empty (nothing to depreciate this period) · loading (preview compute) · error (per-asset failure surfaced, run continues) · selected (previewed period ready to post)
- **Gating**: `finance.assets.run-depreciation` *(assumed)*

## Data
- Owns / writes: `fin_fixed_assets`, `fin_depreciation_entries` (stores the posted `journal_entry_id`). All amounts integer minor units via brick/money; final period absorbs the rounding remainder so straight-line sums exactly to `cost − salvage`.
- Reads: own tables only.
- Cross-domain writes: GL posting only via `LedgerService::post` (balanced entry) — never writes `fin_journal_*` directly ([[../../../../security/data-ownership]]).

## Relations
- Consumes: no events.
- Feeds: `RunDepreciationCommand` (finance queue, monthly 1st 02:30, idempotent on unique asset+period). In-domain service call `runMonthlyDepreciation`; cross-domain call to `LedgerService::post`.

See [[../architecture]], [[../api]], [[../data-model]].

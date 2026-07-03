---
domain: finance
module: fixed-assets
feature: disposal
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature — Disposal

Sale or scrap of an asset, with gain/loss computed against net book value and posted to the GL.

- Triggered from the `FixedAssetResource` dispose action (#1 CRUD resource, [[../../../../architecture/ui-strategy]]).
- `FixedAssetService::dispose(DisposeAssetData)`: `gain/loss = proceeds − NBV`, where `NBV = cost − accumulated_depreciation`. The resulting gain/loss posts a GL entry via `LedgerService::post`.
- Sets asset `status = disposed`, stamps `disposed_at` and `disposal_proceeds_cents`.
- A second disposal of the same asset throws `AlreadyDisposedException`.
- `DisposeAssetData` validates `disposal_proceeds_cents ≥ 0` and `disposed_at ≥ purchase_date`.

All amounts integer minor units via brick/money.

UNVERIFIED: the specific GL accounts the disposal gain/loss posts to are not enumerated in the spec — see [[../unknowns]].

## UI
- **Kind**: simple-resource
- **Page**: dispose action on `FixedAssetResource` under `/finance/assets`.
- **Layout**: dispose action opens a form (proceeds, disposal date); computed gain/loss shown before confirm.
- **Key interactions**: trigger dispose, enter proceeds + date, confirm; blocked if already disposed.
- **States**: empty (n/a — acts on an existing asset) · loading (posting) · error (`AlreadyDisposedException` on second disposal; validation on proceeds/date) · selected (asset row marked disposed)
- **Gating**: `finance.assets.dispose` *(assumed)*

## Data
- Owns / writes: `fin_fixed_assets` — sets `status = disposed`, `disposed_at`, `disposal_proceeds_cents`. All amounts integer minor units via brick/money; `gain/loss = proceeds − NBV`.
- Reads: own tables only (`NBV = cost − accumulated_depreciation`).
- Cross-domain writes: gain/loss GL entry via `LedgerService::post` only — never writes `fin_journal_*` directly ([[../../../../security/data-ownership]]). UNVERIFIED: exact GL accounts (see note above).

## Relations
- Consumes: no events.
- Feeds: no events. In-domain service call `dispose(DisposeAssetData)`; cross-domain call to `LedgerService::post`.

## Test Checklist

### Unit
- [ ] `gain/loss = proceeds − NBV` where `NBV = cost − accumulated_depreciation` (brick/money, integer cents)
- [ ] `DisposeAssetData` validates `disposal_proceeds_cents ≥ 0` and `disposed_at ≥ purchase_date`

### Feature (Pest)
- [ ] `dispose` posts the gain/loss GL entry via `LedgerService::post`, sets `status = disposed`, stamps `disposed_at` + `disposal_proceeds_cents`, all under `DB::transaction` + `lockForUpdate`
- [ ] A second disposal of the same asset throws `AlreadyDisposedException` (re-read under lock)
- [ ] Tenant isolation: company A cannot dispose company B assets

### Livewire
- [ ] Dispose action on `FixedAssetResource` shows computed gain/loss before confirm and is blocked without `finance.assets.dispose`; governed by the `panel-action` rate limiter

See [[../architecture]], [[../api]], [[../security]].

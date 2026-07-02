---
domain: finance
module: fixed-assets
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Fixed Assets — DTOs, Services & Events

## DTOs

### CreateAssetData
| Field | Type | Validation |
|---|---|---|
| name | string | required |
| category | string | required (defaults applied at create) |
| cost_cents | int | min:1 |
| purchase_date | date | not future |
| useful_life_months | int | min:1 |
| method | string | in set (straight-line / declining) |
| salvage_cents | int | `< cost` — "Salvage value must be below cost." |

### DisposeAssetData
| Field | Type | Validation |
|---|---|---|
| asset_id | ulid | required |
| disposal_proceeds_cents | int | ≥ 0 |
| disposed_at | date | ≥ purchase_date |

### AssetData (output)
Asset record projection including NBV (`cost − accumulated_depreciation`) and status.

DTOs use `spatie/laravel-data` per [[../../../architecture/patterns/dto-pattern]].

## Services & Actions

`FixedAssetService` (concrete service; money via brick/money):

- `create(CreateAssetData $data): AssetData`.
- `schedule(string $assetId): Collection` — full-life projection; final period absorbs the rounding remainder.
- `runMonthlyDepreciation(string $period): DepreciationResult` — per asset: compute, post GL, record entry; sets `fully-depreciated` at NBV = salvage.
- `dispose(DisposeAssetData $data): AssetData` — `gain/loss = proceeds − NBV` → GL entry; throws `AlreadyDisposedException`.

## Events

This module fires and consumes no cross-domain events. Depreciation and disposal post to the ledger via direct, in-domain `LedgerService::post` calls (same finance domain — no events), per the same-domain rule in [[../../../architecture/event-bus]].

See [[security]], [[../general-ledger/_module]], [[features/depreciation]], [[features/disposal]].

---
domain: finance
module: fixed-assets
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
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

## Filament Artifacts

**Nav group:** Assets *(assumed)*

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `FixedAssetResource` | #1 CRUD resource | tweaks: state-badge-column (status: active / fully-depreciated / disposed), custom-header-actions (dispose) | list filters: category, method, status; dispose action needs `finance.assets.dispose` + `panel-action` rate limiter (posts money to GL) |
| `DepreciationRunPage` | #7 wizard custom page | [[../../../architecture/patterns/page-blueprints#Wizard]] — pick run month → preview per-asset charge → post → result summary; realtime none | `/finance/assets/depreciation`; post step needs `finance.assets.run-depreciation` + `panel-action` rate limiter (posts money to GL) |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('finance.assets.view-any') && BillingService::hasModule('finance.assets')`
per [[../../../architecture/filament-patterns]] #1. `DepreciationRunPage` is a custom page and MUST state this
explicitly — Filament does not auto-gate custom pages. No public/portal surface in this module.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Asset register CRUD (form, API) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Monthly depreciation posting (`runMonthlyDepreciation`, posts to GL) | Pessimistic | `DB::transaction()` + `lockForUpdate()` on the asset, re-read, compute, post balanced GL entry, insert `fin_depreciation_entries` under unique `(asset, period)` — money mutation ([[../../../architecture/patterns/states]] for the `fully-depreciated` transition) |
| Disposal (`dispose`, posts gain/loss to GL, one-way) | Pessimistic | `DB::transaction()` + `lockForUpdate()` on the asset, re-read status (reject if already `disposed` → `AlreadyDisposedException`), compute `gain/loss = proceeds − NBV`, post GL entry, set `status = disposed` — money mutation |
| Posted depreciation/disposal GL entries + `fin_depreciation_entries` rows | n-a | append-only once posted; NBV/schedule reads are read-only derived computations |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

See [[../../../architecture/patterns/interface-service]], [[data-model]], [[api]], [[../general-ledger/_module]].

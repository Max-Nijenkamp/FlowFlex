---
domain: operations
module: stock-adjustments
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Stock Adjustments — Architecture

## Approval state

`ops_stock_adjustments.status` — a simple `pending-approval` / `applied` flag *(assumed)* rather than a full `spatie/laravel-model-states` machine (only one meaningful transition). Above-threshold adjustments start `pending-approval` with stock untouched; approval flips to `applied` and posts the movement.

## Services & Actions

`AdjustmentService`:

| Method | Notes |
|---|---|
| `adjust(CreateAdjustmentData): StockAdjustment` | computes `value_impact_cents` (delta × item cost, brick/money). Over threshold → `pending-approval` (no stock change). Else → `applied` + `StockService::move(adjust)`. |
| `approve(string $adjustmentId): void` | approver ≠ adjuster; flips to `applied` + posts `StockService::move(adjust)`. |
| `stocktake(StocktakeData): StocktakeResult` | computes deltas vs current levels; writes one adjustment per non-zero delta (reason `stocktake correction`); applies (or queues for approval) each. |

Negative delta beyond available is rejected. Threshold is a company setting *(assumed €500)*.

---

## Events

Fires none, consumes none. GL posting for write-offs is **deferred** — v1 emits no `StockWrittenOff` event; instead a report ([[./features/write-off-report]]) is exported for finance to journal manually. (Automating this later would be an event to finance.ledger — future ADR.)

---

## Filament Artifacts

**Nav group:** Inventory

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `StockAdjustmentResource` | #1 CRUD resource | tweaks: state-badge-column (pending-approval / applied), custom-header-actions (approve / export) | tabs All / Pending; reason + period report filters; Excel export ([[./features/write-off-report]]) |
| `StocktakePage` | #7 wizard custom page | [[../../../architecture/patterns/page-blueprints#Wizard]] | warehouse pick → count grid → preview deltas → confirm |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('operations.adjustments.view-any') && BillingService::hasModule('operations.adjustments')`
per [[../../../architecture/filament-patterns]] #1. `StocktakePage` is a custom page and MUST state this explicitly — Filament does not auto-gate custom pages. The approve action requires `operations.adjustments.approve`; create/stocktake require `operations.adjustments.create`. No public/portal surfaces — the `operations` panel is authenticated only.

**Security note** ([[../../../_archive/build-history/security-audit-2026-06-11]]): the stocktake bulk submission carries a `panel-action` limiter and the report export an `exports` limiter — see [[./security]].

---

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Adjustment create / edit (`AdjustmentService::adjust`, form) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Apply an under-threshold adjustment (`StockService::move(adjust)`) | Pessimistic | `lockForUpdate()` on the `ops_stock_levels` row, validate delta ≤ available, post movement — inventory decrement |
| Approve (`approve`, pending-approval → applied) | Pessimistic | `DB::transaction()` + `lockForUpdate()` on the adjustment (re-check `approved_by ≠ adjusted_by`) and the level row before posting the movement |
| Stocktake bulk apply (`stocktake`) | Pessimistic | per non-zero delta, `lockForUpdate()` on the level row inside the transaction before each `StockService::move(adjust)` |
| Write-off report | n/a (read-only) | Aggregation over `ops_stock_adjustments`; no writes |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

---

## Search & Realtime

- No Meilisearch index in v1 *(assumed)*.
- No realtime.

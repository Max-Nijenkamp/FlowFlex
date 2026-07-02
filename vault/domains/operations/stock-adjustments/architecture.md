---
domain: operations
module: stock-adjustments
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
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

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `StockAdjustmentResource` | #1 CRUD resource | approve action, pending tab, reason/period report filters |
| `StocktakePage` | #7 custom page | warehouse pick → count grid → preview deltas → confirm |

**Access contract:** `canAccess() = Auth::user()->can('operations.adjustments.view-any') && BillingService::hasModule('operations.adjustments')` per [[../../../architecture/filament-patterns]] #1 — the custom page states it explicitly.

**Security note** ([[../../../build/security-audit-2026-06-11]]): rate-limit the stocktake bulk submission (per company) to throttle large bulk-adjustment runs.

---

## Search & Realtime

- No Meilisearch index in v1 *(assumed)*.
- No realtime.

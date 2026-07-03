---
domain: operations
module: goods-receipt
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Goods Receipt — Architecture

## Services & Actions

`GrnService::receive(CreateGrnData $data): GrnData` — one DB transaction doing, in order:

1. write `ops_goods_receipts` + `ops_grn_lines`;
2. per accepted line, `StockService::move(in @ warehouse, qty_accepted, PO line cost)`;
3. `PurchaseOrderService::recordReceipt(poId, lineReceipts)` → updates `quantity_received` + PO status (`partially_received` / `received`);
4. fire `GoodsReceived` (accepted totals only).

All four steps commit together or the whole receipt rolls back (atomic). Steps 2–3 are same-domain service calls (no direct writes to inventory/PO tables); step 4 is the only cross-boundary effect.

Over-receipt beyond `ordered × 1.1` tolerance is rejected before any write *(assumed 10%)*. Rejected quantity requires a `reject_reason`.

---

## Events

### Fires: GoodsReceived

| Payload field | Type | Notes |
|---|---|---|
| company_id | string | always first |
| grn_id | string | |
| po_id | string | |
| supplier_id | string | |
| accepted_total_cents | int | accepted qty × PO line cost, brick/money |
| currency | string | ISO 4217 |
| received_at | CarbonImmutable | |

Intended consumer: `finance.ap` → draft bill + 3-way match (PO ↔ GRN ↔ bill). If finance.ap is inactive the event fires unconsumed. Contract source of truth: [[../../../architecture/event-bus]].

Consumes: none.

---

## Filament Artifacts

**Nav group:** Purchasing

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `GoodsReceiptResource` | #1 CRUD resource | tweaks: read-only-flow-owned (writes owned by `GrnService` via `ReceiveGoodsPage`), state-badge-column | GRN list + read-only view; linked from PO view; list filters: PO, warehouse, status |
| `ReceiveGoodsPage` | #7 wizard custom page | [[../../../architecture/patterns/page-blueprints#Wizard]] *(assumed — a create-from-PO receiving grid framed as a single-flow entry page; not a clean multi-step wizard, see QUESTIONS)* | create-from-PO: lines prefilled with open qty, accept/reject per line, running discrepancy check |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('operations.goods-receipt.view-any') && BillingService::hasModule('operations.goods-receipt')`
per [[../../../architecture/filament-patterns]] #1. `ReceiveGoodsPage` is a custom page and MUST state this explicitly — Filament does not auto-gate custom pages. The `create` action additionally requires `operations.goods-receipt.create`. There are no public/portal surfaces — the `operations` panel is authenticated only.

---

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| GRN creation (`GrnService::receive`) | n/a (append-only) | A GRN is a new immutable receipt record with no update path — no concurrent edit of an existing row; the four steps below run inside one `DB::transaction()` (all-or-nothing) |
| Stock `in` posting (via `StockService::move`) | Pessimistic | `lockForUpdate()` on the stock-level row inside the receive transaction — inventory increment per [[../../../architecture/patterns/optimistic-locking]] pessimistic tier |
| PO receipt + status update (via `PurchaseOrderService::recordReceipt`) | Pessimistic | `DB::transaction()` + `lockForUpdate()` on the PO, re-read `quantity_received`, transition status per [[../../../architecture/patterns/states]] |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

---

## Search & Realtime

- Meilisearch: GRNs indexed on `grn_number`, PO number *(assumed)*.
- No realtime.

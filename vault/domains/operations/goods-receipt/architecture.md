---
domain: operations
module: goods-receipt
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
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

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `GoodsReceiptResource` | #1 CRUD resource | GRN list + read-only view; linked from PO view |
| `ReceiveGoodsPage` | #7 custom page | create-from-PO: lines prefilled with open qty, accept/reject per line, running discrepancy check |

**Access contract:** `canAccess() = Auth::user()->can('operations.goods-receipt.view-any') && BillingService::hasModule('operations.goods-receipt')` per [[../../../architecture/filament-patterns]] #1 — the custom page states it explicitly.

---

## Search & Realtime

- Meilisearch: GRNs indexed on `grn_number`, PO number *(assumed)*.
- No realtime.

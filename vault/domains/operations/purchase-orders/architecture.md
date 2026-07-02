---
domain: operations
module: purchase-orders
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Purchase Orders — Architecture

## State Machine

Column: `ops_purchase_orders.status` — `spatie/laravel-model-states`, base `PoState`.

| State | Transitions to | Triggered by (permission) | Side effects |
|---|---|---|---|
| `draft` | `sent` | `operations.purchase-orders.send` | assign `po_number`, queue `GeneratePoPdfJob` + `PurchaseOrderMail` |
| `sent` | `partially_received` | GRN acceptance (some lines still open) | via `recordReceipt` (same domain) |
| `sent` / `partially_received` | `received` | GRN completes all lines | |
| `draft` / `sent` | `cancelled` | `operations.purchase-orders.cancel` | blocked after any receipt |

Initial: `draft`. Transitions audited via activitylog.

---

## Services & Actions

Interface→Service: `PurchaseOrderServiceInterface` → `PurchaseOrderService`.

| Method | Notes |
|---|---|
| `create(CreatePoData): PoData` | totals via brick/money; line cost defaults from `PreferredSupplierFor::item` |
| `send(string $poId): PoData` | draft→sent; assigns number; queues PDF + mail (rate-limited) |
| `recordReceipt(string $poId, array $lineReceipts): void` | **called BY the GRN module** (same domain); updates `quantity_received` + status |
| `createFromRequisition(string $requisitionId): PoData` | procurement hook |

---

## Events

Fires none. Consumes none. Receiving is a same-domain `recordReceipt` call from GRN; the `GoodsReceived` event is fired by [[../goods-receipt/_module|goods-receipt]], not here.

---

## Filament Artifacts

**Nav group:** Purchasing

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `PurchaseOrderResource` | #1 CRUD resource | line repeater, send/cancel actions, PDF preview, receipt-progress columns |

**Access contract:** `canAccess() = Auth::user()->can('operations.purchase-orders.view-any') && BillingService::hasModule('operations.purchase-orders')` per [[../../../architecture/filament-patterns]] #1.

**Security note** ([[../../../build/security-audit-2026-06-11]]): rate-limit the `send` action / `GeneratePoPdfJob` + `PurchaseOrderMail` dispatch (per-company throttle) to prevent PDF/email abuse.

---

## Search & Realtime

- Meilisearch: POs indexed on `po_number`, supplier name *(assumed)*.
- No realtime.

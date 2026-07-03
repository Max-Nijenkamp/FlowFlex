---
domain: operations
module: purchase-orders
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
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

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `PurchaseOrderResource` | #1 CRUD resource | tweaks: inline-relation-repeater (PO lines), state-badge-column, custom-header-actions (send / cancel / create-from-requisition), pdf-preview-panel | receipt-progress columns; create-from-requisition visible only when procurement active |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('operations.purchase-orders.view-any') && BillingService::hasModule('operations.purchase-orders')`
per [[../../../architecture/filament-patterns]] #1. Header actions each carry their own permission (`send` / `cancel` / `create`); the create-from-requisition action additionally requires the procurement module active. No public/portal surfaces — the `operations` panel is authenticated only.

**Security note** ([[../../../_archive/build-history/security-audit-2026-06-11]]): the `send` action (PDF render + supplier email) is throttled by the `panel-action` limiter — see [[./security]].

---

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| PO CRUD (form, API) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Send (`send`, draft→sent) | Pessimistic | `DB::transaction()` + `lockForUpdate()` state transition (assigns `po_number`, queues PDF + mail) per [[../../../architecture/patterns/states]] |
| Record receipt (`recordReceipt`, →partially_received / received) | Pessimistic | `DB::transaction()` + `lockForUpdate()` on the PO, re-read `quantity_received`, transition status per [[../../../architecture/patterns/states]] |
| Cancel (`cancel`, draft/sent→cancelled) | Pessimistic | `DB::transaction()` + `lockForUpdate()`; re-check no receipt exists before transitioning |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

---

## Search & Realtime

- Meilisearch: POs indexed on `po_number`, supplier name *(assumed)*.
- No realtime.

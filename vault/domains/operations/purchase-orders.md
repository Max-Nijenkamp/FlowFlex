---
type: module
domain: Operations
domain-key: operations
panel: operations
module-key: operations.purchase-orders
status: planned
priority: p3
depends-on: [operations.inventory, operations.suppliers, core.billing, core.rbac, foundation.queues]
soft-depends: [operations.goods-receipt, finance.ap, procurement.requisitions]
fires-events: []
consumes-events: []
patterns: [states, money, pdf, email]
tables: [ops_purchase_orders, ops_po_lines]
permission-prefix: operations.purchase-orders
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Purchase Orders

Create and track purchase orders to suppliers. Goods are received against POs via [[domains/operations/goods-receipt|goods-receipt]] (which fires `GoodsReceived` to Finance — this module fires no events itself).

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/operations/inventory\|operations.inventory]] + [[domains/operations/suppliers\|operations.suppliers]] | lines reference items; PO targets supplier |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/foundation/queue-workers\|foundation.queues]] | gating, permissions, PDF/mail jobs |
| Soft | [[domains/operations/goods-receipt\|operations.goods-receipt]] | receiving; without it POs track status manually |
| Soft | [[domains/finance/accounts-payable\|finance.ap]] | bills via GoodsReceived |
| Soft | [[domains/procurement/requisitions\|procurement.requisitions]] | approved requisitions convert to POs |

---

## Core Features

- PO record: supplier, line items (item, qty, unit cost), expected delivery, status
- Status machine: `draft → sent → partially_received → received | cancelled`
- PO numbering (auto-increment per company, `PO-2026-001`)
- Line items reference inventory items (unit cost defaults from supplier catalogue)
- Receiving handled by GRN module — GRN updates line `quantity_received` + PO status (same-domain direct call)
- Partial receipts supported
- PO PDF generation (spatie/laravel-pdf) and email to supplier
- 3-way match readiness (PO → GRN → bill)

---

## Data Model

### ops_purchase_orders

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| po_number | string | unique `(company_id, po_number)` |
| supplier_id | ulid FK ops_suppliers | |
| status | string default `draft` | state machine |
| expected_delivery | date nullable | |
| total_cents | bigint | Σ lines |
| currency | string(3) | |
| requisition_id | ulid nullable | procurement origin |
| pdf_path | string nullable | |
| deleted_at | timestamp nullable | |

**Indexes:** `(company_id, status)`, `(company_id, supplier_id)`

### ops_po_lines — id, po_id FK, company_id, item_id FK, quantity_ordered decimal(12,2) (> 0), quantity_received decimal(12,2) default 0, unit_cost_cents bigint

---

## State Machine

| State | Transitions to | Triggered by (permission) | Side effects |
|---|---|---|---|
| `draft` | `sent` | `operations.purchase-orders.send` | number assigned, PDF + supplier mail |
| `sent` | `partially_received` | GRN acceptance (some lines open) | |
| `sent` / `partially_received` | `received` | GRN completes all lines | |
| `draft` / `sent` | `cancelled` | `operations.purchase-orders.cancel` | not after any receipt |

Audited.

---

## DTOs

### CreatePoData — supplier_id (active), expected_delivery? (future), lines[{item_id, quantity_ordered > 0, unit_cost_cents?}] min:1 (cost defaults from supplier catalogue — "No cost known for this item from this supplier." when neither given)

## Services & Actions

Interface→Service: `PurchaseOrderServiceInterface` → `PurchaseOrderService`.

- `create(CreatePoData)` / `send(poId)` — totals via brick/money
- `recordReceipt(string $poId, array $lineReceipts): void` — called BY GRN module (same domain); updates lines + status
- `createFromRequisition(string $requisitionId): PoData` — procurement hook

---

## Filament

**Nav group:** Purchasing

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `PurchaseOrderResource` | #1 CRUD resource | line repeater, send/cancel actions, PDF preview, receipt progress columns |

---

## Permissions

`operations.purchase-orders.view-any` · `operations.purchase-orders.create` · `operations.purchase-orders.send` · `operations.purchase-orders.cancel`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] PO numbers sequential unique
- [ ] Totals via brick/money; line cost defaults from supplier catalogue
- [ ] Status transitions per machine; cancel blocked after receipt
- [ ] Partial GRN → partially_received; complete → received
- [ ] Send generates PDF + queues supplier mail
- [ ] Requisition conversion links origin

---

## Build Manifest

```
database/migrations/xxxx_create_ops_purchase_orders_table.php
database/migrations/xxxx_create_ops_po_lines_table.php
app/Models/Operations/{PurchaseOrder,PoLine}.php
app/States/Operations/PurchaseOrder/{PoState,Draft,Sent,PartiallyReceived,Received,Cancelled}.php
app/Data/Operations/{CreatePoData,PoData}.php
app/Contracts/Operations/PurchaseOrderServiceInterface.php
app/Services/Operations/PurchaseOrderService.php
app/Jobs/Operations/GeneratePoPdfJob.php
app/Mail/Operations/PurchaseOrderMail.php
app/Filament/Operations/Resources/PurchaseOrderResource.php
database/factories/Operations/{PurchaseOrderFactory,PoLineFactory}.php
tests/Feature/Operations/{PurchaseOrderTest,PoReceiptTest}.php
```

---

## Related

- [[domains/operations/suppliers]]
- [[domains/operations/inventory]]
- [[domains/operations/goods-receipt]]
- [[domains/finance/accounts-payable]]

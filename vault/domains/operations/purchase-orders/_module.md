---
domain: operations
module: purchase-orders
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Purchase Orders

Create and track purchase orders to suppliers. Goods are received against POs via [[../goods-receipt/_module|goods-receipt]] (which fires `GoodsReceived` to Finance — this module fires no events itself).

> Operations hosts the [[../../procurement/_index|Procurement]] panel. See [[../../../decisions/decision-2026-06-01-panel-consolidation]].

---

## Module-key

`operations.purchase-orders`

**Priority:** p3
**Panel:** operations (Orange)
**Permission prefix:** `operations.purchase-orders`
**Tables:** `ops_purchase_orders`, `ops_po_lines`

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../inventory/_module\|operations.inventory]] | PO lines reference items |
| Hard | [[../suppliers/_module\|operations.suppliers]] | PO targets a supplier; line cost defaults from catalogue |
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] | gating + permissions |
| Hard | [[../../foundation/queue-workers/_module\|foundation.queues]] | PDF + mail jobs |
| Soft | [[../goods-receipt/_module\|operations.goods-receipt]] | receiving; without it PO status is manual |
| Soft | [[../../finance/accounts-payable/_module\|finance.ap]] | bills via `GoodsReceived` |
| Soft | [[../../procurement/requisitions/_module\|procurement.requisitions]] | approved requisitions convert to POs |

---

## Core Features

- PO record: supplier, line items (item, qty, unit cost), expected delivery, status
- Status machine: `draft → sent → partially_received → received | cancelled`
- Auto PO numbering per company (`PO-2026-001`)
- Line cost defaults from preferred supplier catalogue
- Receiving handled by GRN (same-domain direct call updates `quantity_received` + status)
- PO PDF (spatie/laravel-pdf) + email to supplier
- 3-way match readiness (PO → GRN → bill)

See features: [[./features/po-lifecycle|PO Lifecycle]] · [[./features/pdf-and-email|PDF & Supplier Email]] · [[./features/requisition-conversion|Requisition Conversion]].

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

## Test Checklist

- [ ] Tenant isolation: company A cannot see/send/cancel company B's POs
- [ ] Module gating: artifacts hidden when `operations.purchase-orders` inactive
- [ ] PO numbers sequential + unique per company
- [ ] Totals via brick/money; line cost defaults from supplier catalogue
- [ ] Status transitions per machine; cancel blocked after any receipt
- [ ] Partial GRN → partially_received; complete → received
- [ ] Send generates PDF + queues supplier mail (rate-limited)
- [ ] Requisition conversion links origin

---

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | `PreferredSupplierFor::item` | operations.suppliers | line cost default |
| Provides | `PurchaseOrderService::recordReceipt` | operations.goods-receipt (same domain) | GRN updates lines + status |
| Reads | `createFromRequisition` | procurement.requisitions | approved requisition → PO |

**Data ownership:** `operations.purchase-orders` writes only `ops_purchase_orders`, `ops_po_lines`. It fires **no** events; the Finance-facing `GoodsReceived` is fired by [[../goods-receipt/_module|goods-receipt]]. Stock is never written here ([[../../../security/data-ownership]]).

---

## Related

- [[../suppliers/_module|operations.suppliers]]
- [[../inventory/_module|operations.inventory]]
- [[../goods-receipt/_module|operations.goods-receipt]]
- [[../../finance/accounts-payable/_module|finance.ap]]
- [[../_index|Operations MOC]]

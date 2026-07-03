---
domain: operations
module: goods-receipt
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Goods Receipt

Record receipt of goods against purchase orders. Updates inventory (same-domain direct call) and fires `GoodsReceived` for Finance AP bill creation + 3-way match.

> Operations hosts the [[../../procurement/_index|Procurement]] panel. See [[../../../decisions/decision-2026-06-01-panel-consolidation]].

---

## Module-key

`operations.goods-receipt`

**Priority:** p3
**Panel:** operations (Orange)
**Permission prefix:** `operations.goods-receipt`
**Tables:** `ops_goods_receipts`, `ops_grn_lines`

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../purchase-orders/_module\|operations.purchase-orders]] | GRN is received against a PO |
| Hard | [[../inventory/_module\|operations.inventory]] | accepted stock via `StockService::move` |
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] | gating + permissions |
| Soft | [[../../finance/accounts-payable/_module\|finance.ap]] | consumes `GoodsReceived`; event fires unconsumed otherwise |

---

## Core Features

- Goods Receipt Note (GRN) linked to a PO; received quantities per line
- Partial receipts: receive some lines/quantities, leave PO partially open
- Quality check: accept/reject received quantities with reason
- Auto-update stock on acceptance (movement `in` at PO line cost)
- Discrepancy flagging: received ≠ ordered (over-receipt blocked beyond 10% tolerance *(assumed)*)
- 3-way match reference (PO ↔ GRN ↔ supplier bill)
- GRN numbering per company

See features: [[./features/receiving|Receiving]] · [[./features/quality-check|Quality Check]] · [[./features/three-way-match-event|GoodsReceived Event]].

---

## Build Manifest

```
database/migrations/xxxx_create_ops_goods_receipts_table.php
database/migrations/xxxx_create_ops_grn_lines_table.php
app/Models/Operations/{GoodsReceipt,GrnLine}.php
app/Data/Operations/{CreateGrnData,GrnData}.php
app/Services/Operations/GrnService.php
app/Events/Operations/GoodsReceived.php
app/Filament/Operations/Resources/GoodsReceiptResource.php
app/Filament/Operations/Pages/ReceiveGoodsPage.php
database/factories/Operations/GoodsReceiptFactory.php
tests/Feature/Operations/{GoodsReceiptTest,ThreeWayMatchEventTest}.php
```

---

## Test Checklist

- [ ] Tenant isolation: company A cannot receive against company B's POs
- [ ] Module gating: artifacts hidden when `operations.goods-receipt` inactive
- [ ] Accepted qty creates stock movement at PO line cost; rejected does not
- [ ] accepted + rejected = received enforced; over-receipt beyond tolerance rejected
- [ ] Partial GRN → PO partially_received; completing GRN → received
- [ ] `GoodsReceived` fired with contract payload (accepted totals only)
- [ ] Rejection requires a reason
- [ ] Atomic: stock + PO + event together or nothing

---

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Fires | `GoodsReceived` | finance.ap | draft bill + 3-way match; carries accepted totals only |
| Reads / calls | `StockService::move`, `PurchaseOrderService::recordReceipt` | operations.inventory, operations.purchase-orders (same domain) | stock in + PO status update |

**Data ownership:** `operations.goods-receipt` writes only `ops_goods_receipts`, `ops_grn_lines`. Stock is written by inventory's `StockService`; PO receipts by `PurchaseOrderService` (both same-domain calls). The **only** cross-domain effect is firing `GoodsReceived` — finance.ap's own listener writes finance tables, never this module ([[../../../security/data-ownership]]).

---

## Related

- [[../purchase-orders/_module|operations.purchase-orders]]
- [[../inventory/_module|operations.inventory]]
- [[../../finance/accounts-payable/_module|finance.ap]]
- [[../../../architecture/event-bus]]
- [[../_index|Operations MOC]]

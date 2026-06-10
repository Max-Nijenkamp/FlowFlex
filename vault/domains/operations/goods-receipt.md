---
type: module
domain: Operations
domain-key: operations
panel: operations
module-key: operations.goods-receipt
status: planned
priority: p3
depends-on: [operations.purchase-orders, operations.inventory, core.billing, core.rbac]
soft-depends: [finance.ap]
fires-events: [GoodsReceived]
consumes-events: []
patterns: [events]
tables: [ops_goods_receipts, ops_grn_lines]
permission-prefix: operations.goods-receipt
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Goods Receipt

Record receipt of goods against purchase orders. Updates inventory (same-domain direct call) and fires `GoodsReceived` for Finance AP bill creation + 3-way match.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/operations/purchase-orders\|operations.purchase-orders]] + [[domains/operations/inventory\|operations.inventory]] | GRN against PO; accepted stock via `StockService::move` |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | [[domains/finance/accounts-payable\|finance.ap]] | consumes `GoodsReceived`; event fires unconsumed otherwise |

---

## Core Features

- Goods Receipt Note (GRN): linked to a PO, records received quantities per line
- Partial receipts: receive some lines/quantities, leave PO partially open
- Quality check: accept/reject received quantities with reason
- Auto-update stock levels on acceptance (movement `in` with PO line cost)
- Discrepancy flagging: received qty ≠ ordered qty (over-receipt blocked beyond 10% tolerance *(assumed)*)
- GRN reference for 3-way match (PO ↔ GRN ↔ supplier bill)
- Receipt history per PO and per item
- GRN numbering per company

---

## Data Model

### ops_goods_receipts

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| grn_number | string | unique `(company_id, grn_number)` |
| po_id | ulid FK | |
| warehouse_id | ulid FK | |
| received_by | ulid FK users | |
| received_at | timestamp | |
| status | string default `accepted` | accepted / partially-rejected *(assumed: simple enum)* |

### ops_grn_lines — id, grn_id FK, company_id, po_line_id FK, item_id FK, quantity_received decimal, quantity_accepted decimal, quantity_rejected decimal (received = accepted + rejected — cross-check), reject_reason nullable (required when rejected > 0)

---

## DTOs

### CreateGrnData — po_id (sent/partially_received), warehouse_id, lines[{po_line_id, quantity_received > 0, quantity_accepted, quantity_rejected, reject_reason?}] — per line: accepted + rejected = received; cumulative received ≤ ordered × 1.1

## Services & Actions

- `GrnService::receive(CreateGrnData $data): GrnData` — one transaction: GRN rows + `StockService::move(in)` per accepted line + `PurchaseOrderService::recordReceipt` + fires `GoodsReceived`

## Events

### Fires: GoodsReceived
| Payload field | Type |
|---|---|
| company_id | string |
| grn_id | string |
| po_id | string |
| supplier_id | string |
| accepted_total_cents | int |
| currency | string |
| received_at | CarbonImmutable |

Consumer: finance.ap draft bill + 3-way match ([[architecture/event-bus]]).

---

## Filament

**Nav group:** Purchasing

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `GoodsReceiptResource` | #1 CRUD resource | create-from-PO (lines prefilled with open qty), accept/reject per line; linked from PO view |

---

## Permissions

`operations.goods-receipt.view-any` · `operations.goods-receipt.create`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Accepted qty creates stock movement at PO line cost; rejected does not
- [ ] accepted + rejected = received enforced; over-receipt beyond tolerance rejected
- [ ] Partial GRN leaves PO partially_received; completing GRN closes PO
- [ ] `GoodsReceived` fired with contract payload (accepted totals only)
- [ ] Rejection requires reason
- [ ] Atomic: stock + PO + event together or nothing

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
database/factories/Operations/GoodsReceiptFactory.php
tests/Feature/Operations/{GoodsReceiptTest,ThreeWayMatchEventTest}.php
```

---

## Related

- [[domains/operations/purchase-orders]]
- [[domains/operations/inventory]]
- [[domains/finance/accounts-payable]]
- [[architecture/event-bus]]

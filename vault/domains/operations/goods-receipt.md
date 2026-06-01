---
type: module
domain: Operations
panel: operations
module-key: operations.goods-receipt
status: planned
color: "#4ADE80"
---

# Goods Receipt

Record receipt of goods against purchase orders. Updates inventory and enables 3-way match with supplier invoices.

## Core Features

- Goods Receipt Note (GRN): linked to a PO, records received quantities per line
- Partial receipts: receive some lines/quantities, leave PO partially open
- Quality check: accept/reject received quantities with reason
- Auto-update stock levels on acceptance
- Discrepancy flagging: received qty ≠ ordered qty
- GRN reference for 3-way match (PO ↔ GRN ↔ supplier invoice)
- Receipt history per PO and per item

## Data Model

| Table | Key Columns |
|---|---|
| `ops_goods_receipts` | company_id, grn_number, po_id, warehouse_id, received_by, received_at, status |
| `ops_grn_lines` | grn_id, company_id, po_line_id, item_id, quantity_received, quantity_accepted, quantity_rejected, reject_reason |

## Filament

**Nav group:** Purchasing

- `GoodsReceiptResource` — create GRN from PO, record quantities, accept/reject
- Linked from PO view page

## Cross-Domain / Events

- Fires `GoodsReceived` → Inventory (add accepted stock), Finance (enable 3-way match)

## Related

- [[domains/operations/purchase-orders]]
- [[domains/operations/inventory]]
- [[domains/finance/accounts-payable]]

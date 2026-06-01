---
type: module
domain: Operations
panel: operations
module-key: operations.purchase-orders
status: planned
color: "#4ADE80"
---

# Purchase Orders

Create and track purchase orders to suppliers. Receive goods against POs and update inventory.

## Core Features

- PO record: supplier, line items (item, qty, unit cost), expected delivery, status
- Status machine: `draft → sent → partially_received → received → cancelled`
- PO numbering (auto-increment per company)
- Line items reference inventory items
- Goods receipt: record received quantities, update stock levels
- Partial receipts supported
- PO PDF generation (spatie/laravel-pdf) and email to supplier
- Integration with Finance AP: received PO creates a supplier invoice/bill
- 3-way match readiness (PO → receipt → invoice)

## Data Model

| Table | Key Columns |
|---|---|
| `ops_purchase_orders` | company_id, po_number, supplier_id, status, expected_delivery, total_cents, currency |
| `ops_po_lines` | po_id, company_id, item_id, quantity_ordered, quantity_received, unit_cost_cents |

## Filament

**Nav group:** Purchasing

- `PurchaseOrderResource` — list, create, send, receive goods action, view
- PO PDF preview

## Cross-Domain / Events

- Fires `PurchaseOrderReceived` → Finance (create bill/AP), Inventory (add stock)

## Related

- [[domains/operations/suppliers]]
- [[domains/operations/inventory]]
- [[domains/finance/accounts-payable]]

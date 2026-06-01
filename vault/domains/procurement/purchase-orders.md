---
type: module
domain: Procurement
panel: operations
module-key: procurement.purchase-orders
status: planned
color: "#4ADE80"
---

# Purchase Orders (Procurement view)

Procurement-managed purchase orders created from approved requisitions. Shares the PO data model with Operations but adds a procurement approval and sourcing layer.

> **Note**: The PO entity is defined in [[domains/operations/purchase-orders]]. This module adds the procurement-specific workflow (sourcing, approval, supplier selection) when both Operations and Procurement are active. If Operations is not active, Procurement provides its own lightweight PO handling.

## Core Features

- Create PO from approved requisition
- Supplier selection / sourcing: compare quotes from suppliers
- PO approval (separate from requisition approval — final sign-off)
- Send PO to supplier (PDF + email)
- Track PO status to receipt
- Spend commitment tracking (committed vs actual)

## Data Model

Uses `ops_purchase_orders` + `ops_po_lines` (shared with Operations). Adds:

| Table | Key Columns |
|---|---|
| `proc_po_sourcing` | po_id, company_id, supplier_id, quote_amount_cents, selected |

## Filament

**Nav group:** Purchase Orders

- `ProcurementPoResource` — sourcing comparison, approval, send
- Quote comparison view

## Related

- [[domains/operations/purchase-orders]]
- [[domains/procurement/requisitions]]
- [[domains/procurement/supplier-catalogue]]

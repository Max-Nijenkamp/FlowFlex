---
type: module
domain: Operations
panel: operations
module-key: operations.purchase-orders
status: planned
color: "#4ADE80"
---

# Purchase Orders

> Create and approve purchase orders, receive goods against them, and match supplier invoices to eliminate purchase fraud and data entry duplication.

**Panel:** `operations`
**Module key:** `operations.purchase-orders`

## What It Does

Purchase Orders manages the full procure-to-receive cycle. A buyer creates a PO listing line items, quantities, and agreed unit prices against a chosen supplier. The PO routes through a configurable approval workflow before being sent. When goods arrive the warehouse team records a goods receipt against the PO — partial receipts are supported. Finance then three-way matches the supplier invoice against the PO and goods receipt before approving payment. This process eliminates duplicate payments and unauthorised spend.

## Features

### Core
- PO creation: select supplier, add line items (product, quantity, unit cost, tax code), set delivery date and destination warehouse
- PO numbering: auto-generated sequential PO number with configurable prefix
- Status workflow: draft → submitted → approved → sent → partially received → fully received → closed
- Approval workflow: single or multi-level approvals with configurable spend thresholds per approver tier
- Send to supplier: email PDF to supplier directly from the PO record
- Goods receipt: record received quantities per line; partial receipt leaves PO open for outstanding balance
- Three-way matching: link supplier invoice to PO and goods receipt; flag discrepancies in quantity or price

### Advanced
- Blanket POs: standing order with a total value cap; goods receipts draw down against the blanket
- PO amendment: raise a change order on a sent PO; supplier must acknowledge amendment
- Delivery schedule: split a PO line into multiple expected delivery dates
- Landed cost allocation: allocate freight, duty, and insurance costs across received line items
- Supplier catalogue integration: pull unit prices from supplier catalogue in [[supplier-management]]
- Budget checking: warn buyer if PO would exceed department budget from [[../finance/INDEX]]

### AI-Powered
- Price variance alert: flag if negotiated unit cost deviates more than X% from historical average for that SKU
- Demand-driven PO suggestion: auto-draft PO when stock falls below reorder point based on lead time

## Data Model

```erDiagram
    ops_purchase_orders {
        ulid id PK
        ulid company_id FK
        string po_number
        ulid supplier_id FK
        ulid warehouse_id FK
        string status
        decimal total_value
        string currency
        date expected_delivery
        ulid created_by FK
        ulid approved_by FK
        timestamp approved_at
        timestamps timestamps
        softDeletes deleted_at
    }

    ops_po_lines {
        ulid id PK
        ulid po_id FK
        ulid product_id FK
        string description
        integer qty_ordered
        integer qty_received
        decimal unit_cost
        decimal line_total
        timestamps timestamps
    }

    ops_goods_receipts {
        ulid id PK
        ulid po_id FK
        ulid warehouse_id FK
        ulid received_by FK
        date received_on
        string notes
        timestamps timestamps
    }

    ops_grn_lines {
        ulid id PK
        ulid grn_id FK
        ulid po_line_id FK
        integer qty_received
        string lot_number
        date expiry_date
    }

    ops_purchase_orders ||--o{ ops_po_lines : "contains"
    ops_purchase_orders ||--o{ ops_goods_receipts : "receipted by"
    ops_goods_receipts ||--o{ ops_grn_lines : "details"
```

| Table | Purpose |
|---|---|
| `ops_purchase_orders` | PO header with supplier, status, and approval |
| `ops_po_lines` | Line items with ordered and received quantities |
| `ops_goods_receipts` | Goods receipt note (GRN) per delivery |
| `ops_grn_lines` | Per-line received quantities with lot/expiry |

## Permissions

```
operations.purchase-orders.view-any
operations.purchase-orders.create
operations.purchase-orders.approve
operations.purchase-orders.receive
operations.purchase-orders.delete
```

## Filament

**Resource class:** `PurchaseOrderResource`
**Pages:** List, Create, Edit, View
**Custom pages:** `GoodsReceiptPage` (receive goods against an open PO)
**Widgets:** `PendingApprovalsWidget` (POs awaiting the current user's approval)
**Nav group:** Inventory

## Displaces

| Competitor | Feature Replaced |
|---|---|
| TradeGecko / Cin7 Purchases | Purchase order management |
| SAP Business One Purchasing | Full procure-to-pay cycle |
| Procurify | PO approvals and spend management |
| Coupa (SMB) | PO creation and three-way matching |

## Related

- [[inventory]] — goods receipts update stock levels
- [[supplier-management]] — supplier records and catalogue prices
- [[warehousing]] — received goods go to warehouse bins
- [[quality-control]] — inspection gate on receipt before stock update
- [[../finance/INDEX]] — supplier invoices matched against POs

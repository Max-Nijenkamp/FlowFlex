---
type: module
domain: Procurement & Spend Management
panel: procurement
module-key: procurement.orders
status: planned
color: "#4ADE80"
---

# Purchase Orders

> PO creation from approved requisitions, supplier confirmation tracking, and three-way match with GRNs and invoices.

**Panel:** `procurement`
**Module key:** `procurement.orders`

---

## What It Does

Purchase Orders formalises a purchase commitment into a legally binding document sent to the supplier. Once a requisition is approved, the procurement team converts it to a PO — confirming quantities, final pricing, delivery date, and payment terms. The PO is emailed to the supplier with a PDF attachment and a confirmation request. When goods arrive, the PO is matched against the Goods Received Note (GRN) and subsequently against the supplier's invoice to complete a three-way match before authorising payment.

---

## Features

### Core
- PO creation from requisition: pre-populate from the approved requisition with editable fields
- PO numbering: auto-generated sequential PO numbers with configurable prefix
- Line item detail: description, quantity ordered, unit price, total, delivery date
- Supplier email: send the PO PDF to the supplier with a supplier acknowledgement request
- PO status: draft, sent, confirmed, partially received, fully received, closed, cancelled
- PO PDF: branded purchase order document with company and supplier details

### Advanced
- Manual PO creation: raise a PO directly without a preceding requisition for framework contracts
- Partial deliveries: track partial receipt of goods against a PO
- PO amendments: issue a PO amendment when quantities or prices change after issue
- Supplier portal access: optional link for the supplier to confirm the PO and provide an expected delivery date
- Blanket POs: framework orders with a total value limit from which multiple call-offs are drawn

### AI-Powered
- Three-way match: automatically match PO quantities and prices against GRN and invoice; flag discrepancies
- Lead time prediction: predict the expected delivery date based on supplier's historical lead times for similar items
- Price variance alert: flag when the invoiced price differs from the PO price by more than a threshold

---

## Data Model

```erDiagram
    purchase_orders {
        ulid id PK
        ulid requisition_id FK
        ulid supplier_id FK
        ulid company_id FK
        string po_number
        date issue_date
        date expected_delivery_date
        decimal total_amount
        string currency
        json line_items
        string status
        string pdf_url
        timestamps created_at_updated_at
    }

    purchase_orders }o--|| purchase_requisitions : "derived from"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `purchase_orders` | PO records | `id`, `requisition_id`, `supplier_id`, `po_number`, `issue_date`, `total_amount`, `status` |

---

## Permissions

```
procurement.orders.view-any
procurement.orders.create
procurement.orders.update
procurement.orders.cancel
procurement.orders.send-to-supplier
```

---

## Filament

- **Resource:** `App\Filament\Procurement\Resources\PurchaseOrderResource`
- **Pages:** `ListPurchaseOrders`, `CreatePurchaseOrder`, `EditPurchaseOrder`, `ViewPurchaseOrder`
- **Custom pages:** `PoMatchingPage`, `OpenPoReportPage`
- **Widgets:** `OpenPosWidget`, `PendingDeliveryWidget`
- **Nav group:** Orders

---

## Displaces

| Feature | FlowFlex | Coupa | SAP Ariba | Procurify |
|---|---|---|---|---|
| Requisition-to-PO conversion | Yes | Yes | Yes | Yes |
| Three-way matching | Yes | Yes | Yes | No |
| Blanket POs | Yes | Yes | Yes | No |
| AI lead time prediction | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[purchase-requisitions]] — POs originate from approved requisitions
- [[goods-received-notes]] — GRNs matched against POs on delivery
- [[supplier-catalog]] — supplier details drawn from the catalog
- [[finance/INDEX]] — matched POs authorise payment in accounts payable

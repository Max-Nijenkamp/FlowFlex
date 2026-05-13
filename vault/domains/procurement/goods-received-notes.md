---
type: module
domain: Procurement & Spend Management
panel: procurement
module-key: procurement.grns
status: planned
color: "#4ADE80"
---

# Goods Received Notes

> GRN creation against open POs — received items vs ordered quantity, partial receipt handling, and discrepancy flagging.

**Panel:** `procurement`
**Module key:** `procurement.grns`

---

## What It Does

Goods Received Notes (GRNs) confirm that goods or services have been received and in what quantity and condition. When a delivery arrives, the receiving team creates a GRN against the relevant purchase order, records the quantities actually received per line item, notes any damage or shortages, and attaches a delivery note photo if needed. The GRN is matched against the PO to detect discrepancies, and the matched data feeds the three-way match process when the supplier's invoice arrives. Partial deliveries create an open balance on the PO for the remaining items.

---

## Features

### Core
- GRN creation: linked to an open PO; pre-populate line items from the PO
- Quantity received: enter actual received quantity per line item; automatic comparison to ordered quantity
- Delivery note reference: record the supplier's delivery note number for cross-referencing
- Damage and shortfall recording: flag damaged or missing items with a description
- Partial receipt: receive part of a PO and leave remaining lines open for future delivery
- GRN status: draft, confirmed, matched, discrepancy

### Advanced
- Quality inspection: record a pass/fail quality check per line item before confirming receipt
- Batch/lot tracking: record batch or lot numbers for regulated goods
- Return to supplier: create a returns note for goods that fail inspection
- Attachment upload: attach delivery note photo or packing slip
- Automated PO closure: auto-close the PO when all lines are fully received

### AI-Powered
- Three-way match automation: automatically compare GRN quantities and prices against the PO and incoming invoice
- Discrepancy resolution suggestion: AI suggests whether a discrepancy should result in a credit note or PO amendment
- Delivery performance tracking: score each supplier's on-time and in-full delivery performance

---

## Data Model

```erDiagram
    goods_received_notes {
        ulid id PK
        ulid purchase_order_id FK
        ulid supplier_id FK
        ulid company_id FK
        ulid received_by FK
        string grn_number
        date received_date
        string delivery_note_reference
        string status
        json line_items
        timestamps created_at_updated_at
    }

    grn_discrepancies {
        ulid id PK
        ulid grn_id FK
        string po_line_item_id
        integer ordered_quantity
        integer received_quantity
        string discrepancy_type
        text notes
        string resolution
    }

    goods_received_notes ||--o{ grn_discrepancies : "has"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `goods_received_notes` | GRN records | `id`, `purchase_order_id`, `supplier_id`, `grn_number`, `received_date`, `status` |
| `grn_discrepancies` | Quantity discrepancies | `id`, `grn_id`, `ordered_quantity`, `received_quantity`, `discrepancy_type`, `resolution` |

---

## Permissions

```
procurement.grns.create
procurement.grns.view-any
procurement.grns.confirm
procurement.grns.view-discrepancies
procurement.grns.export
```

---

## Filament

- **Resource:** `App\Filament\Procurement\Resources\GoodsReceivedNoteResource`
- **Pages:** `ListGoodsReceivedNotes`, `CreateGoodsReceivedNote`, `ViewGoodsReceivedNote`
- **Custom pages:** `DiscrepancyResolutionPage`, `ThreeWayMatchPage`
- **Widgets:** `OpenDeliveriesWidget`, `DiscrepancyAlertWidget`
- **Nav group:** Orders

---

## Displaces

| Feature | FlowFlex | Coupa | SAP Ariba | Procurify |
|---|---|---|---|---|
| GRN creation against PO | Yes | Yes | Yes | Yes |
| Partial receipt handling | Yes | Yes | Yes | Yes |
| Three-way match | Yes | Yes | Yes | No |
| AI discrepancy resolution | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[purchase-orders]] — GRNs matched against open POs
- [[supplier-catalog]] — supplier on GRN references catalog
- [[spend-analytics]] — goods received data feeds category spend analytics
- [[finance/INDEX]] — three-way match authorises payment

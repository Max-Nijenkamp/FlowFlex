---
type: module
domain: Procurement & Spend Management
panel: procurement
phase: 3
status: complete
cssclasses: domain-procurement
migration_range: 981000–981499
last_updated: 2026-05-12
---

# Goods Received Notes (GRN)

Confirm delivery of goods or completion of services against a PO. GRN triggers the 3-way match process and, for physical goods, updates inventory.

---

## GRN Creation

When goods arrive or service is completed:
1. Warehouse/admin opens the PO
2. Enters quantities received per line item
3. Notes any discrepancies (short delivery, damaged goods, wrong items)
4. Uploads delivery documentation (delivery note, service completion certificate)
5. Submits GRN → triggers [[three-way-match-invoice-approval]]

### Partial Receipts
GRN can cover partial delivery (PO partially received):
- PO status → `Partially Received`
- Remaining open quantity tracked
- Multiple GRNs allowed against one PO until fully received

### Service Confirmation
For service POs (consulting, maintenance, etc.):
- "Goods received" = "Service completed/delivered"
- Confirmation by project manager or department head
- Optionally link to project milestone completion

---

## Discrepancy Handling

If received quantity ≠ PO quantity:
- Short delivery: flag → notify supplier → credit note or re-delivery
- Over-delivery: flag → accept excess at PO price OR return surplus
- Wrong items: flag → return merchandise authorisation (RMA to supplier)
- Damaged: flag → photo upload → insurance claim or supplier credit

---

## Inventory Integration

For physical goods:
- GRN triggers stock increase in [[warehouse-management]] module
- Lot/serial number assignment at receiving (if lot-tracked items)
- PO unit cost becomes stock cost (for FIFO/FEFO costing)

---

## Data Model

### `proc_grns`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| po_id | ulid | FK |
| received_by | ulid | FK `employees` |
| received_date | date | |
| status | enum | draft/submitted/with_discrepancy/accepted |
| delivery_ref | varchar(100) | supplier delivery note number |
| notes | text | nullable |

### `proc_grn_line_items`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| grn_id | ulid | FK |
| po_line_item_id | ulid | FK |
| quantity_ordered | decimal(10,4) | |
| quantity_received | decimal(10,4) | |
| discrepancy_type | enum | nullable: short/over/damaged/wrong |
| notes | text | nullable |

---

## Migration

```
981000_create_proc_grns_table
981001_create_proc_grn_line_items_table
```

---

## Related

- [[MOC_Procurement]]
- [[purchase-orders]]
- [[three-way-match-invoice-approval]]
- [[MOC_Operations]] — inventory update on receipt

---
type: module
domain: Procurement & Spend Management
panel: procurement
phase: 3
status: complete
cssclasses: domain-procurement
migration_range: 980500–980999
last_updated: 2026-05-12
---

# Purchase Orders

Formal POs issued to suppliers. Auto-generated from approved requisitions or created manually. PO number is the legal reference for the transaction — required for 3-way match.

---

## PO Creation

From approved requisition (auto-convert) or manual (direct PO without requisition for low-value items).

PO contains:
- PO number (sequential, tenant-prefixed: `PO-2026-04521`)
- Supplier details (from supplier register)
- Line items: description, quantity, unit price, total
- Delivery address
- Required by date
- Payment terms (Net 30, Net 60, etc.)
- Cost centre / department
- Budget code
- Approver signature

---

## PO States

```
Draft → Issued (sent to supplier) → Partially Received → Fully Received → Closed
                                  → Cancelled
```

### Sending
- Email PDF to supplier contacts
- Supplier portal: supplier logs in and acknowledges PO receipt
- EDI (Electronic Data Interchange): for large suppliers with EDI capability

---

## PO Amendments

If scope changes post-issue:
- Amendment request → approval workflow → Revised PO issued (PO-2026-04521-R1)
- Amendment log: all changes tracked with reason and approver

---

## Data Model

### `proc_purchase_orders`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| po_number | varchar(50) | unique per tenant |
| requisition_id | ulid | nullable FK |
| supplier_id | ulid | FK |
| status | enum | draft/issued/partially_received/fully_received/closed/cancelled |
| issued_date | date | nullable |
| required_by_date | date | nullable |
| payment_terms | varchar(100) | "Net 30" |
| subtotal | decimal(14,2) | |
| tax_amount | decimal(14,2) | |
| total | decimal(14,2) | |
| currency | char(3) | |
| notes | text | nullable |

---

## Migration

```
980500_create_proc_purchase_orders_table
980501_create_proc_po_line_items_table
980502_create_proc_po_amendments_table
```

---

## Related

- [[MOC_Procurement]]
- [[purchase-requisitions]]
- [[goods-received-notes-grn]]
- [[three-way-match-invoice-approval]]

---
type: module
domain: Procurement & Spend Management
panel: procurement
phase: 3
status: complete
cssclasses: domain-procurement
migration_range: 981500–981999
last_updated: 2026-05-12
---

# 3-Way Match & Invoice Approval

Automatically match supplier invoices against POs and GRNs before approving for payment. Prevents overpayment, duplicate invoices, and fraud.

---

## The 3-Way Match

Three documents must align:
1. **Purchase Order** — what was ordered at what price
2. **Goods Received Note** — what was actually delivered
3. **Supplier Invoice** — what the supplier is charging

Match rules (configurable tolerances):
- Invoice quantity ≤ GRN quantity ✓
- Invoice unit price within ±2% of PO unit price ✓
- Invoice total within ±2% tolerance ✓

**All match → auto-approve for payment**  
**Any mismatch → flag for manual review**

---

## Invoice Capture

Supplier invoices enter via:
- **Email**: supplier sends PDF to `invoices@company.flowflex.io` → OCR extraction (via AI doc processing module)
- **Supplier portal**: supplier uploads invoice against their PO
- **Manual upload**: AP clerk uploads PDF
- **EDI**: electronic invoice from large suppliers

OCR extracts: PO number, invoice date, line items, totals. Human reviews extracted fields before match.

---

## Exception Handling

When match fails:
| Exception | Action |
|---|---|
| Price variance > tolerance | Route to procurement manager |
| Quantity over GRN | Contact supplier for credit note |
| No PO found | Route to AP manager (maverick spend) |
| Duplicate invoice number | Auto-reject, notify AP clerk |
| Invoice for cancelled PO | Auto-hold, notify AP + buyer |

Exception resolved → approve for payment OR raise dispute with supplier.

---

## Payment Release

Approved invoices pushed to Finance AP as a payment run:
- Scheduled payment runs (e.g., every Wednesday + Friday)
- Manual immediate payment (for urgent)
- BACS/SEPA batch file generated → uploaded to bank
- Payment confirmation updates invoice status → sent to supplier

---

## Data Model

### `proc_supplier_invoices`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| supplier_id | ulid | FK |
| po_id | ulid | nullable FK |
| grn_ids | json | array of GRN IDs |
| invoice_number | varchar(100) | |
| invoice_date | date | |
| due_date | date | |
| match_status | enum | pending/matched/exception/approved/rejected |
| exception_type | varchar(100) | nullable |
| approved_at | timestamp | nullable |
| payment_ref | varchar(100) | nullable |
| total | decimal(14,2) | |
| currency | char(3) | |

---

## Migration

```
981500_create_proc_supplier_invoices_table
981501_create_proc_invoice_match_results_table
981502_create_proc_invoice_exceptions_table
```

---

## Related

- [[MOC_Procurement]]
- [[purchase-orders]]
- [[goods-received-notes-grn]]
- [[MOC_Finance]] — approved invoices → AP payment run

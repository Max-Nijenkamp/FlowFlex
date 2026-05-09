---
tags: [flowflex, domain/operations, vendor, supplier, portal, phase/5]
domain: Operations
panel: operations
color: "#D97706"
status: planned
last_updated: 2026-05-08
---

# Vendor Portal

A self-service portal for your suppliers. They can submit invoices, view open purchase orders, update delivery status, and upload compliance documents — without emailing your procurement team 15 times. Reduces admin, accelerates AP, and keeps supplier data current. Replaces manual supplier communication and expensive vendor management add-ons.

**Who uses it:** Procurement managers, AP team, external suppliers
**Filament Panel:** `operations` (admin); Vue + Inertia (supplier portal)
**Depends on:** Core, [[Purchasing & Procurement]], [[Accounts Payable & Receivable]], [[Contact & Company Management]]
**Phase:** 5

---

## Features

### Supplier Self-Registration

- Invite link: send invitation email to supplier with unique registration link
- Registration form: company name, VAT number, bank account details, payment terms preference
- Document upload: certifications, insurance certificates, quality certificates on registration
- Approval: admin reviews and approves/rejects supplier registration
- Automatic CRM company record created on approval

### Purchase Order Visibility

- Supplier sees all open POs addressed to them
- PO details: line items, quantities, delivery address, required delivery date, terms
- Acknowledge PO: supplier confirms they received and will fulfil
- Update delivery date: supplier can update expected delivery date (flags if changed)
- Partial deliveries: supplier marks which lines will ship in which batch
- Download: PDF of PO available for supplier's own records

### Invoice Submission

- Supplier uploads invoice PDF or enters line items manually
- Invoice matched to PO automatically (2-way or 3-way match)
- Discrepancy flagging: if invoice amount ≠ PO amount → flagged for procurement review
- Invoice status visible to supplier: received / under review / approved / paid / rejected
- Rejection reason shown to supplier with instructions to resubmit

### Delivery Notifications

- Supplier marks goods as dispatched, enters tracking number/carrier
- Auto-notification to operations team: "Supplier X dispatched order, ETA Thursday"
- Goods receipt: operations team confirms receipt in [[Purchasing & Procurement]] — supplier notified

### Document Management

- Supplier uploads and maintains compliance documents:
  - Insurance certificate (expiry tracked)
  - Quality management certificates (ISO 9001, ISO 14001)
  - Safety certification
  - Bank details (secure upload)
- Document expiry alerts: 60/30 days before expiry → supplier notified to re-upload
- Admin can block supplier orders if compliance documents expired

### Supplier Communication

- Messaging thread per PO: supplier ↔ procurement team
- Threaded, timestamped, all parties see same history
- Attachments in thread
- Email notification for new messages

### Performance Scorecard (Supplier View)

- Suppliers can see their own performance data:
  - On-time delivery rate (last 12 months)
  - Invoice accuracy rate
  - Return/rejection rate
  - Overall score band: A / B / C / D
- Motivation: transparency improves supplier behaviour

---

## Database Tables (3)

### `operations_vendor_portal_accounts`
| Column | Type | Notes |
|---|---|---|
| `supplier_id` | ulid FK | → crm_companies |
| `email` | string | portal login |
| `status` | enum | `pending`, `approved`, `suspended` |
| `bank_account_details` | json encrypted | IBAN, BIC, account name |
| `approved_at` | timestamp nullable | |
| `approved_by` | ulid FK nullable | |

### `operations_supplier_invoices`
| Column | Type | Notes |
|---|---|---|
| `purchase_order_id` | ulid FK nullable | |
| `supplier_id` | ulid FK | |
| `invoice_number` | string | supplier's invoice ref |
| `invoice_date` | date | |
| `due_date` | date | |
| `amount` | decimal | |
| `currency` | string | |
| `file_id` | ulid FK | uploaded PDF |
| `match_status` | enum | `unmatched`, `matched`, `discrepancy` |
| `status` | enum | `submitted`, `review`, `approved`, `paid`, `rejected` |
| `rejection_reason` | text nullable | |

### `operations_vendor_documents`
| Column | Type | Notes |
|---|---|---|
| `supplier_id` | ulid FK | |
| `document_type` | string | insurance, iso_cert, safety, bank_details |
| `file_id` | ulid FK | |
| `expires_at` | date nullable | |
| `status` | enum | `valid`, `expiring`, `expired` |
| `uploaded_at` | timestamp | |

---

## Permissions

```
operations.vendor-portal.manage
operations.vendor-portal.view-invoices
operations.vendor-portal.approve-invoices
operations.vendor-portal.view-documents
```

---

## Competitor Comparison

| Feature | FlowFlex | Coupa | Jaggaer | SAP Ariba |
|---|---|---|---|---|
| No separate subscription | ✅ | ❌ (€€€€) | ❌ (€€€€) | ❌ (€€€€) |
| Supplier self-registration | ✅ | ✅ | ✅ | ✅ |
| Invoice submission portal | ✅ | ✅ | ✅ | ✅ |
| 3-way PO matching | ✅ | ✅ | ✅ | ✅ |
| Compliance doc expiry tracking | ✅ | ✅ | ✅ | ✅ |
| SMB-friendly (< 500 employees) | ✅ | ❌ | ❌ | ❌ |

---

## Related

- [[Operations Overview]]
- [[Purchasing & Procurement]]
- [[Accounts Payable & Receivable]]
- [[Contact & Company Management]]
- [[Supply Chain Visibility]]

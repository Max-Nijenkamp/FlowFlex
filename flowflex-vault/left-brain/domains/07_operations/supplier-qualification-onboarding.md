---
type: module
domain: Operations & Field Service
panel: operations
cssclasses: domain-operations
phase: 5
status: planned
migration_range: 300000–399999
last_updated: 2026-05-09
---

# Supplier Qualification & Onboarding

Structured process for onboarding new suppliers — self-service registration, document collection, qualification scoring, approval workflow. Separate from Vendor Portal (which handles ongoing PO/invoice management for approved suppliers).

**Panel:** `operations`  
**Phase:** 5

---

## Features

### Supplier Self-Registration Portal (`/suppliers/register`)
- Public or invite-only registration page
- Supplier fills: company details, contact info, product/service categories, bank details
- Upload required documents (self-service):
  - Certificate of incorporation
  - Trade insurance / liability certificate
  - VAT registration certificate
  - ISO / quality certifications
  - Modern slavery statement (UK, companies >£36m turnover)
  - GDPR/data processing agreement (if supplier handles personal data)
  - Financial accounts / credit reference
- E-sign terms & conditions on registration

### Qualification Scorecard
- Configurable scoring criteria (weight per criterion):
  - Financial stability (credit score via D&B / Experian API)
  - Insurance coverage (value, expiry, coverage type)
  - Quality certifications (ISO 9001, ISO 14001, IATF 16949)
  - Sustainability rating (carbon disclosure, ESG score)
  - References (number of references provided + verification)
  - On-site audit score (if physical audit completed)
- Auto-calculated total score → Preferred / Approved / Conditional / Rejected
- Score threshold configurable per category (e.g. minimum insurance value)

### Approval Workflow
- Qualification request → routes to procurement manager
- Secondary approval for high-value/critical suppliers (above spend threshold)
- Reviewer checklist: verify each document, mark as verified
- Request additional information from supplier (email from within platform)
- Approval / rejection with reason → email notification to supplier

### Supplier Risk Monitoring (Ongoing)
- Document expiry alerts (insurance cert expiring in 30 days → request renewal)
- Credit monitoring: flag if supplier credit score drops below threshold
- News monitoring (optional: Google News API for supplier name → flag negative press)
- Annual re-qualification: auto-request updated documents annually

### Supplier Segmentation
- Categories: Critical / Strategic / Preferred / Standard / Spot
- Spend-based segmentation (auto-updated from PO history)
- Single-source dependency flag (if >80% of a product comes from one supplier)
- Geographic risk flagging (supplier in high-risk country)

---

## Data Model

```erDiagram
    supplier_qualification_requests {
        ulid id PK
        ulid company_id FK
        ulid supplier_id FK
        string status
        decimal qualification_score
        string qualification_tier
        json scorecard_breakdown
        ulid reviewed_by FK
        timestamp submitted_at
        timestamp approved_at
        timestamp next_review_date
    }

    supplier_documents {
        ulid id PK
        ulid supplier_id FK
        string document_type
        string storage_path
        date expiry_date
        string verification_status
        ulid verified_by FK
        timestamp verified_at
    }
```

---

## Events

| Event | When | Consumed By |
|---|---|---|
| `SupplierQualified` | Approval granted | Operations (supplier now available for POs), Notifications |
| `SupplierDocumentExpiring` | 30 days before expiry | Notifications (procurement manager + supplier) |
| `SupplierRiskFlagged` | Credit/news alert triggered | Notifications (procurement manager) |

---

## Permissions

```
operations.supplier-qual.view
operations.supplier-qual.review
operations.supplier-qual.approve
operations.supplier-qual.manage-criteria
```

---

## Related

- [[MOC_Operations]]
- [[vendor-portal]] — ongoing PO/invoice management for approved suppliers

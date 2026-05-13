---
type: module
domain: AI & Automation
panel: ai
cssclasses: domain-ai
phase: 6
status: complete
migration_range: 460008
last_updated: 2026-05-12
right_brain_log: "[[builder-log-ai-phase6]]"
---

# AI Document Processing & OCR

Intelligent document extraction — auto-read invoices, contracts, receipts, and purchase orders. Eliminate manual data entry. Replaces Rossum, Mindee, and Adobe Acrobat AI.

---

## Features

### Document Ingestion
- Email inbox listener (forward invoices to `bills@company.flowflex.app`)
- File upload (drag & drop, mobile camera scan)
- WhatsApp/SMS image scan for receipts
- Bulk PDF import
- Connected to file storage (auto-process new files in designated folder)

### OCR & Data Extraction
- Supplier invoices: vendor name, invoice number, date, due date, line items, VAT amount, total
- Receipts: merchant, amount, date, VAT
- Purchase orders: supplier, PO number, items, quantities, prices
- Contracts: parties, effective date, expiry, key clauses, obligations
- Bank statements: transactions list, opening/closing balance
- Passports / ID documents: name, DOB, document number, expiry (for HR/compliance)

### AI Confidence & Review
- Confidence score per extracted field
- Low-confidence fields highlighted for human review
- Side-by-side: original document + extracted data
- One-click correction (trains the model per company)
- Bulk approve high-confidence extractions

### Routing
- Extracted invoice → auto-create in Finance AP/AR
- Extracted receipt → auto-create in Expense Management
- Extracted contract → auto-create in Legal Contract Management
- Extracted PO → match to existing Purchase Order in Operations

### Audit
- Full extraction history per document
- Who approved, what was corrected
- Extraction accuracy rate over time

---

## Data Model

```erDiagram
    document_processing_jobs {
        ulid id PK
        ulid company_id FK
        string document_type
        string source
        string file_url
        string status
        json extracted_data
        decimal confidence_score
        ulid reviewed_by FK
        timestamp processed_at
    }

    extraction_corrections {
        ulid id PK
        ulid job_id FK
        string field_name
        string original_value
        string corrected_value
        ulid corrected_by FK
    }
```

---

## Events

| Event | When | Consumed By |
|---|---|---|
| `DocumentExtracted` | Extraction complete | Finance / Operations / Legal (create record), Notifications |
| `LowConfidenceExtraction` | Score < threshold | Notifications (review queue) |
| `DocumentExtractionCorrected` | Human corrects | AI (model feedback loop) |

---

## Permissions

```
ai.document-processing.view-any
ai.document-processing.approve
ai.document-processing.configure
```

---

## Competitors Displaced

Rossum · Mindee · Nanonets · Azure Document Intelligence · Adobe Acrobat AI

---

## Related

- [[MOC_AI]]
- [[MOC_Finance]] — extracted invoices/receipts
- [[MOC_Legal]] — extracted contracts
- [[MOC_Operations]] — extracted purchase orders

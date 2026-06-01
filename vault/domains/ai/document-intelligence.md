---
type: module
domain: AI & Automation
panel: ai
module-key: ai.document-intelligence
status: planned
color: "#4ADE80"
---

# Document Intelligence

Extract structured data from documents (invoices, receipts, contracts, CVs) using OCR + LLM. Auto-populate records from uploaded files.

## Core Features

- Upload document → extract structured fields
- Supported types: invoices (vendor, amount, date, line items), receipts (expenses), CVs (applicant data), contracts (key terms)
- OCR for scanned documents + LLM for field extraction
- Review step: extracted data shown for human confirmation before saving
- Auto-create records: extracted invoice → Finance bill, CV → recruitment applicant, receipt → expense
- Confidence scores per extracted field
- Batch processing via queue
- Template learning: improve extraction for recurring document formats

## Data Model

| Table | Key Columns |
|---|---|
| `ai_extractions` | company_id, document_media_id, document_type, extracted_data (json), confidence (json), status (pending/reviewed/applied), target_record_type, target_record_id |

## Filament

**Nav group:** Document Intelligence

- `DocumentExtractionResource` — upload, review extracted data, confirm + create record
- Extraction review page with field confidence highlighting

## Cross-Domain / Jobs / Security

- Extraction runs via queue (OCR + LLM are slow) — see [[architecture/queue-jobs]]
- Creates records in Finance (bills/expenses), HR (applicants) after review
- LLM API key encrypted; uploaded docs under `companies/{id}/` (see [[architecture/security]])

## Related

- [[domains/finance/expenses]]
- [[domains/hr/recruitment]]
- [[domains/ai/copilot]]

---
type: module
domain: AI & Automation
domain-key: ai
panel: ai
module-key: ai.document-intelligence
status: planned
priority: p3
depends-on: [ai.config, core.billing, core.rbac, core.files, foundation.queues]
soft-depends: [finance.ap, finance.expenses, hr.recruitment]
fires-events: []
consumes-events: []
patterns: [queues]
tables: [ai_extractions]
permission-prefix: ai.document-intelligence
encrypted-fields: ["ai_extractions.extracted_data"]
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Document Intelligence

Extract structured data from documents (invoices, receipts, CVs) using OCR + LLM. Auto-populate records from uploaded files — always behind a human review step.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/ai/model-config\|ai.config]] | LlmGateway (vision/extraction calls, budget) |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/file-storage\|core.files]] + [[domains/foundation/queue-workers\|foundation.queues]] | gating, permissions, uploads, extraction jobs |
| Soft | [[domains/finance/accounts-payable\|finance.ap]] (invoice → bill), [[domains/finance/expenses\|finance.expenses]] (receipt → expense), [[domains/hr/recruitment\|hr.recruitment]] (CV → applicant) | apply targets — types offered only when target module active |

---

## Core Features

- Upload document → extract structured fields (queued — OCR + LLM are slow)
- Supported types: invoice (vendor, amount, date, line items), receipt (expense fields), CV (applicant data); contracts later *(assumed)*
- LLM extraction with document-type schemas; pdftotext/vision per file type *(assumed: LLM-vision primary, OCR fallback)*
- **Review step mandatory**: extracted data shown with per-field confidence for human confirmation before any record is created
- Apply: confirmed extraction → target module's Create DTO/service (bill / expense / applicant) — same validation as manual entry
- Confidence scores per extracted field (low-confidence highlighted)
- Batch processing via queue
- Template learning deferred *(assumed)*

---

## Data Model

### ai_extractions

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| document_media_id | ulid FK media | tenant-scoped upload |
| document_type | string | invoice / receipt / cv |
| status | string default `processing` | processing / extracted / reviewed / applied / failed |
| 🔐 extracted_data | text | encrypted cast — holds parsed PII/bank data (IBAN/BIC, DOB, gov IDs, personal email); stored as encrypted text, structured shape (schema per type) decoded app-side, never raw jsonb. See [[architecture/patterns/encryption]] |
| confidence | jsonb | per field 0–1 |
| target_record_type / target_record_id | string / ulid nullable | applied link |
| reviewed_by | ulid nullable | |
| tokens_used | int default 0 | |
| deleted_at | timestamp nullable | |

---

## DTOs

### CreateExtractionData — file (pdf/jpg/png per security rules), document_type (in set, target module active)
### ApplyExtractionData — extraction_id (reviewed), corrected_data{} (overrides) — mapped to target Create DTO

## Services & Actions

- `ExtractDocumentJob` — `default` queue, WithCompanyContext: file → LlmGateway extraction with type schema → fields + confidence
- `ExtractionService::apply(ApplyExtractionData)` — maps to `ApService::createBill` / `ExpenseService::submit` / `RecruitmentService` applicant creation; records link; **never bypasses target validation**
- Failure → status failed + error, retryable

---

## Filament

**Nav group:** Document Intelligence

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `DocumentExtractionResource` | #1 CRUD resource | upload (create), review page with confidence highlighting, apply action |

---

## Permissions

`ai.document-intelligence.upload` · `ai.document-intelligence.review` · `ai.document-intelligence.apply` (+ target-module create permission checked at apply)

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Type offered only when target module active
- [ ] Apply requires review; goes through target DTO validation (invalid extraction rejected like manual input)
- [ ] Apply requires target-module permission
- [ ] Confidence per field stored; failure path retryable
- [ ] Usage metered via LlmGateway; provider mocked
- [ ] Files tenant-scoped under `companies/{id}/`

---

## Build Manifest

```
database/migrations/xxxx_create_ai_extractions_table.php
app/Models/AI/Extraction.php
app/Data/AI/{CreateExtractionData,ApplyExtractionData}.php
app/Services/AI/ExtractionService.php
app/Jobs/AI/ExtractDocumentJob.php
app/Support/AI/ExtractionSchemas.php (per-type field schemas)
app/Filament/AI/Resources/DocumentExtractionResource.php
database/factories/AI/ExtractionFactory.php
tests/Feature/AI/{ExtractionFlowTest,ExtractionApplyTest}.php
```

---

## Related

- [[domains/ai/model-config]]
- [[domains/finance/expenses]]
- [[domains/hr/recruitment]]
- [[architecture/queue-jobs]]

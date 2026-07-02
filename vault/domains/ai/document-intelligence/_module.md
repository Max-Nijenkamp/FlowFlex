---
domain: ai
module: document-intelligence
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Document Intelligence

Extract structured data from documents (invoices, receipts, CVs) using OCR + LLM, then auto-populate business records from the uploaded files — always behind a mandatory human review step. Every LLM/vision call routes through [[../model-config/_module|ai.config]]'s metered `LlmGateway`; the confirmed extraction is applied to a target module (bill / expense / applicant) **through that module's own Create service**, never a direct table write.

## Module-key

| Field | Value |
|---|---|
| key | `ai.document-intelligence` |
| priority | p3 |
| panel | ai |
| permission-prefix | `ai.document-intelligence` |
| tables | `ai_extractions` |
| encrypted-fields | `ai_extractions.extracted_data` |

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../model-config/_module\|ai.config]] | `LlmGateway` — vision/extraction calls, metered + budget-gated |
| Hard | [[../../core/billing/_module\|core.billing]] | Module gating (`hasModule`) |
| Hard | [[../../core/rbac/_module\|core.rbac]] | Permissions, `canAccess()` |
| Hard | [[../../core/file-storage/_module\|core.files]] | Tenant-scoped uploaded media |
| Hard | [[../../foundation/queue-workers/_module\|foundation.queues]] | `ExtractDocumentJob` (OCR + LLM are slow) |
| Soft | [[../../finance/accounts-payable/_module\|finance.ap]] | invoice → bill (apply target) |
| Soft | [[../../finance/expenses/_module\|finance.expenses]] | receipt → expense (apply target) |
| Soft | [[../../hr/recruitment/_module\|hr.recruitment]] | CV → applicant (apply target) |

Soft targets: an extraction type is offered **only when the target module is active** for the company.

## Core Features

- **Upload → extract** — upload a document, a queued job extracts structured fields (OCR + LLM are slow).
- **Supported types** — invoice (vendor, amount, date, line items), receipt (expense fields), CV (applicant data); contracts later *(assumed)*.
- **Per-field confidence** — every extracted field carries a 0–1 confidence score; low-confidence fields are flagged for the reviewer.
- **Mandatory review** — extracted data is shown for human confirmation/correction before any record is created (a correctness *and* security control).
- **Apply to record** — the confirmed extraction is mapped to the target module's Create DTO/service (bill / expense / applicant), passing the same validation as manual entry.
- **Batch processing** — many documents processed via the queue.
- Template learning deferred *(assumed)*.

## See features/

- [[features/upload-and-extract|Upload & Extract]] — upload a document; a queued `ExtractDocumentJob` runs OCR + LLM extraction.
- [[features/review-and-confirm|Review & Confirm]] — per-field confidence highlighting; human confirms/corrects before apply.
- [[features/apply-to-record|Apply to Record]] — map the confirmed extraction to the target module's Create service.

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

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Type offered only when target module active
- [ ] Apply requires review; goes through target DTO validation (invalid extraction rejected like manual input)
- [ ] Apply requires target-module permission
- [ ] Confidence per field stored; failure path retryable
- [ ] Usage metered via `LlmGateway`; provider mocked
- [ ] Files tenant-scoped under `companies/{id}/`

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | `LlmGateway::complete(feature, messages, opts)` | [[../model-config/_module\|ai.config]] | vision/extraction calls, metered + budget-gated — the single LLM path |
| Reads | uploaded media (tenant-scoped) | [[../../core/file-storage/_module\|core.files]] | the document to extract from, under `companies/{id}/` |
| Writes (cross-domain) | `ApService::createBill` / `ExpenseService::submit` / recruitment applicant creation | finance.ap · finance.expenses · hr.recruitment | **apply** goes through the owning module's Create service/DTO — a cross-domain write that must **never** touch another module's tables directly ([[../../../security/data-ownership]]) |
| Fires | *(none)* | — | apply is a synchronous target-service call, not an event |

document-intelligence **writes only `ai_extractions`**. Extraction types are offered only when the target module is active. It fires no domain events — the apply happens via a synchronous target-service call.

> [!warning] UNVERIFIED
> Whether **apply** should be a synchronous target-service call (current design) or an emitted event consumed by the target module. Event decoupling would be cleaner but loses the immediate validation-error surface the reviewer needs. Recorded as an alternative, not adopted.

**Data ownership:** `ai.document-intelligence` writes only `ai_extractions`; the created bill/expense/applicant is owned and written by the target module via its own service ([[../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../model-config/_module|ai.config]] · [[../../finance/expenses/_module|Expenses]] · [[../../hr/recruitment/_module|Recruitment]]
- [[../../../architecture/queue-jobs]]

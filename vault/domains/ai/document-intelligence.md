---
type: module
domain: AI & Automation
panel: ai
module-key: ai.document-intelligence
status: planned
color: "#4ADE80"
---

# Document Intelligence

> AI-powered document analysis that extracts key fields, classifies document types, and summarises contracts and reports automatically.

**Panel:** `ai`
**Module key:** `ai.document-intelligence`

---

## What It Does

Document Intelligence applies AI to unstructured documents uploaded anywhere in FlowFlex. When a PDF or image is uploaded to a finance invoice, a DMS record, or a contract in the legal module, Document Intelligence can automatically extract structured data (amounts, dates, party names, account numbers), classify the document type, and generate a plain-language summary. Extracted fields can populate Filament form fields automatically, reducing manual data entry and transcription errors.

---

## Features

### Core
- Document upload trigger: runs on any document uploaded in participating FlowFlex modules
- Document classification: identify document type (invoice, contract, ID, receipt, report)
- Field extraction: pull structured fields — amounts, dates, names, reference numbers — into structured output
- Plain-language summary: one-paragraph description of the document's purpose and key points
- Confidence scores: each extracted field includes a confidence percentage
- Human review queue: low-confidence extractions flagged for manual verification

### Advanced
- Template-based extraction: define field schemas for recurring document types in the company
- Multi-language support: extract fields from documents in non-English languages
- Batch processing: process a folder of documents in a single job
- Comparison mode: compare two document versions and highlight field changes
- Audit trail: log every extraction with model version and extracted values for compliance

### AI-Powered
- Adaptive extraction: model improves accuracy over time from human corrections via feedback loop
- Anomaly flagging: detect unusual fields (e.g. invoice amount 300% above supplier average)
- Contract risk signals: identify non-standard clauses or missing standard provisions in contracts

---

## Data Model

```erDiagram
    document_intelligence_jobs {
        ulid id PK
        ulid company_id FK
        string source_module
        string source_record_type
        ulid source_record_id FK
        string file_url
        string document_type
        json extracted_fields
        text summary
        string status
        decimal overall_confidence
        ulid reviewed_by FK
        timestamp processed_at
        timestamps created_at_updated_at
    }

    document_field_corrections {
        ulid id PK
        ulid job_id FK
        string field_name
        string original_value
        string corrected_value
        ulid corrected_by FK
        timestamp corrected_at
    }

    document_intelligence_jobs ||--o{ document_field_corrections : "corrected via"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `document_intelligence_jobs` | Extraction runs | `id`, `company_id`, `source_module`, `document_type`, `extracted_fields`, `status` |
| `document_field_corrections` | Human corrections | `id`, `job_id`, `field_name`, `original_value`, `corrected_value` |

---

## Permissions

```
ai.document-intelligence.use
ai.document-intelligence.review-queue
ai.document-intelligence.manage-templates
ai.document-intelligence.view-audit-log
ai.document-intelligence.export
```

---

## Filament

- **Resource:** `App\Filament\Ai\Resources\DocumentIntelligenceJobResource`
- **Pages:** `ListDocumentIntelligenceJobs`, `ViewDocumentIntelligenceJob`
- **Custom pages:** `DocumentReviewQueuePage`, `ExtractionTemplatePage`
- **Widgets:** `ExtractionAccuracyWidget`, `PendingReviewWidget`
- **Nav group:** Intelligence

---

## Displaces

| Feature | FlowFlex | Custom OpenAI | Adobe Acrobat AI | DocParser |
|---|---|---|---|---|
| Classification + extraction | Yes | Custom | Partial | Yes |
| Human review queue | Yes | No | No | Yes |
| Feedback-based improvement | Yes | No | No | No |
| Native platform integration | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Implementation Notes

**External dependency — AI provider:** Document Intelligence uses two separate AI APIs:
1. **OpenAI GPT-4o with vision input** (`gpt-4o` model): For structured field extraction from PDFs and images. The document is uploaded to OpenAI's Files API or sent as a base64-encoded image in the message content. The prompt includes a JSON schema defining the expected output fields per document type.
2. **Anthropic Claude claude-sonnet-4-6** (alternative): Can process PDF documents directly via the Files API. Slightly better at long-document context. Either provider works — decide in an ADR and implement one interface.

**OCR pre-processing:** For scanned image-based PDFs (no text layer), the `ocr` module runs first to produce a raw text string. This raw text is then passed to the document intelligence LLM prompt instead of the image. For native digital PDFs (with text layer), skip OCR and pass the PDF directly to the vision model.

**Auto-trigger on upload:** Document intelligence runs automatically when a file is attached to a participating model. This is implemented via a `DocumentUploadedListener` that listens for `spatie/laravel-media-library`'s `MediaAdded` event. The listener checks if the media's model type is registered as a participating module (`DocumentIntelligenceRegistry::isRegistered($modelType)`) and if so dispatches `ProcessDocumentIntelligenceJob` (queued on the `ai` queue).

**Confidence scores and human review queue:** Each extracted field has a confidence score returned by the LLM. Fields below `0.85` confidence are flagged. `DocumentReviewQueuePage` is a custom Filament `Page` that shows pending jobs ordered by lowest overall confidence. Reviewers click a field to edit its value — this creates a `document_field_corrections` record and updates `document_intelligence_jobs.extracted_fields` with the corrected value.

**Feedback loop:** Corrections in `document_field_corrections` are batched weekly and fed back to the AI via few-shot examples in the extraction prompt. `app/Services/AI/DocumentIntelligenceTrainingService.php` reads recent corrections and constructs an updated prompt with 3–5 correction examples. This is prompt engineering, not model fine-tuning — no separate ML training infrastructure needed.

**Template-based extraction:** `ExtractionTemplatePage` is a custom Filament `Page` where administrators define field schemas per document type. Each template is stored as `{document_type, fields: [{name, description, type, required}]}` in a `document_intelligence_templates` table — not currently defined in the data model. Add it.

## Related

- [[ocr]] — OCR feeds raw text into document intelligence
- [[dms/document-library]] — DMS documents auto-processed on upload
- [[finance/INDEX]] — invoice field extraction
- [[legal/INDEX]] — contract analysis and clause detection
- [[workflow-builder]] — trigger workflows from extraction events

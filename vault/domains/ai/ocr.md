---
type: module
domain: AI & Automation
panel: ai
module-key: ai.ocr
status: planned
color: "#4ADE80"
---

# OCR

> Optical character recognition for uploaded documents — extract structured data from invoices, receipts, identity documents, and scanned forms.

**Panel:** `ai`
**Module key:** `ai.ocr`

---

## What It Does

OCR processes image-based or scanned documents uploaded anywhere in FlowFlex and converts them into machine-readable text and structured data. When an employee uploads a scanned invoice to the finance module or a receipt to an expense report, OCR extracts the text layer and passes it to Document Intelligence for field extraction. The module supports standard document types with pre-configured extraction templates and can handle ad-hoc documents with general text extraction. Results are searchable and the raw extracted text is stored alongside the original file.

---

## Features

### Core
- Image-to-text conversion: process JPG, PNG, TIFF, and scanned PDF files
- Pre-built templates: invoice, receipt, purchase order, identity document, bank statement
- Raw text extraction: extract all text from a document even without a structured template
- Structured field output: map extracted text to named fields (total amount, date, vendor, VAT number)
- Confidence scores: per-field and overall document confidence rating
- Review queue: low-confidence documents flagged for human verification

### Advanced
- Multi-page document handling: extract and stitch text from multi-page PDFs
- Table extraction: recognise tabular data within documents (e.g. invoice line items)
- Handwriting recognition: process handwritten text in forms and notes
- Bulk processing: queue multiple documents for batch OCR processing
- Storage of raw extracted text: full text stored for downstream search indexing

### AI-Powered
- Layout-aware extraction: understand document structure (headers, footers, columns) for more accurate field mapping
- Auto-template suggestion: when a new document type is detected, suggest a template based on layout pattern
- Error correction: contextual correction of OCR errors using domain-specific vocabulary (e.g. supplier name dictionary)

---

## Data Model

```erDiagram
    ocr_jobs {
        ulid id PK
        ulid company_id FK
        string source_module
        ulid source_record_id FK
        string file_url
        string file_type
        string template_used
        text raw_text
        json structured_data
        decimal confidence_score
        string status
        ulid reviewed_by FK
        timestamp processed_at
        timestamps created_at_updated_at
    }

    ocr_corrections {
        ulid id PK
        ulid job_id FK
        string field_name
        string original_value
        string corrected_value
        ulid corrected_by FK
        timestamp corrected_at
    }

    ocr_jobs ||--o{ ocr_corrections : "corrected via"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `ocr_jobs` | OCR processing runs | `id`, `company_id`, `source_module`, `template_used`, `raw_text`, `structured_data`, `confidence_score`, `status` |
| `ocr_corrections` | Human corrections | `id`, `job_id`, `field_name`, `original_value`, `corrected_value` |

---

## Permissions

```
ai.ocr.use
ai.ocr.review-queue
ai.ocr.manage-templates
ai.ocr.view-audit-log
ai.ocr.export
```

---

## Filament

- **Resource:** `App\Filament\Ai\Resources\OcrJobResource`
- **Pages:** `ListOcrJobs`, `ViewOcrJob`
- **Custom pages:** `OcrReviewQueuePage`, `OcrTemplatePage`
- **Widgets:** `OcrVolumeWidget`, `OcrAccuracyWidget`
- **Nav group:** Intelligence

---

## Displaces

| Feature | FlowFlex | Adobe Acrobat | ABBYY | Custom Tesseract |
|---|---|---|---|---|
| Invoice extraction | Yes | Yes | Yes | Custom |
| Table extraction | Yes | Yes | Yes | Custom |
| Native platform integration | Yes | No | No | No |
| Human review queue | Yes | No | Partial | No |
| Included in platform | Yes | No | No | No |

---

## Implementation Notes

**External dependency — OCR engine (must be decided before build):** Three options:
1. **Google Cloud Vision API** (`google/cloud-vision` PHP package): High accuracy, good table extraction, handwriting support. Per-page pricing (~$1.50/1,000 pages). Requires a Google Cloud project and service account JSON credentials (`GOOGLE_CLOUD_VISION_CREDENTIALS` in `.env`).
2. **AWS Textract** (`aws/aws-sdk-php`): Similar pricing and capabilities to Google Vision. Particularly strong at form field extraction (key-value pairs). Works well if the app is already AWS-hosted.
3. **Azure AI Document Intelligence** (formerly Form Recognizer): Microsoft's OCR offering. Strong pre-built models for invoices, receipts, IDs. `azure/document-intelligence` SDK.
4. **Tesseract (self-hosted, via `thiagoalessio/tesseract_ocr` PHP binding):** Free, no per-page cost, but lower accuracy and requires Tesseract binary in the Docker image. Not recommended for production invoice processing.

**Recommended:** Google Cloud Vision API for general OCR. Store provider choice as `OCR_PROVIDER` in `.env`. Implement `app/Contracts/AI/OcrProviderInterface.php` so the provider is swappable.

**Processing pipeline:** `ocr_jobs` is created on upload → `ProcessOcrJob` is dispatched (queued on `ai` queue) → provider API is called → raw text and structured data written to `ocr_jobs` → if `confidence_score < 0.75`, set `status = review_needed` → if integrated with document-intelligence module, dispatch `ProcessDocumentIntelligenceJob` with the raw text.

**Table extraction:** Google Vision returns `FullTextAnnotation` with bounding boxes — table structure is inferred from the relative positions of text blocks. For invoice line items, use Google's `DocumentAI` (a higher-level service within Cloud Vision) which has pre-built invoice models that correctly parse line item tables.

**File storage:** Uploaded files are stored via `spatie/laravel-media-library` on the source model (e.g. a finance expense record). The `ocr_jobs.file_url` stores the signed S3 URL. OCR jobs should not re-download the file on retry — store it in S3 with a long-lived non-expiring key.

**Filament:** `OcrReviewQueuePage` and `OcrTemplatePage` are custom `Page` classes. The review queue shows the original document (PDF.js render) alongside the extracted structured data fields — the reviewer can correct any field inline. This is architecturally similar to `DocumentReviewQueuePage` in the legal module.

## Related

- [[document-intelligence]] — OCR text feeds into document intelligence for analysis
- [[finance/INDEX]] — invoice and receipt OCR on upload
- [[travel/expense-reports]] — receipt OCR for expense claims
- [[procurement/INDEX]] — PO and GRN document extraction

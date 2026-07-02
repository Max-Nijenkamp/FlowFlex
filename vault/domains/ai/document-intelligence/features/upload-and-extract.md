---
domain: ai
module: document-intelligence
feature: upload-and-extract
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Upload & Extract

Upload a document (invoice / receipt / CV); a queued job runs OCR + LLM extraction and writes back structured fields with per-field confidence. The starting point of the extraction pipeline.

## Behaviour

- Upload creates an `ai_extractions` row with `status: processing` and dispatches `ExtractDocumentJob`.
- The job (default queue, `WithCompanyContext`) resolves the tenant-scoped media, calls `LlmGateway::complete('document-intelligence', …)` with the per-type schema, and writes parsed fields + confidence → `status: extracted`.
- Failure (provider/parse error) → `status: failed` + error message, **retryable**.
- The `document_type` is only accepted when its target module is active for the company.
- Batchable: many documents can be uploaded and processed via the queue.

## UI

- **Kind**: simple-resource   <!-- DocumentExtractionResource: list + create/upload; a queued job runs behind it -->
- **Page**: "Extractions" (`/app/ai/extractions`) *(route slug assumed)*
- **Layout**: table of extractions (document type, status badge, uploaded-at, confidence summary); create action = file upload + document-type select.
- **Key interactions**: upload file → row appears as `processing`; status badge updates when the job finishes (poll/refresh — no realtime broadcast specced); failed rows offer a retry action.
- **States**: empty (no extractions → "upload a document to get started" CTA) · loading (`processing` badge + spinner) · error (`failed` badge → error message + retry) · selected (row opens the review screen).
- **Gating**: `ai.document-intelligence.upload` + `hasModule('ai.document-intelligence')`. Upload contract: `max:10240` KB, MIME whitelist pdf/jpg/png, tenant-scoped `companies/{id}/` path.

## Data

- Owns / writes: `ai_extractions` (this module's only table).
- Reads: uploaded media from [[../../../core/file-storage/_module|core.files]] (tenant-scoped, read-only); `LlmGateway` from [[../../model-config/_module|ai.config]].
- Cross-domain writes: none at this stage ([[../../../../security/data-ownership]]).

## Relations

- Consumes: `LlmGateway::complete` (ai.config); media (core.files).
- Feeds: the extracted row into [[review-and-confirm|Review & Confirm]].
- Shared entity: `media` owned by core.files.

## Unknowns

> [!warning] UNVERIFIED
> Extraction strategy per MIME type (LLM-vision primary vs OCR fallback) and the `/app/ai/extractions` route slug are assumed. No realtime "processing → extracted" broadcast is specced. See [[../unknowns]].

## Related

- [[../_module|Document Intelligence]] · [[review-and-confirm|Review & Confirm]] · [[apply-to-record|Apply to Record]]
- [[../architecture]] · [[../../../../architecture/queue-jobs]]

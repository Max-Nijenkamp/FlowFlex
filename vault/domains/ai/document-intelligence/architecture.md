---
domain: ai
module: document-intelligence
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Document Intelligence — Architecture

See also [[_module|ai.document-intelligence._module]], [[../../../architecture/filament-patterns]], [[../../../architecture/queue-jobs]], [[../../../architecture/patterns/encryption]], [[../../../architecture/patterns/dto-pattern]], [[../model-config/architecture|ai.config architecture]] (for `LlmGateway`).

---

## Flow

```
upload (DocumentExtractionResource create)
   → ai_extractions row (status: processing)
   → dispatch ExtractDocumentJob (default queue, WithCompanyContext)
        → resolve document media (core.files, tenant-scoped)
        → LlmGateway::complete('document-intelligence', …) with per-type schema  ← metered by ai.config
        → parse fields + per-field confidence
        → status: extracted  (or failed + error on provider/parse failure — retryable)
   → human review (Review & Confirm)  → status: reviewed
   → apply (Apply to Record)  → target module Create service  → status: applied, target link recorded
```

---

## Services & Actions

- **`ExtractDocumentJob`** — `default` queue, `WithCompanyContext`. Loads the tenant-scoped media, calls `LlmGateway::complete` with the document-type schema, writes back parsed fields + per-field confidence. Failure → `status: failed` + error message, **retryable**. Usage/cost is metered inside `LlmGateway` (never here).
- **`ExtractionService::apply(ApplyExtractionData)`** — maps the confirmed (and optionally corrected) extraction to the target module's Create call: `ApService::createBill` / `ExpenseService::submit` / recruitment applicant creation. Records `target_record_type` / `target_record_id`; **never bypasses the target's validation**. This is the only place a cross-domain write happens, and it goes through the owning service ([[../../../security/data-ownership]]).
- **`ExtractionSchemas`** (`app/Support/AI`) — per-type field schemas (invoice / receipt / cv) that shape the LLM prompt and validate the parsed result.

---

## Extraction Strategy

> [!warning] UNVERIFIED
> Extraction is assumed **LLM-vision primary with an OCR (`pdftotext`) fallback** per file type — vision for images/scanned PDFs, text extraction for native PDFs. The exact routing per MIME type, and whether OCR ever runs standalone, are unconfirmed.

- Supported document types: invoice, receipt, cv. Contracts later *(assumed)*.
- Template learning (remembering a vendor's layout to skip the LLM) is deferred *(assumed)*.

---

## Filament Artifacts

**Nav group:** Document Intelligence

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `DocumentExtractionResource` | #1 CRUD resource | tweaks: state-badge-column (processing/extracted/reviewed/applied/failed), custom-header-actions (apply, retry) | list + create/**upload** (behind `ExtractDocumentJob`); review page = per-field confidence form (view page variant); apply action gated per feature |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('ai.document-intelligence.view-any') && BillingService::hasModule('ai.document-intelligence')`
per [[../../../architecture/filament-patterns]] #1.

The resource hosts three feature slices — see [[features/upload-and-extract]], [[features/review-and-confirm]], [[features/apply-to-record]]. Pattern references: [[../../../architecture/filament-patterns]], [[../../../architecture/patterns/custom-pages]], [[../../../architecture/ui-strategy]].

---

## Jobs & Scheduling

- `ExtractDocumentJob` — the only async work; one job per uploaded document, batchable via the queue. No scheduled commands. No Meilisearch index and no realtime broadcast for this module *(assumed — a "processing → extracted" broadcast would be a nice-to-have but is not specced)*.

---

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Review edits (field corrections) | Optimistic | `updated_at` stale-check → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Apply to record | Pessimistic | `DB::transaction()` + `lockForUpdate()` on the extraction — applied exactly once; second applier rejected, target created once |
| Job writeback (parse results) | n/a | Single writer (`ExtractDocumentJob`) per extraction row |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

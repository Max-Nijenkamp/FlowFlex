---
domain: ai
module: document-intelligence
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Document Intelligence — Unknowns & Assumptions

All items below are unverified. They function as authoritative defaults at build time but are overridable via ADR.

---

## Open Questions

1. **Extraction strategy per MIME type.** Assumed LLM-vision primary with a `pdftotext`/OCR fallback. The exact routing (vision for images/scanned PDFs, text extraction for native PDFs) and whether OCR ever runs standalone are unconfirmed.
2. **Apply: sync service call vs. event.** Current design is a synchronous target-service call (immediate validation errors for the reviewer). An `ExtractionApplied` event consumed by the target module would decouple further. Which wins?
3. **Programmatic ingestion.** No REST/email-in endpoint is specced. If invoices arrive by email or API rather than manual upload, an ingestion path is needed — not yet designed.
4. **Retention of `extracted_data`.** It holds regulated PII. What is the retention/prune policy, and does an applied extraction keep the raw parsed data or drop it once the target record exists? Confirm against [[../../../architecture/data-lifecycle]].
5. **Route slug.** The `/ai` extractions route is assumed — confirm the panel slug for `ai` artifacts.
6. **Realtime status.** A "processing → extracted" broadcast would improve UX but is not specced.

---

## Assumed Items (unverified)

- `*(assumed)*` — LLM-vision primary, OCR fallback.
- `*(assumed)*` — apply is a synchronous target-service call, not an event.
- `*(assumed)*` — supported types are invoice / receipt / cv; contracts later.
- `*(assumed)*` — template learning (remember a vendor's layout to skip the LLM) is deferred.
- `*(assumed)*` — extractions route `/app/ai/extractions`.
- `*(assumed)*` — no realtime status broadcast in v1.

> [!warning] UNVERIFIED
> The two highest-impact open items are the **extraction strategy per MIME type** (shapes `ExtractDocumentJob` + `ExtractionSchemas`) and the **retention policy for encrypted `extracted_data`** (a GDPR obligation, not just a nice-to-have). Resolve both before build.

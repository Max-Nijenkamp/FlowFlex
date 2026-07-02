---
domain: ai
module: document-intelligence
feature: review-and-confirm
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Review & Confirm

The mandatory human-in-the-loop step: the reviewer sees every extracted field with its confidence, corrects low-confidence or wrong values, and confirms — moving the extraction to `reviewed`. No record can be created until this happens.

## Behaviour

- Loads an `extracted` extraction, decrypts `extracted_data` app-side, and renders each field beside its 0–1 confidence.
- Low-confidence fields are highlighted for attention; the reviewer can edit any field (corrections stored as overrides).
- Confirming sets `reviewed_by` + `status: reviewed`, unlocking [[apply-to-record|Apply to Record]].
- Review is a **security + correctness control** — it prevents an LLM hallucination or a poisoned document from silently producing a fraudulent record.

## UI

- **Kind**: custom-page   <!-- bespoke review layout: field + confidence side-by-side, inline edits -->
- **Page**: "Review extraction" (`/app/ai/extractions/{id}/review`) *(route slug assumed)*
- **Layout**: two-pane — left = document preview (the uploaded file); right = extracted fields, each with a confidence chip + editable value; low-confidence fields flagged. Confirm button at the bottom.
- **Key interactions**: click a flagged field → edit inline; hover a confidence chip → exact score; Confirm → validates presence of required fields → `status: reviewed`.
- **States**: empty (n/a — always tied to one extraction) · loading (fields loading / decrypting) · error (extraction still `processing` → "not ready"; `failed` → error + back to retry) · selected (field being edited highlighted).
- **Gating**: `ai.document-intelligence.review`.

## Data

- Owns / writes: `ai_extractions` (sets corrections, `reviewed_by`, `status`) — this module's own table.
- Reads: the document media (core.files) for the preview; decrypted `extracted_data`.
- Cross-domain writes: none — no target record is touched here ([[../../../../security/data-ownership]]).

## Relations

- Consumes: extracted row from [[upload-and-extract|Upload & Extract]]; media (core.files) for preview.
- Feeds: the reviewed extraction into [[apply-to-record|Apply to Record]].
- Shared entity: `reviewed_by` → platform `users` (read-only).

## Test Checklist

### Unit
- [ ] Per-field confidence thresholds classify fields for highlight (low-confidence flagged)

### Feature (Pest)
- [ ] Confirm sets `reviewed` + `reviewed_by`; corrections persist over parsed values
- [ ] Concurrent review edits: second saver gets `StaleRecordException` (optimistic guard)

### Livewire
- [ ] Review form renders parsed fields with confidence highlighting; save validates corrections
- [ ] Denied without `ai.document-intelligence.review`

## Unknowns

> [!warning] UNVERIFIED
> The review route slug and the document-preview rendering approach (inline PDF/image viewer) are assumed. Retention of the decrypted PII shown here is an open GDPR question. See [[../unknowns]].

## Related

- [[../_module|Document Intelligence]] · [[upload-and-extract|Upload & Extract]] · [[apply-to-record|Apply to Record]]
- [[../security]] · [[../../../../architecture/patterns/encryption]]

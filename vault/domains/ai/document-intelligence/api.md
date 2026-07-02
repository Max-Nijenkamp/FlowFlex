---
domain: ai
module: document-intelligence
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Document Intelligence — API / DTOs

The surface is two input DTOs plus the internal `ExtractionService`. There is no external HTTP API; extraction runs via a queued job and apply runs in-process through the target module's service.

---

## CreateExtractionData (input)

Written by the `DocumentExtractionResource` upload/create.

| Field | Type | Rules |
|---|---|---|
| `file` | uploaded file | `max:10240` KB; MIME whitelist pdf / jpg / png; stored tenant-scoped under `companies/{id}/` |
| `document_type` | enum | in: invoice, receipt, cv — **only accepted when the matching target module is active** for the company |

On save: creates the `ai_extractions` row (`status: processing`) and dispatches `ExtractDocumentJob`.

---

## ApplyExtractionData (input)

Written by the Apply action.

| Field | Type | Rules |
|---|---|---|
| `extraction_id` | ulid | must reference a **reviewed** extraction (`status: reviewed`) |
| `corrected_data` | array | reviewer overrides applied on top of the parsed fields (nullable) |

Mapped by `ExtractionService::apply` to the target module's Create DTO — same validation as manual entry.

---

## ExtractionService (command API — in-process)

- `ExtractionService::apply(ApplyExtractionData): TargetRecord` — maps the confirmed extraction to `ApService::createBill` / `ExpenseService::submit` / recruitment applicant creation. Enforces: review done, actor holds the **target-module create permission**, and the target's own validation. Records `target_record_type` / `target_record_id`; sets `status: applied`. **Never writes the target table directly** ([[../../../security/data-ownership]]).
- Extraction itself is invoked by `ExtractDocumentJob` (see [[architecture]]), not a public method — it calls `LlmGateway::complete('document-intelligence', …)`, which is the metered path owned by [[../model-config/api|ai.config]].

---

## Public / Portal Endpoints

None. Internal `/ai` panel surface + a queued job + an in-process service call.

> [!warning] UNVERIFIED
> No REST endpoint is specced for programmatic upload (e.g. email-in or an API drop). If invoices arrive by email/API rather than manual upload, an ingestion endpoint is needed — not yet designed.

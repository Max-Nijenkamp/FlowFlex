---
domain: ai
module: document-intelligence
feature: apply-to-record
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Apply to Record

Turn a reviewed extraction into a real business record — a bill, an expense, or an applicant — by calling the **target module's own Create service**. This is the only cross-domain write in the module, and it never touches another module's tables directly.

## Behaviour

- Available only on a `reviewed` extraction; requires the actor's **target-module create permission** (holding `ai.document-intelligence.apply` alone is not enough).
- `ExtractionService::apply(ApplyExtractionData)` maps the confirmed (+ corrected) fields to the target Create DTO and calls the owning service: `ApService::createBill` / `ExpenseService::submit` / recruitment applicant creation.
- The target's **own validation runs** — an invalid extraction is rejected exactly like invalid manual entry; the path cannot bypass validation or write the target table directly.
- On success: records `target_record_type` / `target_record_id` and sets `status: applied`.

## UI

- **Kind**: custom-page action   <!-- an action on the review page / resource, not a page of its own -->
- **Page**: Apply button within "Review extraction" (`/app/ai/extractions/{id}/review`) *(route slug assumed)*
- **Layout**: confirm modal summarising what will be created ("Create bill for €1,240 from Acme Ltd?") with the target module + record type named.
- **Key interactions**: click Apply → confirm modal → target service call → on success, link to the created record; on validation failure, show the target's errors inline (reviewer can go back and correct).
- **States**: empty (n/a) · loading (creating target record) · error (target validation failed → inline errors + back to review; missing target permission → "you can't create a bill" ) · selected (n/a).
- **Gating**: `ai.document-intelligence.apply` **and** the target module's create permission (e.g. `finance.ap.create`).

## Data

- Owns / writes: `ai_extractions` only (sets `target_*`, `status: applied`).
- Reads: the reviewed extraction.
- Cross-domain writes: **via the target module's Create service only** — the bill/expense/applicant is owned and written by that module, never by document-intelligence ([[../../../../security/data-ownership]]).

## Relations

- Feeds (command call): `ApService::createBill` ([[../../../finance/accounts-payable/_module|finance.ap]]), `ExpenseService::submit` ([[../../../finance/expenses/_module|finance.expenses]]), applicant creation ([[../../../hr/recruitment/_module|hr.recruitment]]).
- Shared entity: the polymorphic `target_record` pointer (target module owns the record).

## Unknowns

> [!warning] UNVERIFIED
> Whether apply should stay a synchronous target-service call (current design, immediate validation errors) or become an `ExtractionApplied` event consumed by the target module is unresolved. See [[../decisions]] + [[../unknowns]].

## Related

- [[../_module|Document Intelligence]] · [[review-and-confirm|Review & Confirm]] · [[upload-and-extract|Upload & Extract]]
- [[../api]] · [[../../../../security/data-ownership]]

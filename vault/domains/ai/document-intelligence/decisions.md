---
domain: ai
module: document-intelligence
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Document Intelligence — Decisions

---

## Human review is mandatory before any record is created

An extraction never auto-creates a bill/expense/applicant. The `status: reviewed` gate (a human confirming/correcting the fields) sits between extraction and apply. Rationale: an LLM hallucination or a poisoned document must not silently write a fraudulent financial or HR record. Review is a security + correctness control, not UX polish.

---

## Apply goes through the target module's Create service — never a direct write

`ExtractionService::apply` maps the confirmed extraction to `ApService::createBill` / `ExpenseService::submit` / recruitment applicant creation. It passes the same validation as manual entry and requires the actor's target-module create permission. This module writes only `ai_extractions`; the created record is owned and written by the target module ([[../../../security/data-ownership]]).

---

## Apply is a synchronous service call, not an event *(for now)*

Apply calls the target service in-process so the reviewer sees validation errors immediately. An event-driven alternative (fire `ExtractionApplied`, target module's listener creates the record) would decouple further but loses that immediate error surface. Recorded as an alternative, not adopted — see [[unknowns]].

---

## Extraction runs on the queue, metered through `LlmGateway`

OCR + LLM/vision are slow, so extraction is an `ExtractDocumentJob` on the `default` queue under `WithCompanyContext`. All model calls go through `LlmGateway::complete`, which meters tokens/cost and enforces the budget — document-intelligence never calls a provider directly.

---

## `extracted_data` is encrypted at rest

Parsed fields routinely contain IBAN/BIC, DOB, government IDs, and personal email. `extracted_data` uses the `encrypted` cast on a `text` column — never plaintext, never query-scannable jsonb ([[../../../architecture/patterns/encryption]]).

---

## Extraction types gated on target-module activation

An extraction type (invoice / receipt / cv) is offered only when its apply target (finance.ap / finance.expenses / hr.recruitment) is active for the company. No point extracting an invoice a tenant can't turn into a bill.

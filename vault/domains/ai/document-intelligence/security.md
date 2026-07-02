---
domain: ai
module: document-intelligence
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Document Intelligence — Security

See also [[../../../security/tenancy-isolation]], [[../../../security/authn-authz]], [[../../../security/data-ownership]], [[../../../architecture/patterns/encryption]].

---

## Permissions

| Permission | Description |
|---|---|
| `ai.document-intelligence.upload` | Upload a document and trigger extraction |
| `ai.document-intelligence.review` | Open the review screen; confirm/correct extracted fields |
| `ai.document-intelligence.apply` | Apply a reviewed extraction to a target record |

**Plus** the target module's own create permission (e.g. `finance.ap.create`, `finance.expenses.create`, `hr.recruitment.applicants.create`) — checked at **apply** time. Holding `ai.document-intelligence.apply` alone is never sufficient to create a bill/expense/applicant.

---

## Access Contract

```php
canAccess() = Auth::user()->can('ai.document-intelligence.view-any')
           && BillingService::hasModule('ai.document-intelligence')
```

Per [[../../../architecture/filament-patterns]] #1 — every artifact gates on both; custom pages state it explicitly.

---

## Upload Contract

- **Size:** `max:10240` KB per file (medium finding, [[../../../build/security-audit-2026-06-11]]) — stated explicitly alongside the type whitelist.
- **Type whitelist:** pdf / jpg / png only. No arbitrary MIME types reach the extraction path.
- **Path:** stored tenant-scoped under `companies/{id}/` via [[../../core/file-storage/_module|core.files]] — one company can never read another's document media (CompanyScope + path isolation).

---

## Extracted Data at Rest

- `ai_extractions.extracted_data` uses the `encrypted` cast on a `text` column — it holds IBAN/BIC, DOB, government IDs, and personal email pulled off documents. Never plaintext, never query-scannable jsonb ([[../../../architecture/patterns/encryption]]).

---

## Human Review as a Security + Correctness Control

- **No record is created without a human confirming the extraction.** The mandatory review step (`status: reviewed` before apply) is not just UX polish — it prevents an LLM hallucination or a poisoned document from silently writing a fraudulent bill/expense/applicant.
- **Apply always goes through the target DTO's validation.** `ExtractionService::apply` maps to the owning module's Create service, which validates exactly as it would for manual entry. An invalid extraction is rejected the same way invalid manual input is — the extraction path **cannot** bypass target validation or write the target table directly ([[../../../security/data-ownership]]).

---

## Tenant Isolation

- All `ai_extractions` rows scoped by `company_id` via `BelongsToCompany` + `CompanyScope`.
- `ExtractDocumentJob` runs under `WithCompanyContext`, so media resolution, the `LlmGateway` usage log, and the written row all bind to the acting company.

See [[../../../security/tenancy-isolation]] and [[../../../architecture/multi-tenancy]].

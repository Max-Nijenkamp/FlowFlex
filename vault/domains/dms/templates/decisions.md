---
domain: dms
module: templates
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Document Templates — Decisions

## ADR: Generated documents go through `dms.library`, never a direct write

- **Context:** Generation produces a finished document that must live in the Document Library with folder access, versioning, and search — all owned by `dms.library`.
- **Decision:** `TemplateService::generate` stores its output by calling `DocumentService::upload`; templates never writes `dms_documents` or media bytes itself.
- **Consequences:** One owning service for every document write; tenant isolation, folder-access checks, and text extraction all apply for free. Templates owns only `dms_templates` ([[../../../security/data-ownership]]).

## ADR: Merge sources are a whitelist registry, sensitive fields excluded *(assumed)*

- **Context:** Merge fields could otherwise pull arbitrary HR / CRM columns into a document.
- **Decision:** `MergeSourceRegistry` exposes a fixed per-provider whitelist; sensitive fields (salary, national ID) are never registered. Templates declare only whitelisted or manual fields.
- **Consequences:** Data exposure is bounded to the whitelist; HR / CRM stay read-only merge sources; absent modules just fall back to manual entry.

## ADR: System templates are read-only, copy-on-edit

- **Context:** Built-in starter templates (`is_system`) should be a stable baseline, not editable in place.
- **Decision:** Seeded `is_system` templates are read-only; editing one creates a company-owned copy (`is_system = false`) that the user then edits.
- **Consequences:** The baseline can be re-seeded/upgraded without clobbering user edits; no accidental mutation of shared starters.

## ADR: PDF via `spatie/laravel-pdf`, rendered inline *(assumed)*

- **Context:** Output must be either a plain document or a branded PDF.
- **Decision:** PDF output renders through `spatie/laravel-pdf` with the company brand, inline in the (rate-limited) generate request v1.
- **Consequences:** No queue dependency for v1; if rendering proves heavy a queued render job is a follow-up ([[unknowns]]).

## ADR: Unknown placeholder at save is a validation error

- **Decision:** Every `{{field}}` in the body must match a declared merge field; an undeclared placeholder blocks save with an error listing it.
- **Consequences:** Generation can always resolve every placeholder; no silent undeclared-field leakage into output.

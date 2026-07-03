---
domain: dms
module: templates
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Document Templates — Architecture

## Services & Actions

| Class | Type | Responsibility |
|---|---|---|
| `TemplateService::generate(GenerateDocumentData): DocumentData` | service method | Resolve merge source → substitute declared fields into the purified body → render (PDF via `spatie/laravel-pdf` when requested) → store via `dms.library` `DocumentService::upload`. Returns the library `DocumentData`. |
| `MergeSourceRegistry::register(string $type, class-string $provider)` | registry / support | HR & CRM providers map declared fields → model data. **Whitelisted fields only**; sensitive fields never registered. Providers register at boot when their module is active. |
| Save-time placeholder check | validation | Every `{{field}}` in the body must be a declared merge field; an unknown placeholder at save → validation error listing it. |

`TemplateService` reads employee/contact data **only** through the registered providers (read-only), and writes documents **only** through the library service — never `dms_documents` directly ([[../../../security/data-ownership]]).

## Filament Artifacts

**Nav group:** Templates

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `DocumentTemplateResource` | #1 CRUD resource | tweaks: custom-header-actions (duplicate/copy-on-edit for system templates) | body is a Tiptap editor (HTMLPurifier) with a merge-field insert menu; `is_system` rows read-only |
| `GenerateFromTemplatePage` | #7 wizard custom page | [[../../../architecture/patterns/page-blueprints#Wizard]] | stepper: template → merge source / fields → target folder → output; generate gated on `dms.templates.generate` |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('dms.templates.view-any') && BillingService::hasModule('dms.templates')`
per [[../../../architecture/filament-patterns]] #1. `GenerateFromTemplatePage` is a custom page and MUST state it
explicitly — Filament does not auto-gate custom pages. The generate action carries an additional
`dms.templates.generate` gate, and its target folder must pass `dms.library`'s `accessibleFoldersFor` (a second gate).

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Template CRUD (`DocumentTemplateResource` form) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| System-template copy-on-edit | n/a | Creates a new company-owned row (append); the seeded `is_system` original is never mutated |
| Generate document | n/a | Writes no `dms_templates` row — the output document is created through `dms.library`'s `DocumentService::upload` (single writer in that module) |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Events

None fired or consumed. Templates defines no cross-domain events in v1; see [[../../../architecture/event-bus]] for the platform contract and [[unknowns]].

## Jobs & Scheduling

None v1. Generation runs inline in the request (rate-limited — see [[security]]); if PDF rendering proves heavy, a queued render job is an open question in [[unknowns]] *(assumed)*.

## Search & Realtime

No search index, no realtime. Templates are a small per-company set browsed through the resource table.

---
domain: dms
module: templates
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
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

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `DocumentTemplateResource` | Templates | #1 CRUD resource | Table + form; body is a Tiptap editor with a merge-field insert menu. System templates (`is_system`) are read-only → copy-on-edit. |
| `GenerateFromTemplatePage` | Templates | #7 wizard custom page | Stepper: template → merge source / fields → target folder → generate. |

See [[../../../architecture/filament-patterns]] and [[../../../architecture/ui-strategy]].

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('dms.templates.view-any')
        && BillingService::hasModule('dms.templates');
}
```

Custom pages state this explicitly. The generate action carries an additional `dms.templates.generate` gate.

## Events

None fired or consumed. Templates defines no cross-domain events in v1; see [[../../../architecture/event-bus]] for the platform contract and [[unknowns]].

## Jobs & Scheduling

None v1. Generation runs inline in the request (rate-limited — see [[security]]); if PDF rendering proves heavy, a queued render job is an open question in [[unknowns]] *(assumed)*.

## Search & Realtime

No search index, no realtime. Templates are a small per-company set browsed through the resource table.

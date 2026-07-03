---
domain: core
module: file-storage
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# File Storage

File upload and media management for all domains via `spatie/laravel-media-library`. Every model can carry attachments; files are stored under `companies/{company_id}/` in S3/R2. An always-free core module — the storage infrastructure consumed by every domain with uploads.

## Module-key

`core.files`

**Priority:** v1-core  
**Panel:** app (no standalone surface — Media fields ride inside other modules' forms)  
**Permission prefix:** none (access rides on the owning record's module permissions)  
**Tables:** `media` (Spatie Media Library, + `company_id`)  
**Events:** fires none · consumes none

## Sibling notes

- [[architecture]] — path generator, `FileStorageService`, temporary-URL flow
- [[data-model]] — the Spatie `media` table (+ `company_id`)
- [[security]] — upload MIME/ext validation, pre-signed URLs, tenant path isolation, GDPR erasure
- Features: [[features/path-generator]] · [[features/upload-security]]

No `api.md` — this module exposes no events or DTOs of its own; upload validation rides on per-module Data classes.

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | foundation.tenancy | tenant-segregated storage paths |
| Hard | [[../company-settings/_module]] (core.settings) | max file size per company |

## Core Features

- Polymorphic file attachments on any model via Spatie Media Library
- Storage path always `companies/{company_id}/{model}/{file}` — never raw `Storage::put()`
- `FileStorageService::pathFor($model, $filename)` — single method for constructing storage paths (custom `PathGenerator` registered with Media Library)
- Filament Media Library plugin for file fields in Filament forms
- Supported types: documents (PDF, DOCX), images (JPEG, PNG, WebP), spreadsheets (XLSX, CSV)
- Upload security: MIME + extension validation, no executables, size limits (see [[security]])
- Max file size configurable per company in Company Settings
- File URLs are pre-signed S3 URLs (1h TTL) — no public file exposure
- GDPR: files ABOUT a person deleted on erasure per [[../../../architecture/data-lifecycle]]

## Test Checklist

- [ ] Tenant isolation: company A cannot resolve a temporary URL for company B media *(assumed: ownership check in action)*
- [ ] Module gating: n/a (platform module, always active — storage infra for every domain)
- [ ] Every stored file path starts with `companies/{company_id}/` (PathGenerator test, incl. conversions)
- [ ] Disallowed extension (.php / .exe) rejected
- [ ] MIME / extension mismatch rejected
- [ ] Per-company max size enforced from settings
- [ ] Temporary URL expires (TTL set)

## Build Manifest (corrected to flat paths)

```
database/migrations/xxxx_create_media_table.php (published + company_id)
app/Support/Media/CompanyPathGenerator.php
app/Support/Services/FileStorageService.php
app/Actions/TemporaryUrlAction.php
config/media-library.php (path_generator binding)
tests/Feature/Core/{FileStoragePathTest,FileUploadSecurityTest}.php
```

Spec listed `app/Actions/Core/TemporaryUrlAction.php`; real layout is flat — corrected above.

## Cross-Domain Edges

| Direction | Event | Other module | Effect |
|---|---|---|---|
| — | none | all domains | Fires and consumes no domain events. Every domain with uploads **calls** `FileStorageService` / a Media Library field; file-storage is the sole writer of the `media` table. Person-related media erasure is driven by [[../data-privacy/_module]] via the [[../../../architecture/data-lifecycle]] cascade (data-privacy triggers; the owning domain removes its own media). |

Data ownership: file-storage owns and writes only the `media` table (+ the physical `companies/{company_id}/...` layout). It reads per-company max-file-size from [[../company-settings/_module]] and `CompanyContext` read-only, and references polymorphic owner models (owned by other domains) as type+id only. Other domains store files by calling the service, never by writing `media` directly ([[../../../security/data-ownership]]).

## Related

- [[../../../architecture/multi-tenancy]] — storage path tenant isolation
- [[../../../security/encryption]]
- [[../../../architecture/data-lifecycle]] — file erasure rules
- [[../../../decisions/decision-2026-06-20-full-mapping-conventions]]
- [[../company-settings/_module]] · [[../../../architecture/packages]] · [[../../../glossary]]

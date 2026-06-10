---
type: module
domain: Core Platform
domain-key: core
panel: app
module-key: core.files
status: planned
priority: v1-core
depends-on: [foundation.tenancy, core.settings]
soft-depends: []
fires-events: []
consumes-events: []
patterns: []
tables: [media]
permission-prefix: core.files
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# File Storage

File upload and media management for all domains via `spatie/laravel-media-library`. Every model can have attachments. Files stored under `companies/{company_id}/` in S3/R2. Always-free core module — infrastructure consumed by every domain with uploads.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/foundation/multi-tenancy-layer\|foundation.tenancy]] | tenant-segregated storage paths |
| Hard | [[domains/core/company-settings\|core.settings]] | max file size per company |

---

## Core Features

- Polymorphic file attachments on any model via Spatie Media Library
- Storage path always `companies/{company_id}/{model}/{file}` — never raw `Storage::put()`
- `FileStorageService::pathFor($model, $filename)` — single method for constructing storage paths (custom `PathGenerator` registered with Media Library)
- Filament Media Library plugin for file fields in Filament forms
- File types: documents (PDF, DOCX), images (JPEG, PNG, WebP), spreadsheets (XLSX, CSV)
- Upload security per [[architecture/security]]: MIME + extension validation, no executables, size limits
- Max file size configurable per company in Company Settings
- File URLs are pre-signed S3 URLs (1h TTL) — no public file exposure
- GDPR: files ABOUT a person deleted on erasure per [[architecture/data-lifecycle]]

---

## Data Model

Spatie `media` table (published migration) + `company_id` column added, indexed. No other tables.

## DTOs

Upload validation handled by Filament/Media Library field config + per-module Data classes (`FileTypes`, `MaxSize` attributes per [[architecture/security]]).

## Services & Actions

- `FileStorageService::pathFor(Model $model, string $filename): string` — `companies/{company_id}/{table}/{model_id}/{filename}`
- `CompanyPathGenerator` (Media Library `PathGenerator` implementation) — enforces the prefix for every conversion/responsive image too
- `TemporaryUrlAction::run(Media $media): string` — pre-signed URL, 1h

---

## Filament

No standalone resource — Media Library plugin fields used inside other modules' forms. Optional `/app` storage usage widget *(assumed: deferred)*.

---

## Permissions

None of its own — access control rides on the owning record's module permissions.

---

## Test Checklist

- [ ] Every stored file path starts with `companies/{company_id}/` (PathGenerator test, incl. conversions)
- [ ] Tenant isolation: company A cannot resolve a temporary URL for company B media *(assumed: ownership check in action)*
- [ ] Disallowed extension (.php/.exe) rejected
- [ ] MIME/extension mismatch rejected
- [ ] Per-company max size enforced from settings
- [ ] Temporary URL expires (TTL set)

---

## Build Manifest

```
database/migrations/xxxx_create_media_table.php (published + company_id)
app/Support/Media/CompanyPathGenerator.php
app/Support/Services/FileStorageService.php
app/Actions/Core/TemporaryUrlAction.php
config/media-library.php (path_generator binding)
tests/Feature/Core/{FileStoragePathTest,FileUploadSecurityTest}.php
```

---

## Related

- [[architecture/multi-tenancy]] — storage path tenant isolation
- [[architecture/security]] — upload rules
- [[architecture/data-lifecycle]] — file erasure rules
- [[architecture/packages]] (`filament/spatie-laravel-media-library-plugin`)

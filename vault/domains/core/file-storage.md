---
type: module
domain: Core Platform
panel: app
module-key: core.files
status: planned
color: "#4ADE80"
---

# File Storage

File upload and media management for all domains via `spatie/laravel-media-library`. Every model can have attachments. Files stored under `companies/{company_id}/` in S3/R2.

---

## Core Features

- Polymorphic file attachments on any model via Spatie Media Library
- Storage path always `companies/{company_id}/{model}/{file}` — never raw `Storage::put()`
- `FileStorageService::pathFor($model, $filename)` — single method for constructing storage paths
- Filament Media Library plugin for file fields in Filament forms
- File types: documents (PDF, DOCX), images (JPEG, PNG, WebP), spreadsheets (XLSX, CSV)
- Max file size configurable per company in Company Settings
- File URLs are pre-signed S3 URLs — no public file exposure

---

## Filament

**All panels:** Filament Media Library plugin fields available in any resource form.

---

## Related

- [[architecture/multi-tenancy]] — storage path tenant isolation
- [[architecture/packages]] (`filament/spatie-laravel-media-library-plugin`)

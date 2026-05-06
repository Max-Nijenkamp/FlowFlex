---
tags: [flowflex, core, files, storage, s3, phase/1]
domain: Core Platform
panel: workspace
color: "#2199C8"
status: built
last_updated: 2026-05-06
---

# File Storage

Unified file storage abstraction used by every module. Files are stored on S3 (or Cloudflare R2 in production) with a local disk fallback for development. All access goes through signed, expiring URLs — raw storage paths are never exposed.

**Who uses it:** Every module that handles documents, images, or attachments
**Filament Panel:** used internally; no standalone panel page
**Depends on:** [[Multi-Tenancy & Workspace]]
**Build complexity:** Low — 1 model, 1 service, 2 migrations

## Storage Path Convention

```
companies/{company-slug}/{collection}/{filename}
```

Example: `companies/acme-corp/hr-documents/contract_john_doe.pdf`

## Collections

Arbitrary string namespace per module. Examples:

| Collection | Used by |
|---|---|
| `hr-documents` | HR module — contracts, right-to-work |
| `invoices` | Finance — PDF invoices |
| `avatars` | Workspace — company logos, profile photos |
| `tasks` | Projects — task attachments |
| `expense-receipts` | Finance — receipt photos |

## Database Table: `files`

| Column | Type | Notes |
|---|---|---|
| `id` | char(26) ULID | Primary key |
| `company_id` | char(26) | FK to companies |
| `uploaded_by_tenant_id` | char(26) | FK to tenants |
| `disk` | varchar | `s3`, `r2`, `local` |
| `path` | varchar | Full storage path |
| `original_name` | varchar | Original filename from uploader |
| `mime_type` | varchar | e.g. `application/pdf`, `image/png` |
| `size` | integer | File size in bytes |
| `collection` | varchar | Logical grouping string |
| `model_type` | varchar | Polymorphic morph type |
| `model_id` | varchar | Polymorphic morph id |
| `deleted_at` | timestamp | Soft delete |

## File Model

`app/Models/File.php`

```php
$file->url(60);          // signed URL valid 60 minutes (falls back to public URL on local disk)
$file->humanSize();      // "1.23 MB"
$file->isImage();        // true if mime starts with 'image/'

// Scopes
File::inCollection('hr-documents')->get();
File::forModel($employee)->get();
```

## FileStorageService

`app/Services/FileStorageService.php` — singleton registered in `AppServiceProvider`.

```php
// Store a file (returns File model)
$file = $fileStorageService->store(
    file: $request->file('document'),
    collection: 'hr-documents',
    disk: 's3',             // optional, defaults to config('filesystems.default')
    model: $employee,       // optional polymorphic relation
);

// Delete a file (removes from disk + soft-deletes DB record)
$fileStorageService->delete($file);

// Get a signed URL
$url = $fileStorageService->temporaryUrl($file, minutes: 60);
```

## URL Security

- All URLs are **signed and time-limited** via `Storage::temporaryUrl()`
- Local disk doesn't support temporary URLs — falls back to `Storage::url()` automatically
- Raw S3 paths are never returned to the browser
- File access goes through the `File` model, not direct disk calls

## Company Logo

`app/Models/Company.php` — `logo_file_id` column, `logo(): BelongsTo`, `logoUrl(): ?string`.
Managed via `ManageCompany` workspace settings page with Filament `FileUpload` component.

## Related

- [[Multi-Tenancy & Workspace]]
- [[Document Management]] (Phase 2 — user-facing file browser)
- [[Security Rules]]
- [[Tech Stack]]

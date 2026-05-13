---
type: module
domain: Core Platform
panel: app
module-key: core.files
status: planned
color: "#4ADE80"
---

# File Storage

> spatie/laravel-media-library integration with S3/R2 backend — attach, version, and retrieve files on any model across every domain.

**Panel:** `app`
**Module key:** `core.files`

## What It Does

File Storage provides the unified media attachment layer for all other domain modules. Any Eloquent model that uses the `HasMedia` interface from `spatie/laravel-media-library` can store and retrieve files through this module. Files are stored on Cloudflare R2 (or any S3-compatible backend) with local disk fallback for development. The module handles upload, virus scanning, conversion (e.g. thumbnail generation for images), and serving via signed temporary URLs to prevent direct public access to tenant files.

## Features

### Core
- `spatie/laravel-media-library` installed and configured with S3/R2 backend in production, local disk in development
- Any model adds file attachment support via `implements HasMedia` + `use InteractsWithMedia` — no per-domain plumbing required
- Media collections per model: each model defines named collections with rules (e.g. `employees` → profile photo max 5 MB PNG/JPG; `invoices` → PDF attachments max 20 MB)
- Signed temporary URLs for all file downloads — files never publicly accessible without an authenticated signed link (TTL: 30 minutes)
- Upload via Filament `FileUpload` component — drag-and-drop, progress bar, validation feedback

### Advanced
- File versioning: re-uploading to the same collection slot creates a new version; previous versions retained and retrievable
- Media conversions: automatic thumbnail generation for images (200×200, 800×600) via queued conversion jobs
- Virus scanning: uploaded files scanned via ClamAV (configurable) before being made available — infected files quarantined and owner notified
- Per-collection storage quotas: configurable per company (e.g. 10 GB total) with dashboard showing usage
- Soft delete: media records soft-deleted when parent model is deleted; hard delete runs via a scheduled cleanup job after retention period

### AI-Powered
- Document parsing: PDFs uploaded to relevant collections (invoices, contracts) are OCR-parsed and text extracted for search indexing — enables full-text search across all uploaded documents
- Auto-tagging: image uploads analysed to suggest tags (logo, photo, document, chart) for easier retrieval

## Data Model

```erDiagram
    media {
        bigint id PK
        ulid company_id FK
        string model_type
        ulid model_id FK
        string collection_name
        string name
        string file_name
        string mime_type
        string disk
        string conversions_disk
        bigint size
        json manipulations
        json custom_properties
        json generated_conversions
        json responsive_images
        integer order_column
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `collection_name` | Named collection defined on the parent model |
| `disk` | `s3` in production, `local` in development |
| `size` | File size in bytes |
| `generated_conversions` | JSON tracking which image conversions have completed |

## Permissions

- `core.files.upload`
- `core.files.download`
- `core.files.delete`
- `core.files.view-all`
- `core.files.manage-quotas`

## Filament

- **Resource:** `MediaLibraryResource` — admin-only view of all files per company with size, collection, and model link
- **Pages:** None (uploads handled inline on parent model forms)
- **Custom pages:** None
- **Widgets:** `StorageUsageWidget` — company storage quota bar shown on dashboard
- **Nav group:** Settings (app panel, admin only)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Dropbox Business | Document storage for business files |
| Box | Secure file management and sharing |
| Google Drive | Workspace file attachment |
| DocuWare | Document management and storage |

## Related

- [[data-import]]
- [[company-settings]]
- [[audit-log]]

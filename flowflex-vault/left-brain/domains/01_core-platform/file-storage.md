---
type: module
domain: Core Platform
panel: app
cssclasses: domain-admin
phase: 1
status: complete
migration_range: 010001–019999
last_updated: 2026-05-12
right_brain_log: "[[builder-log-core-platform-phase1]]"
---

# File Storage

Centralised file attachment infrastructure using `spatie/laravel-medialibrary` v11. All file uploads across every domain module go through this layer. Files are stored in company-scoped S3 paths; private files are served via signed URLs. There is no dedicated Filament resource — file management is embedded in each domain module's resource.

**Panel:** embedded in all domain panels (no standalone file storage panel)  
**Phase:** 1 — must exist before any domain module stores files

---

## Features

### Media Library Integration

- `spatie/laravel-medialibrary` v11 as the universal attachment layer
- Any domain model that needs file attachments adds `HasMedia` + `InteractsWithMedia` traits and defines its collections via `registerMediaCollections()`
- The `media` table (published migration) stores all attachment metadata
- Media items are always company-scoped via the parent model's `company_id`

### Company-Scoped S3 Paths

Storage path pattern: `company/{company_id}/{collection}/{filename}`

Examples:
- Company logo: `company/01JXXXX/logos/logo.png`
- Employee contract: `company/01JXXXX/contracts/contract-01JYYYY.pdf`
- Import file: `company/01JXXXX/imports/employees-2026-05-10.csv`

This path structure ensures:
- Files from different tenants never share a prefix
- S3 bucket policies can be scoped per company prefix
- Pre-signed URLs can be verified against the requesting company

### Storage Disks

| Environment | Disk | Notes |
|-------------|------|-------|
| Production | `s3` | AWS S3 or S3-compatible (e.g. Cloudflare R2) |
| Development | `local` | `storage/app/public/` |
| Testing | `local` (fake) | `Storage::fake('local')` in tests |

### Signed URLs (Private Files)

- All files stored in `s3` disk are private by default (no public ACL)
- Download URLs are generated via `getTemporaryUrl()` with a 24-hour TTL
- Domain resources must never expose raw S3 paths — always generate a signed URL at request time

### File Size Limits

| Type | Max Size |
|------|----------|
| Images (logo, avatar) | 5 MB |
| Documents (PDF, Word) | 20 MB |
| Import files (CSV, XLSX) | 50 MB |

These limits are enforced at the Filament `FileUpload` component level per domain resource.

### Company Branding Files

- `Company` model gets `HasMedia` for two collections:
  - `logo` — used in app panel header and emails
  - `favicon` — used as browser tab icon
- `logo_path` and `favicon_path` columns on the `companies` table store the public/signed URL for quick access without a media query

### Domain Model Pattern

Any domain model that stores files:

```php
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class EmployeeDocument extends Model implements HasMedia
{
    use InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('contracts')
            ->singleFile();
    }
}
```

---

## Data Model

The `media` table is managed by `spatie/laravel-medialibrary` (published migration). Key columns:

```
media {
    bigint id PK
    string model_type
    string model_id        -- ULID of the parent model
    string uuid "unique"
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

---

## Permissions

Managed per domain — no `core.files.*` permissions needed at the Core Platform level. Each domain resource controls who can upload/delete attachments via its own Filament `canCreate()`, `canDelete()` guards.

---

## Related

- [[MOC_CorePlatform]]
- [[company-workspace-settings]] — branding uploads use this layer
- [[data-import-engine]] — import files stored via this layer
- [[concept-multi-tenancy]]

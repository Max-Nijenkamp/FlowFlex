---
type: module
domain: Learning & Development
panel: lms
module-key: lms.content
status: planned
color: "#4ADE80"
---

# Content Library

> Centralised shared media library for course content — videos, PDFs, images, and SCORM packages — reusable across any course.

**Panel:** `lms`
**Module key:** `lms.content`

---

## What It Does

Content Library is the asset management backbone of the LMS panel. Instead of uploading the same video or PDF into multiple courses, authors upload once to the library and embed content into any lesson with a reference. The library supports tagging, search, version replacement (upload a new version of a file while maintaining the same embed reference), and SCORM package hosting for organisations migrating content from legacy LMS systems.

---

## Features

### Core
- File upload: video (MP4/WebM), PDF, images, audio, PowerPoint
- Video hosting: direct upload with adaptive streaming (HLS)
- SCORM 1.2 and SCORM 2004 package upload and hosting
- xAPI (Tin Can) statement receiving and storage
- Tagging and categorisation for findability
- Search: full-text search across titles, descriptions, and tags
- Embed picker: insert library items into course lesson blocks

### Advanced
- Version management: upload replacement file; existing course embeds auto-update
- Usage tracking: see which courses and lessons reference each asset
- Expiry flagging: mark assets as outdated and prompt authors to update linked lessons
- Bulk upload: drag-and-drop multiple files at once
- External links: reference YouTube, Vimeo, or external URLs alongside hosted assets

### AI-Powered
- Auto-tagging: AI suggests relevant tags on upload based on content analysis
- Transcript generation: auto-generate text transcripts from uploaded video
- Duplicate detection: flag files that appear to be duplicates of existing library items

---

## Data Model

```erDiagram
    content_assets {
        ulid id PK
        ulid company_id FK
        string title
        string type
        string file_url
        string mime_type
        integer file_size_bytes
        string scorm_version
        json tags
        boolean is_deprecated
        integer version
        ulid uploaded_by FK
        timestamps created_at_updated_at
    }

    content_asset_usages {
        ulid id PK
        ulid asset_id FK
        ulid lesson_id FK
        timestamps created_at_updated_at
    }

    content_assets ||--o{ content_asset_usages : "referenced in"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `content_assets` | Media files | `id`, `company_id`, `title`, `type`, `file_url`, `scorm_version`, `is_deprecated` |
| `content_asset_usages` | Where assets are used | `id`, `asset_id`, `lesson_id` |

---

## Permissions

```
lms.content.view-any
lms.content.upload
lms.content.update
lms.content.delete
lms.content.manage-scorm
```

---

## Filament

- **Resource:** `App\Filament\Lms\Resources\ContentAssetResource`
- **Pages:** `ListContentAssets`, `UploadContentAsset`, `EditContentAsset`
- **Custom pages:** `ContentLibraryBrowserPage` (media picker used inside course builder)
- **Widgets:** `LibraryStorageWidget`, `DeprecatedAssetsWidget`
- **Nav group:** Catalog

---

## Displaces

| Feature | FlowFlex | Cornerstone | Docebo | TalentLMS |
|---|---|---|---|---|
| SCORM hosting | Yes | Yes | Yes | Yes |
| xAPI support | Yes | Yes | Yes | No |
| Version management | Yes | Yes | No | No |
| AI auto-tagging | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[courses]] — lessons embed assets from the content library
- [[assessments]] — assessment media stored here
- [[compliance-training]] — compliance course materials
- [[analytics]] — asset usage and engagement metrics

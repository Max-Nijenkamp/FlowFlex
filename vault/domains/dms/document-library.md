---
type: module
domain: Document Management
panel: dms
module-key: dms.library
status: planned
color: "#4ADE80"
---

# Document Library

> Centralised document repository — upload, categorise, tag, search, and version history for all company documents.

**Panel:** `dms`
**Module key:** `dms.library`

---

## What It Does

Document Library is the single repository for all company documents. Teams upload files in any format — PDF, Word, Excel, PowerPoint, images — and organise them in a folder hierarchy with categories and tags. Every document has a full version history; uploading a new version preserves the previous ones. Full-text search works across document titles, tags, and text content (for supported formats). Access permissions can be set at folder or document level, restricting visibility to specific teams or roles.

---

## Features

### Core
- Folder hierarchy: create nested folders to organise documents by department, project, or type
- File upload: drag-and-drop or file picker for PDF, Office formats, images, and other file types
- Version history: upload a new version; previous versions retained and accessible
- Tagging: multi-value tags for cross-folder categorisation
- Full-text search: search across titles, tags, and extracted text content
- Access control: set view and edit permissions at folder or document level

### Advanced
- Document categories: classify documents by type (policy, contract, report, template, invoice)
- Star/favourite: bookmark frequently accessed documents
- Bulk upload: upload multiple files at once with shared metadata
- Download history: see who downloaded a document and when
- Thumbnail preview: in-browser preview for PDFs and images without downloading

### AI-Powered
- Auto-tagging: AI suggests tags on upload based on document content analysis
- Duplicate detection: flag when an uploaded file appears to be a duplicate of an existing document
- Summary generation: AI generates a one-paragraph document summary on upload

---

## Data Model

```erDiagram
    dms_folders {
        ulid id PK
        ulid company_id FK
        ulid parent_id FK
        string name
        json permissions
        timestamps created_at_updated_at
    }

    dms_documents {
        ulid id PK
        ulid folder_id FK
        ulid company_id FK
        string title
        string file_url
        string mime_type
        integer file_size_bytes
        string category
        json tags
        integer version
        ulid uploaded_by FK
        timestamp deleted_at
        timestamps created_at_updated_at
    }

    dms_document_versions {
        ulid id PK
        ulid document_id FK
        integer version_number
        string file_url
        ulid uploaded_by FK
        timestamp created_at
    }

    dms_folders ||--o{ dms_documents : "contains"
    dms_documents ||--o{ dms_document_versions : "has"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `dms_folders` | Folder hierarchy | `id`, `company_id`, `parent_id`, `name`, `permissions` |
| `dms_documents` | Document records | `id`, `folder_id`, `title`, `file_url`, `category`, `tags`, `version` |
| `dms_document_versions` | Version history | `id`, `document_id`, `version_number`, `file_url` |

---

## Permissions

```
dms.library.view
dms.library.upload
dms.library.update
dms.library.delete
dms.library.manage-permissions
```

---

## Filament

- **Resource:** `App\Filament\Dms\Resources\DmsDocumentResource`
- **Pages:** `ListDmsDocuments`, `CreateDmsDocument`, `ViewDmsDocument`
- **Custom pages:** `DocumentLibraryBrowserPage`, `DocumentVersionHistoryPage`
- **Widgets:** `RecentDocumentsWidget`, `StorageUsageWidget`
- **Nav group:** Library

---

## Displaces

| Feature | FlowFlex | SharePoint | Box | Confluence |
|---|---|---|---|---|
| Version history | Yes | Yes | Yes | Yes |
| Full-text search | Yes | Yes | Yes | Yes |
| AI auto-tagging | Yes | No | No | No |
| Native platform integration | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[document-collaboration]] — collaboration on documents stored here
- [[document-workflows]] — documents submitted to review/approval workflows
- [[document-retention]] — retention policies applied to document categories
- [[document-templates]] — templates stored in the library

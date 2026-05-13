---
type: module
domain: Projects & Work
panel: projects
module-key: projects.documents
status: planned
color: "#4ADE80"
---

# Documents

> Project document management — upload, version, organise, and link files to tasks and milestones within the context of a project.

**Panel:** `projects`
**Module key:** `projects.documents`

## What It Does

The Documents module gives each project its own document library. Team members upload files (specs, designs, reports, contracts) directly into the project. Each document can be versioned — uploading a new version retains the history of previous versions. Documents can be linked to specific tasks or milestones so context is always one click away. Access is controlled by project membership. The module uses the Core File Storage module for actual upload and storage — it adds the project-scoped organisation layer on top.

## Features

### Core
- Project document library: list of all files uploaded to a project with name, type, uploader, and upload date
- Upload: drag-and-drop or file picker — supports any file type, size limit set by Core File Storage quota
- Folders: organise documents into named folders within the project (e.g. Designs, Specs, Contracts)
- Task link: attach a document to one or more tasks or milestones — visible in the task detail attachment tab
- Search: full-text search across document names within the current project

### Advanced
- Document versioning: re-upload a file to the same document record to create a new version — version history list with download link for each version
- Preview: inline preview for common formats (PDF, PNG, JPG, DOCX via Google Docs viewer embed)
- Approval workflow: mark a document as "under review" — reviewer approves or requests changes — triggers the Approvals module
- Expiry dates: set an expiry date on a document (e.g. insurance certificate valid until December) — alert sent before expiry
- Sharing: generate a time-limited signed URL to share a document with an external party without FlowFlex access

### AI-Powered
- Auto-tagging: AI analyses the document name and first page content to suggest tags (spec, design, contract, report) for easier filtering
- Duplicate detection: when uploading, AI compares against existing documents in the project and warns if a very similar file already exists

## Data Model

```erDiagram
    proj_documents {
        ulid id PK
        ulid company_id FK
        ulid project_id FK
        ulid folder_id FK
        string name
        string description
        integer version
        string status
        date expires_at
        ulid uploaded_by FK
        timestamps created_at/updated_at
    }

    proj_document_versions {
        ulid id PK
        ulid document_id FK
        integer version_number
        string file_path
        bigint file_size
        ulid uploaded_by FK
        timestamps created_at/updated_at
    }

    proj_document_links {
        ulid document_id FK
        string linkable_type
        ulid linkable_id FK
    }
```

| Column | Notes |
|---|---|
| `version` | Current version number — incremented on each upload |
| `status` | draft / under_review / approved |
| `proj_document_links` | Polymorphic link to tasks or milestones |

## Permissions

- `projects.documents.view`
- `projects.documents.upload`
- `projects.documents.delete`
- `projects.documents.manage-folders`
- `projects.documents.share`

## Filament

- **Resource:** `ProjectDocumentResource`
- **Pages:** `ListProjectDocuments` (with folder tree sidebar), `ViewProjectDocument` (with version history)
- **Custom pages:** None
- **Widgets:** `RecentDocumentsWidget` — five most recently uploaded documents on project dashboard
- **Nav group:** Resources (projects panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Confluence | Project document management |
| Notion | Project file and document storage |
| SharePoint | Team document library |
| Google Drive | Project-scoped document management |

## Related

- [[tasks]]
- [[milestones]]
- [[wikis]]
- [[approvals]]
- [[file-storage]]
